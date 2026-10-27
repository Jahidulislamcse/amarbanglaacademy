<?php

namespace Tests\Feature;

use App\Models\Course;
use Database\Seeders\LmsSeeder;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class LmsLandingTest extends TestCase
{
    use RefreshDatabase;

    protected function setUp(): void
    {
        parent::setUp();
        $this->seed(LmsSeeder::class);
    }

    public function test_landing_page_loads_successfully(): void
    {
        $response = $this->get('/');

        $response->assertStatus(200);
        $response->assertSee('আমার বাংলা একাডেমি');
        $response->assertSee('সাংবাদিকতা, আইন ও উপস্থাপনা');
        $response->assertSee('মডিউল');
        $response->assertSee('প্রশিক্ষকবৃন্দ');
        $response->assertSee('বিকাশ');
        $response->assertSee('Zoom');
    }

    public function test_course_detail_page_loads(): void
    {
        $course = Course::first();
        $this->assertNotNull($course);

        $response = $this->get('/courses/' . $course->slug);
        $response->assertStatus(200);
        $response->assertSee($course->bangla_title ?? $course->title);
    }

    public function test_checkout_page_loads(): void
    {
        $course = Course::first();

        $response = $this->get('/courses/' . $course->slug . '/checkout');
        $response->assertStatus(200);
        $response->assertSee('পেমেন্ট মেথড');
        $response->assertSee('বিকাশ');
    }

    public function test_enrollment_and_payment_process(): void
    {
        $course = Course::first();

        $response = $this->post('/courses/' . $course->slug . '/enroll', [
            'student_name' => 'Alim Hossain',
            'student_email' => 'alim@example.com',
            'student_phone' => '01712345678',
            'payment_method' => 'bkash',
            'sender_phone' => '01712345678',
            'transaction_id' => 'BK12345678',
        ]);

        $response->assertRedirect();
        $this->assertDatabaseHas('enrollments', [
            'course_id' => $course->id,
            'student_email' => 'alim@example.com',
        ]);
        $this->assertDatabaseHas('payments', [
            'course_id' => $course->id,
            'transaction_id' => 'BK12345678',
            'payment_method' => 'bkash',
        ]);
    }

    public function test_live_classes_and_exams_pages_load(): void
    {
        $response1 = $this->get('/live-classes');
        $response1->assertStatus(200);
        $response1->assertSee('Zoom');

        $response2 = $this->get('/exams');
        $response2->assertStatus(200);
        $response2->assertSee('অনলাইন এক্সাম');
    }
}
