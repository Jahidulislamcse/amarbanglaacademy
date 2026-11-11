<?php

namespace Tests\Feature;

use App\Models\Batch;
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
        $response->assertSee('১ম ব্যাচ');
        $response->assertSee('ভর্তি বন্ধ');
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

        // By default, 1st batch enrollment is closed
        $closedResponse = $this->get('/courses/' . $course->slug . '/checkout');
        $closedResponse->assertStatus(200);
        $closedResponse->assertSee('বর্তমানে অনলাইন ভর্তি কার্যক্রম বন্ধ রয়েছে');

        // When a batch opens for enrollment, checkout displays the EPS payment form
        Batch::where('course_id', $course->id)->first()->update(['enrollment_open' => true]);

        $response = $this->get('/courses/' . $course->slug . '/checkout');
        $response->assertStatus(200);
        $response->assertSee('EPS অনলাইন পেমেন্ট');
        $response->assertSee('বিকাশ');
    }

    public function test_legacy_manual_enroll_redirects_to_checkout(): void
    {
        $course = Course::first();

        $response = $this->post('/courses/' . $course->slug . '/enroll', [
            'student_name' => 'Alim Hossain',
            'student_email' => 'alim@example.com',
            'student_phone' => '01712345678',
        ]);

        $response->assertRedirect(route('courses.checkout', $course->slug));
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

    public function test_logo_and_favicon_assets_exist_and_render_across_pages(): void
    {
        $this->assertFileExists(public_path('images/AmarBanglaAcademy.png'));
        $this->assertFileExists(public_path('images/favicon.png'));
        $this->assertFileExists(public_path('favicon.ico'));
        $this->assertGreaterThan(0, filesize(public_path('favicon.ico')));

        // Direct fallback route for /storage/AmarBanglaAcademy.png works
        $storageLogo = $this->get('/storage/AmarBanglaAcademy.png');
        $storageLogo->assertStatus(200);
        $storageLogo->assertHeader('content-type', 'image/png');

        // Public landing page includes logo and favicon tags
        $home = $this->get('/');
        $home->assertStatus(200);
        $home->assertSee('images/AmarBanglaAcademy.png', false);
        $home->assertSee('images/favicon.png', false);
        $home->assertSee('favicon.ico', false);

        // Login pages include logo and favicon tags
        foreach (['/login', '/admin/login'] as $url) {
            $res = $this->get($url);
            $res->assertStatus(200);
            $res->assertSee('images/AmarBanglaAcademy.png', false);
            $res->assertSee('images/favicon.png', false);
            $res->assertSee('favicon.ico', false);
        }
    }
}
