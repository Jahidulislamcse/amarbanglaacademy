<?php

namespace App\Http\Controllers;

use App\Models\Attendance;
use App\Models\AttendanceSession;
use App\Models\Course;
use App\Models\Enrollment;
use App\Models\Payment;
use App\Models\User;
use App\Services\EpsGateway;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Hash;
use Symfony\Component\HttpFoundation\StreamedResponse;

class AdminController extends Controller
{
    /**
     * Show Admin Login Form
     */
    public function showLoginForm()
    {
        if (Auth::check()) {
            return redirect()->route('admin.students');
        }

        return view('admin.login');
    }

    /**
     * Handle Admin Login
     */
    public function login(Request $request)
    {
        $credentials = $request->validate([
            'email' => ['required', 'string'],
            'password' => ['required', 'string'],
        ], [
            'email.required' => 'ইমেইল বা ইউজারনেম প্রদান করুন।',
            'password.required' => 'পাসওয়ার্ড প্রদান করুন।',
        ]);

        $remember = $request->boolean('remember');

        if (Auth::attempt(['email' => $credentials['email'], 'password' => $credentials['password']], $remember)) {
            $request->session()->regenerate();
            return redirect()->intended(route('admin.students'))
                ->with('success', 'স্বাগতম! আপনি সফলভাবে এডমিন প্যানেলে প্রবেশ করেছেন।');
        }

        return back()
            ->withInput($request->only('email', 'remember'))
            ->withErrors([
                'email' => 'প্রদত্ত ইমেইল বা পাসওয়ার্ড সঠিক নয়। অনুগ্রহ করে পুনরায় চেষ্টা করুন।',
            ]);
    }

    /**
     * Handle Admin Logout
     */
    public function logout(Request $request)
    {
        Auth::logout();
        $request->session()->invalidate();
        $request->session()->regenerateToken();

        return redirect()->route('admin.login')
            ->with('success', 'আপনি সফলভাবে লগআউট হয়েছেন।');
    }

    /**
     * Purchased Students List & Dashboard (Excludes pending payments)
     */
    public function students(Request $request)
    {
        // 1. KPI Stats (Strictly for completed course purchases)
        $completedPayments = Payment::where('status', 'completed');

        $stats = [
            'total_students' => (clone $completedPayments)->count(),
            'total_revenue' => (clone $completedPayments)->sum('amount'),
            'eps_count' => (clone $completedPayments)->where('payment_method', 'eps')->count(),
            'mobile_count' => (clone $completedPayments)->whereIn('payment_method', ['bkash', 'nagad', 'rocket', 'card'])->count(),
            'pending_count' => Payment::where('status', 'pending')->count(),
        ];

        // 2a. Pending payments needing admin review / manual approval
        $pendingPayments = Payment::with(['enrollment.user', 'course'])
            ->where('status', 'pending')
            ->latest()
            ->get();

        // 2b. Query builder: strictly show completed payment registrations
        $query = Payment::with(['enrollment.user', 'course'])
            ->where('status', 'completed')
            ->latest();

        // Search filter (name, phone, email, trxId, sender phone)
        if ($search = trim((string) $request->input('search'))) {
            $query->where(function ($q) use ($search) {
                $q->where('student_name', 'like', "%{$search}%")
                  ->orWhere('student_phone', 'like', "%{$search}%")
                  ->orWhere('student_email', 'like', "%{$search}%")
                  ->orWhere('transaction_id', 'like', "%{$search}%")
                  ->orWhere('sender_phone', 'like', "%{$search}%");
            });
        }

        // Payment method filter
        if ($method = $request->input('method')) {
            $query->where('payment_method', $method);
        }

        // Course filter
        if ($courseId = $request->input('course_id')) {
            $query->where('course_id', $courseId);
        }

        $payments = $query->paginate(15)->withQueryString();
        $courses = Course::all(['id', 'title', 'bangla_title']);
        $primaryCourse = Course::first();

        return view('admin.students', compact('payments', 'pendingPayments', 'stats', 'courses', 'primaryCourse'));
    }

    /**
     * Update Payment & Enrollment Status (e.g. approve pending payment)
     */
    public function updateStatus(Request $request, Payment $payment)
    {
        $validated = $request->validate([
            'status' => ['required', 'string', 'in:completed,pending,failed,cancelled'],
            'notes' => ['nullable', 'string', 'max:500'],
        ]);

        $payment->status = $validated['status'];
        if ($request->filled('notes')) {
            $payment->notes = $validated['notes'];
        }
        $payment->save();

        // Sync linked enrollment status
        if ($payment->enrollment) {
            if ($validated['status'] === 'completed') {
                $payment->enrollment->update(['status' => 'active']);
            } elseif ($validated['status'] === 'cancelled' || $validated['status'] === 'failed') {
                $payment->enrollment->update(['status' => 'cancelled']);
            } elseif ($validated['status'] === 'pending') {
                $payment->enrollment->update(['status' => 'pending']);
            }
        }

        // Auto-clean duplicates if payment was approved to completed
        if ($validated['status'] === 'completed') {
            Enrollment::where('course_id', $payment->course_id)
                ->where('id', '!=', $payment->enrollment_id)
                ->where('status', 'pending')
                ->where(function ($q) use ($payment) {
                    $q->where('student_email', $payment->student_email)
                      ->orWhere('student_phone', $payment->student_phone);
                })
                ->delete();

            Payment::where('course_id', $payment->course_id)
                ->where('id', '!=', $payment->id)
                ->where('status', 'pending')
                ->where(function ($q) use ($payment) {
                    $q->where('student_email', $payment->student_email)
                      ->orWhere('student_phone', $payment->student_phone);
                })
                ->update([
                    'status' => 'cancelled',
                    'notes' => 'Auto-cancelled on admin approval of payment #' . $payment->id,
                ]);
        }

        return back()->with('success', 'পেমেন্ট ও ভর্তির স্ট্যাটাস সফলভাবে আপডেট করা হয়েছে।');
    }

    /**
     * Live EPS Verification Check from EPS Server
     */
    public function verifyEps(Payment $payment, EpsGateway $eps)
    {
        if (strtolower($payment->payment_method) !== 'eps') {
            return back()->with('error', 'শুধুমাত্র EPS অনলাইন পেমেন্টের ক্ষেত্রে সরাসরি ভেরিফিকেশন প্রযোজ্য।');
        }

        try {
            $verify = $eps->verify($payment->transaction_id);
            $status = strtolower($verify['Status'] ?? '');

            if ($status === 'success') {
                $payment->update([
                    'status' => 'completed',
                    'notes' => 'EPS Live Verified: SUCCESS. Customer Trx ID: ' . ($verify['EPSCustomerTransactionId'] ?? 'N/A'),
                ]);

                if ($payment->enrollment) {
                    $payment->enrollment->update(['status' => 'active']);
                }

                return back()->with('success', 'EPS গেটওয়ে রিপোর্ট: পেমেন্ট সফল! শিক্ষার্থী নিশ্চিতভাবে ফি পরিশোধ করেছেন (Customer TrxID: ' . ($verify['EPSCustomerTransactionId'] ?? 'N/A') . ')।');
            } else {
                $rawStatus = $verify['Status'] ?? 'Initialize / Unpaid';

                // Automatically update status to cancelled since EPS server confirmed payment is NOT successful
                $payment->update([
                    'status' => 'cancelled',
                    'notes' => 'EPS Live Checked: Unpaid/Failed (Status: ' . $rawStatus . '). Automatically moved to cancelled.',
                ]);

                if ($payment->enrollment) {
                    $payment->enrollment->update(['status' => 'cancelled']);
                }

                return back()->with('error', "EPS গেটওয়ে রিপোর্ট: টাকা এখনো জমা হয়নি! EPS স্ট্যাটাস: {$rawStatus} (Paid: NO)। এই পেমেন্টটি সফল তালিকা থেকে বাতিল (Cancelled) করা হয়েছে।");
            }
        } catch (\Throwable $e) {
            return back()->with('error', 'EPS গেটওয়েতে সংযোগ করতে সমস্যা হয়েছে: ' . $e->getMessage());
        }
    }

    /**
     * Delete Payment and Student Enrollment Record
     */
    public function destroyPayment(Payment $payment)
    {
        $studentName = $payment->student_name;
        $enrollment = $payment->enrollment;
        $user = $enrollment?->user;

        DB::beginTransaction();
        try {
            // Delete payment
            $payment->delete();

            // If enrollment has no other payments, delete enrollment
            if ($enrollment) {
                $otherPayments = Payment::where('enrollment_id', $enrollment->id)->count();
                if ($otherPayments === 0) {
                    $enrollment->delete();
                }
            }

            // If linked user has no other enrollments and is student role, delete user
            if ($user && $user->role === 'student') {
                $otherEnrollments = Enrollment::where('user_id', $user->id)->count();
                if ($otherEnrollments === 0) {
                    $user->delete();
                }
            }

            DB::commit();
            return back()->with('success', "শিক্ষার্থী '{$studentName}'-এর ভর্তি ও পেমেন্ট রেকর্ড সফলভাবে মুছে ফেলা হয়েছে।");
        } catch (\Throwable $e) {
            DB::rollBack();
            return back()->with('error', 'রেকর্ড মুছে ফেলতে সমস্যা হয়েছে: ' . $e->getMessage());
        }
    }

    /**
     * Clean duplicate pending enrollments and orphaned pending payments
     */
    public function cleanDuplicates()
    {
        $cleanedEnrollments = 0;
        $cleanedPayments = 0;

        // 1. Delete pending enrollments for students who already have an active enrollment in the same course
        $activeStudents = Enrollment::where('status', 'active')->get(['id', 'course_id', 'student_email', 'student_phone']);

        foreach ($activeStudents as $active) {
            $duplicateEnrollments = Enrollment::where('course_id', $active->course_id)
                ->where('id', '!=', $active->id)
                ->where('status', 'pending')
                ->where(function ($q) use ($active) {
                    $q->where('student_email', $active->student_email)
                      ->orWhere('student_phone', $active->student_phone);
                })
                ->get();

            foreach ($duplicateEnrollments as $dup) {
                Payment::where('enrollment_id', $dup->id)->update([
                    'enrollment_id' => $active->id,
                ]);

                $dup->delete();
                $cleanedEnrollments++;
            }

            // Cancel any orphaned pending payments for this active student
            $dupPayments = Payment::where('course_id', $active->course_id)
                ->where('status', 'pending')
                ->where(function ($q) use ($active) {
                    $q->where('student_email', $active->student_email)
                      ->orWhere('student_phone', $active->student_phone);
                })
                ->update([
                    'status' => 'cancelled',
                    'notes' => 'Auto-cancelled: Student already has active enrollment #' . $active->id,
                ]);

            $cleanedPayments += $dupPayments;
        }

        // 2. For students with multiple pending enrollments (e.g. repeated click attempts),
        // keep only the latest pending enrollment and delete older duplicate pending rows
        $pendingGroups = Enrollment::where('status', 'pending')
            ->select('course_id', 'student_phone', DB::raw('COUNT(*) as total'), DB::raw('MAX(id) as keep_id'))
            ->groupBy('course_id', 'student_phone')
            ->having('total', '>', 1)
            ->get();

        foreach ($pendingGroups as $group) {
            $toDelete = Enrollment::where('course_id', $group->course_id)
                ->where('student_phone', $group->student_phone)
                ->where('status', 'pending')
                ->where('id', '!=', $group->keep_id)
                ->get();

            foreach ($toDelete as $dup) {
                Payment::where('enrollment_id', $dup->id)->update(['enrollment_id' => $group->keep_id]);
                $dup->delete();
                $cleanedEnrollments++;
            }
        }

        return back()->with('success', "ডুপ্লিকেট পরিষ্কার সম্পন্ন! {$cleanedEnrollments}টি অতিরিক্ত পেন্ডিং রেজিস্ট্রেশন মুছে ফেলা হয়েছে এবং {$cleanedPayments}টি পেন্ডিং পেমেন্ট গোছানো হয়েছে।");
    }

    /**
     * Export Enrolled Students to CSV (Excludes pending payments)
     */
    public function exportCsv(Request $request): StreamedResponse
    {
        $query = Payment::with(['enrollment', 'course'])
            ->where('status', 'completed')
            ->latest();

        if ($search = trim((string) $request->input('search'))) {
            $query->where(function ($q) use ($search) {
                $q->where('student_name', 'like', "%{$search}%")
                  ->orWhere('student_phone', 'like', "%{$search}%")
                  ->orWhere('student_email', 'like', "%{$search}%")
                  ->orWhere('transaction_id', 'like', "%{$search}%")
                  ->orWhere('sender_phone', 'like', "%{$search}%");
            });
        }

        if ($method = $request->input('method')) {
            $query->where('payment_method', $method);
        }

        if ($courseId = $request->input('course_id')) {
            $query->where('course_id', $courseId);
        }

        $payments = $query->get();
        $filename = 'students_amar_bangla_academy_' . date('Y-m-d_His') . '.csv';

        $headers = [
            'Content-Type' => 'text/csv; charset=UTF-8',
            'Content-Disposition' => "attachment; filename=\"{$filename}\"",
            'Pragma' => 'no-cache',
            'Cache-Control' => 'must-revalidate, post-check=0, pre-check=0',
            'Expires' => '0',
        ];

        return response()->stream(function () use ($payments) {
            $handle = fopen('php://output', 'w');

            // UTF-8 BOM so Excel opens Bangla text without mojibake
            fprintf($handle, chr(0xEF) . chr(0xBB) . chr(0xBF));

            // CSV Header Row
            fputcsv($handle, [
                'ID',
                'শিক্ষার্থীর নাম (Student Name)',
                'মোবাইল নম্বর (Phone)',
                'ইমেইল (Email)',
                'কোর্স (Course)',
                'ফি (Amount BDT)',
                'পেমেন্ট মাধ্যম (Payment Method)',
                'ট্রানজেকশন আইডি (Transaction ID)',
                'প্রেরক নম্বর (Sender Phone)',
                'পেমেন্ট স্ট্যাটাস (Payment Status)',
                'ভর্তি স্ট্যাটাস (Enrollment Status)',
                'ভর্তির তারিখ ও সময় (Enrolled At)',
                'নোট (Notes)',
            ]);

            $index = 1;
            foreach ($payments as $payment) {
                $statusBn = match ($payment->status) {
                    'completed' => 'সফল (Completed)',
                    'pending' => 'অপেক্ষমাণ (Pending)',
                    'failed' => 'ব্যর্থ (Failed)',
                    'cancelled' => 'বাতিল (Cancelled)',
                    default => $payment->status,
                };

                $enrollmentStatusBn = match ($payment->enrollment?->status) {
                    'active' => 'সক্রিয় (Active)',
                    'pending' => 'অপেক্ষমাণ (Pending)',
                    'completed' => 'সম্পন্ন (Completed)',
                    'cancelled' => 'বাতিল (Cancelled)',
                    default => $payment->enrollment?->status ?? 'N/A',
                };

                $methodName = match (strtolower($payment->payment_method)) {
                    'eps' => 'EPS Gateway (Online)',
                    'bkash' => 'বিকাশ (bKash)',
                    'nagad' => 'নগদ (Nagad)',
                    'rocket' => 'রকেট (Rocket)',
                    'card' => 'কার্ড (Card)',
                    default => strtoupper($payment->payment_method),
                };

                fputcsv($handle, [
                    $index++,
                    $payment->student_name,
                    $payment->student_phone,
                    $payment->student_email,
                    $payment->course?->bangla_title ?? $payment->course?->title ?? 'N/A',
                    $payment->amount,
                    $methodName,
                    $payment->transaction_id,
                    $payment->sender_phone ?? $payment->student_phone,
                    $statusBn,
                    $enrollmentStatusBn,
                    $payment->created_at ? $payment->created_at->format('Y-m-d h:i A') : 'N/A',
                    $payment->notes ?? '',
                ]);
            }

            fclose($handle);
        }, 200, $headers);
    }

    /**
     * Dedicated Live Meetings Management Dashboard
     */
    public function liveMeetings()
    {
        $primaryCourse = Course::first();
        $totalStudents = Enrollment::where('status', 'active')->count();

        return view('admin.live-meetings', compact('primaryCourse', 'totalStudents'));
    }

    /**
     * Update Live Class Meeting Link & Details
     */
    public function updateMeetingLink(Request $request, Course $course)
    {
        $rawUrl = trim((string) $request->input('live_meeting_url'));
        if (!empty($rawUrl) && !str_starts_with($rawUrl, 'http://') && !str_starts_with($rawUrl, 'https://')) {
            $rawUrl = 'https://' . $rawUrl;
            $request->merge(['live_meeting_url' => $rawUrl]);
        }

        $validated = $request->validate([
            'live_meeting_url' => ['nullable', 'url', 'max:1000'],
            'live_meeting_id' => ['nullable', 'string', 'max:100'],
            'live_meeting_passcode' => ['nullable', 'string', 'max:100'],
            'live_class_topic' => ['nullable', 'string', 'max:255'],
            'live_schedule_text' => ['nullable', 'string', 'max:255'],
        ], [
            'live_meeting_url.url' => 'সঠিক মিটিং লিংক প্রদান করুন (যেমন: https://zoom.us/j/... অথবা https://meet.google.com/...)।',
        ]);

        $isLiveNow = $request->boolean('is_live_now');

        $course->update([
            'live_meeting_url' => $validated['live_meeting_url'] ?? null,
            'live_meeting_id' => $validated['live_meeting_id'] ?? null,
            'live_meeting_passcode' => $validated['live_meeting_passcode'] ?? null,
            'live_class_topic' => $validated['live_class_topic'] ?? null,
            'live_schedule_text' => $validated['live_schedule_text'] ?? $course->live_schedule_text,
            'is_live_now' => $isLiveNow,
        ]);

        // When live is turned on, automatically ensure an attendance session exists for today
        if ($isLiveNow) {
            AttendanceSession::firstOrCreate(
                [
                    'course_id' => $course->id,
                    'session_date' => now()->toDateString(),
                ],
                [
                    'title' => $validated['live_class_topic'] ?: ('লাইভ ক্লাস (' . now()->format('d M Y') . ')'),
                    'started_at' => now(),
                    'is_active' => true,
                    'status' => 'completed',
                ]
            );
        }

        return back()->with('success', 'লাইভ ক্লাসের মিটিং লিংক ও বিবরণী সফলভাবে সংরক্ষিত হয়েছে! শিক্ষার্থীরা এখন তাদের ড্যাশবোর্ড থেকে এক ক্লিকে সরাসরি যুক্ত হতে পারবে।');
    }

    /**
     * Create or Update Student LMS Login Account & Password
     */
    public function manageStudentAccount(Request $request, Enrollment $enrollment)
    {
        $validated = $request->validate([
            'username' => ['nullable', 'string', 'max:100'],
            'password' => ['required', 'string', 'min:4', 'max:100'],
        ]);

        $password = trim($validated['password']);
        $rawUsername = trim($validated['username'] ?? '') ?: $enrollment->student_phone;

        $user = $enrollment->user;

        if (!$user) {
            $user = User::where('email', $enrollment->student_email)
                ->orWhere('phone', $enrollment->student_phone)
                ->first();
        }

        if (!$user) {
            $user = new User();
            $user->name = $enrollment->student_name;
            $user->email = $enrollment->student_email;
            $user->phone = $enrollment->student_phone;
            $user->role = 'student';
        }

        $user->username = $rawUsername;
        $user->password = Hash::make($password);
        $user->plain_password = $password;
        $user->save();

        $enrollment->update(['user_id' => $user->id]);

        $loginId = $user->username ?: $user->phone ?: $user->email;

        return back()->with('success', "{$enrollment->student_name}-এর LMS অ্যাকাউন্ট তৈরি/আপডেট সম্পন্ন! ইউজার আইডি: {$loginId}, পাসওয়ার্ড: {$password}");
    }

    /**
     * Manually Enroll and Create Student from Admin Panel
     */
    public function storeStudent(Request $request)
    {
        $validated = $request->validate([
            'student_name' => ['required', 'string', 'max:100'],
            'student_phone' => ['required', 'string', 'max:25'],
            'student_email' => ['nullable', 'email', 'max:100'],
            'course_id' => ['required', 'exists:courses,id'],
            'amount' => ['required', 'numeric', 'min:0'],
            'payment_method' => ['required', 'string', 'max:50'],
            'transaction_id' => ['nullable', 'string', 'max:100'],
            'sender_phone' => ['nullable', 'string', 'max:25'],
            'username' => ['nullable', 'string', 'max:100'],
            'password' => ['nullable', 'string', 'min:4', 'max:100'],
            'notes' => ['nullable', 'string', 'max:500'],
        ], [
            'student_name.required' => 'শিক্ষার্থীর নাম প্রদান করুন।',
            'student_phone.required' => 'শিক্ষার্থীর মোবাইল নম্বর প্রদান করুন।',
            'course_id.required' => 'কোর্স নির্বাচন করুন।',
            'amount.required' => 'পরিশোধিত টাকার পরিমাণ উল্লেখ করুন।',
        ]);

        $course = Course::findOrFail($validated['course_id']);
        $rawPhone = trim($validated['student_phone']);
        $cleanPhone = preg_replace('/[^0-9]/', '', $rawPhone);
        
        $email = trim($validated['student_email'] ?? '') ?: ($cleanPhone . '@student.amarbangla.edu');
        $rawPassword = trim($validated['password'] ?? '') ?: ('AB2026#' . random_int(100, 999));
        $username = trim($validated['username'] ?? '') ?: $rawPhone;
        $trxId = trim($validated['transaction_id'] ?? '') ?: ('MANUAL' . now()->format('YmdHis') . random_int(10, 99));

        try {
            DB::beginTransaction();

            // 1. Find or create User
            $user = User::where('phone', $rawPhone)
                ->orWhere('phone', $cleanPhone)
                ->orWhere('email', $email)
                ->first();

            if (!$user) {
                $user = new User();
                $user->name = $validated['student_name'];
                $user->email = $email;
                $user->phone = $rawPhone;
                $user->role = 'student';
            }

            $user->username = $username;
            $user->password = Hash::make($rawPassword);
            $user->plain_password = $rawPassword;
            $user->save();

            // 2. Find or create Enrollment
            $enrollment = Enrollment::where('course_id', $course->id)
                ->where(function ($q) use ($rawPhone, $cleanPhone, $email) {
                    $q->where('student_phone', $rawPhone)
                      ->orWhere('student_phone', $cleanPhone)
                      ->orWhere('student_email', $email);
                })
                ->first();

            if (!$enrollment) {
                $enrollment = Enrollment::create([
                    'user_id' => $user->id,
                    'course_id' => $course->id,
                    'student_name' => $validated['student_name'],
                    'student_email' => $email,
                    'student_phone' => $rawPhone,
                    'status' => 'active',
                    'enrolled_at' => now(),
                    'expires_at' => now()->addMonths(12),
                ]);
            } else {
                $enrollment->update([
                    'user_id' => $user->id,
                    'student_name' => $validated['student_name'],
                    'student_email' => $email,
                    'student_phone' => $rawPhone,
                    'status' => 'active',
                    'enrolled_at' => now(),
                ]);
            }

            // 3. Create completed Payment record
            Payment::create([
                'enrollment_id' => $enrollment->id,
                'course_id' => $course->id,
                'student_name' => $validated['student_name'],
                'student_email' => $email,
                'student_phone' => $rawPhone,
                'amount' => (float) $validated['amount'],
                'payment_method' => $validated['payment_method'],
                'transaction_id' => $trxId,
                'sender_phone' => $validated['sender_phone'] ?: $rawPhone,
                'status' => 'completed',
                'notes' => $validated['notes'] ?: 'এডমিন প্যানেল থেকে ম্যানুয়ালি ভর্তি নিশ্চিতকৃত।',
            ]);

            // Auto-clean any other duplicate pending enrollments for this student
            Enrollment::where('course_id', $course->id)
                ->where('id', '!=', $enrollment->id)
                ->where('status', 'pending')
                ->where(function ($q) use ($rawPhone, $cleanPhone, $email) {
                    $q->where('student_phone', $rawPhone)
                      ->orWhere('student_phone', $cleanPhone)
                      ->orWhere('student_email', $email);
                })
                ->delete();

            DB::commit();

            return back()->with('success', "শিক্ষার্থী {$validated['student_name']} সফলভাবে ম্যানুয়ালি ভর্তি করা হয়েছে! ইউজার আইডি: {$username}, পাসওয়ার্ড: {$rawPassword} (TrxID: {$trxId})");

        } catch (\Throwable $e) {
            DB::rollBack();
            return back()->withInput()->with('error', 'শিক্ষার্থী ভর্তি করতে সমস্যা হয়েছে: ' . $e->getMessage());
        }
    }

    /**
     * Admin Attendance Sessions Overview
     */
    public function attendanceIndex(Request $request)
    {
        $primaryCourse = Course::first();
        $totalActiveStudents = Enrollment::where('status', 'active')
            ->when($primaryCourse, fn($q) => $q->where('course_id', $primaryCourse->id))
            ->count();

        $sessions = AttendanceSession::where('course_id', $primaryCourse?->id)
            ->withCount(['attendances as present_count' => function ($q) {
                $q->whereIn('status', ['present', 'late']);
            }])
            ->orderBy('session_date', 'desc')
            ->orderBy('id', 'desc')
            ->paginate(15);

        $totalSessions = AttendanceSession::where('course_id', $primaryCourse?->id)
            ->where('status', '!=', 'cancelled')
            ->count();

        // Calculate average attendance across sessions
        $allSessions = AttendanceSession::where('course_id', $primaryCourse?->id)
            ->where('status', '!=', 'cancelled')
            ->withCount(['attendances as present_count' => fn($q) => $q->whereIn('status', ['present', 'late'])])
            ->get();

        $avgPercentage = 0;
        if ($allSessions->count() > 0 && $totalActiveStudents > 0) {
            $totalPresentSum = $allSessions->sum('present_count');
            $avgPercentage = (int) round(($totalPresentSum / ($allSessions->count() * $totalActiveStudents)) * 100);
        }

        // Today's attendance session if any
        $todaySession = AttendanceSession::where('course_id', $primaryCourse?->id)
            ->whereDate('session_date', now()->toDateString())
            ->first();

        return view('admin.attendance.index', compact(
            'primaryCourse',
            'sessions',
            'totalActiveStudents',
            'totalSessions',
            'avgPercentage',
            'todaySession'
        ));
    }

    /**
     * Create New Attendance Session
     */
    public function storeAttendanceSession(Request $request)
    {
        $primaryCourse = Course::first();
        $validated = $request->validate([
            'title' => ['required', 'string', 'max:255'],
            'session_date' => ['required', 'date'],
            'notes' => ['nullable', 'string', 'max:500'],
        ]);

        $session = AttendanceSession::create([
            'course_id' => $primaryCourse->id,
            'title' => $validated['title'],
            'session_date' => $validated['session_date'],
            'started_at' => now(),
            'status' => 'completed',
            'is_active' => true,
            'notes' => $validated['notes'] ?? null,
            'created_by' => Auth::id(),
        ]);

        return redirect()->route('admin.attendance.session', $session->id)
            ->with('success', 'নতুন ক্লাস সেশন সফলভাবে তৈরি করা হয়েছে! এখন উপস্থিতি পরিচালনা করুন।');
    }

    /**
     * View Attendance Sheet for a Session
     */
    public function showAttendanceSession(AttendanceSession $session)
    {
        $course = $session->course;

        // Get all active enrolled students with user relation
        $enrollments = Enrollment::where('course_id', $course->id)
            ->where('status', 'active')
            ->with('user')
            ->orderBy('student_name', 'asc')
            ->get();

        // Get map of user_id => Attendance record
        $attendances = Attendance::where('attendance_session_id', $session->id)
            ->get()
            ->keyBy('user_id');

        $presentCount = $attendances->filter(fn($a) => $a->isPresent())->count();
        $totalEnrolled = $enrollments->count();
        $percentage = $totalEnrolled > 0 ? (int) round(($presentCount / $totalEnrolled) * 100) : 0;

        return view('admin.attendance.sheet', compact(
            'session',
            'course',
            'enrollments',
            'attendances',
            'presentCount',
            'totalEnrolled',
            'percentage'
        ));
    }

    /**
     * Toggle Student Attendance in a Session
     */
    public function toggleAttendance(Request $request, AttendanceSession $session)
    {
        $validated = $request->validate([
            'user_id' => ['required', 'exists:users,id'],
        ]);

        $userId = $validated['user_id'];
        $attendance = Attendance::where('attendance_session_id', $session->id)
            ->where('user_id', $userId)
            ->first();

        $enrollment = Enrollment::where('user_id', $userId)
            ->where('course_id', $session->course_id)
            ->first();

        if ($attendance && $attendance->status === 'present') {
            $attendance->update([
                'status' => 'absent',
            ]);
            $newStatus = 'absent';
        } else {
            if ($attendance) {
                $attendance->update([
                    'status' => 'present',
                    'attended_at' => now(),
                    'join_method' => 'manual_admin',
                ]);
            } else {
                Attendance::create([
                    'attendance_session_id' => $session->id,
                    'user_id' => $userId,
                    'course_id' => $session->course_id,
                    'enrollment_id' => $enrollment?->id,
                    'status' => 'present',
                    'attended_at' => now(),
                    'join_method' => 'manual_admin',
                ]);
            }
            $newStatus = 'present';
        }

        if ($request->wantsJson()) {
            $presentCount = Attendance::where('attendance_session_id', $session->id)
                ->whereIn('status', ['present', 'late'])
                ->count();
            return response()->json([
                'success' => true,
                'status' => $newStatus,
                'present_count' => $presentCount,
            ]);
        }

        return back()->with('success', 'শিক্ষার্থীর উপস্থিতি সফলভাবে আপডেট করা হয়েছে!');
    }

    /**
     * Mark All Enrolled Students Present for a Session
     */
    public function markAllPresent(AttendanceSession $session)
    {
        $enrollments = Enrollment::where('course_id', $session->course_id)
            ->where('status', 'active')
            ->whereNotNull('user_id')
            ->get();

        foreach ($enrollments as $enr) {
            Attendance::updateOrCreate(
                [
                    'attendance_session_id' => $session->id,
                    'user_id' => $enr->user_id,
                ],
                [
                    'course_id' => $session->course_id,
                    'enrollment_id' => $enr->id,
                    'status' => 'present',
                    'attended_at' => now(),
                    'join_method' => 'manual_admin',
                ]
            );
        }

        return back()->with('success', 'সকল সক্রিয় শিক্ষার্থীকে উপস্থিত মার্ক করা হয়েছে!');
    }

    /**
     * Delete an Attendance Session
     */
    public function destroyAttendanceSession(AttendanceSession $session)
    {
        $session->delete();
        return redirect()->route('admin.attendance.index')
            ->with('success', 'ক্লাস সেশন ও উপস্থিতি রেকর্ড সফলভাবে মুছে ফেলা হয়েছে।');
    }

    /**
     * Student-wise Attendance Summary
     */
    public function attendanceStudents(Request $request)
    {
        $primaryCourse = Course::first();
        $search = trim((string) $request->input('search', ''));

        $totalSessions = AttendanceSession::where('course_id', $primaryCourse?->id)
            ->where('status', '!=', 'cancelled')
            ->count();

        $studentsQuery = Enrollment::where('course_id', $primaryCourse?->id)
            ->where('status', 'active')
            ->with(['user.attendances' => function ($q) use ($primaryCourse) {
                $q->where('course_id', $primaryCourse?->id);
            }]);

        if (!empty($search)) {
            $studentsQuery->where(function ($q) use ($search) {
                $q->where('student_name', 'like', "%{$search}%")
                  ->orWhere('student_phone', 'like', "%{$search}%")
                  ->orWhere('student_email', 'like', "%{$search}%");
            });
        }

        $students = $studentsQuery->orderBy('student_name', 'asc')->paginate(20);

        return view('admin.attendance.students', compact('primaryCourse', 'students', 'totalSessions', 'search'));
    }
}
