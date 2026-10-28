<?php

use App\Http\Controllers\AdminController;
use App\Http\Controllers\LmsController;
use App\Http\Controllers\EpsPaymentController;
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

// Live Classrooms (Zoom & Google Meet integration)
Route::get('/live-classes', [LmsController::class, 'liveClasses'])->name('live.classes');

// Exams & Certification System
Route::get('/exams', [LmsController::class, 'exams'])->name('exams.index');

// Admin Panel Authentication & Student Management
Route::prefix('admin')->name('admin.')->group(function () {
    Route::middleware('guest')->group(function () {
        Route::get('/login', [AdminController::class, 'showLoginForm'])->name('login');
        Route::post('/login', [AdminController::class, 'login'])->name('login.post');
    });

    Route::middleware('auth')->group(function () {
        Route::get('/', fn () => redirect()->route('admin.students'))->name('dashboard');
        Route::get('/students', [AdminController::class, 'students'])->name('students');
        Route::get('/students/export', [AdminController::class, 'exportCsv'])->name('students.export');
        Route::patch('/payments/{payment}/status', [AdminController::class, 'updateStatus'])->name('payments.update-status');
        Route::post('/logout', [AdminController::class, 'logout'])->name('logout');
        Route::get('/logout', [AdminController::class, 'logout']); // allow GET logout
    });
});

// Default login route fallback alias
Route::get('/login', fn () => redirect()->route('admin.login'))->name('login');

