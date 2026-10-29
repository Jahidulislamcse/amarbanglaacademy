<?php

namespace Tests\Feature;

use App\Models\Course;
use App\Models\Enrollment;
use App\Models\Payment;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Support\Facades\Hash;
use Tests\TestCase;

class AdminPanelTest extends TestCase
{
    use RefreshDatabase;

    protected User $admin;
    protected Course $course;

    protected function setUp(): void
    {
        parent::setUp();
        $this->seed(\Database\Seeders\LmsSeeder::class);

        $this->admin = User::where('email', 'admin@amar-bangla.edu')->first();
        $this->course = Course::first();
    }

    public function test_guest_is_redirected_to_admin_login(): void
    {
        $response = $this->get('/admin/students');
        $response->assertRedirect('/admin/login');
    }

    public function test_admin_login_page_renders_successfully(): void
    {
        $response = $this->get('/admin/login');
        $response->assertStatus(200);
        $response->assertSee('এডমিন লগইন');
        $response->assertSee('admin@amar-bangla.edu');
    }

    public function test_admin_can_login_with_valid_credentials(): void
    {
        $response = $this->post('/admin/login', [
            'email' => 'admin@amar-bangla.edu',
            'password' => 'password123',
        ]);

        $response->assertRedirect('/admin/students');
        $this->assertAuthenticatedAs($this->admin);
    }

    public function test_admin_can_login_with_secondary_domain_email(): void
    {
        $response = $this->post('/admin/login', [
            'email' => 'admin@amarbanglaacademy.com',
            'password' => 'password123',
        ]);

        $response->assertRedirect('/admin/students');
        $this->assertAuthenticated();
    }

    public function test_admin_cannot_login_with_invalid_credentials(): void
    {
        $response = $this->from('/admin/login')->post('/admin/login', [
            'email' => 'admin@amar-bangla.edu',
            'password' => 'wrongpassword',
        ]);

        $response->assertRedirect('/admin/login');
        $response->assertSessionHasErrors('email');
        $this->assertGuest();
    }

    public function test_authenticated_admin_can_view_student_roster(): void
    {
        $enrollment = Enrollment::create([
            'course_id' => $this->course->id,
            'student_name' => 'রফিকুল ইসলাম',
            'student_email' => 'rafiq@example.com',
            'student_phone' => '01700112233',
            'status' => 'active',
            'enrolled_at' => now(),
        ]);

        $payment = Payment::create([
            'enrollment_id' => $enrollment->id,
            'course_id' => $this->course->id,
            'student_name' => 'রফিকুল ইসলাম',
            'student_email' => 'rafiq@example.com',
            'student_phone' => '01700112233',
            'amount' => 999.00,
            'payment_method' => 'eps',
            'transaction_id' => 'EPS12345678',
            'status' => 'completed',
        ]);

        $response = $this->actingAs($this->admin)->get('/admin/students');

        $response->assertStatus(200);
        $response->assertSee('রফিকুল ইসলাম');
        $response->assertSee('01700112233');
        $response->assertSee('EPS12345678');
        $response->assertSee('EPS অনলাইন');
        $response->assertSee('৳999');
        $response->assertSee('openStudentModal(' . $payment->id . ')', false);
        $response->assertSee('studentDetailModal');
    }

    public function test_admin_can_search_students(): void
    {
        $enrollment1 = Enrollment::create([
            'course_id' => $this->course->id,
            'student_name' => 'শাকিল আহমেদ',
            'student_email' => 'shakil@example.com',
            'student_phone' => '01811223344',
            'status' => 'active',
        ]);

        Payment::create([
            'enrollment_id' => $enrollment1->id,
            'course_id' => $this->course->id,
            'student_name' => 'শাকিল আহমেদ',
            'student_email' => 'shakil@example.com',
            'student_phone' => '01811223344',
            'amount' => 999.00,
            'payment_method' => 'bkash',
            'transaction_id' => 'BK1234567',
            'status' => 'completed',
        ]);

        $enrollment2 = Enrollment::create([
            'course_id' => $this->course->id,
            'student_name' => 'তানজিলা খাতুন',
            'student_email' => 'tanjila@example.com',
            'student_phone' => '01999887766',
            'status' => 'active',
        ]);

        Payment::create([
            'enrollment_id' => $enrollment2->id,
            'course_id' => $this->course->id,
            'student_name' => 'তানজিলা খাতুন',
            'student_email' => 'tanjila@example.com',
            'student_phone' => '01999887766',
            'amount' => 999.00,
            'payment_method' => 'nagad',
            'transaction_id' => 'NG9876543',
            'status' => 'completed',
        ]);

        // Search for Shakil
        $response = $this->actingAs($this->admin)->get('/admin/students?search=শাকিল');
        $response->assertStatus(200);
        $response->assertSee('শাকিল আহমেদ');
        $response->assertDontSee('তানজিলা খাতুন');
    }

    public function test_admin_can_update_payment_status(): void
    {
        $enrollment = Enrollment::create([
            'course_id' => $this->course->id,
            'student_name' => 'কামরুল হাসান',
            'student_email' => 'kamrul@example.com',
            'student_phone' => '01777000111',
            'status' => 'pending',
        ]);

        $payment = Payment::create([
            'enrollment_id' => $enrollment->id,
            'course_id' => $this->course->id,
            'student_name' => 'কামরুল হাসান',
            'student_email' => 'kamrul@example.com',
            'student_phone' => '01777000111',
            'amount' => 999.00,
            'payment_method' => 'bkash',
            'transaction_id' => 'BK999888',
            'status' => 'pending',
        ]);

        $response = $this->actingAs($this->admin)->patch("/admin/payments/{$payment->id}/status", [
            'status' => 'completed',
            'notes' => 'ম্যানুয়াল ভেরিফিকেশন সম্পন্ন',
        ]);

        $response->assertRedirect();
        $this->assertEquals('completed', $payment->fresh()->status);
        $this->assertEquals('active', $enrollment->fresh()->status);
    }

    public function test_admin_can_export_students_csv(): void
    {
        $enrollment = Enrollment::create([
            'course_id' => $this->course->id,
            'student_name' => 'মাহমুদুল হক',
            'student_email' => 'mahmud@example.com',
            'student_phone' => '01600112233',
            'status' => 'active',
        ]);

        Payment::create([
            'enrollment_id' => $enrollment->id,
            'course_id' => $this->course->id,
            'student_name' => 'মাহমুদুল হক',
            'student_email' => 'mahmud@example.com',
            'student_phone' => '01600112233',
            'amount' => 999.00,
            'payment_method' => 'eps',
            'transaction_id' => 'EPS990011',
            'status' => 'completed',
        ]);

        $response = $this->actingAs($this->admin)->get('/admin/students/export');

        $response->assertStatus(200);
        $response->assertHeader('Content-Type', 'text/csv; charset=UTF-8');
        $this->assertStringContainsString('মাহমুদুল হক', $response->streamedContent());
        $this->assertStringContainsString('01600112233', $response->streamedContent());
        $this->assertStringContainsString('EPS990011', $response->streamedContent());
    }

    public function test_pending_payments_are_not_shown_in_students_roster(): void
    {
        // 1. Pending registration (abandoned / incomplete)
        $pendingEnrollment = Enrollment::create([
            'course_id' => $this->course->id,
            'student_name' => 'অসম্পূর্ণ ছাত্র',
            'student_email' => 'incomplete@example.com',
            'student_phone' => '01500000000',
            'status' => 'pending',
        ]);

        Payment::create([
            'enrollment_id' => $pendingEnrollment->id,
            'course_id' => $this->course->id,
            'student_name' => 'অসম্পূর্ণ ছাত্র',
            'student_email' => 'incomplete@example.com',
            'student_phone' => '01500000000',
            'amount' => 999.00,
            'payment_method' => 'eps',
            'transaction_id' => 'EPS_PENDING_999',
            'status' => 'pending',
        ]);

        // 2. Completed registration
        $completedEnrollment = Enrollment::create([
            'course_id' => $this->course->id,
            'student_name' => 'সফল ছাত্র',
            'student_email' => 'success@example.com',
            'student_phone' => '01711223344',
            'status' => 'active',
        ]);

        Payment::create([
            'enrollment_id' => $completedEnrollment->id,
            'course_id' => $this->course->id,
            'student_name' => 'সফল ছাত্র',
            'student_email' => 'success@example.com',
            'student_phone' => '01711223344',
            'amount' => 999.00,
            'payment_method' => 'eps',
            'transaction_id' => 'EPS_SUCCESS_111',
            'status' => 'completed',
        ]);

        $response = $this->actingAs($this->admin)->get('/admin/students');
        $response->assertStatus(200);

        // Completed student appears in the main enrolled roster
        $response->assertSee('সফল ছাত্র');
        $response->assertSee('EPS_SUCCESS_111');

        // Pending student appears in the pending-approval section (for admin review)
        // but must NOT appear in the main completed student roster table
        $response->assertSee('EPS_PENDING_999');         // visible in pending panel
        $response->assertSee('অসম্পূর্ণ ছাত্র');          // visible in pending panel

        // Verify the pending section label is present
        $response->assertSee('অনুমোদনের অপেক্ষায়');

        // Verify CSV export also excludes pending payments
        $csvResponse = $this->actingAs($this->admin)->get('/admin/students/export');
        $this->assertStringContainsString('সফল ছাত্র', $csvResponse->streamedContent());
        $this->assertStringNotContainsString('অসম্পূর্ণ ছাত্র', $csvResponse->streamedContent());
    }

    public function test_admin_can_clean_duplicate_enrollments(): void
    {
        // Create 1 active enrollment
        $active = Enrollment::create([
            'course_id' => $this->course->id,
            'student_name' => 'শাহামির আহমেদ',
            'student_email' => 'shuvo@example.com',
            'student_phone' => '01816377601',
            'status' => 'active',
        ]);

        Payment::create([
            'enrollment_id' => $active->id,
            'course_id' => $this->course->id,
            'student_name' => 'শাহামির আহমেদ',
            'student_email' => 'shuvo@example.com',
            'student_phone' => '01816377601',
            'amount' => 999.00,
            'payment_method' => 'eps',
            'transaction_id' => 'TX_ACT_1',
            'status' => 'completed',
        ]);

        // Create 3 duplicate pending enrollments for the same student
        for ($i = 1; $i <= 3; $i++) {
            $dup = Enrollment::create([
                'course_id' => $this->course->id,
                'student_name' => 'শাহামির আহমেদ',
                'student_email' => 'shuvo@example.com',
                'student_phone' => '01816377601',
                'status' => 'pending',
            ]);

            Payment::create([
                'enrollment_id' => $dup->id,
                'course_id' => $this->course->id,
                'student_name' => 'শাহামির আহমেদ',
                'student_email' => 'shuvo@example.com',
                'student_phone' => '01816377601',
                'amount' => 999.00,
                'payment_method' => 'eps',
                'transaction_id' => 'TX_DUP_' . $i,
                'status' => 'pending',
            ]);
        }

        $this->assertEquals(4, Enrollment::where('student_phone', '01816377601')->count());

        $response = $this->actingAs($this->admin)->post(route('admin.students.clean-duplicates'));
        $response->assertSessionHas('success');

        // Only 1 enrollment should remain for this student
        $this->assertEquals(1, Enrollment::where('student_phone', '01816377601')->count());
        $this->assertEquals('active', Enrollment::where('student_phone', '01816377601')->first()->status);

        // Duplicate pending payments should be cancelled
        $this->assertEquals(3, Payment::where('student_phone', '01816377601')->where('status', 'cancelled')->count());
    }

    public function test_admin_can_verify_eps_payment_live_success(): void
    {
        $payment = Payment::create([
            'course_id' => $this->course->id,
            'student_name' => 'Md Shahadat',
            'student_email' => 'shahadat@example.com',
            'student_phone' => '01314818933',
            'amount' => 999.00,
            'payment_method' => 'eps',
            'transaction_id' => 'CRS202610291335069958',
            'status' => 'pending',
        ]);

        \Illuminate\Support\Facades\Http::fake([
            '*/Auth/GetToken' => \Illuminate\Support\Facades\Http::response(['token' => 'mock_token'], 200),
            '*/EPSEngine/CheckMerchantTransactionStatus*' => \Illuminate\Support\Facades\Http::response([
                'Status' => 'SUCCESS',
                'EPSCustomerTransactionId' => 'CUST_SUCCESS_789',
            ], 200),
        ]);

        $response = $this->actingAs($this->admin)->post(route('admin.payments.verify-eps', $payment->id));
        $response->assertSessionHas('success');

        $this->assertEquals('completed', $payment->fresh()->status);
    }

    public function test_admin_can_verify_eps_payment_live_unpaid(): void
    {
        $payment = Payment::create([
            'course_id' => $this->course->id,
            'student_name' => 'Md Shahadat',
            'student_email' => 'shahadat@example.com',
            'student_phone' => '01314818933',
            'amount' => 999.00,
            'payment_method' => 'eps',
            'transaction_id' => 'CRS202610291335069958_UNPAID',
            'status' => 'pending',
        ]);

        \Illuminate\Support\Facades\Http::fake([
            '*/Auth/GetToken' => \Illuminate\Support\Facades\Http::response(['token' => 'mock_token'], 200),
            '*/EPSEngine/CheckMerchantTransactionStatus*' => \Illuminate\Support\Facades\Http::response([
                'Status' => 'Initialize',
            ], 200),
        ]);

        $response = $this->actingAs($this->admin)->post(route('admin.payments.verify-eps', $payment->id));
        $response->assertSessionHas('error');

        // Must still NOT be completed!
        $this->assertNotEquals('completed', $payment->fresh()->status);
    }

    public function test_admin_can_logout(): void
    {
        $response = $this->actingAs($this->admin)->post('/admin/logout');
        $response->assertRedirect('/admin/login');
        $this->assertGuest();
    }
}
