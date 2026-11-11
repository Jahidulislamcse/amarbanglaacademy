<?php

namespace Tests\Feature;

use App\Models\Attendance;
use App\Models\AttendanceSession;
use App\Models\Batch;
use App\Models\Course;
use App\Models\Enrollment;
use App\Models\LiveMeeting;
use App\Models\Payment;
use App\Models\User;
use Database\Seeders\LmsSeeder;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Http;
use Tests\TestCase;

class BatchManagementTest extends TestCase
{
    use RefreshDatabase;

    private User $admin;
    private Course $course;

    protected function setUp(): void
    {
        parent::setUp();
        $this->seed(LmsSeeder::class);

        $this->admin = User::where('email', 'admin@amar-bangla.edu')->first();
        $this->course = Course::first();
    }

    public function test_seeded_first_batch_has_enrollment_closed_by_default(): void
    {
        $batch1 = Batch::where('course_id', $this->course->id)->first();
        $this->assertNotNull($batch1);
        $this->assertEquals('১ম ব্যাচ', $batch1->name);
        $this->assertFalse((bool) $batch1->enrollment_open);
        $this->assertEquals('running', $batch1->status);
        $this->assertFalse($this->course->isEnrollmentOpen());

        // Public landing page shows 1st batch enrollment is closed
        $landing = $this->get('/');
        $landing->assertStatus(200);
        $landing->assertSee('১ম ব্যাচ');
        $landing->assertSee('ভর্তি বন্ধ');

        // Checkout page shows closed enrollment state and no EPS pay submit button
        $checkout = $this->get(route('courses.checkout', $this->course->slug));
        $checkout->assertStatus(200);
        $checkout->assertSee('বর্তমানে অনলাইন ভর্তি কার্যক্রম বন্ধ রয়েছে');
        $checkout->assertDontSee('নিরাপদ EPS গেটওয়েতে পেমেন্ট করুন');

        // EPS payment POST is blocked when no batch is open
        $payResponse = $this->post(route('eps.pay', $this->course->slug), [
            'student_name' => 'Test Applicant',
            'student_email' => 'applicant@example.com',
            'student_phone' => '01700001111',
        ]);
        $payResponse->assertRedirect(route('courses.checkout', $this->course->slug));
        $payResponse->assertSessionHas('error');
        $this->assertDatabaseMissing('payments', ['student_email' => 'applicant@example.com']);
    }

    public function test_admin_can_create_new_batch_while_keeping_course_content_same(): void
    {
        $initialModulesCount = $this->course->modules()->count();
        $initialLessonsCount = $this->course->lessons()->count();
        $initialInstructorsCount = $this->course->instructors()->count();
        $this->assertEquals(12, $initialModulesCount);

        $indexResponse = $this->actingAs($this->admin)->get(route('admin.batches.index'));
        $indexResponse->assertStatus(200);
        $indexResponse->assertSee('ব্যাচ ম্যানেজমেন্ট');
        $indexResponse->assertSee('১ম ব্যাচ');

        // Create 2nd Batch with custom pricing & start date
        $storeResponse = $this->actingAs($this->admin)->post(route('admin.batches.store'), [
            'course_id' => $this->course->id,
            'name' => '২য় ব্যাচ',
            'batch_number' => 2,
            'start_date' => '2026-12-15',
            'start_date_text' => '১৫ ডিসেম্বর ২০২৬',
            'schedule_text' => 'প্রতি বৃহস্পতি, শুক্র ও শনিবার রাত ৯:০০ টা',
            'seat_limit' => 40,
            'regular_price' => 3000,
            'discount_price' => 1499,
            'discount_badge_text' => '২য় ব্যাচে আর্লি বার্ড ছাড়',
            'offer_label' => 'প্রথম ৪০ জনের জন্য',
            'status' => 'upcoming',
            'enrollment_open' => '1',
            'close_other_batches' => '1',
        ]);

        $storeResponse->assertSessionHas('success');

        $batch2 = Batch::where('name', '২য় ব্যাচ')->first();
        $this->assertNotNull($batch2);
        $this->assertTrue((bool) $batch2->enrollment_open);
        $this->assertEquals(1499.0, $batch2->effective_price);
        $this->assertEquals(40, $batch2->seats_remaining);

        // Batch 1 remains closed
        $batch1 = Batch::where('name', '১ম ব্যাচ')->first();
        $this->assertFalse((bool) $batch1->enrollment_open);

        // Course modules, lessons, and instructors remain completely unchanged (shared)
        $this->assertEquals($initialModulesCount, $this->course->modules()->count());
        $this->assertEquals($initialLessonsCount, $this->course->lessons()->count());
        $this->assertEquals($initialInstructorsCount, $this->course->instructors()->count());

        // Landing & Checkout dynamically reflect 2nd Batch
        $landing = $this->get('/');
        $landing->assertStatus(200);
        $landing->assertSee('২য় ব্যাচ');
        $landing->assertSee('১৫ ডিসেম্বর ২০২৬');
        $landing->assertSee('১,৪৯৯');

        $checkout = $this->get(route('courses.checkout', $this->course->slug));
        $checkout->assertStatus(200);
        $checkout->assertSee('২য় ব্যাচ');
        $checkout->assertSee('1,499');
        $checkout->assertSee('নিরাপদ EPS গেটওয়েতে পেমেন্ট করুন');

        // EPS Pay assigns batch_id to 2nd Batch and charges 1499
        Http::fake([
            '*/Auth/GetToken' => Http::response(['token' => 'mock_jwt', 'status' => 'true'], 200),
            '*/EPSEngine/InitializeEPS' => Http::response([
                'RedirectURL' => 'https://sandboxpg.epay.com.bd/checkout/batch2-test',
                'Status' => 'SUCCESS',
            ], 200),
        ]);

        $payResponse = $this->post(route('eps.pay', $this->course->slug), [
            'batch_id' => $batch2->id,
            'student_name' => 'Batch Two Student',
            'student_email' => 'batch2@example.com',
            'student_phone' => '01855556666',
        ]);
        $payResponse->assertRedirect('https://sandboxpg.epay.com.bd/checkout/batch2-test');

        $this->assertDatabaseHas('enrollments', [
            'course_id' => $this->course->id,
            'batch_id' => $batch2->id,
            'student_email' => 'batch2@example.com',
        ]);
        $this->assertDatabaseHas('payments', [
            'course_id' => $this->course->id,
            'batch_id' => $batch2->id,
            'student_email' => 'batch2@example.com',
            'amount' => 1499.00,
        ]);
    }

    public function test_admin_can_toggle_enrollment_update_and_safely_delete_batches(): void
    {
        $batch2 = Batch::create([
            'course_id' => $this->course->id,
            'name' => '২য় ব্যাচ',
            'batch_number' => 2,
            'start_date_text' => '১৫ ডিসেম্বর ২০২৬',
            'seat_limit' => 30,
            'regular_price' => 3000,
            'discount_price' => 1299,
            'enrollment_open' => true,
            'status' => 'upcoming',
            'is_active' => true,
        ]);

        // Toggle closed
        $this->actingAs($this->admin)
            ->post(route('admin.batches.toggle-enrollment', $batch2->id))
            ->assertSessionHas('success');
        $this->assertFalse((bool) $batch2->fresh()->enrollment_open);
        $this->assertFalse($this->course->isEnrollmentOpen());

        // Toggle open again
        $this->actingAs($this->admin)
            ->post(route('admin.batches.toggle-enrollment', $batch2->id))
            ->assertSessionHas('success');
        $this->assertTrue((bool) $batch2->fresh()->enrollment_open);
        $this->assertTrue($this->course->isEnrollmentOpen());

        // Update batch details
        $this->actingAs($this->admin)
            ->put(route('admin.batches.update', $batch2->id), [
                'name' => '২য় ব্যাচ (বিশেষ)',
                'batch_number' => 2,
                'start_date_text' => '২০ ডিসেম্বর ২০২৬',
                'seat_limit' => 45,
                'seats_remaining_override' => 12,
                'regular_price' => 3000,
                'discount_price' => 1199,
                'status' => 'upcoming',
                'enrollment_open' => '1',
                'is_active' => '1',
            ])
            ->assertSessionHas('success');

        $batch2->refresh();
        $this->assertEquals('২য় ব্যাচ (বিশেষ)', $batch2->name);
        $this->assertEquals(12, $batch2->seats_remaining);
        $this->assertEquals(1199.0, $batch2->effective_price);

        // Cannot delete batch with student enrollment
        Enrollment::create([
            'course_id' => $this->course->id,
            'batch_id' => $batch2->id,
            'student_name' => 'Linked Student',
            'student_email' => 'linked@example.com',
            'student_phone' => '01733334444',
            'status' => 'active',
        ]);

        $this->actingAs($this->admin)
            ->delete(route('admin.batches.destroy', $batch2->id))
            ->assertSessionHas('error');
        $this->assertDatabaseHas('batches', ['id' => $batch2->id]);

        // Can delete an empty batch
        $emptyBatch = Batch::create([
            'course_id' => $this->course->id,
            'name' => '৩য় ব্যাচ (খালি)',
            'batch_number' => 3,
            'enrollment_open' => false,
            'status' => 'upcoming',
            'is_active' => true,
        ]);

        $this->actingAs($this->admin)
            ->delete(route('admin.batches.destroy', $emptyBatch->id))
            ->assertSessionHas('success');
        $this->assertDatabaseMissing('batches', ['id' => $emptyBatch->id]);
    }

    public function test_admin_filters_students_meetings_and_attendance_by_batch(): void
    {
        $batch1 = Batch::where('course_id', $this->course->id)->first();
        $batch2 = Batch::create([
            'course_id' => $this->course->id,
            'name' => '২য় ব্যাচ',
            'batch_number' => 2,
            'enrollment_open' => true,
            'status' => 'upcoming',
            'is_active' => true,
        ]);

        $user1 = User::create([
            'name' => 'প্রথম ব্যাচ ছাত্রী',
            'email' => 'student1@example.com',
            'phone' => '01711000001',
            'username' => '01711000001',
            'password' => Hash::make('Pass#1234'),
            'role' => 'student',
        ]);
        $enr1 = Enrollment::create([
            'user_id' => $user1->id,
            'course_id' => $this->course->id,
            'batch_id' => $batch1->id,
            'student_name' => 'প্রথম ব্যাচ ছাত্রী',
            'student_email' => 'student1@example.com',
            'student_phone' => '01711000001',
            'status' => 'active',
            'enrolled_at' => now(),
        ]);
        Payment::create([
            'enrollment_id' => $enr1->id,
            'course_id' => $this->course->id,
            'batch_id' => $batch1->id,
            'student_name' => 'প্রথম ব্যাচ ছাত্রী',
            'student_email' => 'student1@example.com',
            'student_phone' => '01711000001',
            'amount' => 999,
            'payment_method' => 'bkash',
            'transaction_id' => 'TXB1001',
            'status' => 'completed',
        ]);

        $user2 = User::create([
            'name' => 'দ্বিতীয় ব্যাচ ছাত্র',
            'email' => 'student2@example.com',
            'phone' => '01722000002',
            'username' => '01722000002',
            'password' => Hash::make('Pass#1234'),
            'role' => 'student',
        ]);
        $enr2 = Enrollment::create([
            'user_id' => $user2->id,
            'course_id' => $this->course->id,
            'batch_id' => $batch2->id,
            'student_name' => 'দ্বিতীয় ব্যাচ ছাত্র',
            'student_email' => 'student2@example.com',
            'student_phone' => '01722000002',
            'status' => 'active',
            'enrolled_at' => now(),
        ]);
        Payment::create([
            'enrollment_id' => $enr2->id,
            'course_id' => $this->course->id,
            'batch_id' => $batch2->id,
            'student_name' => 'দ্বিতীয় ব্যাচ ছাত্র',
            'student_email' => 'student2@example.com',
            'student_phone' => '01722000002',
            'amount' => 1499,
            'payment_method' => 'eps',
            'transaction_id' => 'TXB2002',
            'status' => 'completed',
        ]);

        // Filter /admin/students by batch 1 and batch 2
        $resB1 = $this->actingAs($this->admin)->get(route('admin.students', ['batch_id' => $batch1->id]));
        $resB1->assertStatus(200);
        $resB1->assertSee('প্রথম ব্যাচ ছাত্রী');
        $resB1->assertDontSee('দ্বিতীয় ব্যাচ ছাত্র');

        $resB2 = $this->actingAs($this->admin)->get(route('admin.students', ['batch_id' => $batch2->id]));
        $resB2->assertStatus(200);
        $resB2->assertSee('দ্বিতীয় ব্যাচ ছাত্র');
        $resB2->assertDontSee('প্রথম ব্যাচ ছাত্রী');

        // Live meetings per batch
        LiveMeeting::create([
            'course_id' => $this->course->id,
            'batch_id' => $batch1->id,
            'title' => 'ব্যাচ ১ স্পেশাল মিটিং',
            'meeting_url' => 'https://zoom.us/j/111111',
            'starts_at' => now()->addDay(),
            'is_live_now' => true,
        ]);
        LiveMeeting::create([
            'course_id' => $this->course->id,
            'batch_id' => $batch2->id,
            'title' => 'ব্যাচ ২ ওরিয়েন্টেশন ক্লাস',
            'meeting_url' => 'https://zoom.us/j/222222',
            'starts_at' => now()->addDays(2),
            'is_live_now' => false,
        ]);

        $meetingsB2 = $this->actingAs($this->admin)->get(route('admin.live-meetings', ['batch_id' => $batch2->id]));
        $meetingsB2->assertStatus(200);
        $meetingsB2->assertSee('ব্যাচ ২ ওরিয়েন্টেশন ক্লাস');
        $meetingsB2->assertDontSee('ব্যাচ ১ স্পেশাল মিটিং');

        // Attendance session scoped strictly to Batch 2 only shows Batch 2 student
        $sessionB2 = AttendanceSession::create([
            'course_id' => $this->course->id,
            'batch_id' => $batch2->id,
            'title' => 'ব্যাচ ২ ক্লাস ১ হাজিরা',
            'session_date' => now()->toDateString(),
            'status' => 'completed',
        ]);

        $sheetB2 = $this->actingAs($this->admin)->get(route('admin.attendance.session', $sessionB2->id));
        $sheetB2->assertStatus(200);
        $sheetB2->assertSee('দ্বিতীয় ব্যাচ ছাত্র');
        $sheetB2->assertDontSee('প্রথম ব্যাচ ছাত্রী');
    }

    public function test_student_portal_isolates_live_status_meetings_recordings_and_attendance_per_batch(): void
    {
        $batch1 = Batch::where('course_id', $this->course->id)->first();
        $batch2 = Batch::create([
            'course_id' => $this->course->id,
            'name' => '২য় ব্যাচ',
            'batch_number' => 2,
            'enrollment_open' => true,
            'status' => 'running',
            'is_active' => true,
        ]);

        $student2 = User::create([
            'name' => 'তানভীর আহমেদ',
            'email' => 'tanvir.b2@example.com',
            'phone' => '01911223399',
            'username' => '01911223399',
            'password' => Hash::make('Pass#1234'),
            'role' => 'student',
        ]);
        Enrollment::create([
            'user_id' => $student2->id,
            'course_id' => $this->course->id,
            'batch_id' => $batch2->id,
            'student_name' => 'তানভীর আহমেদ',
            'student_email' => 'tanvir.b2@example.com',
            'student_phone' => '01911223399',
            'status' => 'active',
            'enrolled_at' => now(),
        ]);

        // Mark Course + Batch 1 meeting LIVE NOW (Batch 2 is NOT live)
        $this->course->update(['is_live_now' => true]);
        $meetingB1Rec = LiveMeeting::create([
            'course_id' => $this->course->id,
            'batch_id' => $batch1->id,
            'title' => 'ব্যাচ ১ লাইভ ক্লাস ও রেকর্ডিং',
            'meeting_url' => 'https://zoom.us/j/101010',
            'starts_at' => now(),
            'is_live_now' => true,
            'has_recording' => true,
            'recording_url' => 'https://www.youtube.com/watch?v=dQw4w9WgXcQ',
            'youtube_id' => 'dQw4w9WgXcQ',
        ]);

        // Batch 2 upcoming scheduled meeting + Batch 2 recording
        LiveMeeting::create([
            'course_id' => $this->course->id,
            'batch_id' => $batch2->id,
            'title' => 'ব্যাচ ২ আগামী ক্লাস: নিউজ রাইটিং',
            'meeting_url' => 'https://zoom.us/j/202020',
            'starts_at' => now()->addDay()->setTime(21, 0),
            'schedule_text' => '১৫ ডিসেম্বর রাত ৯:০০ টা',
            'is_live_now' => false,
            'has_recording' => true,
            'recording_url' => 'https://www.youtube.com/watch?v=9bZkp7q19f0',
            'youtube_id' => '9bZkp7q19f0',
        ]);

        // Attendance session in Batch 1 (2 sessions) vs Batch 2 (1 attended session)
        AttendanceSession::create([
            'course_id' => $this->course->id,
            'batch_id' => $batch1->id,
            'title' => 'ব্যাচ ১ পুরাতন সেশন',
            'session_date' => now()->subDays(5)->toDateString(),
            'status' => 'completed',
        ]);
        $sessionB2 = AttendanceSession::create([
            'course_id' => $this->course->id,
            'batch_id' => $batch2->id,
            'title' => 'ব্যাচ ২ প্রথম সেশন',
            'session_date' => now()->subDay()->toDateString(),
            'status' => 'completed',
        ]);
        Attendance::create([
            'attendance_session_id' => $sessionB2->id,
            'user_id' => $student2->id,
            'course_id' => $this->course->id,
            'status' => 'present',
        ]);

        // Student 2 views dashboard
        $dash = $this->actingAs($student2)->get(route('student.dashboard'));
        $dash->assertStatus(200);
        $dash->assertSee('২য় ব্যাচ');
        // Shared curriculum visible
        $dash->assertSee('সাংবাদিকতার পরিচিতি ও মৌলিক ধারণা');
        // Batch 2 live button must NOT be active even though Batch 1 / course.is_live_now is true!
        $dash->assertDontSee('সরাসরি ক্লাসে যোগ দিন');
        $dash->assertSee('ক্লাস শুরুর সময়ে লিংক সক্রিয় হবে');
        // Batch 2 meeting & attendance 1/1 (100%)
        $dash->assertSee('ব্যাচ ২ আগামী ক্লাস: নিউজ রাইটিং');
        $dash->assertDontSee('ব্যাচ ১ লাইভ ক্লাস ও রেকর্ডিং');
        $dash->assertSee('1/1 টি ক্লাসে উপস্থিত');

        // Student 2 cannot watch Batch 1 recording directly
        $watchB1 = $this->actingAs($student2)->get(route('student.recordings.watch', $meetingB1Rec->id));
        $watchB1->assertRedirect(route('student.dashboard'));
        $watchB1->assertSessionHas('error');
    }

    public function test_manual_student_creation_assigns_selected_batch(): void
    {
        $batch2 = Batch::create([
            'course_id' => $this->course->id,
            'name' => '২য় ব্যাচ',
            'batch_number' => 2,
            'enrollment_open' => true,
            'status' => 'upcoming',
            'is_active' => true,
        ]);

        $response = $this->actingAs($this->admin)->post(route('admin.students.store'), [
            'student_name' => 'ম্যানুয়াল ২য় ব্যাচ ছাত্র',
            'student_phone' => '01877665544',
            'student_email' => 'manual.b2@example.com',
            'course_id' => $this->course->id,
            'batch_id' => $batch2->id,
            'amount' => 1499,
            'payment_method' => 'bkash',
            'transaction_id' => 'BKB2MANUAL01',
            'username' => '01877665544',
            'password' => 'Pass#5678',
        ]);

        $response->assertSessionHas('success');

        $this->assertDatabaseHas('enrollments', [
            'student_phone' => '01877665544',
            'batch_id' => $batch2->id,
            'status' => 'active',
        ]);
        $this->assertDatabaseHas('payments', [
            'transaction_id' => 'BKB2MANUAL01',
            'batch_id' => $batch2->id,
            'amount' => 1499,
            'status' => 'completed',
        ]);
    }
}
