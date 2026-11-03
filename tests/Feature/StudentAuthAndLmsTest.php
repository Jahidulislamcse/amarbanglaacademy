<?php

namespace Tests\Feature;

use App\Models\Course;
use App\Models\Enrollment;
use App\Models\Payment;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Support\Facades\Hash;
use Tests\TestCase;

class StudentAuthAndLmsTest extends TestCase
{
    use RefreshDatabase;

    private User $admin;
    private Course $course;

    protected function setUp(): void
    {
        parent::setUp();
        $this->seed(\Database\Seeders\LmsSeeder::class);

        $this->admin = User::where('email', 'admin@amar-bangla.edu')->first();
        $this->course = Course::first();
    }

    public function test_student_login_page_renders_successfully(): void
    {
        $response = $this->get('/login');
        $response->assertStatus(200);
        $response->assertSee('শিক্ষার্থী লগইন');
        $response->assertSee('ইউজার আইডি');

        $response2 = $this->get('/student/login');
        $response2->assertStatus(200);
    }

    public function test_admin_can_update_live_meeting_link(): void
    {
        $response = $this->actingAs($this->admin)->post(route('admin.courses.update-meeting', $this->course->id), [
            'live_meeting_url' => 'https://us05web.zoom.us/j/1234567890?pwd=test',
            'live_class_topic' => 'মডিউল ১: সাংবাদিকতার পরিচিতি',
            'live_meeting_id' => '123 456 7890',
            'live_meeting_passcode' => '998877',
            'is_live_now' => '1',
        ]);

        $response->assertSessionHas('success');

        $this->course->refresh();
        $this->assertEquals('https://us05web.zoom.us/j/1234567890?pwd=test', $this->course->live_meeting_url);
        $this->assertEquals('মডিউল ১: সাংবাদিকতার পরিচিতি', $this->course->live_class_topic);
        $this->assertEquals('123 456 7890', $this->course->live_meeting_id);
        $this->assertEquals('998877', $this->course->live_meeting_passcode);
        $this->assertTrue((bool) $this->course->is_live_now);
    }

    public function test_admin_can_create_student_lms_credentials(): void
    {
        $enrollment = Enrollment::create([
            'course_id' => $this->course->id,
            'student_name' => 'মোঃ সেলিম রানা',
            'student_email' => 'selim@example.com',
            'student_phone' => '01789901188',
            'status' => 'active',
            'enrolled_at' => now(),
        ]);

        $response = $this->actingAs($this->admin)->post(route('admin.students.credentials', $enrollment->id), [
            'username' => 'selim01789',
            'password' => 'AB2026#789',
        ]);

        $response->assertSessionHas('success');

        $enrollment->refresh();
        $this->assertNotNull($enrollment->user_id);

        $user = $enrollment->user;
        $this->assertEquals('selim01789', $user->username);
        $this->assertEquals('AB2026#789', $user->plain_password);
        $this->assertTrue(Hash::check('AB2026#789', $user->password));
        $this->assertEquals('student', $user->role);
    }

    public function test_student_can_login_with_phone_and_password(): void
    {
        $studentUser = User::create([
            'name' => 'অংশৈপ্রু মারমা',
            'email' => 'student@example.com',
            'phone' => '01820680720',
            'username' => '01820680720',
            'password' => Hash::make('StudentPass#2026'),
            'plain_password' => 'StudentPass#2026',
            'role' => 'student',
        ]);

        Enrollment::create([
            'user_id' => $studentUser->id,
            'course_id' => $this->course->id,
            'student_name' => 'অংশৈপ্রু মারমা',
            'student_email' => 'student@example.com',
            'student_phone' => '01820680720',
            'status' => 'active',
            'enrolled_at' => now(),
        ]);

        $response = $this->post('/login', [
            'login_id' => '01820680720',
            'password' => 'StudentPass#2026',
        ]);

        $response->assertRedirect('/student/dashboard');
        $this->assertAuthenticatedAs($studentUser);
    }

    public function test_student_cannot_login_with_invalid_credentials(): void
    {
        User::create([
            'name' => 'টেস্ট ছাত্র',
            'email' => 'test@example.com',
            'phone' => '01700000001',
            'password' => Hash::make('correct_pass'),
            'role' => 'student',
        ]);

        $response = $this->post('/login', [
            'login_id' => '01700000001',
            'password' => 'wrong_pass',
        ]);

        $response->assertSessionHasErrors('login_id');
        $this->assertGuest();
    }

    public function test_unauthenticated_user_cannot_access_student_dashboard(): void
    {
        $response = $this->get('/student/dashboard');
        $response->assertRedirect('/login');
    }

    public function test_authenticated_student_can_view_lms_dashboard(): void
    {
        $studentUser = User::create([
            'name' => 'রাকিবুল ইসলাম',
            'email' => 'rakib@example.com',
            'phone' => '01314818933',
            'username' => 'rakib1314',
            'password' => Hash::make('AB#2026#999'),
            'plain_password' => 'AB#2026#999',
            'role' => 'student',
        ]);

        Enrollment::create([
            'user_id' => $studentUser->id,
            'course_id' => $this->course->id,
            'student_name' => 'রাকিবুল ইসলাম',
            'student_email' => 'rakib@example.com',
            'student_phone' => '01314818933',
            'status' => 'active',
            'enrolled_at' => now(),
        ]);

        $this->course->update([
            'live_meeting_url' => 'https://meet.google.com/abc-defg-hij',
            'live_class_topic' => 'লাইভ ক্লাস: ডিজিটাল জার্নালিজম',
            'is_live_now' => true,
        ]);

        $response = $this->actingAs($studentUser)->get('/student/dashboard');
        $response->assertStatus(200);
        $response->assertSee('স্টুডেন্ট এলএমএস পোর্টাল');
        $response->assertSee('রাকিবুল ইসলাম');
        $response->assertSee('সাংবাদিকতা, আইন ও উপস্থাপনা');
        $response->assertSee('লাইভ ক্লাসে যোগ দিন');
    }

    public function test_live_class_redirects_to_external_meeting_url(): void
    {
        $studentUser = User::create([
            'name' => 'রাকিবুল ইসলাম',
            'email' => 'rakib@example.com',
            'phone' => '01314818933',
            'password' => Hash::make('AB#2026#999'),
            'role' => 'student',
        ]);

        Enrollment::create([
            'user_id' => $studentUser->id,
            'course_id' => $this->course->id,
            'student_name' => 'রাকিবুল ইসলাম',
            'student_email' => 'rakib@example.com',
            'student_phone' => '01314818933',
            'status' => 'active',
            'enrolled_at' => now(),
        ]);

        $this->course->update([
            'live_meeting_url' => 'https://us05web.zoom.us/j/987654321',
        ]);

        $response = $this->actingAs($studentUser)->get('/student/live-class');
        $response->assertRedirect('https://us05web.zoom.us/j/987654321');
    }
}
