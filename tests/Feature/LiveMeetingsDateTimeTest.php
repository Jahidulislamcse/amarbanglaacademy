<?php

namespace Tests\Feature;

use App\Models\Attendance;
use App\Models\Course;
use App\Models\Enrollment;
use App\Models\LiveMeeting;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Support\Facades\Hash;
use Tests\TestCase;

class LiveMeetingsDateTimeTest extends TestCase
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
            'is_live_now' => false,
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

    public function test_admin_can_schedule_date_time_based_meeting(): void
    {
        $response = $this->actingAs($this->admin)->post(route('admin.meetings.store'), [
            'title' => 'ক্লাস ১: সাংবাদিকতার পরিচিতি ও নিউজ সেন্স',
            'meeting_url' => 'https://us05web.zoom.us/j/852999111',
            'meeting_id' => '852 999 111',
            'meeting_passcode' => '998877',
            'scheduled_date' => '2026-11-06',
            'scheduled_time' => '21:00',
            'schedule_text' => '৬ নভেম্বর ২০২৬, রাত ৯:০০ টা',
        ]);

        $response->assertSessionHas('success');

        $meeting = LiveMeeting::where('title', 'ক্লাস ১: সাংবাদিকতার পরিচিতি ও নিউজ সেন্স')->first();
        $this->assertNotNull($meeting);
        $this->assertEquals('https://us05web.zoom.us/j/852999111', $meeting->meeting_url);
        $this->assertEquals('852 999 111', $meeting->meeting_id);
        $this->assertEquals('998877', $meeting->meeting_passcode);
        $this->assertEquals('2026-11-06 21:00:00', $meeting->starts_at->format('Y-m-d H:i:s'));
        $this->assertFalse($meeting->is_live_now);
    }

    public function test_admin_can_toggle_meeting_live_status(): void
    {
        $meeting = LiveMeeting::create([
            'course_id' => $this->course->id,
            'title' => 'ক্লাস ২: অনুসন্ধানী রিপোর্টিং',
            'meeting_url' => 'https://meet.google.com/xyz-uvwx-rst',
            'starts_at' => now()->addDay(),
            'is_live_now' => false,
        ]);

        // 1. Toggle to live now
        $toggleOn = $this->actingAs($this->admin)->post(route('admin.meetings.toggle-live', $meeting->id));
        $toggleOn->assertSessionHas('success');

        $meeting->refresh();
        $this->course->refresh();
        $this->assertTrue((bool) $meeting->is_live_now);
        $this->assertTrue((bool) $this->course->is_live_now);
        $this->assertEquals('https://meet.google.com/xyz-uvwx-rst', $this->course->live_meeting_url);
        $this->assertEquals('ক্লাস ২: অনুসন্ধানী রিপোর্টিং', $this->course->live_class_topic);

        // 2. Toggle to stop live
        $toggleOff = $this->actingAs($this->admin)->post(route('admin.meetings.toggle-live', $meeting->id));
        $toggleOff->assertSessionHas('success');

        $meeting->refresh();
        $this->course->refresh();
        $this->assertFalse((bool) $meeting->is_live_now);
        $this->assertFalse((bool) $this->course->is_live_now);
    }

    public function test_student_sees_scheduled_meeting_date_and_time_on_dashboard(): void
    {
        LiveMeeting::create([
            'course_id' => $this->course->id,
            'title' => 'ক্লাস ৩: ক্যামেরা প্রেজেন্টেশন ও বাচনভঙ্গি',
            'meeting_url' => 'https://us05web.zoom.us/j/123456789',
            'starts_at' => now()->addDays(2)->setTime(21, 0),
            'schedule_text' => 'আগামী পরশু রাত ৯:০০ টা',
            'is_live_now' => false,
        ]);

        $response = $this->actingAs($this->student)->get(route('student.dashboard'));

        $response->assertStatus(200);
        $response->assertSee('ক্লাস ৩: ক্যামেরা প্রেজেন্টেশন ও বাচনভঙ্গি');
        $response->assertSee('আগামী পরশু রাত ৯:০০ টা');
        $response->assertSee('ক্লাস শুরুর সময়ে লিংক সক্রিয় হবে');
        $response->assertDontSee('সরাসরি ক্লাসে যোগ দিন');
    }

    public function test_student_sees_active_join_button_and_attendance_when_meeting_is_live(): void
    {
        $meeting = LiveMeeting::create([
            'course_id' => $this->course->id,
            'title' => 'ক্লাস ৪: ডিজিটাল মিডিয়া ও সাইবার আইন',
            'meeting_url' => 'https://us05web.zoom.us/j/999888777',
            'starts_at' => now(),
            'is_live_now' => true,
        ]);

        $this->course->update(['is_live_now' => true]);

        // Student Dashboard shows active button
        $response = $this->actingAs($this->student)->get(route('student.dashboard'));
        $response->assertStatus(200);
        $response->assertSee('সরাসরি ক্লাসে যোগ দিন');
        $response->assertSee('এখন লাইভ চলছে');

        // Joining live class records attendance and redirects to Zoom URL
        $joinResponse = $this->actingAs($this->student)->get(route('student.live-class'));
        $joinResponse->assertRedirect('https://us05web.zoom.us/j/999888777');

        $attendance = Attendance::where('user_id', $this->student->id)->where('status', 'present')->first();
        $this->assertNotNull($attendance);
    }

    public function test_admin_can_update_existing_scheduled_meeting(): void
    {
        $meeting = LiveMeeting::create([
            'course_id' => $this->course->id,
            'title' => 'পুরাতন শিরোনাম',
            'meeting_url' => 'https://us05web.zoom.us/j/111222333',
            'meeting_id' => '111 222 333',
            'meeting_passcode' => '112233',
            'starts_at' => now()->addDay(),
            'is_live_now' => false,
        ]);

        $response = $this->actingAs($this->admin)->put(route('admin.meetings.update', $meeting->id), [
            'title' => 'হালনাগাদ করা শিরোনাম: লাইভ ব্রডকাস্টিং',
            'meeting_url' => 'https://meet.google.com/abc-defg-hij',
            'meeting_id' => 'abc-defg-hij',
            'meeting_passcode' => '445566',
            'scheduled_date' => '2026-11-10',
            'scheduled_time' => '20:30',
            'schedule_text' => '১০ নভেম্বর ২০২৬, রাত ৮:৩০ টা',
            'is_live_now' => '1',
        ]);

        $response->assertSessionHas('success');

        $meeting->refresh();
        $this->course->refresh();

        $this->assertEquals('হালনাগাদ করা শিরোনাম: লাইভ ব্রডকাস্টিং', $meeting->title);
        $this->assertEquals('https://meet.google.com/abc-defg-hij', $meeting->meeting_url);
        $this->assertEquals('google_meet', $meeting->platform);
        $this->assertEquals('2026-11-10 20:30:00', $meeting->starts_at->format('Y-m-d H:i:s'));
        $this->assertTrue((bool) $meeting->is_live_now);
        $this->assertTrue((bool) $this->course->is_live_now);
        $this->assertEquals('হালনাগাদ করা শিরোনাম: লাইভ ব্রডকাস্টিং', $this->course->live_class_topic);
    }

    public function test_live_meetings_page_does_not_contain_legacy_default_meeting_form(): void
    {
        $response = $this->actingAs($this->admin)->get(route('admin.live-meetings'));

        $response->assertStatus(200);
        $response->assertDontSee('কোর্সের মূল ডিফল্ট মিটিং লিংক ও শিডিউল টেক্সট');
        $response->assertDontSee('ডিফল্ট মিটিং সংরক্ষণ করুন');
        $response->assertDontSee('কোনো নির্দিষ্ট তারিখের শিডিউল মিটিং সেট না থাকলে শিক্ষার্থীরা সর্বদা এই লিংক ও শিডিউল দেখতে পাবে');
        $response->assertSee('লাইভ ক্লাস ও মিটিং রুম কন্ট্রোল (তারিখ ও সময়ভিত্তিক শিডিউল)');
        $response->assertSee('+ নতুন মিটিং শিডিউল করুন');
    }
}
