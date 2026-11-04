<?php

namespace App\Http\Controllers;

use App\Models\Course;
use App\Models\Enrollment;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class StudentController extends Controller
{
    /**
     * Student LMS Dashboard
     */
    public function dashboard()
    {
        $user = Auth::user();

        // Find active enrollment for the student
        $enrollment = Enrollment::with(['course.modules.lessons'])
            ->where(function ($q) use ($user) {
                $q->where('user_id', $user->id)
                  ->orWhere('student_email', $user->email);
                if (!empty($user->phone)) {
                    $q->orWhere('student_phone', $user->phone);
                }
            })
            ->where('status', 'active')
            ->latest()
            ->first();

        // Get main course details with syllabus modules
        $course = $enrollment?->course ?? Course::with(['modules.lessons', 'instructors'])->first();

        return view('student.dashboard', compact('user', 'enrollment', 'course'));
    }

    /**
     * Direct Live Class Meeting Redirect (Regular Zoom or Google Meet)
     */
    public function joinLiveClass()
    {
        $user = Auth::user();

        $enrollment = Enrollment::with(['course'])
            ->where(function ($q) use ($user) {
                $q->where('user_id', $user->id)
                  ->orWhere('student_email', $user->email);
                if (!empty($user->phone)) {
                    $q->orWhere('student_phone', $user->phone);
                }
            })
            ->where('status', 'active')
            ->latest()
            ->first();

        $course = $enrollment?->course ?? Course::first();

        $meetingUrl = trim((string) ($course?->live_meeting_url ?? ''));

        if (!empty($meetingUrl)) {
            // Direct redirect to Zoom / Google Meet meeting URL
            return redirect()->away($meetingUrl);
        }

        return redirect()->route('student.dashboard')
            ->with('info', 'বর্তমান ক্লাসের লাইভ মিটিং লিংক এখনো সক্রিয় হয়নি। ক্লাস শুরুর ১৫-২০ মিনিট পূর্বে এখানে লিংক সক্রিয় করা হবে অথবা WhatsApp গ্রুপে চোখ রাখুন।');
    }

    /**
     * Classroom route (redirects directly to live meeting URL)
     */
    public function classroom()
    {
        return $this->joinLiveClass();
    }
}
