<?php

namespace Tests\Feature;

use App\Models\Attendance;
use App\Models\AttendanceSession;
use App\Models\Course;
use App\Models\Enrollment;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Support\Facades\Hash;
use Tests\TestCase;

class AttendanceSystemTest extends TestCase
{
    use RefreshDatabase;

    protected User $admin;
    protected User $student;
    protected Course $course;
    protected Enrollment $enrollment;

    protected function setUp(): void
    {
        parent::setUp();

        $this->admin = User::create([
            'name' => 'এডমিন ব্যবহারকারী',
            'email' => 'admin@amarbanglaacademy.com',
            'role' => 'admin',
            'password' => Hash::make('AdminPass#2026'),
        ]);

        $this->course = Course::create([
            'title' => 'Journalism, Media Law and News Presentation',
            'bangla_title' => 'সাংবাদিকতা, আইন ও উপস্থাপনা',
            'slug' => 'journalism-law-presentation',
            'subtitle' => '১ম ব্যাচে সরাসরি লাইভ ক্লাস',
            'description' => 'কোর্স বিবরণী...',
            'regular_price' => 3000,
            'discount_price' => 999,
            'live_meeting_url' => 'https://us05web.zoom.us/j/987654321',
            'live_schedule_text' => 'প্রতি বৃহস্পতি, শুক্র ও শনি রাত ৯:০০ টা',
            'is_live_now' => true,
        ]);

        $this->student = User::create([
            'name' => 'অংশৈপ্রু মারমা',
            'email' => 'ongshoipru@example.com',
            'phone' => '01820680720',
            'username' => '01820680720',
            'role' => 'student',
            'password' => Hash::make('StudentPass#2026'),
        ]);

        $this->enrollment = Enrollment::create([
            'user_id' => $this->student->id,
            'course_id' => $this->course->id,
            'student_name' => 'অংশৈপ্রু মারমা',
            'student_email' => 'ongshoipru@example.com',
            'student_phone' => '01820680720',
            'status' => 'active',
            'enrolled_at' => now(),
        ]);
    }

    public function test_student_joining_live_class_automatically_records_attendance(): void
    {
        $this->course->update([
            'is_live_now' => true,
            'live_class_topic' => 'মডিউল ১: সাংবাদিকতার পরিচিতি',
        ]);

        $response = $this->actingAs($this->student)->get(route('student.live-class'));

        $response->assertRedirect('https://us05web.zoom.us/j/987654321');

        // Check attendance session created
        $session = AttendanceSession::where('course_id', $this->course->id)
            ->whereDate('session_date', now()->toDateString())
            ->first();
        $this->assertNotNull($session);
        $this->assertEquals('মডিউল ১: সাংবাদিকতার পরিচিতি', $session->title);

        // Check attendance record created
        $attendance = Attendance::where('attendance_session_id', $session->id)
            ->where('user_id', $this->student->id)
            ->first();
        $this->assertNotNull($attendance);
        $this->assertEquals('present', $attendance->status);
        $this->assertEquals('online_join', $attendance->join_method);

        // Repeated joins do not create duplicate attendance records
        $this->actingAs($this->student)->get(route('student.live-class'));
        $this->assertEquals(1, Attendance::where('attendance_session_id', $session->id)->where('user_id', $this->student->id)->count());
    }

    public function test_student_dashboard_displays_attendance_percentage_and_history(): void
    {
        // Session 1: Student Attended
        $session1 = AttendanceSession::create([
            'course_id' => $this->course->id,
            'title' => 'ক্লাস ১: সাংবাদিকতার পরিচিতি',
            'session_date' => now()->subDays(2)->toDateString(),
            'status' => 'completed',
        ]);
        Attendance::create([
            'attendance_session_id' => $session1->id,
            'user_id' => $this->student->id,
            'course_id' => $this->course->id,
            'status' => 'present',
            'attended_at' => now()->subDays(2),
        ]);

        // Session 2: Student Absent
        AttendanceSession::create([
            'course_id' => $this->course->id,
            'title' => 'ক্লাস ২: সংবাদ কাঠামো ও লিড',
            'session_date' => now()->subDays(1)->toDateString(),
            'status' => 'completed',
        ]);

        $response = $this->actingAs($this->student)->get(route('student.dashboard'));

        $response->assertStatus(200);
        $response->assertSee('আমার উপস্থিতি');
        $response->assertSee('50%');
        $response->assertSee('1/2 টি ক্লাসে উপস্থিত');
        $response->assertSee('ক্লাস ১: সাংবাদিকতার পরিচিতি');
        $response->assertSee('উপস্থিত');
        $response->assertSee('ক্লাস ২: সংবাদ কাঠামো ও লিড');
        $response->assertSee('অনুপস্থিত');
    }

    public function test_admin_can_view_attendance_management_page(): void
    {
        AttendanceSession::create([
            'course_id' => $this->course->id,
            'title' => 'ক্লাস ১: বেসিক জার্নালিজম',
            'session_date' => now()->toDateString(),
            'status' => 'completed',
        ]);

        $response = $this->actingAs($this->admin)->get(route('admin.attendance.index'));

        $response->assertStatus(200);
        $response->assertSee('লাইভ ক্লাসের হাজিরা ও উপস্থিতি ব্যবস্থাপনা');
        $response->assertSee('ক্লাস ১: বেসিক জার্নালিজম');
    }

    public function test_admin_can_create_new_attendance_session(): void
    {
        $response = $this->actingAs($this->admin)->post(route('admin.attendance.sessions.store'), [
            'title' => 'ক্লাস ৩: অনুসন্ধানী সাংবাদিকতা',
            'session_date' => '2026-11-06',
            'notes' => 'গুরুত্বপূর্ণ প্র্যাকটিক্যাল সেশন',
        ]);

        $session = AttendanceSession::where('title', 'ক্লাস ৩: অনুসন্ধানী সাংবাদিকতা')->first();
        $this->assertNotNull($session);
        $this->assertEquals('2026-11-06', $session->session_date->format('Y-m-d'));

        $response->assertRedirect(route('admin.attendance.session', $session->id));
    }

    public function test_admin_can_view_session_sheet_and_toggle_attendance(): void
    {
        $session = AttendanceSession::create([
            'course_id' => $this->course->id,
            'title' => 'ক্লাস ৪: তথ্য অধিকার আইন',
            'session_date' => now()->toDateString(),
            'status' => 'completed',
        ]);

        // View session sheet
        $viewResponse = $this->actingAs($this->admin)->get(route('admin.attendance.session', $session->id));
        $viewResponse->assertStatus(200);
        $viewResponse->assertSee('ক্লাস ৪: তথ্য অধিকার আইন');
        $viewResponse->assertSee($this->student->name);

        // Toggle student attendance to Present
        $toggle1 = $this->actingAs($this->admin)->post(route('admin.attendance.toggle', $session->id), [
            'user_id' => $this->student->id,
        ]);
        $toggle1->assertSessionHas('success');

        $att = Attendance::where('attendance_session_id', $session->id)->where('user_id', $this->student->id)->first();
        $this->assertNotNull($att);
        $this->assertEquals('present', $att->status);

        // Toggle again to Absent
        $toggle2 = $this->actingAs($this->admin)->post(route('admin.attendance.toggle', $session->id), [
            'user_id' => $this->student->id,
        ]);
        $toggle2->assertSessionHas('success');

        $att->refresh();
        $this->assertEquals('absent', $att->status);
    }

    public function test_admin_can_mark_all_students_present_for_session(): void
    {
        $student2 = User::create([
            'name' => 'সেলিম হোসেন',
            'email' => 'selim@example.com',
            'phone' => '01711223344',
            'role' => 'student',
            'password' => Hash::make('Pass#123'),
        ]);

        Enrollment::create([
            'user_id' => $student2->id,
            'course_id' => $this->course->id,
            'student_name' => 'সেলিম হোসেন',
            'student_email' => 'selim@example.com',
            'student_phone' => '01711223344',
            'status' => 'active',
            'enrolled_at' => now(),
        ]);

        $session = AttendanceSession::create([
            'course_id' => $this->course->id,
            'title' => 'ক্লাস ৫: সাইবার নিরাপত্তা',
            'session_date' => now()->toDateString(),
            'status' => 'completed',
        ]);

        $response = $this->actingAs($this->admin)->post(route('admin.attendance.mark-all-present', $session->id));
        $response->assertSessionHas('success');

        $this->assertEquals(2, Attendance::where('attendance_session_id', $session->id)->where('status', 'present')->count());
    }

    public function test_admin_can_view_student_wise_attendance_summary(): void
    {
        $session = AttendanceSession::create([
            'course_id' => $this->course->id,
            'title' => 'ক্লাস ১',
            'session_date' => now()->subDay()->toDateString(),
            'status' => 'completed',
        ]);

        Attendance::create([
            'attendance_session_id' => $session->id,
            'user_id' => $this->student->id,
            'course_id' => $this->course->id,
            'status' => 'present',
        ]);

        $response = $this->actingAs($this->admin)->get(route('admin.attendance.students'));
        $response->assertStatus(200);
        $response->assertSee('শিক্ষার্থীভিত্তিক উপস্থিতির হার ও রিপোর্ট');
        $response->assertSee($this->student->name);
        $response->assertSee('100%');
        $response->assertSee('সার্টিফিকেট যোগ্য');
    }
}
