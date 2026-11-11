<?php

use App\Http\Controllers\AdminController;
use App\Http\Controllers\LmsController;
use App\Http\Controllers\EpsPaymentController;
use App\Http\Controllers\StudentAuthController;
use App\Http\Controllers\StudentController;
use Illuminate\Support\Facades\Route;

/*
|--------------------------------------------------------------------------
| Amar Bangla Academy - LMS Routes
|--------------------------------------------------------------------------
*/

// LMS Landing Page (Flagship Course Showcase, Curriculum, Instructors, Fees, Supports)
Route::get('/', [LmsController::class, 'index'])->name('home');

// Course Detail Showcase
Route::get('/courses/{slug}', [LmsController::class, 'courseDetail'])->name('courses.show');

// Online Payment & Checkout
Route::get('/courses/{slug}/checkout', [LmsController::class, 'checkout'])->name('courses.checkout');
Route::post('/courses/{slug}/enroll', [LmsController::class, 'processPayment'])->name('courses.enroll');
Route::get('/enrollment/success/{id}', [LmsController::class, 'paymentSuccess'])->name('payment.success');

// EPS Easy Payment System Gateway Routes (bKash, Nagad, Rocket, Cards)
Route::post('/courses/{slug}/eps-pay', [EpsPaymentController::class, 'pay'])->name('eps.pay');
Route::match(['get', 'post'], '/payment/eps/success', [EpsPaymentController::class, 'success'])->name('eps.success');
Route::match(['get', 'post'], '/payment/eps/fail', [EpsPaymentController::class, 'fail'])->name('eps.fail');
Route::match(['get', 'post'], '/payment/eps/cancel', [EpsPaymentController::class, 'cancel'])->name('eps.cancel');

// Live Classrooms & Exams Showcase
Route::get('/live-classes', [LmsController::class, 'liveClasses'])->name('live.classes');
Route::get('/exams', [LmsController::class, 'exams'])->name('exams.index');

// Student Authentication
Route::get('/login', [StudentAuthController::class, 'showLoginForm'])->name('login');
Route::post('/login', [StudentAuthController::class, 'login'])->name('login.post');
Route::get('/student/login', [StudentAuthController::class, 'showLoginForm'])->name('student.login');
Route::post('/student/logout', [StudentAuthController::class, 'logout'])->name('student.logout');
Route::get('/student/logout', [StudentAuthController::class, 'logout']);
Route::get('/logout', [StudentAuthController::class, 'logout'])->name('logout');

// Student LMS Portal
Route::middleware('auth')->prefix('student')->name('student.')->group(function () {
    Route::get('/dashboard', [StudentController::class, 'dashboard'])->name('dashboard');
    Route::get('/classroom', [StudentController::class, 'classroom'])->name('classroom');
    Route::get('/live-class', [StudentController::class, 'joinLiveClass'])->name('live-class');
    Route::get('/recordings/{meeting}', [StudentController::class, 'watchRecording'])->name('recordings.watch');
});

// Admin Panel Authentication & Student Management
Route::prefix('admin')->name('admin.')->group(function () {
    Route::middleware('guest')->group(function () {
        Route::get('/login', [AdminController::class, 'showLoginForm'])->name('login');
        Route::post('/login', [AdminController::class, 'login'])->name('login.post');
    });

    Route::middleware('auth')->group(function () {
        Route::get('/', fn () => redirect()->route('admin.students'))->name('dashboard');
        Route::get('/students', [AdminController::class, 'students'])->name('students');
        Route::post('/students/store', [AdminController::class, 'storeStudent'])->name('students.store');

        // Batch Management (Dynamic Batches with Shared Course Content)
        Route::get('/batches', [AdminController::class, 'batchesIndex'])->name('batches.index');
        Route::post('/batches', [AdminController::class, 'storeBatch'])->name('batches.store');
        Route::put('/batches/{batch}', [AdminController::class, 'updateBatch'])->name('batches.update');
        Route::post('/batches/{batch}/toggle-enrollment', [AdminController::class, 'toggleBatchEnrollment'])->name('batches.toggle-enrollment');
        Route::delete('/batches/{batch}', [AdminController::class, 'destroyBatch'])->name('batches.destroy');

        Route::get('/live-meetings', [AdminController::class, 'liveMeetings'])->name('live-meetings');
        Route::post('/meetings', [AdminController::class, 'storeMeeting'])->name('meetings.store');
        Route::put('/meetings/{meeting}', [AdminController::class, 'updateMeeting'])->name('meetings.update');
        Route::post('/meetings/{meeting}/recording', [AdminController::class, 'updateRecording'])->name('meetings.recording.update');
        Route::delete('/meetings/{meeting}/recording', [AdminController::class, 'removeRecording'])->name('meetings.recording.destroy');
        Route::post('/meetings/{meeting}/toggle-live', [AdminController::class, 'toggleMeetingLive'])->name('meetings.toggle-live');
        Route::delete('/meetings/{meeting}', [AdminController::class, 'destroyMeeting'])->name('meetings.destroy');
        Route::get('/students/export', [AdminController::class, 'exportCsv'])->name('students.export');
        Route::post('/students/clean-duplicates', [AdminController::class, 'cleanDuplicates'])->name('students.clean-duplicates');
        Route::patch('/payments/{payment}/status', [AdminController::class, 'updateStatus'])->name('payments.update-status');
        Route::delete('/payments/{payment}', [AdminController::class, 'destroyPayment'])->name('payments.destroy');
        Route::post('/payments/{payment}/verify-eps', [AdminController::class, 'verifyEps'])->name('payments.verify-eps');
        Route::post('/courses/{course}/update-meeting', [AdminController::class, 'updateMeetingLink'])->name('courses.update-meeting');
        Route::post('/students/{enrollment}/credentials', [AdminController::class, 'manageStudentAccount'])->name('students.credentials');

        // Live Class Attendance Management
        Route::get('/attendance', [AdminController::class, 'attendanceIndex'])->name('attendance.index');
        Route::post('/attendance/sessions', [AdminController::class, 'storeAttendanceSession'])->name('attendance.sessions.store');
        Route::get('/attendance/sessions/{session}', [AdminController::class, 'showAttendanceSession'])->name('attendance.session');
        Route::post('/attendance/sessions/{session}/toggle', [AdminController::class, 'toggleAttendance'])->name('attendance.toggle');
        Route::post('/attendance/sessions/{session}/mark-all-present', [AdminController::class, 'markAllPresent'])->name('attendance.mark-all-present');
        Route::delete('/attendance/sessions/{session}', [AdminController::class, 'destroyAttendanceSession'])->name('attendance.sessions.destroy');
        Route::get('/attendance/students', [AdminController::class, 'attendanceStudents'])->name('attendance.students');

        Route::post('/logout', [AdminController::class, 'logout'])->name('logout');
        Route::get('/logout', [AdminController::class, 'logout']); // allow GET logout
    });
});

