<?php

namespace App\Http\Controllers;

use App\Models\Course;
use App\Models\Enrollment;
use App\Models\Payment;
use App\Models\User;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
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
     * Purchased Students List & Dashboard
     */
    public function students(Request $request)
    {
        // 1. KPI Stats
        $stats = [
            'total_students' => Enrollment::count(),
            'total_revenue' => Payment::where('status', 'completed')->sum('amount'),
            'completed_count' => Payment::where('status', 'completed')->count(),
            'pending_count' => Payment::where('status', 'pending')->count(),
            'eps_count' => Payment::where('payment_method', 'eps')->count(),
        ];

        // 2. Query builder with relations
        $query = Payment::with(['enrollment', 'course'])->latest();

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

        // Status filter
        if ($status = $request->input('status')) {
            if (in_array($status, ['completed', 'pending', 'failed', 'cancelled'])) {
                $query->where('status', $status);
            }
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

        return view('admin.students', compact('payments', 'stats', 'courses'));
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

        return back()->with('success', 'পেমেন্ট ও ভর্তির স্ট্যাটাস সফলভাবে আপডেট করা হয়েছে।');
    }

    /**
     * Export Enrolled Students to CSV
     */
    public function exportCsv(Request $request): StreamedResponse
    {
        $query = Payment::with(['enrollment', 'course'])->latest();

        if ($search = trim((string) $request->input('search'))) {
            $query->where(function ($q) use ($search) {
                $q->where('student_name', 'like', "%{$search}%")
                  ->orWhere('student_phone', 'like', "%{$search}%")
                  ->orWhere('student_email', 'like', "%{$search}%")
                  ->orWhere('transaction_id', 'like', "%{$search}%")
                  ->orWhere('sender_phone', 'like', "%{$search}%");
            });
        }

        if ($status = $request->input('status')) {
            if (in_array($status, ['completed', 'pending', 'failed', 'cancelled'])) {
                $query->where('status', $status);
            }
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
}
