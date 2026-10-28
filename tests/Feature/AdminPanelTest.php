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

    public function test_admin_can_logout(): void
    {
        $response = $this->actingAs($this->admin)->post('/admin/logout');
        $response->assertRedirect('/admin/login');
        $this->assertGuest();
    }
}
