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
     */
    public function processPayment(Request $request, string $slug)
    {
        $course = Course::where('slug', $slug)->firstOrFail();

        $validated = $request->validate([
            'student_name' => 'required|string|max:100',
            'student_email' => 'required|email|max:100',
            'student_phone' => 'required|string|max:20',
            'payment_method' => 'required|string|in:bkash,nagad,rocket,card',
            'sender_phone' => 'nullable|string|max:20',
            'transaction_id' => 'nullable|string|max:50',
        ]);

        $trxId = $validated['transaction_id'] 
            ? strtoupper($validated['transaction_id']) 
            : strtoupper(substr($validated['payment_method'], 0, 2)) . rand(10000000, 99999999);

        // 1. Check if student already has active enrollment in this course
        $activeEnrollment = Enrollment::where('course_id', $course->id)
            ->where('status', 'active')
            ->where(function ($q) use ($validated) {
                $q->where('student_email', $validated['student_email'])
                  ->orWhere('student_phone', $validated['student_phone']);
            })
            ->latest()
            ->first();

        if ($activeEnrollment) {
            $existingPayment = Payment::where('enrollment_id', $activeEnrollment->id)
                ->where('status', 'completed')
                ->latest()
                ->first();

            if ($existingPayment) {
                return redirect()->route('payment.success', $existingPayment->id)
                    ->with('info', 'আপনি ইতিমধ্যে এই কোর্সে সফলভাবে ভর্তি হয়েছেন!');
            }
        }

        // 2. Reuse pending enrollment if one exists, avoiding duplicate rows
        $enrollment = Enrollment::where('course_id', $course->id)
            ->where('status', 'pending')
            ->where(function ($q) use ($validated) {
                $q->where('student_email', $validated['student_email'])
                  ->orWhere('student_phone', $validated['student_phone']);
            })
            ->latest()
            ->first();

        if ($enrollment) {
            $enrollment->update([
                'student_name' => $validated['student_name'],
                'student_email' => $validated['student_email'],
                'student_phone' => $validated['student_phone'],
                'status' => 'active',
                'enrolled_at' => now(),
            ]);
        } else {
            $enrollment = Enrollment::create([
                'course_id' => $course->id,
                'student_name' => $validated['student_name'],
                'student_email' => $validated['student_email'],
                'student_phone' => $validated['student_phone'],
                'status' => 'active',
                'enrolled_at' => now(),
                'expires_at' => now()->addMonths(12),
            ]);
        }

        // Remove any other extra pending duplicate enrollments for this student
        Enrollment::where('course_id', $course->id)
            ->where('id', '!=', $enrollment->id)
            ->where('status', 'pending')
            ->where(function ($q) use ($validated) {
                $q->where('student_email', $validated['student_email'])
                  ->orWhere('student_phone', $validated['student_phone']);
            })
            ->delete();

        // Create payment record
        $payment = Payment::create([
            'enrollment_id' => $enrollment->id,
            'course_id' => $course->id,
            'student_name' => $validated['student_name'],
            'student_email' => $validated['student_email'],
            'student_phone' => $validated['student_phone'],
            'amount' => $course->effective_price,
            'payment_method' => $validated['payment_method'],
            'transaction_id' => $trxId,
            'sender_phone' => $validated['sender_phone'] ?? $validated['student_phone'],
            'status' => 'completed',
            'notes' => 'Instant online payment verified successfully.',
        ]);

        // Cancel previous pending payments for this student
        Payment::where('course_id', $course->id)
            ->where('id', '!=', $payment->id)
            ->where('status', 'pending')
            ->where(function ($q) use ($validated) {
                $q->where('student_email', $validated['student_email'])
                  ->orWhere('student_phone', $validated['student_phone']);
            })
            ->update([
                'status' => 'cancelled',
                'notes' => 'Auto-cancelled: Student completed payment #' . $payment->id,
            ]);

        return redirect()->route('payment.success', $payment->id)
            ->with('success', 'অভিনন্দন! আপনার ভর্তি ও পেমেন্ট সফলভাবে সম্পন্ন হয়েছে। ক্লাসের সব নোটিশ ও লাইভ ক্লাসের লিঙ্ক পেতে অনুগ্রহ করে WhatsApp গ্রুপে যুক্ত হন।');
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
