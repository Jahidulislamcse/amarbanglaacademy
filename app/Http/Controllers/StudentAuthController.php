<?php

namespace App\Http\Controllers;

use App\Models\User;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Hash;

class StudentAuthController extends Controller
{
    /**
     * Show Student Login Form
     */
    public function showLoginForm()
    {
        if (Auth::check()) {
            if (Auth::user()->isAdmin()) {
                return redirect()->route('admin.students');
            }
            return redirect()->route('student.dashboard');
        }

        return view('student.login');
    }

    /**
     * Handle Student / General User Login
     */
    public function login(Request $request)
    {
        $validated = $request->validate([
            'login_id' => ['required', 'string'],
            'password' => ['required', 'string'],
        ], [
            'login_id.required' => 'ইউজার আইডি, মোবাইল নম্বর বা ইমেইল লিখুন।',
            'password.required' => 'পাসওয়ার্ড প্রদান করুন।',
        ]);

        $loginId = trim($validated['login_id']);
        $password = $validated['password'];
        $remember = $request->boolean('remember');

        // Look up by username, phone, or email
        $user = User::where(function ($q) use ($loginId) {
            $q->where('email', $loginId)
              ->orWhere('phone', $loginId)
              ->orWhere('username', $loginId);
        })->first();

        // Also handle phone with/without country code 880
        if (!$user && (str_starts_with($loginId, '01') || str_starts_with($loginId, '8801'))) {
            $cleanPhone = preg_replace('/[^0-9]/', '', $loginId);
            $localPhone = str_starts_with($cleanPhone, '880') ? substr($cleanPhone, 2) : $cleanPhone;
            $intlPhone = '880' . ltrim($localPhone, '0');

            $user = User::where(function ($q) use ($localPhone, $intlPhone) {
                $q->where('phone', $localPhone)
                  ->orWhere('phone', $intlPhone)
                  ->orWhere('username', $localPhone)
                  ->orWhere('username', $intlPhone);
            })->first();
        }

        if ($user && Hash::check($password, $user->password)) {
            Auth::login($user, $remember);
            $request->session()->regenerate();

            if ($user->isAdmin()) {
                return redirect()->intended(route('admin.students'))
                    ->with('success', 'স্বাগতম এডমিন! আপনি এডমিন প্যানেলে প্রবেশ করেছেন।');
            }

            return redirect()->intended(route('student.dashboard'))
                ->with('success', 'স্বাগতম ' . $user->name . '! আপনি সফলভাবে স্টুডেন্ট পোর্টালে প্রবেশ করেছেন।');
        }

        return back()
            ->withInput($request->only('login_id', 'remember'))
            ->withErrors([
                'login_id' => 'প্রদত্ত ইউজার আইডি বা পাসওয়ার্ড সঠিক নয়। অনুগ্রহ করে পুনরায় যাচাই করুন।',
            ]);
    }

    /**
     * Handle Student Logout
     */
    public function logout(Request $request)
    {
        Auth::logout();
        $request->session()->invalidate();
        $request->session()->regenerateToken();

        return redirect()->route('student.login')
            ->with('success', 'আপনি সফলভাবে লগআউট হয়েছেন।');
    }
}
