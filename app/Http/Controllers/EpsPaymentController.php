<?php

namespace App\Http\Controllers;

use App\Models\Course;
use App\Models\Enrollment;
use App\Models\Payment;
use App\Services\EpsGateway;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Log;

class EpsPaymentController extends Controller
{
    /**
     * Initiate EPS Online Payment for a Course
     */
    public function pay(Request $request, string $slug, EpsGateway $eps)
    {
        $course = Course::where('slug', $slug)->firstOrFail();

        $validated = $request->validate([
            'student_name' => 'required|string|max:100',
            'student_email' => 'required|email|max:100',
            'student_phone' => 'required|string|max:20',
        ]);

        $amount = (float) $course->effective_price;

        // 0. If student already has an active enrollment in this course, redirect to their existing receipt
        $activeEnrollment = Enrollment::where('course_id', $course->id)
            ->where('status', 'active')
            ->where(function ($q) use ($validated) {
                $q->where('student_email', $validated['student_email'])
                  ->orWhere('student_phone', $validated['student_phone']);
            })
            ->latest()
            ->first();

        if ($activeEnrollment) {
            $completedPayment = Payment::where('enrollment_id', $activeEnrollment->id)
                ->where('status', 'completed')
                ->latest()
                ->first();

            if ($completedPayment) {
                return redirect()->route('payment.success', $completedPayment->id)
                    ->with('info', 'আপনি ইতিমধ্যে এই কোর্সে সফলভাবে ভর্তি হয়েছেন!');
            }
        }

        $transactionId = 'CRS' . now()->format('YmdHis') . random_int(1000, 9999);

        try {
            DB::beginTransaction();

            // 1. Reuse existing pending enrollment if available, avoiding duplicate rows
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
                ]);
            } else {
                $enrollment = Enrollment::create([
                    'course_id' => $course->id,
                    'student_name' => $validated['student_name'],
                    'student_email' => $validated['student_email'],
                    'student_phone' => $validated['student_phone'],
                    'status' => 'pending',
                    'enrolled_at' => now(),
                    'expires_at' => now()->addMonths(12),
                ]);
            }

            // Remove any extra duplicate pending enrollments for this student
            Enrollment::where('course_id', $course->id)
                ->where('id', '!=', $enrollment->id)
                ->where('status', 'pending')
                ->where(function ($q) use ($validated) {
                    $q->where('student_email', $validated['student_email'])
                      ->orWhere('student_phone', $validated['student_phone']);
                })
                ->delete();

            // Cancel older pending payments for this enrollment so only 1 active pending exists
            Payment::where('enrollment_id', $enrollment->id)
                ->where('status', 'pending')
                ->update([
                    'status' => 'cancelled',
                    'notes' => 'Superseded by newer payment initiation.',
                ]);

            // 2. Create a pending payment record
            $payment = Payment::create([
                'enrollment_id' => $enrollment->id,
                'course_id' => $course->id,
                'student_name' => $validated['student_name'],
                'student_email' => $validated['student_email'],
                'student_phone' => $validated['student_phone'],
                'amount' => $amount,
                'payment_method' => 'eps',
                'transaction_id' => $transactionId,
                'sender_phone' => $validated['student_phone'],
                'status' => 'pending',
                'notes' => 'EPS payment initiated. Awaiting gateway completion.',
            ]);

            DB::commit();

            // Store transaction data in session for backup
            session()->put('eps_pending_' . $transactionId, [
                'course_id' => $course->id,
                'course_slug' => $course->slug,
                'enrollment_id' => $enrollment->id,
                'payment_id' => $payment->id,
                'student_name' => $validated['student_name'],
                'student_email' => $validated['student_email'],
                'student_phone' => $validated['student_phone'],
                'amount' => $amount,
            ]);

            // 3. Initialize EPS transaction
            $response = $eps->initialize([
                'order_id' => $transactionId,
                'transaction_id' => $transactionId,
                'amount' => $amount,
                'customer_name' => $validated['student_name'],
                'customer_email' => $validated['student_email'],
                'customer_phone' => $validated['student_phone'],
                'product_name' => $course->title,
            ]);

            $redirectUrl = data_get($response, 'RedirectURL');

            if (!$redirectUrl) {
                return redirect()->route('courses.checkout', $course->slug)
                    ->with('error', 'EPS পেমেন্ট গেটওয়েতে সংযোগ করতে সমস্যা হচ্ছে (Invalid Redirect URL)। অনুগ্রহ করে আবার চেষ্টা করুন।');
            }

            return redirect()->away(trim($redirectUrl));
        } catch (\Throwable $e) {
            DB::rollBack();
            Log::error('EPS Pay Exception: ' . $e->getMessage(), [
                'slug' => $slug,
                'transaction_id' => $transactionId ?? null,
            ]);

            return redirect()->route('courses.checkout', $course->slug)
                ->with('error', 'পেমেন্ট গেটওয়েতে সমস্যা হচ্ছে (' . $e->getMessage() . ')। অনুগ্রহ করে কিছুক্ষণ পর আবার চেষ্টা করুন।');
        }
    }

    /**
     * EPS Payment Success Callback
     */
    public function success(Request $request, EpsGateway $eps)
    {
        $data = array_merge(
            $request->all(),
            json_decode(file_get_contents('php://input'), true) ?? []
        );

        $transactionId = $data['MerchantTransactionId']
            ?? $data['merchantTransactionId']
            ?? $data['TransactionId']
            ?? $data['transactionId']
            ?? $request->query('MerchantTransactionId')
            ?? $request->query('merchantTransactionId')
            ?? $request->query('TransactionId')
            ?? $request->query('transactionId');

        if (!$transactionId) {
            return redirect()->route('home')->with('error', 'পেমেন্ট তথ্য পাওয়া যায়নি (Transaction ID Missing)।');
        }

        $payment = Payment::where('transaction_id', $transactionId)->first();
        $sessionData = session()->get('eps_pending_' . $transactionId);

        try {
            // Verify with EPS API
            $verify = $eps->verify($transactionId);
            $status = strtolower($verify['Status'] ?? '');

            if ($status !== 'success') {
                if ($payment) {
                    $payment->update([
                        'status' => 'failed',
                        'notes' => 'EPS verification failed with status: ' . ($verify['Status'] ?? 'unknown'),
                    ]);
                }
                session()->forget('eps_pending_' . $transactionId);

                $slug = $payment?->course?->slug ?? $sessionData['course_slug'] ?? null;
                $redirect = $slug ? route('courses.checkout', $slug) : route('home');
                return redirect($redirect)->with('error', 'পেমেন্ট ভেরিফিকেশন সফল হয়নি। স্ট্যাটাস: ' . ($verify['Status'] ?? 'Failed'));
            }

            DB::beginTransaction();

            if ($payment) {
                // Update payment to completed
                $payment->update([
                    'status' => 'completed',
                    'notes' => 'EPS Payment Verified Successfully. Customer Trx ID: ' . ($verify['EPSCustomerTransactionId'] ?? 'N/A'),
                ]);

                // Activate enrollment
                if ($payment->enrollment) {
                    $payment->enrollment->update([
                        'status' => 'active',
                        'enrolled_at' => now(),
                    ]);
                }

                // Auto-cancel any other pending payment attempts for this student in this course
                Payment::where('course_id', $payment->course_id)
                    ->where('id', '!=', $payment->id)
                    ->where('status', 'pending')
                    ->where(function ($q) use ($payment) {
                        $q->where('student_email', $payment->student_email)
                          ->orWhere('student_phone', $payment->student_phone);
                    })
                    ->update([
                        'status' => 'cancelled',
                        'notes' => 'Auto-cancelled: Student completed payment #' . $payment->id,
                    ]);

                // Delete any duplicate pending enrollments for this student in this course
                Enrollment::where('course_id', $payment->course_id)
                    ->where('id', '!=', $payment->enrollment_id)
                    ->where('status', 'pending')
                    ->where(function ($q) use ($payment) {
                        $q->where('student_email', $payment->student_email)
                          ->orWhere('student_phone', $payment->student_phone);
                    })
                    ->delete();
            } elseif ($sessionData) {
                // Recover from session if payment record was missing
                $enrollment = Enrollment::find($sessionData['enrollment_id']) ?? Enrollment::create([
                    'course_id' => $sessionData['course_id'],
                    'student_name' => $sessionData['student_name'],
                    'student_email' => $sessionData['student_email'],
                    'student_phone' => $sessionData['student_phone'],
                    'status' => 'active',
                    'enrolled_at' => now(),
                    'expires_at' => now()->addMonths(12),
                ]);

                $payment = Payment::create([
                    'enrollment_id' => $enrollment->id,
                    'course_id' => $sessionData['course_id'],
                    'student_name' => $sessionData['student_name'],
                    'student_email' => $sessionData['student_email'],
                    'student_phone' => $sessionData['student_phone'],
                    'amount' => $sessionData['amount'],
                    'payment_method' => 'eps',
                    'transaction_id' => $transactionId,
                    'sender_phone' => $sessionData['student_phone'],
                    'status' => 'completed',
                    'notes' => 'EPS Payment Verified from Session. Trx ID: ' . ($verify['EPSCustomerTransactionId'] ?? 'N/A'),
                ]);
            }

            DB::commit();
            session()->forget('eps_pending_' . $transactionId);

            if ($payment) {
                return redirect()->route('payment.success', $payment->id)
                    ->with('success', 'অভিনন্দন! আপনার EPS অনলাইন পেমেন্ট সফল হয়েছে এবং কোর্সে ভর্তি নিশ্চিত করা হয়েছে। ক্লাসের সব নোটিশ ও লাইভ ক্লাসের লিঙ্ক পেতে অনুগ্রহ করে WhatsApp গ্রুপে যুক্ত হন।');
            }

            return redirect()->route('home')->with('success', 'পেমেন্ট সফলভাবে যাচাই হয়েছে।');
        } catch (\Throwable $e) {
            DB::rollBack();
            Log::error('EPS Success Callback Exception: ' . $e->getMessage(), [
                'transaction_id' => $transactionId,
            ]);

            return redirect()->route('home')->with('error', 'পেমেন্ট প্রক্রিয়াকরণে সমস্যা: ' . $e->getMessage());
        }
    }

    /**
     * EPS Payment Fail Callback
     */
    public function fail(Request $request)
    {
        $transactionId = $request->query('MerchantTransactionId')
            ?? $request->query('merchantTransactionId')
            ?? $request->input('MerchantTransactionId')
            ?? $request->input('merchantTransactionId');

        if ($transactionId) {
            Payment::where('transaction_id', $transactionId)->update([
                'status' => 'failed',
                'notes' => 'Payment failed at EPS gateway callback.',
            ]);
            session()->forget('eps_pending_' . $transactionId);
        }

        return redirect()->route('home')
            ->with('error', 'EPS পেমেন্ট সম্পন্ন হয়নি বা বাতিল হয়েছে। অনুগ্রহ করে আবার চেষ্টা করুন।');
    }

    /**
     * EPS Payment Cancel Callback
     */
    public function cancel(Request $request)
    {
        $transactionId = $request->query('MerchantTransactionId')
            ?? $request->query('merchantTransactionId')
            ?? $request->input('MerchantTransactionId')
            ?? $request->input('merchantTransactionId');

        if ($transactionId) {
            Payment::where('transaction_id', $transactionId)->update([
                'status' => 'cancelled',
                'notes' => 'Payment cancelled by user at EPS gateway.',
            ]);
            session()->forget('eps_pending_' . $transactionId);
        }

        return redirect()->route('home')
            ->with('info', 'EPS পেমেন্ট বাতিল করা হয়েছে। আপনি যেকোনো সময় আবার ভর্তি প্রক্রিয়া সম্পন্ন করতে পারেন।');
    }
}
