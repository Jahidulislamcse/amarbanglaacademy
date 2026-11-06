<?php

namespace Tests\Feature;

use App\Models\Course;
use App\Models\Enrollment;
use App\Models\LiveMeeting;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Support\Facades\Hash;
use Tests\TestCase;

class ClassRecordingSystemTest extends TestCase
{
    use RefreshDatabase;

    protected User $admin;
    protected User $student;
    protected Course $course;
    protected Enrollment $enrollment;
    protected LiveMeeting $meeting;

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

        $this->meeting = LiveMeeting::create([
            'course_id' => $this->course->id,
            'title' => 'ক্লাস ১: সাংবাদিকতার পরিচিতি ও মৌলিক ধারণা',
            'meeting_url' => 'https://us05web.zoom.us/j/85212345678',
            'starts_at' => now()->subDay()->setTime(21, 0),
            'is_live_now' => false,
            'status' => 'completed',
        ]);
    }

    public function test_admin_can_share_and_update_class_recording_with_youtube_url(): void
    {
        $response = $this->actingAs($this->admin)->post(route('admin.meetings.recording.update', $this->meeting->id), [
            'recording_url' => 'https://www.youtube.com/watch?v=dQw4w9WgXcQ',
            'recording_duration' => '১ ঘণ্টা ৪৫ মিনিট',
            'recording_notes' => '১ম ক্লাসের পূর্ণাঙ্গ রেকর্ডিং ও স্লাইড নোট।',
        ]);

        $response->assertSessionHas('success');

        $this->meeting->refresh();
        $this->assertTrue($this->meeting->hasRecording());
        $this->assertEquals('https://www.youtube.com/watch?v=dQw4w9WgXcQ', $this->meeting->recording_url);
        $this->assertEquals('dQw4w9WgXcQ', $this->meeting->youtube_id);
        $this->assertEquals('১ ঘণ্টা ৪৫ মিনিট', $this->meeting->recording_duration);
        $this->assertEquals('completed', $this->meeting->status);
    }

    public function test_admin_can_remove_recording_from_class_meeting(): void
    {
        $this->meeting->update([
            'recording_url' => 'https://youtu.be/dQw4w9WgXcQ',
            'has_recording' => true,
        ]);

        $response = $this->actingAs($this->admin)->delete(route('admin.meetings.recording.destroy', $this->meeting->id));
        $response->assertSessionHas('success');

        $this->meeting->refresh();
        $this->assertFalse($this->meeting->hasRecording());
        $this->assertNull($this->meeting->recording_url);
    }

    public function test_student_sees_recording_button_and_archive_on_dashboard(): void
    {
        $this->meeting->update([
            'recording_url' => 'https://www.youtube.com/watch?v=dQw4w9WgXcQ',
            'recording_duration' => '১ ঘণ্টা ৪৫ মিনিট',
            'has_recording' => true,
        ]);

        $response = $this->actingAs($this->student)->get(route('student.dashboard'));

        $response->assertStatus(200);
        $response->assertSee('ক্লাস রেকর্ডিং ও ভিডিও লেকচার');
        $response->assertSee('রেকর্ডিং দেখুন');
        $response->assertSee(route('student.recordings.watch', $this->meeting->id));
    }

    public function test_student_can_open_watch_recording_page_with_unbranded_secure_player(): void
    {
        $this->meeting->update([
            'recording_url' => 'https://www.youtube.com/watch?v=dQw4w9WgXcQ',
            'recording_duration' => '১ ঘণ্টা ৪৫ মিনিট',
            'recording_notes' => 'সংবাদের সংজ্ঞা ও অনুসন্ধানী রিপোর্টিং নোট',
            'has_recording' => true,
        ]);

        $response = $this->actingAs($this->student)->get(route('student.recordings.watch', $this->meeting->id));

        $response->assertStatus(200);
        // Page has secure storage player branding
        $response->assertSee('সুরক্ষিত স্টোরেজ প্লেয়ার');
        $response->assertSee('HD 1080p');
        // Player has YouTube ID passed safely to masked API handler
        $response->assertSee('data-youtube-id="dQw4w9WgXcQ"', false);
        // Uses cropper wrapper with -12% top offset to cut off all YouTube logos/buttons
        $response->assertSee('yt-cropper-wrap');
        // Has student anti-piracy watermark
        $response->assertSee('🔒 অংশৈপ্রু মারমা');
        // Has custom HTML5 scrubber controls
        $response->assertSee('scrubber-track');
        $response->assertSee('btn-play-toggle');
    }

    public function test_unauthenticated_user_cannot_access_student_recording(): void
    {
        $this->meeting->update([
            'recording_url' => 'https://youtu.be/dQw4w9WgXcQ',
            'has_recording' => true,
        ]);

        $response = $this->get(route('student.recordings.watch', $this->meeting->id));
        $response->assertRedirect(route('login'));
    }

    public function test_youtube_id_extraction_helper(): void
    {
        $this->assertEquals('dQw4w9WgXcQ', LiveMeeting::extractYoutubeId('https://www.youtube.com/watch?v=dQw4w9WgXcQ'));
        $this->assertEquals('dQw4w9WgXcQ', LiveMeeting::extractYoutubeId('https://youtu.be/dQw4w9WgXcQ?t=120'));
        $this->assertEquals('dQw4w9WgXcQ', LiveMeeting::extractYoutubeId('https://www.youtube.com/embed/dQw4w9WgXcQ?si=abc'));
        $this->assertEquals('dQw4w9WgXcQ', LiveMeeting::extractYoutubeId('https://www.youtube.com/live/dQw4w9WgXcQ'));
        $this->assertEquals('dQw4w9WgXcQ', LiveMeeting::extractYoutubeId('dQw4w9WgXcQ'));
        $this->assertNull(LiveMeeting::extractYoutubeId('https://vimeo.com/123456789'));
        $this->assertNull(LiveMeeting::extractYoutubeId(''));
    }
}
