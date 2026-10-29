<?php

namespace Tests\Feature;

use App\Models\Course;
use App\Models\Enrollment;
use App\Models\Payment;
use Database\Seeders\LmsSeeder;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Support\Facades\Http;
use Tests\TestCase;

class EpsPaymentTest extends TestCase
{
    use RefreshDatabase;

    protected function setUp(): void
    {
        parent::setUp();
        $this->seed(LmsSeeder::class);
    }

    public function test_eps_payment_initiation_redirects_to_gateway(): void
    {
        $course = Course::first();

        // Fake EPS API calls
        Http::fake([
            '*/Auth/GetToken' => Http::response([
                'token' => 'mock_jwt_token_12345',
                'status' => 'true',
            ], 200),
            '*/EPSEngine/InitializeEPS' => Http::response([
                'RedirectURL' => 'https://sandboxpg.epay.com.bd/checkout/test-session-123',
                'Status' => 'SUCCESS',
            ], 200),
        ]);

        $response = $this->post(route('eps.pay', $course->slug), [
            'student_name' => 'Md Rafiqul Islam',
            'student_email' => 'rafiq@example.com',
            'student_phone' => '01812345678',
        ]);

        $response->assertRedirect('https://sandboxpg.epay.com.bd/checkout/test-session-123');

        $this->assertDatabaseHas('enrollments', [
            'course_id' => $course->id,
            'student_email' => 'rafiq@example.com',
            'status' => 'pending',
        ]);

        $this->assertDatabaseHas('payments', [
            'course_id' => $course->id,
            'student_email' => 'rafiq@example.com',
            'payment_method' => 'eps',
            'status' => 'pending',
        ]);
    }

    public function test_eps_success_callback_activates_enrollment(): void
    {
        $course = Course::first();
        $trxId = 'CRS' . now()->format('YmdHis') . '9999';

        $enrollment = Enrollment::create([
            'course_id' => $course->id,
            'student_name' => 'Nusrat Jahan',
            'student_email' => 'nusrat@example.com',
            'student_phone' => '01700000000',
            'status' => 'pending',
            'enrolled_at' => now(),
        ]);

        $payment = Payment::create([
            'enrollment_id' => $enrollment->id,
            'course_id' => $course->id,
            'student_name' => 'Nusrat Jahan',
            'student_email' => 'nusrat@example.com',
            'student_phone' => '01700000000',
            'amount' => 999.00,
            'payment_method' => 'eps',
            'transaction_id' => $trxId,
            'status' => 'pending',
        ]);

        // Fake EPS verification API
        Http::fake([
            '*/Auth/GetToken' => Http::response([
                'token' => 'mock_jwt_token_12345',
            ], 200),
            '*/EPSEngine/CheckMerchantTransactionStatus*' => Http::response([
                'Status' => 'SUCCESS',
                'EPSCustomerTransactionId' => 'EPS-CUST-778899',
                'Amount' => '999.00',
            ], 200),
        ]);

        $response = $this->get(route('eps.success', [
            'MerchantTransactionId' => $trxId,
        ]));

        $response->assertRedirect(route('payment.success', $payment->id));

        $this->assertDatabaseHas('enrollments', [
            'id' => $enrollment->id,
            'status' => 'active',
        ]);

        $this->assertDatabaseHas('payments', [
            'id' => $payment->id,
            'status' => 'completed',
        ]);
    }

    public function test_eps_cancel_callback_marks_payment_cancelled(): void
    {
        $course = Course::first();
        $trxId = 'CRS' . now()->format('YmdHis') . '1111';

        $payment = Payment::create([
            'course_id' => $course->id,
            'student_name' => 'Cancelled User',
            'student_email' => 'cancel@example.com',
            'student_phone' => '01700000000',
            'amount' => 999.00,
            'payment_method' => 'eps',
            'transaction_id' => $trxId,
            'status' => 'pending',
        ]);

        $response = $this->get(route('eps.cancel', [
            'MerchantTransactionId' => $trxId,
        ]));

        $response->assertRedirect(route('home'));

        $this->assertDatabaseHas('payments', [
            'id' => $payment->id,
            'status' => 'cancelled',
        ]);
    }

    public function test_repeated_eps_pay_requests_reuse_pending_enrollment_without_creating_duplicates(): void
    {
        $course = Course::first();

        Http::fake([
            '*/Auth/GetToken' => Http::response(['token' => 'mock_jwt_token', 'status' => 'true'], 200),
            '*/EPSEngine/InitializeEPS' => Http::response([
                'RedirectURL' => 'https://sandboxpg.epay.com.bd/checkout/test-session-123',
                'Status' => 'SUCCESS',
            ], 200),
        ]);

        $data = [
            'student_name' => 'Shahamir Shuvo',
            'student_email' => 'shuvo@example.com',
            'student_phone' => '01816377601',
        ];

        // First click
        $this->post(route('eps.pay', $course->slug), $data);

        // Second click 1 second later
        $this->post(route('eps.pay', $course->slug), $data);

        // Third click
        $this->post(route('eps.pay', $course->slug), $data);

        // MUST only have 1 enrollment in the database!
        $this->assertEquals(1, Enrollment::where('student_phone', '01816377601')->count());

        // Older pending payment attempts must be cancelled so only 1 active pending exists
        $this->assertEquals(1, Payment::where('student_phone', '01816377601')->where('status', 'pending')->count());
        $this->assertEquals(2, Payment::where('student_phone', '01816377601')->where('status', 'cancelled')->count());
    }

    public function test_already_active_student_redirects_to_receipt_instead_of_duplicate_enrollment(): void
    {
        $course = Course::first();

        $active = Enrollment::create([
            'course_id' => $course->id,
            'student_name' => 'Active Student',
            'student_email' => 'active@example.com',
            'student_phone' => '01899999999',
            'status' => 'active',
        ]);

        $payment = Payment::create([
            'enrollment_id' => $active->id,
            'course_id' => $course->id,
            'student_name' => 'Active Student',
            'student_email' => 'active@example.com',
            'student_phone' => '01899999999',
            'amount' => 999.00,
            'payment_method' => 'eps',
            'transaction_id' => 'TX_ACTIVE_ALREADY',
            'status' => 'completed',
        ]);

        // Attempting to pay again for the same course
        $response = $this->post(route('eps.pay', $course->slug), [
            'student_name' => 'Active Student',
            'student_email' => 'active@example.com',
            'student_phone' => '01899999999',
        ]);

        $response->assertRedirect(route('payment.success', $payment->id));

        // Still exactly 1 enrollment!
        $this->assertEquals(1, Enrollment::where('student_phone', '01899999999')->count());
    }
}
