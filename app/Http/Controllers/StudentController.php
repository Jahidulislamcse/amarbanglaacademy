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
     * In-System Live Classroom (100% inside LMS page without any external redirect)
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

        // Dedicated in-system room name for this course & batch
        $batchNum = preg_replace('/[^0-9]/', '', (string)($course?->batch_no ?? '1')) ?: '1';
        $courseSlug = $course?->slug ? str_replace('-', '', ucwords($course->slug, '-')) : 'Journalism';
        $defaultRoom = 'AmarBanglaAcademy_' . $courseSlug . '_Batch' . $batchNum;

        // Base room URL (if custom Jitsi room URL provided, use that; otherwise use dedicated academy room)
        if (!empty($meetingUrl) && str_contains(strtolower($meetingUrl), 'meet.jit.si')) {
            $baseRoomUrl = explode('#', $meetingUrl)[0];
        } else {
            $baseRoomUrl = 'https://meet.jit.si/' . $defaultRoom;
        }

        // Student / Instructor display name inside the meeting
        $displayName = urlencode($user->name ?: ($user->role === 'admin' ? 'শিক্ষক / মডারেটর' : 'শিক্ষার্থী'));

        // Embedded WebRTC URL with pre-filled name, audio auto-managed, clean in-system UI
        $embedUrl = rtrim($baseRoomUrl, '/') . '#userInfo.displayName="' . $displayName . '"&config.prejoinPageEnabled=false&config.disableDeepLinking=true';

        return view('student.classroom', compact('user', 'enrollment', 'course', 'embedUrl', 'meetingUrl'));
    }

    /**
     * Direct Live Class Meeting Route (Redirects to In-System Classroom)
     */
    public function joinLiveClass()
    {
        return redirect()->route('student.classroom');
    }
}
