<?php

namespace App\Http\Controllers;

use App\Models\Course;
use App\Models\Enrollment;
use App\Models\Payment;
use Illuminate\Http\Request;
use Illuminate\Support\Str;

class LmsController extends Controller
{
    /**
     * LMS Landing Page with rich course showcase
     */
    public function index()
    {
        // Get flagship featured course with modules, lessons, instructors, exams
        $flagshipCourse = Course::with(['modules.lessons', 'modules.exams', 'instructors', 'exams'])
            ->where('is_featured', true)
            ->first() ?? Course::with(['modules.lessons', 'instructors'])->first();

        // Get all other published courses
        $courses = Course::with(['instructors'])
            ->where('status', 'published')
            ->get();

        // Statistics
        $stats = [
            'total_students' => '25,000+',
            'course_count' => Course::count(),
            'satisfaction_rate' => '98.7%',
            'instructors_count' => '15+',
            'active_live_batches' => '8',
        ];

        return view('landing', compact('flagshipCourse', 'courses', 'stats'));
    }

    /**
     * Dedicated Course Detail Page
     */
    public function courseDetail(string $slug)
    {
        $course = Course::with(['modules.lessons', 'modules.exams', 'instructors', 'exams'])
            ->where('slug', $slug)
            ->firstOrFail();

        $relatedCourses = Course::where('id', '!=', $course->id)
            ->where('status', 'published')
            ->take(3)
            ->get();

        return view('courses.show', compact('course', 'relatedCourses'));
    }

    /**
     * Checkout Page for Online Payment
     */
    public function checkout(string $slug)
    {
        $course = Course::with('instructors')->where('slug', $slug)->firstOrFail();
        return view('checkout', compact('course'));
    }

    /**
     * Process Online Payment & Instant Enrollment
     * (Deprecated: System strictly uses official EPS Online Payment Gateway)
     */
    public function processPayment(Request $request, string $slug)
    {
        $course = Course::where('slug', $slug)->firstOrFail();

        return redirect()->route('courses.checkout', $course->slug)
            ->with('info', 'ম্যানুয়াল পেমেন্ট অনুমোদিত নয়। অনুগ্রহ করে নিরাপদ EPS অনলাইন গেটওয়ের মাধ্যমে বিকাশ, নগদ বা কার্ড দিয়ে পেমেন্ট সম্পন্ন করুন।');
    }

    /**
     * Payment Success / Enrollment Confirmation
     */
    public function paymentSuccess(int $id)
    {
        $payment = Payment::with(['course.modules.lessons', 'enrollment'])->findOrFail($id);
        return view('payment-success', compact('payment'));
    }

    /**
     * Live Classes & Meeting Portal Preview
     */
    public function liveClasses()
    {
        $courses = Course::with(['modules.lessons' => function ($query) {
            $query->whereIn('type', ['live_zoom', 'live_meet']);
        }])->get();

        return view('live-classes', compact('courses'));
    }

    /**
     * Exams & Certifications Portal Preview
     */
    public function exams()
    {
        $courses = Course::with(['exams', 'modules'])->get();
        return view('exams', compact('courses'));
    }
}
