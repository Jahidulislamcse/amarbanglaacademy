<?php

namespace App\Http\Controllers;

use App\Models\Attendance;
use App\Models\AttendanceSession;
use App\Models\Course;
use App\Models\Enrollment;
use App\Models\LiveMeeting;
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
            ->whereIn('status', ['active', 'completed'])
            ->latest()
            ->first();

        // Auto-heal enrollment user link if needed
        if ($enrollment && $enrollment->user_id !== $user->id) {
            $enrollment->update(['user_id' => $user->id, 'status' => 'active']);
        }

        // Get main course details with syllabus modules
        $course = $enrollment?->course ?? Course::with(['modules.lessons', 'instructors'])->first();

        // Active or next scheduled live meeting
        $currentMeeting = $course?->currentOrNextMeeting();

        // All scheduled live meetings for student schedule timeline
        $scheduledMeetings = [];
        if ($course) {
            $scheduledMeetings = LiveMeeting::where('course_id', $course->id)
                ->where('status', '!=', 'cancelled')
                ->orderBy('starts_at', 'asc')
                ->get();
        }

        // Calculate attendance stats for student
        $attendanceStats = $user->attendanceStatsForCourse($course?->id);

        // Fetch all class sessions with this student's attendance record
        $sessions = [];
        if ($course) {
            $sessions = AttendanceSession::where('course_id', $course->id)
                ->where('status', '!=', 'cancelled')
                ->with(['attendances' => function ($q) use ($user) {
                    $q->where('user_id', $user->id);
                }])
                ->orderBy('session_date', 'desc')
                ->orderBy('id', 'desc')
                ->get();
        }

        // Fetch all meetings that have published recordings
        $recordings = [];
        if ($course) {
            $recordings = LiveMeeting::where('course_id', $course->id)
                ->withRecordings()
                ->orderBy('starts_at', 'asc')
                ->get();
        }

        return view('student.dashboard', compact(
            'user',
            'enrollment',
            'course',
            'currentMeeting',
            'scheduledMeetings',
            'attendanceStats',
            'sessions',
            'recordings'
        ));
    }

    /**
     * Watch Class Recording (Unbranded Secure Video Player)
     */
    public function watchRecording(LiveMeeting $meeting)
    {
        $user = Auth::user();

        if (!$meeting->hasRecording()) {
            return redirect()->route('student.dashboard')->with('error', 'এই ক্লাসের রেকর্ডিং এখনো প্রকাশ করা হয়নি।');
        }

        $course = $meeting->course ?? Course::first();

        // Other recordings in this course for playlist
        $otherRecordings = LiveMeeting::where('course_id', $course->id)
            ->withRecordings()
            ->orderBy('starts_at', 'asc')
            ->get();

        return view('student.recording-player', compact(
            'user',
            'course',
            'meeting',
            'otherRecordings'
        ));
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

        // Check date/time based meeting first, then fallback to course attributes
        $currentMeeting = $course?->currentOrNextMeeting();

        $isLive = ($currentMeeting && $currentMeeting->is_live_now) || ($course?->is_live_now);
        $meetingUrl = trim((string) ($currentMeeting?->meeting_url ?? $course?->live_meeting_url ?? ''));
        $scheduleText = $currentMeeting?->formatted_schedule ?? $course?->live_schedule_text ?? 'রাত ৯:০০ টা';

        // If not live yet and user is a student, redirect to dashboard with schedule notice
        if ($user->role === 'student' && !$isLive) {
            return redirect()->route('student.dashboard')
                ->with('info', "ক্লাস এখনো শুরু হয়নি বা লাইভ লিংক সক্রিয় করা হয়নি। পরবর্তী ক্লাসের সময়সূচি: {$scheduleText}। নির্ধারিত সময়ে ক্লাস শুরু হলে বাটনটি সক্রিয় হবে।");
        }

        // Automatic attendance record when student joins live class
        if ($user->role === 'student' && $course) {
            $topic = $currentMeeting?->title ?: ($course->live_class_topic ?: ('লাইভ ক্লাস (' . now()->format('d M Y') . ')'));
            $sessionDate = $currentMeeting?->starts_at?->toDateString() ?: now()->toDateString();

            $session = AttendanceSession::where('course_id', $course->id)
                ->whereDate('session_date', $sessionDate)
                ->first();

            if (!$session) {
                $session = AttendanceSession::create([
                    'course_id' => $course->id,
                    'title' => $topic,
                    'session_date' => $sessionDate,
                    'started_at' => now(),
                    'is_active' => true,
                    'status' => 'completed',
                ]);
            }

            Attendance::firstOrCreate(
                [
                    'attendance_session_id' => $session->id,
                    'user_id' => $user->id,
                ],
                [
                    'course_id' => $course->id,
                    'enrollment_id' => $enrollment?->id,
                    'status' => 'present',
                    'attended_at' => now(),
                    'ip_address' => request()->ip(),
                    'join_method' => 'online_join',
                ]
            );
        }

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
