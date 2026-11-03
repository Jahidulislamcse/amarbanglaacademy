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
     * In-System Live Classroom (Distraction-free video class within the LMS)
     */
    public function classroom(Request $request)
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
        $lowerUrl = strtolower($meetingUrl);

        $provider = 'in_system';
        $embedUrl = null;

        if (str_contains($lowerUrl, 'meet.google.com')) {
            $provider = 'google_meet';
        } elseif (str_contains($lowerUrl, 'zoom.us')) {
            $provider = 'zoom';
        } elseif (str_contains($lowerUrl, 'youtube.com') || str_contains($lowerUrl, 'youtu.be')) {
            $provider = 'youtube';
            preg_match('/(?:youtu\.be\/|youtube\.com\/(?:embed\/|v\/|watch\?v=|watch\?.+&v=))([\w-]{11})/', $meetingUrl, $matches);
            $youtubeId = $matches[1] ?? null;
            if ($youtubeId) {
                $embedUrl = "https://www.youtube-nocookie.com/embed/{$youtubeId}?autoplay=1&rel=0";
            }
        } elseif (!empty($meetingUrl) && str_contains($lowerUrl, 'meet.jit.si')) {
            $provider = 'in_system';
            $embedUrl = $meetingUrl;
        } else {
            // Default In-System WebRTC classroom (Zero setup, 100% inside our website)
            $provider = 'in_system';
            $batchNum = preg_replace('/[^0-9]/', '', (string)($course?->batch_no ?? '1')) ?: '1';
            $roomName = 'AmarBanglaAcademy_Classroom_Batch' . $batchNum;
            $studentNameClean = urlencode($user->name ?: 'Student');
            $embedUrl = "https://meet.jit.si/{$roomName}#userInfo.displayName=\"{$studentNameClean}\"&config.prejoinPageEnabled=false";
        }

        return view('student.classroom', compact('user', 'enrollment', 'course', 'provider', 'embedUrl', 'meetingUrl'));
    }

    /**
     * Direct Live Class Meeting Route (Redirects to In-System Classroom)
     */
    public function joinLiveClass()
    {
        return redirect()->route('student.classroom');
    }
}
