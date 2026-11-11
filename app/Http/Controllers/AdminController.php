<?php

namespace App\Http\Controllers;

use App\Models\Attendance;
use App\Models\AttendanceSession;
use App\Models\Batch;
use App\Models\Course;
use App\Models\Enrollment;
use App\Models\LiveMeeting;
use App\Models\Payment;
use App\Models\User;
use App\Services\EpsGateway;
use Carbon\Carbon;
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
     * Batch Management Index Page
     */
    public function batchesIndex(Request $request)
    {
        $courses = Course::all(['id', 'title', 'bangla_title', 'regular_price', 'discount_price', 'live_schedule_text']);
        $primaryCourse = Course::find($request->input('course_id')) ?? Course::first();

        if ($primaryCourse) {
            self::reconcileEnrolledStudents($primaryCourse);
        }

        $batches = Batch::with('course')
            ->when($primaryCourse, fn ($q) => $q->where('course_id', $primaryCourse->id))
            ->withCount([
                'enrollments as active_enrollments_count' => fn ($q) => $q->whereIn('status', ['active', 'completed']),
                'payments as completed_payments_count' => fn ($q) => $q->where('status', 'completed'),
                'liveMeetings as meetings_count',
                'attendanceSessions as sessions_count',
            ])
            ->orderBy('batch_number', 'asc')
            ->orderBy('id', 'asc')
            ->get();

        $openBatch = $primaryCourse?->openEnrollmentBatch();

        return view('admin.batches.index', compact(
            'primaryCourse',
            'courses',
            'batches',
            'openBatch'
        ));
    }

    /**
     * Create a New Batch (Course content/modules remain shared)
     */
    public function storeBatch(Request $request)
    {
        $primaryCourse = Course::find($request->input('course_id')) ?? Course::firstOrFail();

        $validated = $request->validate([
            'name' => ['required', 'string', 'max:120'],
            'batch_number' => ['nullable', 'integer', 'min:1', 'max:9999'],
            'start_date' => ['nullable', 'date'],
            'start_date_text' => ['nullable', 'string', 'max:120'],
            'schedule_text' => ['nullable', 'string', 'max:255'],
            'seat_limit' => ['nullable', 'integer', 'min:1', 'max:10000'],
            'seats_remaining_override' => ['nullable', 'integer', 'min:0', 'max:10000'],
            'regular_price' => ['nullable', 'numeric', 'min:0'],
            'discount_price' => ['nullable', 'numeric', 'min:0'],
            'discount_badge_text' => ['nullable', 'string', 'max:255'],
            'offer_label' => ['nullable', 'string', 'max:255'],
            'status' => ['nullable', 'string', 'in:upcoming,running,completed'],
            'notes' => ['nullable', 'string', 'max:1000'],
        ], [
            'name.required' => 'ব্যাচের নাম প্রদান করুন (যেমন: ২য় ব্যাচ)।',
        ]);

        $batchNumber = $validated['batch_number'] ?? ((Batch::where('course_id', $primaryCourse->id)->max('batch_number') ?? 0) + 1);
        $startDate = !empty($validated['start_date']) ? Carbon::parse($validated['start_date']) : null;
        $startDateText = trim((string) ($validated['start_date_text'] ?? ''));
        if (empty($startDateText) && $startDate) {
            $startDateText = Batch::formatBanglaDate($startDate);
        }

        $enrollmentOpen = $request->boolean('enrollment_open');
        $isActive = $request->has('is_active') ? $request->boolean('is_active') : true;

        if ($enrollmentOpen && $request->boolean('close_other_batches')) {
            Batch::where('course_id', $primaryCourse->id)->update(['enrollment_open' => false]);
        }

        $batch = Batch::create([
            'course_id' => $primaryCourse->id,
            'name' => trim($validated['name']),
            'batch_number' => $batchNumber,
            'start_date' => $startDate,
            'start_date_text' => $startDateText ?: null,
            'schedule_text' => trim((string) ($validated['schedule_text'] ?? '')) ?: ($primaryCourse->live_schedule_text ?: 'প্রতি বৃহস্পতি, শুক্র ও শনিবার রাত ৯:০০ টা'),
            'seat_limit' => $validated['seat_limit'] ?? 50,
            'seats_remaining_override' => $validated['seats_remaining_override'] ?? null,
            'regular_price' => $validated['regular_price'] ?? $primaryCourse->regular_price,
            'discount_price' => $validated['discount_price'] ?? $primaryCourse->discount_price,
            'discount_badge_text' => trim((string) ($validated['discount_badge_text'] ?? '')) ?: null,
            'offer_label' => trim((string) ($validated['offer_label'] ?? '')) ?: null,
            'enrollment_open' => $enrollmentOpen,
            'status' => $validated['status'] ?? ($enrollmentOpen ? 'upcoming' : 'running'),
            'is_active' => $isActive,
            'notes' => $validated['notes'] ?? null,
        ]);

        $this->syncCourseBatchSummary($primaryCourse);

        return back()->with('success', "নতুন ব্যাচ '{$batch->name}' সফলভাবে তৈরি করা হয়েছে!" . ($batch->enrollment_open ? ' (অনলাইন ভর্তি চালু রয়েছে)' : ''));
    }

    /**
     * Update Existing Batch Details
     */
    public function updateBatch(Request $request, Batch $batch)
    {
        $validated = $request->validate([
            'name' => ['required', 'string', 'max:120'],
            'batch_number' => ['nullable', 'integer', 'min:1', 'max:9999'],
            'start_date' => ['nullable', 'date'],
            'start_date_text' => ['nullable', 'string', 'max:120'],
            'schedule_text' => ['nullable', 'string', 'max:255'],
            'seat_limit' => ['nullable', 'integer', 'min:1', 'max:10000'],
            'seats_remaining_override' => ['nullable', 'integer', 'min:0', 'max:10000'],
            'regular_price' => ['nullable', 'numeric', 'min:0'],
            'discount_price' => ['nullable', 'numeric', 'min:0'],
            'discount_badge_text' => ['nullable', 'string', 'max:255'],
            'offer_label' => ['nullable', 'string', 'max:255'],
            'status' => ['nullable', 'string', 'in:upcoming,running,completed'],
            'notes' => ['nullable', 'string', 'max:1000'],
        ], [
            'name.required' => 'ব্যাচের নাম প্রদান করুন।',
        ]);

        $startDate = !empty($validated['start_date']) ? Carbon::parse($validated['start_date']) : null;
        $startDateText = trim((string) ($validated['start_date_text'] ?? ''));
        if (empty($startDateText) && $startDate) {
            $startDateText = Batch::formatBanglaDate($startDate);
        }

        $enrollmentOpen = $request->boolean('enrollment_open');
        $isActive = $request->has('is_active') ? $request->boolean('is_active') : $batch->is_active;

        $batch->update([
            'name' => trim($validated['name']),
            'batch_number' => $validated['batch_number'] ?? $batch->batch_number,
            'start_date' => $startDate,
            'start_date_text' => $startDateText ?: null,
            'schedule_text' => trim((string) ($validated['schedule_text'] ?? '')) ?: $batch->schedule_text,
            'seat_limit' => $validated['seat_limit'] ?? $batch->seat_limit,
            'seats_remaining_override' => $request->filled('seats_remaining_override') ? (int) $validated['seats_remaining_override'] : null,
            'regular_price' => $validated['regular_price'] ?? $batch->regular_price,
            'discount_price' => $validated['discount_price'] ?? $batch->discount_price,
            'discount_badge_text' => trim((string) ($validated['discount_badge_text'] ?? '')) ?: null,
            'offer_label' => trim((string) ($validated['offer_label'] ?? '')) ?: null,
            'enrollment_open' => $enrollmentOpen,
            'status' => $validated['status'] ?? $batch->status,
            'is_active' => $isActive,
            'notes' => $validated['notes'] ?? null,
        ]);

        if ($batch->course) {
            $this->syncCourseBatchSummary($batch->course);
        }

        return back()->with('success', "ব্যাচ '{$batch->name}' সফলভাবে হালনাগাদ করা হয়েছে!");
    }

    /**
     * Toggle Enrollment Open/Closed State for a Batch
     */
    public function toggleBatchEnrollment(Batch $batch)
    {
        $newState = !$batch->enrollment_open;

        $batch->update([
            'enrollment_open' => $newState,
            'is_active' => $newState ? true : $batch->is_active,
        ]);

        if ($batch->course) {
            $this->syncCourseBatchSummary($batch->course);
        }

        $msg = $newState
            ? "{$batch->name}-এর অনলাইন ভর্তি চালু (OPEN) করা হয়েছে! ওয়েবসাইটে এখন শিক্ষার্থীরা এই ব্যাচে ভর্তি হতে পারবে।"
            : "{$batch->name}-এর অনলাইন ভর্তি বন্ধ (CLOSED) করা হয়েছে!";

        return back()->with('success', $msg);
    }

    /**
     * Delete a Batch (Only allowed when no student enrollments or payments are linked)
     */
    public function destroyBatch(Batch $batch)
    {
        if ($batch->enrollments()->exists() || $batch->payments()->exists()) {
            return back()->with('error', "'{$batch->name}' ব্যাচে ভর্তিকৃত শিক্ষার্থী/পেমেন্ট রেকর্ড রয়েছে, তাই এটি মুছে ফেলা যাবে না। চাইলে ভর্তি বন্ধ (Close) করে রাখতে পারেন।");
        }

        $course = $batch->course;
        $name = $batch->name;
        $batch->delete();

        if ($course) {
            $this->syncCourseBatchSummary($course);
        }

        return back()->with('success', "ব্যাচ '{$name}' সফলভাবে মুছে ফেলা হয়েছে।");
    }

    /**
     * Sync parent Course's summary batch_no and start_date from the active/latest batch
     */
    protected function syncCourseBatchSummary(Course $course): void
    {
        $refBatch = $course->currentOrLatestBatch();
        if ($refBatch) {
            $batchNoText = $refBatch->name;
            $startText = $refBatch->formatted_start_date;
            if (!empty($startText) && $startText !== 'শীঘ্রই ঘোষণা হবে') {
                $batchNoText .= " (শুরু: {$startText})";
            }

            $course->update([
                'batch_no' => $batchNoText,
                'start_date' => $refBatch->start_date ?? $course->start_date,
            ]);
        }
    }

    /**
     * Purchased Students List & Dashboard (Excludes pending payments)
     */
    public function students(Request $request)
    {
        $batchId = $request->filled('batch_id') ? (int) $request->input('batch_id') : null;

        // 1. KPI Stats (Strictly for completed course purchases, optionally scoped by batch)
        $completedPayments = Payment::where('status', 'completed')
            ->when($batchId, fn ($q) => $q->where('batch_id', $batchId));

        $stats = [
            'total_students' => (clone $completedPayments)->count(),
            'total_revenue' => (clone $completedPayments)->sum('amount'),
            'eps_count' => (clone $completedPayments)->where('payment_method', 'eps')->count(),
            'mobile_count' => (clone $completedPayments)->whereIn('payment_method', ['bkash', 'nagad', 'rocket', 'card'])->count(),
            'pending_count' => Payment::where('status', 'pending')->when($batchId, fn ($q) => $q->where('batch_id', $batchId))->count(),
        ];

        // 2a. Pending payments needing admin review / manual approval
        $pendingPayments = Payment::with(['enrollment.user', 'course', 'batch'])
            ->where('status', 'pending')
            ->when($batchId, fn ($q) => $q->where('batch_id', $batchId))
            ->latest()
            ->get();

        // 2b. Query builder: strictly show completed payment registrations
        $query = Payment::with(['enrollment.user', 'course', 'batch'])
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

        // Batch filter
        if ($batchId) {
            $query->where('batch_id', $batchId);
        }

        // Course filter
        if ($courseId = $request->input('course_id')) {
            $query->where('course_id', $courseId);
        }

        $payments = $query->paginate(15)->withQueryString();
        $courses = Course::all(['id', 'title', 'bangla_title']);
        $batches = Batch::orderBy('batch_number', 'asc')->orderBy('id', 'asc')->get();
        $primaryCourse = Course::first();

        return view('admin.students', compact(
            'payments',
            'pendingPayments',
            'stats',
            'courses',
            'batches',
            'primaryCourse'
        ));
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
                if (!$payment->enrollment->user_id || !$payment->enrollment->user) {
                    self::ensureUserForEnrollment($payment->enrollment);
                }
            } elseif ($validated['status'] === 'cancelled' || $validated['status'] === 'failed') {
                $payment->enrollment->update(['status' => 'cancelled']);
            } elseif ($validated['status'] === 'pending') {
                $payment->enrollment->update(['status' => 'pending']);
            }
        } elseif ($validated['status'] === 'completed') {
            $batchId = $payment->batch_id ?? $payment->course?->currentOrLatestBatch()?->id;

            // Find existing enrollment or create new active enrollment
            $enrollment = Enrollment::where('course_id', $payment->course_id)
                ->when($batchId, fn ($q) => $q->where(fn ($sub) => $sub->where('batch_id', $batchId)->orWhereNull('batch_id')))
                ->where(function ($q) use ($payment) {
                    $q->where('student_email', $payment->student_email)
                      ->orWhere('student_phone', $payment->student_phone);
                })
                ->first();

            if (!$enrollment) {
                $enrollment = Enrollment::create([
                    'course_id' => $payment->course_id,
                    'batch_id' => $batchId,
                    'student_name' => $payment->student_name,
                    'student_email' => $payment->student_email,
                    'student_phone' => $payment->student_phone,
                    'status' => 'active',
                    'enrolled_at' => now(),
                    'expires_at' => now()->addMonths(12),
                ]);
            } else {
                $enrollment->update([
                    'status' => 'active',
                    'batch_id' => $enrollment->batch_id ?? $batchId,
                ]);
            }

            $payment->update([
                'enrollment_id' => $enrollment->id,
                'batch_id' => $payment->batch_id ?? $enrollment->batch_id,
            ]);
            self::ensureUserForEnrollment($enrollment);
        }

        // Auto-clean duplicates if payment was approved to completed
        if ($validated['status'] === 'completed') {
            $scopeBatchId = $payment->batch_id ?? $payment->enrollment?->batch_id;

            Enrollment::where('course_id', $payment->course_id)
                ->when($scopeBatchId, fn ($q) => $q->where('batch_id', $scopeBatchId))
                ->where('id', '!=', $payment->enrollment_id)
                ->where('status', 'pending')
                ->where(function ($q) use ($payment) {
                    $q->where('student_email', $payment->student_email)
                      ->orWhere('student_phone', $payment->student_phone);
                })
                ->delete();

            Payment::where('course_id', $payment->course_id)
                ->when($scopeBatchId, fn ($q) => $q->where('batch_id', $scopeBatchId))
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

        // 1. Delete pending enrollments for students who already have an active enrollment in the same course batch
        $activeStudents = Enrollment::where('status', 'active')->get(['id', 'course_id', 'batch_id', 'student_email', 'student_phone']);

        foreach ($activeStudents as $active) {
            $duplicateEnrollments = Enrollment::where('course_id', $active->course_id)
                ->when($active->batch_id, fn ($q) => $q->where('batch_id', $active->batch_id))
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
                ->when($active->batch_id, fn ($q) => $q->where('batch_id', $active->batch_id))
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
            ->select('course_id', 'batch_id', 'student_phone', DB::raw('COUNT(*) as total'), DB::raw('MAX(id) as keep_id'))
            ->groupBy('course_id', 'batch_id', 'student_phone')
            ->having('total', '>', 1)
            ->get();

        foreach ($pendingGroups as $group) {
            $toDelete = Enrollment::where('course_id', $group->course_id)
                ->when($group->batch_id, fn ($q) => $q->where('batch_id', $group->batch_id), fn ($q) => $q->whereNull('batch_id'))
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
        $query = Payment::with(['enrollment', 'course', 'batch'])
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

        if ($batchId = $request->input('batch_id')) {
            $query->where('batch_id', $batchId);
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
                'ব্যাচ (Batch)',
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
                    $payment->batch?->name ?? $payment->course?->batch_no ?? 'N/A',
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
    public function liveMeetings(Request $request)
    {
        $primaryCourse = Course::first();
        $batches = $primaryCourse
            ? Batch::where('course_id', $primaryCourse->id)
                ->orderBy('batch_number', 'asc')
                ->orderBy('id', 'asc')
                ->get()
            : collect();
        $selectedBatchId = $request->filled('batch_id') ? (int) $request->input('batch_id') : null;

        $totalStudents = Enrollment::where('status', 'active')
            ->when($selectedBatchId, fn ($q) => $q->where('batch_id', $selectedBatchId))
            ->count();

        $meetings = collect();
        $activeMeeting = null;

        if ($primaryCourse) {
            $meetings = LiveMeeting::with('batch')
                ->where('course_id', $primaryCourse->id)
                ->when($selectedBatchId, fn ($q) => $q->where('batch_id', $selectedBatchId))
                ->orderBy('starts_at', 'desc')
                ->get();

            $activeMeeting = $primaryCourse->currentOrNextMeeting($selectedBatchId);
        }

        return view('admin.live-meetings', compact(
            'primaryCourse',
            'totalStudents',
            'meetings',
            'activeMeeting',
            'batches',
            'selectedBatchId'
        ));
    }

    /**
     * Store Scheduled Live Meeting (Date and Time based)
     */
    public function storeMeeting(Request $request)
    {
        $primaryCourse = Course::firstOrFail();

        $rawUrl = trim((string) $request->input('meeting_url'));
        if (!empty($rawUrl) && !str_starts_with($rawUrl, 'http://') && !str_starts_with($rawUrl, 'https://')) {
            $rawUrl = 'https://' . $rawUrl;
            $request->merge(['meeting_url' => $rawUrl]);
        }

        $validated = $request->validate([
            'batch_id' => ['nullable', 'exists:batches,id'],
            'title' => ['required', 'string', 'max:255'],
            'meeting_url' => ['required', 'url', 'max:1000'],
            'meeting_id' => ['nullable', 'string', 'max:100'],
            'meeting_passcode' => ['nullable', 'string', 'max:100'],
            'scheduled_date' => ['required', 'date'],
            'scheduled_time' => ['required', 'string'],
            'schedule_text' => ['nullable', 'string', 'max:255'],
            'notes' => ['nullable', 'string', 'max:500'],
        ], [
            'title.required' => 'ক্লাসের বিষয় বা শিরোনাম প্রদান করুন।',
            'meeting_url.required' => 'মিটিং লিংক আবশ্যক।',
            'meeting_url.url' => 'সঠিক মিটিং লিংক প্রদান করুন (যেমন: https://zoom.us/j/... অথবা https://meet.google.com/...)।',
            'scheduled_date.required' => 'ক্লাসের তারিখ নির্ধারণ করুন।',
            'scheduled_time.required' => 'ক্লাস শুরুর সময় নির্ধারণ করুন।',
        ]);

        $batchId = !empty($validated['batch_id']) ? (int) $validated['batch_id'] : null;
        $startsAt = Carbon::parse($validated['scheduled_date'] . ' ' . $validated['scheduled_time']);
        $endsAt = $startsAt->copy()->addHours(2);

        $platform = 'other';
        if (str_contains(strtolower($validated['meeting_url']), 'zoom')) {
            $platform = 'zoom';
        } elseif (str_contains(strtolower($validated['meeting_url']), 'meet.google')) {
            $platform = 'google_meet';
        }

        $isLiveNow = $request->boolean('is_live_now');

        if ($isLiveNow) {
            // Unset other live meetings for this batch (or entire course if global)
            $resetQuery = LiveMeeting::where('course_id', $primaryCourse->id);
            if ($batchId) {
                $resetQuery->where('batch_id', $batchId);
            }
            $resetQuery->update(['is_live_now' => false]);
        }

        $recordingUrl = trim((string) $request->input('recording_url'));
        $hasRecording = !empty($recordingUrl);

        $meeting = LiveMeeting::create([
            'course_id' => $primaryCourse->id,
            'batch_id' => $batchId,
            'title' => $validated['title'],
            'meeting_url' => $validated['meeting_url'],
            'recording_url' => $hasRecording ? $recordingUrl : null,
            'recording_duration' => $request->input('recording_duration'),
            'recording_notes' => $request->input('recording_notes'),
            'has_recording' => $hasRecording,
            'meeting_id' => $validated['meeting_id'] ?? null,
            'meeting_passcode' => $validated['meeting_passcode'] ?? null,
            'platform' => $platform,
            'starts_at' => $startsAt,
            'ends_at' => $endsAt,
            'schedule_text' => $validated['schedule_text'] ?? null,
            'is_live_now' => $isLiveNow,
            'status' => $isLiveNow ? 'live' : 'scheduled',
            'notes' => $validated['notes'] ?? null,
            'created_by' => Auth::id(),
        ]);

        // Sync with course
        $hasAnyLiveMeeting = LiveMeeting::where('course_id', $primaryCourse->id)
            ->where('is_live_now', true)
            ->exists();

        if ($isLiveNow || !$hasAnyLiveMeeting) {
            $primaryCourse->update([
                'live_meeting_url' => $meeting->meeting_url,
                'live_meeting_id' => $meeting->meeting_id,
                'live_meeting_passcode' => $meeting->meeting_passcode,
                'live_class_topic' => $meeting->title,
                'live_schedule_text' => $meeting->formatted_schedule,
                'is_live_now' => $hasAnyLiveMeeting,
            ]);
        } else {
            $primaryCourse->update(['is_live_now' => $hasAnyLiveMeeting]);
        }

        if ($isLiveNow) {
            AttendanceSession::firstOrCreate(
                [
                    'course_id' => $primaryCourse->id,
                    'batch_id' => $meeting->batch_id,
                    'session_date' => $startsAt->toDateString(),
                ],
                [
                    'title' => $meeting->title,
                    'started_at' => now(),
                    'is_active' => true,
                    'status' => 'completed',
                ]
            );
        }

        return back()->with('success', "তারিখ ও সময়ভিত্তিক মিটিং সফলভাবে শিডিউল করা হয়েছে! ({$meeting->formatted_schedule})");
    }

    /**
     * Update an Existing Scheduled Live Meeting
     */
    public function updateMeeting(Request $request, LiveMeeting $meeting)
    {
        $rawUrl = trim((string) $request->input('meeting_url'));
        if (!empty($rawUrl) && !str_starts_with($rawUrl, 'http://') && !str_starts_with($rawUrl, 'https://')) {
            $rawUrl = 'https://' . $rawUrl;
            $request->merge(['meeting_url' => $rawUrl]);
        }

        $validated = $request->validate([
            'batch_id' => ['nullable', 'exists:batches,id'],
            'title' => ['required', 'string', 'max:255'],
            'meeting_url' => ['required', 'url', 'max:1000'],
            'meeting_id' => ['nullable', 'string', 'max:100'],
            'meeting_passcode' => ['nullable', 'string', 'max:100'],
            'scheduled_date' => ['required', 'date'],
            'scheduled_time' => ['required', 'string'],
            'schedule_text' => ['nullable', 'string', 'max:255'],
            'notes' => ['nullable', 'string', 'max:500'],
        ], [
            'title.required' => 'ক্লাসের বিষয় বা শিরোনাম প্রদান করুন।',
            'meeting_url.required' => 'মিটিং লিংক আবশ্যক।',
            'meeting_url.url' => 'সঠিক মিটিং লিংক প্রদান করুন (যেমন: https://zoom.us/j/... অথবা https://meet.google.com/...)।',
            'scheduled_date.required' => 'ক্লাসের তারিখ নির্ধারণ করুন।',
            'scheduled_time.required' => 'ক্লাস শুরুর সময় নির্ধারণ করুন।',
        ]);

        $batchId = !empty($validated['batch_id']) ? (int) $validated['batch_id'] : null;
        $startsAt = Carbon::parse($validated['scheduled_date'] . ' ' . $validated['scheduled_time']);
        $endsAt = $startsAt->copy()->addHours(2);

        $platform = 'other';
        if (str_contains(strtolower($validated['meeting_url']), 'zoom')) {
            $platform = 'zoom';
        } elseif (str_contains(strtolower($validated['meeting_url']), 'meet.google')) {
            $platform = 'google_meet';
        }

        $isLiveNow = $request->boolean('is_live_now');
        $course = $meeting->course;

        if ($isLiveNow) {
            $resetQuery = LiveMeeting::where('course_id', $course->id)
                ->where('id', '!=', $meeting->id);
            if ($batchId) {
                $resetQuery->where('batch_id', $batchId);
            }
            $resetQuery->update(['is_live_now' => false]);
        }

        $meetingUpdates = [
            'batch_id' => $batchId,
            'title' => $validated['title'],
            'meeting_url' => $validated['meeting_url'],
            'meeting_id' => $validated['meeting_id'] ?? null,
            'meeting_passcode' => $validated['meeting_passcode'] ?? null,
            'platform' => $platform,
            'starts_at' => $startsAt,
            'ends_at' => $endsAt,
            'schedule_text' => $validated['schedule_text'] ?? null,
            'is_live_now' => $isLiveNow,
            'status' => $isLiveNow ? 'live' : 'scheduled',
            'notes' => $validated['notes'] ?? null,
        ];

        if ($request->has('recording_url')) {
            $recUrl = trim((string) $request->input('recording_url'));
            $meetingUpdates['recording_url'] = !empty($recUrl) ? $recUrl : null;
            $meetingUpdates['recording_duration'] = $request->input('recording_duration');
            $meetingUpdates['recording_notes'] = $request->input('recording_notes');
            $meetingUpdates['has_recording'] = !empty($recUrl);
        }

        $meeting->update($meetingUpdates);

        $hasAnyLiveMeeting = LiveMeeting::where('course_id', $course->id)
            ->where('is_live_now', true)
            ->exists();

        if ($isLiveNow) {
            $course->update([
                'live_meeting_url' => $meeting->meeting_url,
                'live_meeting_id' => $meeting->meeting_id,
                'live_meeting_passcode' => $meeting->meeting_passcode,
                'live_class_topic' => $meeting->title,
                'live_schedule_text' => $meeting->formatted_schedule,
                'is_live_now' => true,
            ]);

            AttendanceSession::firstOrCreate(
                [
                    'course_id' => $course->id,
                    'batch_id' => $meeting->batch_id,
                    'session_date' => $startsAt->toDateString(),
                ],
                [
                    'title' => $meeting->title,
                    'started_at' => now(),
                    'is_active' => true,
                    'status' => 'completed',
                ]
            );
        } else {
            $course->update(['is_live_now' => $hasAnyLiveMeeting]);
        }

        return back()->with('success', "মিটিংটি সফলভাবে হালনাগাদ (সম্পাদনা) করা হয়েছে!");
    }

    /**
     * Update/Publish Recording for a Class Meeting (YouTube or Storage Video)
     */
    public function updateRecording(Request $request, LiveMeeting $meeting)
    {
        $validated = $request->validate([
            'recording_url' => ['required', 'string', 'max:1000'],
            'recording_duration' => ['nullable', 'string', 'max:50'],
            'recording_notes' => ['nullable', 'string', 'max:1000'],
        ], [
            'recording_url.required' => 'ভিডিও রেকর্ডিং লিংক (YouTube লিংক বা ভিডিও URL) প্রদান করুন।',
        ]);

        $recordingUrl = trim($validated['recording_url']);

        $meeting->update([
            'recording_url' => $recordingUrl,
            'recording_duration' => $validated['recording_duration'] ?? null,
            'recording_notes' => $validated['recording_notes'] ?? null,
            'has_recording' => true,
            'status' => 'completed',
        ]);

        return back()->with('success', "ক্লাস রেকর্ডিং সফলভাবে সংরক্ষিত ও প্রকাশ করা হয়েছে! (\"{$meeting->title}\")");
    }

    /**
     * Remove Recording from a Class Meeting
     */
    public function removeRecording(LiveMeeting $meeting)
    {
        $meeting->update([
            'recording_url' => null,
            'recording_duration' => null,
            'has_recording' => false,
        ]);

        return back()->with('success', "ক্লাস রেকর্ডিং সফলভাবে সরিয়ে নেওয়া হয়েছে।");
    }

    /**
     * Toggle Live Status for a Scheduled Meeting
     */
    public function toggleMeetingLive(LiveMeeting $meeting)
    {
        $course = $meeting->course;

        if ($meeting->is_live_now) {
            $meeting->update([
                'is_live_now' => false,
                'status' => 'completed',
            ]);
            $hasAnyLiveMeeting = LiveMeeting::where('course_id', $course->id)
                ->where('is_live_now', true)
                ->exists();
            $course->update(['is_live_now' => $hasAnyLiveMeeting]);
            $msg = 'লাইভ মিটিং সফলভাবে বন্ধ করা হয়েছে। শিক্ষার্থীরা এখন পরবর্তী সময়সূচি দেখবে।';
        } else {
            // Turn off other live meetings for this batch (or entire course if global)
            $resetQuery = LiveMeeting::where('course_id', $course->id);
            if ($meeting->batch_id) {
                $resetQuery->where('batch_id', $meeting->batch_id);
            }
            $resetQuery->update(['is_live_now' => false]);

            $meeting->update([
                'is_live_now' => true,
                'status' => 'live',
            ]);

            $course->update([
                'live_meeting_url' => $meeting->meeting_url,
                'live_class_topic' => $meeting->title,
                'live_meeting_id' => $meeting->meeting_id,
                'live_meeting_passcode' => $meeting->meeting_passcode,
                'live_schedule_text' => $meeting->formatted_schedule,
                'is_live_now' => true,
            ]);

            // Ensure attendance session exists (scoped by batch)
            AttendanceSession::firstOrCreate(
                [
                    'course_id' => $course->id,
                    'batch_id' => $meeting->batch_id,
                    'session_date' => now()->toDateString(),
                ],
                [
                    'title' => $meeting->title,
                    'started_at' => now(),
                    'is_active' => true,
                    'status' => 'completed',
                ]
            );

            $msg = "🔴 '{$meeting->title}' লাইভ মিটিং শুরু করা হয়েছে! শিক্ষার্থীদের ড্যাশবোর্ডে সরাসরি জয়েন বাটন সক্রিয় হয়ে গেছে।";
        }

        return back()->with('success', $msg);
    }

    /**
     * Delete a Scheduled Meeting
     */
    public function destroyMeeting(LiveMeeting $meeting)
    {
        $course = $meeting->course;
        $wasLive = $meeting->is_live_now;
        $meeting->delete();

        if ($wasLive && $course) {
            $hasAnyLiveMeeting = LiveMeeting::where('course_id', $course->id)
                ->where('is_live_now', true)
                ->exists();
            $course->update(['is_live_now' => $hasAnyLiveMeeting]);
        }

        return back()->with('success', 'মিটিংটি সফলভাবে মুছে ফেলা হয়েছে।');
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
            'batch_id' => ['nullable', 'exists:batches,id'],
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
        $batchId = !empty($validated['batch_id']) ? (int) $validated['batch_id'] : $course->currentOrLatestBatch()?->id;
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

            // 2. Find or create Enrollment (scoped by batch when batch exists)
            $enrollmentQuery = Enrollment::where('course_id', $course->id)
                ->where(function ($q) use ($rawPhone, $cleanPhone, $email) {
                    $q->where('student_phone', $rawPhone)
                      ->orWhere('student_phone', $cleanPhone)
                      ->orWhere('student_email', $email);
                });

            if ($batchId) {
                $enrollmentQuery->where(function ($q) use ($batchId) {
                    $q->where('batch_id', $batchId)->orWhereNull('batch_id');
                });
            }

            $enrollment = $enrollmentQuery->first();

            if (!$enrollment) {
                $enrollment = Enrollment::create([
                    'user_id' => $user->id,
                    'course_id' => $course->id,
                    'batch_id' => $batchId,
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
                    'batch_id' => $enrollment->batch_id ?? $batchId,
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
                'batch_id' => $batchId ?? $enrollment->batch_id,
                'student_name' => $validated['student_name'],
                'student_email' => $email,
                'student_phone' => $rawPhone,
                'amount' => (float) $validated['amount'],
                'payment_method' => $validated['payment_method'],
                'transaction_id' => $trxId,
                'sender_phone' => ($validated['sender_phone'] ?? null) ?: $rawPhone,
                'status' => 'completed',
                'notes' => ($validated['notes'] ?? null) ?: 'এডমিন প্যানেল থেকে ম্যানুয়ালি ভর্তি নিশ্চিতকৃত।',
            ]);

            // Auto-clean any other duplicate pending enrollments for this student in same batch
            Enrollment::where('course_id', $course->id)
                ->when($batchId, fn ($q) => $q->where(fn ($sub) => $sub->where('batch_id', $batchId)->orWhereNull('batch_id')))
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
     * Ensure all confirmed students with completed payments or enrollments
     * have an active Enrollment record and a linked User account.
     */
    public static function reconcileEnrolledStudents(?Course $course = null): void
    {
        $courseId = $course?->id ?? Course::first()?->id;
        if (!$courseId) {
            return;
        }

        $defaultBatchId = Batch::where('course_id', $courseId)
            ->orderBy('batch_number', 'asc')
            ->orderBy('id', 'asc')
            ->value('id');

        // 1. Ensure every completed payment for this course has an active Enrollment
        $completedPayments = Payment::where(function ($q) use ($courseId) {
                $q->where('course_id', $courseId)
                  ->orWhereNull('course_id');
            })
            ->where('status', 'completed')
            ->get();

        foreach ($completedPayments as $payment) {
            $rawPhone = trim((string) $payment->student_phone);
            $cleanPhone = preg_replace('/[^0-9]/', '', $rawPhone);
            $email = trim((string) $payment->student_email);

            $enrollment = null;
            if ($payment->enrollment_id) {
                $enrollment = Enrollment::find($payment->enrollment_id);
            }

            if (!$enrollment) {
                $enrollmentQuery = Enrollment::where(function ($q) use ($rawPhone, $cleanPhone, $email) {
                    if (!empty($rawPhone)) {
                        $q->where('student_phone', $rawPhone);
                        if (!empty($cleanPhone)) {
                            $q->orWhere('student_phone', $cleanPhone);
                        }
                    }
                    if (!empty($email)) {
                        $q->orWhere('student_email', $email);
                    }
                })->where(function ($q) use ($courseId) {
                    $q->where('course_id', $courseId)->orWhereNull('course_id');
                });

                if ($payment->batch_id) {
                    $enrollmentQuery->where(function ($q) use ($payment) {
                        $q->where('batch_id', $payment->batch_id)->orWhereNull('batch_id');
                    });
                }

                $enrollment = $enrollmentQuery->first();
            }

            $targetBatchId = $payment->batch_id ?? $enrollment?->batch_id ?? $defaultBatchId;

            if (!$enrollment) {
                $enrollment = Enrollment::create([
                    'course_id' => $courseId,
                    'batch_id' => $targetBatchId,
                    'student_name' => $payment->student_name,
                    'student_email' => $email,
                    'student_phone' => $rawPhone,
                    'status' => 'active',
                    'enrolled_at' => $payment->created_at ?? now(),
                    'expires_at' => now()->addMonths(12),
                ]);
            } else {
                $updates = [];
                if ($enrollment->status !== 'active') {
                    $updates['status'] = 'active';
                }
                if (!$enrollment->course_id) {
                    $updates['course_id'] = $courseId;
                }
                if (!$enrollment->batch_id && $targetBatchId) {
                    $updates['batch_id'] = $targetBatchId;
                }
                if (!empty($payment->student_name) && $enrollment->student_name !== $payment->student_name) {
                    $updates['student_name'] = $payment->student_name;
                }
                if (!empty($email) && $enrollment->student_email !== $email) {
                    $updates['student_email'] = $email;
                }
                if (!empty($updates)) {
                    $enrollment->update($updates);
                }
            }

            $paymentUpdates = [];
            if ($payment->enrollment_id !== $enrollment->id) {
                $paymentUpdates['enrollment_id'] = $enrollment->id;
            }
            if ($payment->course_id !== $courseId) {
                $paymentUpdates['course_id'] = $courseId;
            }
            if (!$payment->batch_id && ($enrollment->batch_id ?? $targetBatchId)) {
                $paymentUpdates['batch_id'] = $enrollment->batch_id ?? $targetBatchId;
            }
            if (!empty($paymentUpdates)) {
                $payment->update($paymentUpdates);
            }

            self::ensureUserForEnrollment($enrollment);
        }

        // 2. Also ensure any existing active or completed enrollments have valid user_id, course_id & batch_id
        $enrollments = Enrollment::where(function ($q) use ($courseId) {
                $q->where('course_id', $courseId)->orWhereNull('course_id');
            })
            ->whereIn('status', ['active', 'completed'])
            ->get();

        foreach ($enrollments as $enr) {
            $updates = [];
            if ($enr->status !== 'active') {
                $updates['status'] = 'active';
            }
            if (!$enr->course_id) {
                $updates['course_id'] = $courseId;
            }
            if (!$enr->batch_id && $defaultBatchId) {
                $updates['batch_id'] = $defaultBatchId;
            }
            if (!empty($updates)) {
                $enr->update($updates);
            }

            if (!$enr->user_id || !$enr->user) {
                self::ensureUserForEnrollment($enr);
            }
        }
    }

    /**
     * Ensure an Enrollment is linked to a valid student User
     */
    public static function ensureUserForEnrollment(Enrollment $enrollment): User
    {
        $rawPhone = trim((string) $enrollment->student_phone);
        $cleanPhone = preg_replace('/[^0-9]/', '', $rawPhone);
        $email = trim((string) $enrollment->student_email);

        $user = User::where(function ($q) use ($rawPhone, $cleanPhone, $email) {
            if (!empty($rawPhone)) {
                $q->where('phone', $rawPhone);
                if (!empty($cleanPhone)) {
                    $q->orWhere('phone', $cleanPhone)
                      ->orWhere('username', $cleanPhone);
                }
            }
            if (!empty($email)) {
                $q->orWhere('email', $email);
            }
        })->first();

        if (!$user) {
            $username = strlen($cleanPhone) >= 11 ? substr($cleanPhone, -11) : ($rawPhone ?: 'stu_' . $enrollment->id);
            if (User::where('username', $username)->exists()) {
                $username = $username . '_' . $enrollment->id;
            }
            $userEmail = !empty($email) ? $email : ($username . '@student.amarbangla.edu');
            if (User::where('email', $userEmail)->exists()) {
                $userEmail = 'stu_' . $enrollment->id . '_' . $userEmail;
            }
            $defaultPwd = 'AB2026#' . rand(100, 999);

            $user = User::create([
                'name' => $enrollment->student_name,
                'email' => $userEmail,
                'phone' => $rawPhone,
                'username' => $username,
                'role' => 'student',
                'password' => Hash::make($defaultPwd),
                'plain_password' => $defaultPwd,
            ]);
        } else {
            $userUpdates = [];
            if (!empty($enrollment->student_name) && $user->name !== $enrollment->student_name) {
                $userUpdates['name'] = $enrollment->student_name;
            }
            if (empty($user->phone) && !empty($rawPhone)) {
                $userUpdates['phone'] = $rawPhone;
            }
            if (!empty($email) && $user->email !== $email && !User::where('email', $email)->where('id', '!=', $user->id)->exists()) {
                $userUpdates['email'] = $email;
            }
            if (!empty($userUpdates)) {
                $user->update($userUpdates);
            }
        }

        if ($enrollment->user_id !== $user->id) {
            $enrollment->update(['user_id' => $user->id]);
        }

        return $user;
    }

    /**
     * Admin Attendance Sessions Overview
     */
    public function attendanceIndex(Request $request)
    {
        $primaryCourse = Course::find($request->input('course_id')) ?? Course::first();
        self::reconcileEnrolledStudents($primaryCourse);

        $batches = $primaryCourse
            ? Batch::where('course_id', $primaryCourse->id)
                ->orderBy('batch_number', 'asc')
                ->orderBy('id', 'asc')
                ->get()
            : collect();
        $selectedBatchId = $request->filled('batch_id') ? (int) $request->input('batch_id') : null;

        $totalActiveStudents = Enrollment::where('course_id', $primaryCourse?->id)
            ->when($selectedBatchId, fn ($q) => $q->where('batch_id', $selectedBatchId))
            ->whereIn('status', ['active', 'completed'])
            ->count();

        $batchStudentCounts = Enrollment::where('course_id', $primaryCourse?->id)
            ->whereIn('status', ['active', 'completed'])
            ->whereNotNull('batch_id')
            ->selectRaw('batch_id, COUNT(*) as total')
            ->groupBy('batch_id')
            ->pluck('total', 'batch_id');

        $sessions = AttendanceSession::with('batch')
            ->where('course_id', $primaryCourse?->id)
            ->when($selectedBatchId, fn ($q) => $q->where('batch_id', $selectedBatchId))
            ->withCount(['attendances as present_count' => function ($q) {
                $q->whereIn('status', ['present', 'late']);
            }])
            ->orderBy('session_date', 'desc')
            ->orderBy('id', 'desc')
            ->paginate(15)
            ->withQueryString();

        $totalSessions = AttendanceSession::where('course_id', $primaryCourse?->id)
            ->when($selectedBatchId, fn ($q) => $q->where('batch_id', $selectedBatchId))
            ->where('status', '!=', 'cancelled')
            ->count();

        // Calculate average attendance across sessions
        $allSessions = AttendanceSession::where('course_id', $primaryCourse?->id)
            ->when($selectedBatchId, fn ($q) => $q->where('batch_id', $selectedBatchId))
            ->where('status', '!=', 'cancelled')
            ->withCount(['attendances as present_count' => fn($q) => $q->whereIn('status', ['present', 'late'])])
            ->get();

        $avgPercentage = 0;
        if ($allSessions->count() > 0 && $totalActiveStudents > 0) {
            $totalPossible = $allSessions->sum(function ($s) use ($batchStudentCounts, $totalActiveStudents) {
                return $s->batch_id && isset($batchStudentCounts[$s->batch_id])
                    ? (int) $batchStudentCounts[$s->batch_id]
                    : $totalActiveStudents;
            });
            $totalPresentSum = $allSessions->sum('present_count');
            $avgPercentage = $totalPossible > 0 ? (int) round(($totalPresentSum / $totalPossible) * 100) : 0;
        }

        // Today's attendance session if any
        $todaySession = AttendanceSession::where('course_id', $primaryCourse?->id)
            ->when($selectedBatchId, fn ($q) => $q->where('batch_id', $selectedBatchId))
            ->whereDate('session_date', now()->toDateString())
            ->withCount(['attendances as present_count' => fn($q) => $q->whereIn('status', ['present', 'late'])])
            ->first();

        $courses = Course::all();

        return view('admin.attendance.index', compact(
            'primaryCourse',
            'courses',
            'batches',
            'selectedBatchId',
            'sessions',
            'totalActiveStudents',
            'batchStudentCounts',
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
        $primaryCourse = Course::find($request->input('course_id')) ?? Course::first();
        $validated = $request->validate([
            'batch_id' => ['nullable', 'exists:batches,id'],
            'title' => ['required', 'string', 'max:255'],
            'session_date' => ['required', 'date'],
            'notes' => ['nullable', 'string', 'max:500'],
        ]);

        $session = AttendanceSession::create([
            'course_id' => $primaryCourse->id,
            'batch_id' => !empty($validated['batch_id']) ? (int) $validated['batch_id'] : null,
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
        $session->load('batch');
        $course = $session->course ?? Course::first();
        self::reconcileEnrolledStudents($course);

        // Get all active enrolled students for this session's batch (or all batches if batch_id is null)
        $enrollments = Enrollment::where('course_id', $course->id)
            ->when($session->batch_id, fn ($q) => $q->where('batch_id', $session->batch_id))
            ->whereIn('status', ['active', 'completed'])
            ->with(['user', 'batch'])
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
        $userId = $request->input('user_id');
        $enrollmentId = $request->input('enrollment_id');

        $enrollment = null;
        if ($enrollmentId) {
            $enrollment = Enrollment::find($enrollmentId);
        }

        if (!$userId && $enrollment) {
            $user = $enrollment->user ?? self::ensureUserForEnrollment($enrollment);
            $userId = $user->id;
        }

        if (!$enrollment && $userId) {
            $enrollment = Enrollment::where('user_id', $userId)
                ->where('course_id', $session->course_id)
                ->when($session->batch_id, fn ($q) => $q->where('batch_id', $session->batch_id))
                ->first();
        }

        if (!$userId) {
            return back()->with('error', 'শিক্ষার্থীর লগইন তথ্য পাওয়া যায়নি।');
        }

        $user = User::find($userId);
        if (!$user && $enrollment) {
            $user = self::ensureUserForEnrollment($enrollment);
            $userId = $user->id;
        }

        $attendance = Attendance::where('attendance_session_id', $session->id)
            ->where('user_id', $userId)
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
        $course = $session->course ?? Course::first();
        self::reconcileEnrolledStudents($course);

        $enrollments = Enrollment::where('course_id', $session->course_id)
            ->when($session->batch_id, fn ($q) => $q->where('batch_id', $session->batch_id))
            ->whereIn('status', ['active', 'completed'])
            ->get();

        foreach ($enrollments as $enr) {
            $userId = $enr->user_id;
            if (!$userId || !$enr->user) {
                $user = self::ensureUserForEnrollment($enr);
                $userId = $user->id;
            }

            Attendance::updateOrCreate(
                [
                    'attendance_session_id' => $session->id,
                    'user_id' => $userId,
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
        $primaryCourse = Course::find($request->input('course_id')) ?? Course::first();
        self::reconcileEnrolledStudents($primaryCourse);
        $search = trim((string) $request->input('search', ''));

        $batches = $primaryCourse
            ? Batch::where('course_id', $primaryCourse->id)
                ->orderBy('batch_number', 'asc')
                ->orderBy('id', 'asc')
                ->get()
            : collect();
        $selectedBatchId = $request->filled('batch_id') ? (int) $request->input('batch_id') : null;

        $allCourseSessions = AttendanceSession::where('course_id', $primaryCourse?->id)
            ->where('status', '!=', 'cancelled')
            ->get(['id', 'batch_id']);

        $totalSessions = $selectedBatchId
            ? $allCourseSessions->filter(fn ($s) => $s->batch_id === $selectedBatchId || is_null($s->batch_id))->count()
            : $allCourseSessions->count();

        $studentsQuery = Enrollment::where('course_id', $primaryCourse?->id)
            ->when($selectedBatchId, fn ($q) => $q->where('batch_id', $selectedBatchId))
            ->whereIn('status', ['active', 'completed'])
            ->with(['batch', 'user.attendances' => function ($q) use ($primaryCourse) {
                $q->where('course_id', $primaryCourse?->id);
            }]);

        if (!empty($search)) {
            $studentsQuery->where(function ($q) use ($search) {
                $q->where('student_name', 'like', "%{$search}%")
                  ->orWhere('student_phone', 'like', "%{$search}%")
                  ->orWhere('student_email', 'like', "%{$search}%");
            });
        }

        $students = $studentsQuery->orderBy('student_name', 'asc')->paginate(30)->withQueryString();
        $courses = Course::all();

        return view('admin.attendance.students', compact(
            'primaryCourse',
            'courses',
            'batches',
            'selectedBatchId',
            'students',
            'totalSessions',
            'allCourseSessions',
            'search'
        ));
    }
}
