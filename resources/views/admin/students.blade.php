@extends('layouts.admin')

@section('title', 'ভর্তি হওয়া শিক্ষার্থী তালিকা - আমার বাংলা একাডেমি এডমিন')

@section('content')
<div class="space-y-6">

    <!-- Page Header & Action Bar -->
    <div class="flex flex-col sm:flex-row sm:items-center justify-between gap-4">
        <div>
            <h1 class="text-2xl font-bold text-slate-900 flex items-center gap-2.5">
                <i class="fa-solid fa-graduation-cap text-emerald-600"></i>
                <span>কোর্সে ভর্তি হওয়া শিক্ষার্থী তালিকা</span>
            </h1>
            <p class="text-xs sm:text-sm text-slate-500 mt-1">
                “সাংবাদিকতা, আইন ও উপস্থাপনা” কোর্সে সফল পেমেন্ট সম্পন্নকারী শিক্ষার্থীদের তালিকা ও যোগাযোগের তথ্য
            </p>
        </div>

        <div class="flex items-center gap-2">
            <!-- Clean Duplicates Button -->
            <form method="POST" action="{{ route('admin.students.clean-duplicates') }}"
                  onsubmit="return confirm('অতিরিক্ত ডুপ্লিকেট পেন্ডিং রেজিস্ট্রেশন মুছে পরিষ্কার করতে চান?')">
                @csrf
                <button type="submit"
                        class="inline-flex items-center gap-1.5 bg-amber-500 hover:bg-amber-600 text-white font-semibold text-xs sm:text-sm px-3.5 py-2.5 rounded-xl shadow-sm transition"
                        title="অতিরিক্ত ডুপ্লিকেট পেন্ডিং ডেটা পরিষ্কার করুন">
                    <i class="fa-solid fa-broom"></i>
                    <span>ডুপ্লিকেট ক্লিন</span>
                </button>
            </form>

            <!-- CSV Export Button -->
            <a href="{{ route('admin.students.export', request()->query()) }}"
               class="inline-flex items-center gap-2 bg-emerald-600 hover:bg-emerald-700 text-white font-semibold text-xs sm:text-sm px-4 py-2.5 rounded-xl shadow-sm transition">
                <i class="fa-solid fa-file-csv text-base"></i>
                <span>CSV ডাউনলোড</span>
            </a>
            <!-- Refresh Page -->
            <a href="{{ route('admin.students') }}"
               title="রিফ্রেশ"
               class="inline-flex items-center justify-center w-10 h-10 bg-white hover:bg-slate-50 text-slate-600 border border-slate-200 rounded-xl shadow-sm transition">
                <i class="fa-solid fa-rotate-right"></i>
            </a>
        </div>
    </div>

    <!-- KPI Metric Cards (Purchased Students Only) -->
    <div class="grid grid-cols-2 lg:grid-cols-4 gap-3 sm:gap-4">
        <!-- Total Enrolled Students -->
        <div class="bg-white p-4 rounded-2xl border border-slate-200 shadow-sm flex items-center gap-3">
            <div class="w-12 h-12 rounded-xl bg-emerald-50 text-emerald-600 flex items-center justify-center text-xl shrink-0">
                <i class="fa-solid fa-user-graduate"></i>
            </div>
            <div>
                <div class="text-[11px] font-semibold text-slate-500 uppercase tracking-wider">মোট ভর্তি শিক্ষার্থী</div>
                <div class="text-xl sm:text-2xl font-bold text-slate-900 mt-0.5">{{ number_format($stats['total_students']) }} <span class="text-xs font-normal text-slate-500">জন</span></div>
            </div>
        </div>

        <!-- Total Revenue -->
        <div class="bg-white p-4 rounded-2xl border border-slate-200 shadow-sm flex items-center gap-3">
            <div class="w-12 h-12 rounded-xl bg-emerald-100 text-emerald-800 flex items-center justify-center text-xl shrink-0 font-bold">
                ৳
            </div>
            <div>
                <div class="text-[11px] font-semibold text-slate-500 uppercase tracking-wider">মোট সংগৃহীত ফি</div>
                <div class="text-xl sm:text-2xl font-bold text-emerald-700 mt-0.5">৳{{ number_format($stats['total_revenue']) }}</div>
            </div>
        </div>

        <!-- EPS Online Gateway -->
        <div class="bg-white p-4 rounded-2xl border border-slate-200 shadow-sm flex items-center gap-3">
            <div class="w-12 h-12 rounded-xl bg-blue-50 text-blue-600 flex items-center justify-center text-xl shrink-0">
                <i class="fa-solid fa-bolt"></i>
            </div>
            <div>
                <div class="text-[11px] font-semibold text-slate-500 uppercase tracking-wider">EPS অনলাইন পেমেন্ট</div>
                <div class="text-xl sm:text-2xl font-bold text-blue-700 mt-0.5">{{ number_format($stats['eps_count']) }} <span class="text-xs font-normal text-slate-500">জন</span></div>
            </div>
        </div>

        <!-- Mobile Banking & Others -->
        <div class="bg-white p-4 rounded-2xl border border-slate-200 shadow-sm flex items-center gap-3">
            <div class="w-12 h-12 rounded-xl bg-pink-50 text-pink-600 flex items-center justify-center text-xl shrink-0">
                <i class="fa-solid fa-mobile-screen"></i>
            </div>
            <div>
                <div class="text-[11px] font-semibold text-slate-500 uppercase tracking-wider">বিকাশ/নগদ/অন্যান্য</div>
                <div class="text-xl sm:text-2xl font-bold text-slate-800 mt-0.5">{{ number_format($stats['mobile_count']) }} <span class="text-xs font-normal text-slate-500">জন</span></div>
            </div>
        </div>
    </div>

        <!-- PENDING PAYMENTS ALERT - Students who paid but status not confirmed -->
    @if($pendingPayments->isNotEmpty())
    <div class="bg-amber-50 border border-amber-300 rounded-2xl p-5 shadow-sm">
        <div class="flex items-center gap-3 mb-4">
            <div class="w-10 h-10 rounded-xl bg-amber-100 text-amber-600 flex items-center justify-center text-lg shrink-0">
                <i class="fa-solid fa-triangle-exclamation"></i>
            </div>
            <div>
                <h2 class="text-base font-bold text-amber-900">
                    &#9888; {{ $pendingPayments->count() }}টি পেমেন্ট অনুমোদনের অপেক্ষায়
                </h2>
                <p class="text-xs text-amber-700 mt-0.5">
                    এই শিক্ষার্থীরা পেমেন্ট শুরু করেছেন কিন্তু স্ট্যাটাস <strong>pending</strong> রয়েছে।
                    পেমেন্ট যাচাই করে নিশ্চিত করুন অথবা বাতিল করুন।
                </p>
            </div>
        </div>
        <div class="space-y-3">
            @foreach($pendingPayments as $pp)
            <div class="bg-white rounded-xl border border-amber-200 p-4 flex flex-col sm:flex-row sm:items-center justify-between gap-3">
                <div class="flex items-start gap-3">
                    <div class="w-9 h-9 rounded-full bg-amber-100 text-amber-700 flex items-center justify-center text-sm font-bold shrink-0">
                        {{ strtoupper(substr($pp->student_name, 0, 1)) }}
                    </div>
                    <div class="min-w-0">
                        <p class="font-bold text-slate-900 text-sm">{{ $pp->student_name }}</p>
                        <p class="text-xs text-slate-500">{{ $pp->student_email }} &bull; {{ $pp->student_phone }}</p>
                        <div class="flex flex-wrap gap-2 mt-1">
                            <span class="text-[11px] bg-slate-100 text-slate-600 rounded px-1.5 py-0.5 font-mono">TrxID: {{ $pp->transaction_id }}</span>
                            <span class="text-[11px] bg-blue-50 text-blue-700 rounded px-1.5 py-0.5 font-semibold uppercase">{{ $pp->payment_method }}</span>
                            <span class="text-[11px] bg-emerald-50 text-emerald-700 rounded px-1.5 py-0.5 font-bold">৳{{ number_format($pp->amount) }}</span>
                            <span class="text-[11px] text-slate-400">{{ $pp->created_at ? $pp->created_at->format('d M Y, h:i A') : '' }}</span>
                        </div>
                    </div>
                </div>
                <div class="flex items-center gap-2 shrink-0 flex-wrap sm:flex-nowrap">
                    @if(strtolower($pp->payment_method) === 'eps')
                    <!-- Live EPS Verification Check -->
                    <form method="POST" action="{{ route('admin.payments.verify-eps', $pp->id) }}">
                        @csrf
                        <button type="submit"
                                title="EPS সার্ভার থেকে সরাসরি ভেরিফাই করুন টাকা জমা হয়েছে কিনা"
                                class="inline-flex items-center gap-1.5 px-3 py-2 rounded-xl bg-blue-600 hover:bg-blue-700 text-white text-xs font-bold shadow-sm transition">
                            <i class="fa-solid fa-arrows-rotate"></i>
                            <span>EPS চেক</span>
                        </button>
                    </form>
                    @endif

                    <!-- Manual Approve -->
                    <form method="POST" action="{{ route('admin.payments.update-status', $pp->id) }}"
                          onsubmit="return confirm('সতর্কতা: আপনি কি নিশ্চিত যে শিক্ষার্থী ম্যানুয়ালি বা EPS-এ টাকা পাঠিয়েছেন? ব্যাংক/বিকাশে টাকা জমা না হলে এটি নিশ্চিত করবেন না।')">
                        @csrf @method('PATCH')
                        <input type="hidden" name="status" value="completed">
                        <input type="hidden" name="notes" value="Admin manually approved pending payment.">
                        <button type="submit" class="inline-flex items-center gap-1.5 px-3.5 py-2 rounded-xl bg-emerald-600 hover:bg-emerald-700 text-white text-xs font-bold shadow-sm transition">
                            <i class="fa-solid fa-circle-check"></i> পেমেন্ট নিশ্চিত করুন
                        </button>
                    </form>

                    <!-- Reject / Cancel -->
                    <form method="POST" action="{{ route('admin.payments.update-status', $pp->id) }}"
                          onsubmit="return confirm('পেমেন্ট বাতিল করবেন?')">
                        @csrf @method('PATCH')
                        <input type="hidden" name="status" value="failed">
                        <input type="hidden" name="notes" value="Admin rejected pending payment.">
                        <button type="submit" class="inline-flex items-center gap-1.5 px-3 py-2 rounded-xl bg-red-100 hover:bg-red-200 text-red-700 text-xs font-bold transition">
                            <i class="fa-solid fa-xmark"></i> বাতিল
                        </button>
                    </form>
                </div>
            </div>
            @endforeach
        </div>
    </div>
    @endif

    <!-- Live Class & Meeting Link Management Card -->
    <div class="bg-gradient-to-r from-slate-900 via-emerald-950 to-slate-900 rounded-2xl border border-emerald-800/40 p-5 shadow-lg text-white">
        <div class="flex flex-col md:flex-row md:items-center justify-between gap-4 pb-4 border-b border-emerald-800/50">
            <div class="flex items-center gap-3">
                <div class="w-11 h-11 rounded-2xl bg-emerald-500/20 text-emerald-400 flex items-center justify-center text-xl shrink-0 border border-emerald-500/30">
                    <i class="fa-solid fa-video"></i>
                </div>
                <div>
                    <div class="flex items-center gap-2">
                        <h2 class="text-base font-bold text-white">লাইভ ক্লাস ও মিটিং লিংক ম্যানেজমেন্ট</h2>
                        @if($primaryCourse?->is_live_now)
                            <span class="inline-flex items-center gap-1.5 px-2.5 py-0.5 rounded-full text-[11px] font-bold bg-red-500 text-white animate-pulse">
                                <span class="w-2 h-2 rounded-full bg-white"></span>
                                লাইভ ক্লাস চলছে
                            </span>
                        @else
                            <span class="inline-flex items-center gap-1 px-2.5 py-0.5 rounded-full text-[11px] font-medium bg-slate-800 text-slate-300 border border-slate-700">
                                লাইভ অফলাইন
                            </span>
                        @endif
                    </div>
                    <p class="text-xs text-emerald-200/80 mt-0.5">
                        এখানে জুম বা গুগল মিট লিংক দিলে সকল শিক্ষার্থী তাদের স্টুডেন্ট পোর্টাল থেকে এক ক্লিকে লাইভ ক্লাসে যুক্ত হতে পারবে
                    </p>
                </div>
            </div>
            @if($primaryCourse?->live_meeting_url)
                <div class="flex items-center gap-2">
                    <a href="{{ $primaryCourse->live_meeting_url }}" target="_blank"
                       class="inline-flex items-center gap-1.5 px-3.5 py-2 rounded-xl bg-emerald-600 hover:bg-emerald-500 text-white text-xs font-bold shadow-md transition">
                        <i class="fa-solid fa-arrow-up-right-from-square"></i>
                        <span>মিটিং টেস্ট করুন</span>
                    </a>
                </div>
            @endif
        </div>

        <form method="POST" action="{{ route('admin.courses.update-meeting', $primaryCourse?->id ?? 1) }}" class="mt-4 grid grid-cols-1 md:grid-cols-12 gap-3.5">
            @csrf
            <!-- Live Meeting URL -->
            <div class="md:col-span-6">
                <label class="block text-xs font-medium text-emerald-200 mb-1">
                    Zoom / Google Meet মিটিং লিঙ্ক <span class="text-emerald-400">*</span>
                </label>
                <div class="relative">
                    <i class="fa-solid fa-link absolute left-3.5 top-3 text-slate-400 text-xs"></i>
                    <input type="url" name="live_meeting_url" value="{{ old('live_meeting_url', $primaryCourse?->live_meeting_url) }}"
                           placeholder="https://us05web.zoom.us/j/... অথবা https://meet.google.com/..."
                           class="w-full pl-9 pr-3 py-2 text-xs bg-slate-800/90 border border-emerald-700/50 rounded-xl text-white placeholder-slate-400 focus:outline-none focus:ring-2 focus:ring-emerald-400">
                </div>
            </div>

            <!-- Topic / Title -->
            <div class="md:col-span-6">
                <label class="block text-xs font-medium text-emerald-200 mb-1">
                    আজকের ক্লাসের বিষয় / টপিক
                </label>
                <div class="relative">
                    <i class="fa-solid fa-heading absolute left-3.5 top-3 text-slate-400 text-xs"></i>
                    <input type="text" name="live_class_topic" value="{{ old('live_class_topic', $primaryCourse?->live_class_topic) }}"
                           placeholder="যেমন: ওরিয়েন্টেশন ক্লাস / মডিউল ১: সাংবাদিকতার পরিচিতি"
                           class="w-full pl-9 pr-3 py-2 text-xs bg-slate-800/90 border border-emerald-700/50 rounded-xl text-white placeholder-slate-400 focus:outline-none focus:ring-2 focus:ring-emerald-400">
                </div>
            </div>

            <!-- Meeting ID -->
            <div class="md:col-span-4">
                <label class="block text-xs font-medium text-emerald-200 mb-1">
                    Meeting ID (ঐচ্ছিক)
                </label>
                <input type="text" name="live_meeting_id" value="{{ old('live_meeting_id', $primaryCourse?->live_meeting_id) }}"
                       placeholder="যেমন: 852 1234 5678"
                       class="w-full px-3 py-2 text-xs bg-slate-800/90 border border-emerald-700/50 rounded-xl text-white placeholder-slate-400 focus:outline-none focus:ring-2 focus:ring-emerald-400">
            </div>

            <!-- Passcode -->
            <div class="md:col-span-4">
                <label class="block text-xs font-medium text-emerald-200 mb-1">
                    Passcode / পাসকোড (ঐচ্ছিক)
                </label>
                <input type="text" name="live_meeting_passcode" value="{{ old('live_meeting_passcode', $primaryCourse?->live_meeting_passcode) }}"
                       placeholder="যেমন: 123456"
                       class="w-full px-3 py-2 text-xs bg-slate-800/90 border border-emerald-700/50 rounded-xl text-white placeholder-slate-400 focus:outline-none focus:ring-2 focus:ring-emerald-400">
            </div>

            <!-- Live Now Toggle & Submit -->
            <div class="md:col-span-4 flex items-center justify-between gap-3 pt-2 md:pt-5">
                <label class="inline-flex items-center gap-2 cursor-pointer select-none">
                    <input type="checkbox" name="is_live_now" value="1" {{ $primaryCourse?->is_live_now ? 'checked' : '' }}
                           class="w-4 h-4 text-emerald-500 rounded bg-slate-800 border-emerald-700 focus:ring-emerald-400">
                    <span class="text-xs font-bold text-white">এখন লাইভ চলছে</span>
                </label>

                <button type="submit"
                        class="inline-flex items-center gap-1.5 px-4 py-2 bg-emerald-500 hover:bg-emerald-400 text-slate-900 font-bold text-xs rounded-xl shadow-md transition">
                    <i class="fa-solid fa-floppy-disk"></i>
                    <span>মিটিং সেভ করুন</span>
                </button>
            </div>
        </form>
    </div>

    <!-- Search & Filter Card -->
    <div class="bg-white rounded-2xl border border-slate-200 shadow-sm p-4 sm:p-5">
        <form method="GET" action="{{ route('admin.students') }}" class="grid grid-cols-1 sm:grid-cols-2 lg:grid-cols-12 gap-3 items-end">
            <!-- Search Input -->
            <div class="lg:col-span-7">
                <label for="search" class="block text-xs font-semibold text-slate-700 mb-1.5">
                    শিক্ষার্থী অনুসন্ধান
                </label>
                <div class="relative">
                    <div class="absolute inset-y-0 left-0 pl-3.5 flex items-center pointer-events-none text-slate-400">
                        <i class="fa-solid fa-magnifying-glass text-xs"></i>
                    </div>
                    <input type="text" id="search" name="search" value="{{ request('search') }}"
                        placeholder="নাম, মোবাইল নম্বর, ইমেইল বা ট্রানজেকশন আইডি..."
                        class="w-full pl-9 pr-4 py-2 text-xs sm:text-sm bg-slate-50 border border-slate-300 rounded-xl focus:ring-2 focus:ring-emerald-500 focus:border-emerald-500 outline-none transition">
                </div>
            </div>

            <!-- Payment Method Filter -->
            <div class="lg:col-span-3">
                <label for="method" class="block text-xs font-semibold text-slate-700 mb-1.5">
                    পেমেন্ট মাধ্যম
                </label>
                <select id="method" name="method"
                    class="w-full px-3 py-2 text-xs sm:text-sm bg-slate-50 border border-slate-300 rounded-xl focus:ring-2 focus:ring-emerald-500 focus:border-emerald-500 outline-none transition">
                    <option value="">সকল মাধ্যম</option>
                    <option value="eps" {{ request('method') === 'eps' ? 'selected' : '' }}>EPS অনলাইন গেটওয়ে</option>
                    <option value="bkash" {{ request('method') === 'bkash' ? 'selected' : '' }}>বিকাশ (bKash)</option>
                    <option value="nagad" {{ request('method') === 'nagad' ? 'selected' : '' }}>নগদ (Nagad)</option>
                    <option value="rocket" {{ request('method') === 'rocket' ? 'selected' : '' }}>রকেট (Rocket)</option>
                    <option value="card" {{ request('method') === 'card' ? 'selected' : '' }}>কার্ড (Card)</option>
                </select>
            </div>

            <!-- Action Buttons -->
            <div class="lg:col-span-2 flex items-center gap-2">
                <button type="submit"
                    class="flex-1 bg-slate-900 hover:bg-slate-800 text-white font-semibold py-2 px-3 rounded-xl text-xs sm:text-sm transition flex items-center justify-center gap-1.5 shadow-sm">
                    <i class="fa-solid fa-filter text-xs"></i>
                    <span>ফিল্টার</span>
                </button>
                @if(request()->hasAny(['search', 'method', 'course_id']))
                    <a href="{{ route('admin.students') }}"
                        title="রিসেট"
                        class="bg-slate-100 hover:bg-slate-200 text-slate-600 py-2 px-3 rounded-xl text-xs sm:text-sm transition flex items-center justify-center border border-slate-200">
                        <i class="fa-solid fa-xmark"></i>
                    </a>
                @endif
            </div>
        </form>
    </div>

    <!-- Students Table Card -->
    <div class="bg-white rounded-2xl border border-slate-200 shadow-sm overflow-hidden">
        <div class="p-4 sm:p-5 border-b border-slate-100 flex flex-col sm:flex-row items-start sm:items-center justify-between gap-2">
            <div>
                <h2 class="font-bold text-slate-800 text-base flex items-center gap-2">
                    <i class="fa-solid fa-circle-check text-green-600"></i>
                    <span>সফল কোর্স ক্রয়ের শিক্ষার্থী তালিকা (মোট: {{ $payments->total() }})</span>
                </h2>
                <p class="text-xs text-slate-500 mt-0.5">পেমেন্ট সম্পন্নকারী সকল সক্রিয় শিক্ষার্থীর সম্পূর্ণ বিবরণ</p>
            </div>
            <div class="text-xs text-slate-500 bg-slate-50 px-3 py-1 rounded-lg border border-slate-200">
                পেজ: {{ $payments->currentPage() }} / {{ $payments->lastPage() > 0 ? $payments->lastPage() : 1 }}
            </div>
        </div>

        @if($payments->count() > 0)
            <!-- Responsive Table Container -->
            <div class="overflow-x-auto">
                <table class="w-full text-left border-collapse">
                    <thead>
                        <tr class="bg-slate-50/80 border-b border-slate-200 text-[11px] font-bold text-slate-600 uppercase tracking-wider">
                            <th class="py-3 px-4">#</th>
                            <th class="py-3 px-4">শিক্ষার্থী বিবরণ</th>
                            <th class="py-3 px-4">যোগাযোগ & WhatsApp</th>
                            <th class="py-3 px-4">কোর্স</th>
                            <th class="py-3 px-4 text-right">ফি (টাকা)</th>
                            <th class="py-3 px-4">পেমেন্ট মাধ্যম ও TrxID</th>
                            <th class="py-3 px-4 text-center">স্ট্যাটাস</th>
                            <th class="py-3 px-4 text-center">অ্যাকশন</th>
                        </tr>
                    </thead>
                    <tbody class="divide-y divide-slate-100 text-xs text-slate-700">
                        @foreach($payments as $index => $payment)
                            @php
                                // Clean phone number for WhatsApp URL
                                $rawPhone = preg_replace('/[^0-9]/', '', $payment->student_phone);
                                if (str_starts_with($rawPhone, '0')) {
                                    $waPhone = '880' . substr($rawPhone, 1);
                                } elseif (str_starts_with($rawPhone, '880')) {
                                    $waPhone = $rawPhone;
                                } else {
                                    $waPhone = '880' . $rawPhone;
                                }
                                $waText = urlencode("আসসালামু আলাইকুম " . $payment->student_name . ", আমার বাংলা একাডেমীর কোর্সে ভর্তির বিষয়ে আপনার সাথে যোগাযোগ করা হচ্ছে।");
                                $waUrl = "https://wa.me/{$waPhone}?text={$waText}";

                                $enrollment = $payment->enrollment;
                                $lmsUser = $enrollment?->user;
                                $hasLms = !is_null($lmsUser);
                                $lmsUserId = $lmsUser?->username ?: ($lmsUser?->phone ?: $payment->student_phone);
                                $lmsPassword = $lmsUser?->plain_password ?? '';
                                $loginPortalUrl = url('/login');
                                $courseTitle = $payment->course?->bangla_title ?? $payment->course?->title ?? 'সাংবাদিকতা, আইন ও উপস্থাপনা';

                                $credMsg = urlencode("আসসালামু আলাইকুম {$payment->student_name},\nআমার বাংলা একাডেমীর \"{$courseTitle}\" কোর্সে আপনার ভর্তি নিশ্চিত হয়েছে।\n\nআপনার স্টুডেন্ট পোর্টাল লগইন তথ্য:\n🌐 ওয়েবসাইট: {$loginPortalUrl}\n👤 ইউজার আইডি: {$lmsUserId}\n🔑 পাসওয়ার্ড: {$lmsPassword}\n\nপোর্টালে লগইন করে সরাসরি লাইভ ক্লাস ও কোর্স ম্যাটেরিয়াল পাবেন।\nহেল্পলাইন: 01332549549");
                                $credentialsWaUrl = "https://wa.me/{$waPhone}?text={$credMsg}";

                                $studentModalData = [
                                    'id' => $payment->id,
                                    'enrollment_id' => $payment->enrollment_id,
                                    'student_name' => $payment->student_name,
                                    'student_phone' => $payment->student_phone,
                                    'student_email' => $payment->student_email,
                                    'course_title' => $courseTitle,
                                    'batch_no' => $payment->course?->batch_no ?? '১ম ব্যাচ',
                                    'amount' => number_format($payment->amount),
                                    'payment_method' => $payment->payment_method,
                                    'transaction_id' => $payment->transaction_id,
                                    'sender_phone' => $payment->sender_phone,
                                    'status' => $payment->status,
                                    'enrollment_status' => $enrollment?->status ?? 'active',
                                    'created_at' => $payment->created_at ? $payment->created_at->format('d M Y, h:i A') : 'N/A',
                                    'notes' => $payment->notes ?? '',
                                    'wa_url' => $waUrl,
                                    'has_lms' => $hasLms,
                                    'lms_user_id' => $lmsUserId,
                                    'lms_password' => $lmsPassword,
                                    'credentials_url' => $enrollment ? route('admin.students.credentials', $enrollment->id) : '',
                                    'credentials_wa_url' => $credentialsWaUrl,
                                    'login_portal_url' => $loginPortalUrl,
                                    'update_url' => route('admin.payments.update-status', $payment->id),
                                    'verify_eps_url' => route('admin.payments.verify-eps', $payment->id),
                                ];
                            @endphp
                            <tr class="hover:bg-slate-50/60 transition">
                                <!-- Index -->
                                <td class="py-3.5 px-4 text-slate-400 font-mono">
                                    {{ $payments->firstItem() + $index }}
                                </td>

                                <!-- Student Info -->
                                <td class="py-3.5 px-4">
                                    <div class="font-bold text-slate-900 text-sm">{{ $payment->student_name }}</div>
                                    <div class="text-slate-500 text-[11px] mt-0.5 flex items-center gap-1.5">
                                        <i class="fa-regular fa-envelope text-slate-400"></i>
                                        <span>{{ $payment->student_email }}</span>
                                    </div>
                                    <div class="text-[10px] text-slate-400 mt-1 flex items-center gap-1">
                                        <i class="fa-regular fa-calendar-check text-slate-400"></i>
                                        <span>{{ $payment->created_at ? $payment->created_at->format('d M Y, h:i A') : 'N/A' }}</span>
                                    </div>
                                </td>

                                <!-- Contact & WhatsApp -->
                                <td class="py-3.5 px-4">
                                    <div class="font-mono text-slate-900 font-semibold">{{ $payment->student_phone }}</div>
                                    <div class="flex items-center gap-1.5 mt-1.5">
                                        <!-- WhatsApp Direct Message -->
                                        <a href="{{ $waUrl }}" target="_blank"
                                           class="inline-flex items-center gap-1 bg-green-500 hover:bg-green-600 text-white text-[11px] font-semibold px-2 py-0.5 rounded-lg shadow-sm transition">
                                            <i class="fa-brands fa-whatsapp text-xs"></i>
                                            <span>WhatsApp</span>
                                        </a>
                                        <!-- Call -->
                                        <a href="tel:{{ $payment->student_phone }}"
                                           class="inline-flex items-center gap-1 bg-slate-100 hover:bg-slate-200 text-slate-700 text-[11px] font-medium px-2 py-0.5 rounded-lg border border-slate-200 transition">
                                            <i class="fa-solid fa-phone text-[10px]"></i>
                                            <span>কল</span>
                                        </a>
                                    </div>
                                </td>

                                <!-- Course -->
                                <td class="py-3.5 px-4 max-w-[200px]">
                                    <div class="font-semibold text-slate-800 truncate" title="{{ $payment->course?->bangla_title }}">
                                        {{ $payment->course?->bangla_title ?? 'সাংবাদিকতা, আইন ও উপস্থাপনা' }}
                                    </div>
                                    <span class="inline-block text-[10px] bg-slate-100 text-slate-600 px-2 py-0.5 rounded border border-slate-200 mt-1">
                                        {{ $payment->course?->batch_no ?? '১ম ব্যাচ' }}
                                    </span>
                                </td>

                                <!-- Amount -->
                                <td class="py-3.5 px-4 text-right">
                                    <span class="font-bold text-sm text-emerald-700 font-mono">
                                        ৳{{ number_format($payment->amount) }}
                                    </span>
                                </td>

                                <!-- Payment Method & Trx ID -->
                                <td class="py-3.5 px-4">
                                    <!-- Method Badge -->
                                    <div class="flex items-center gap-1.5">
                                        @if(strtolower($payment->payment_method) === 'eps')
                                            <span class="inline-flex items-center gap-1 px-2 py-0.5 rounded text-[11px] font-semibold bg-blue-50 text-blue-700 border border-blue-200">
                                                <i class="fa-solid fa-bolt text-[10px]"></i>
                                                <span>EPS অনলাইন</span>
                                            </span>
                                        @elseif(strtolower($payment->payment_method) === 'bkash')
                                            <span class="inline-flex items-center gap-1 px-2 py-0.5 rounded text-[11px] font-semibold bg-pink-50 text-pink-700 border border-pink-200">
                                                <span>বিকাশ</span>
                                            </span>
                                        @elseif(strtolower($payment->payment_method) === 'nagad')
                                            <span class="inline-flex items-center gap-1 px-2 py-0.5 rounded text-[11px] font-semibold bg-orange-50 text-orange-700 border border-orange-200">
                                                <span>নগদ</span>
                                            </span>
                                        @elseif(strtolower($payment->payment_method) === 'rocket')
                                            <span class="inline-flex items-center gap-1 px-2 py-0.5 rounded text-[11px] font-semibold bg-purple-50 text-purple-700 border border-purple-200">
                                                <span>রকেট</span>
                                            </span>
                                        @else
                                            <span class="inline-flex items-center gap-1 px-2 py-0.5 rounded text-[11px] font-semibold bg-slate-100 text-slate-700 border border-slate-200">
                                                <span>{{ strtoupper($payment->payment_method) }}</span>
                                            </span>
                                        @endif
                                    </div>

                                    <!-- Transaction ID with Copy -->
                                    <div class="mt-1 flex items-center gap-1">
                                        <span class="font-mono text-[11px] text-slate-700 select-all font-semibold">
                                            {{ $payment->transaction_id }}
                                        </span>
                                        <button type="button"
                                            onclick="copyTrxText('{{ $payment->transaction_id }}', this)"
                                            title="TrxID কপি করুন"
                                            class="text-slate-400 hover:text-emerald-600 p-0.5 transition">
                                            <i class="fa-regular fa-copy text-xs"></i>
                                        </button>
                                    </div>
                                    @if($payment->sender_phone && $payment->sender_phone !== $payment->student_phone)
                                        <div class="text-[10px] text-slate-400 mt-0.5">
                                            প্রেরক: {{ $payment->sender_phone }}
                                        </div>
                                    @endif
                                </td>

                                <!-- Payment & LMS Status Badges -->
                                <td class="py-3.5 px-4 text-center">
                                    <span class="inline-flex items-center gap-1 px-2.5 py-1 rounded-full text-[11px] font-bold bg-green-50 text-green-700 border border-green-200">
                                        <i class="fa-solid fa-circle-check text-[10px]"></i>
                                        <span>পেমেন্ট সফল</span>
                                    </span>
                                    @if($hasLms)
                                        <div class="mt-1">
                                            <span class="inline-flex items-center gap-1 px-2 py-0.5 rounded-full text-[10px] font-medium bg-emerald-50 text-emerald-800 border border-emerald-200"
                                                  title="ইউজার আইডি: {{ $lmsUserId }} (পাসওয়ার্ড: {{ $lmsPassword }})">
                                                <i class="fa-solid fa-user-check text-[9px] text-emerald-600"></i>
                                                <span>LMS সক্রিয়</span>
                                            </span>
                                        </div>
                                    @else
                                        <div class="mt-1">
                                            <span class="inline-flex items-center gap-1 px-2 py-0.5 rounded-full text-[10px] font-medium bg-amber-50 text-amber-800 border border-amber-200">
                                                <i class="fa-solid fa-user-clock text-[9px] text-amber-600"></i>
                                                <span>LMS বাকি</span>
                                            </span>
                                        </div>
                                    @endif
                                </td>

                                <!-- Actions -->
                                <td class="py-3.5 px-4 text-center">
                                    <div class="flex items-center justify-center gap-1.5 flex-wrap">
                                        <!-- View Details Modal Trigger -->
                                        <button type="button"
                                            onclick="openStudentModal({{ $payment->id }})"
                                            title="বিস্তারিত দেখুন"
                                            class="inline-flex items-center gap-1 bg-slate-100 hover:bg-emerald-50 text-slate-700 hover:text-emerald-700 text-xs font-semibold px-2.5 py-1 rounded-lg border border-slate-200 hover:border-emerald-200 transition cursor-pointer">
                                            <i class="fa-solid fa-eye text-xs"></i>
                                            <span>বিস্তারিত</span>
                                        </button>

                                        @if($hasLms)
                                            <!-- Send Credentials via WhatsApp -->
                                            <a href="{{ $credentialsWaUrl }}" target="_blank"
                                               title="WhatsApp-এ ইউজার আইডি ও পাসওয়ার্ড পাঠান"
                                               class="inline-flex items-center gap-1 bg-green-600 hover:bg-green-700 text-white text-xs font-semibold px-2.5 py-1 rounded-lg shadow-sm transition">
                                                <i class="fa-brands fa-whatsapp text-xs"></i>
                                                <span>আইডি পাঠান</span>
                                            </a>
                                        @else
                                            <!-- Quick Create LMS Credentials Trigger -->
                                            <button type="button"
                                                onclick="openStudentModal({{ $payment->id }}, true)"
                                                title="LMS ইউজার আইডি ও পাসওয়ার্ড তৈরি করুন"
                                                class="inline-flex items-center gap-1 bg-emerald-600 hover:bg-emerald-700 text-white text-xs font-semibold px-2.5 py-1 rounded-lg shadow-sm transition">
                                                <i class="fa-solid fa-key text-xs"></i>
                                                <span>লগইন তৈরি</span>
                                            </button>
                                        @endif
                                    </div>
                                    <script id="student-data-{{ $payment->id }}" type="application/json">
                                        {!! json_encode($studentModalData, JSON_UNESCAPED_UNICODE) !!}
                                    </script>
                                </td>
                            </tr>
                        @endforeach
                    </tbody>
                </table>
            </div>

            <!-- Pagination Bar -->
            <div class="p-4 border-t border-slate-100 bg-slate-50/50 flex flex-col sm:flex-row items-center justify-between gap-3 text-xs text-slate-600">
                <div>
                    মোট <span class="font-bold text-slate-800">{{ $payments->total() }}</span> জন সফল শিক্ষার্থীর মধ্যে
                    <span class="font-bold text-slate-800">{{ $payments->firstItem() }}</span> থেকে
                    <span class="font-bold text-slate-800">{{ $payments->lastItem() }}</span> দেখানো হচ্ছে
                </div>
                <div>
                    {{ $payments->links() }}
                </div>
            </div>
        @else
            <!-- Empty State -->
            <div class="p-12 text-center">
                <div class="w-16 h-16 rounded-full bg-slate-100 text-slate-400 flex items-center justify-center mx-auto text-2xl mb-3">
                    <i class="fa-solid fa-user-check"></i>
                </div>
                <h3 class="text-base font-bold text-slate-700">কোনো সফল শিক্ষার্থী রেকর্ড পাওয়া যায়নি</h3>
                <p class="text-xs text-slate-500 mt-1 max-w-sm mx-auto">
                    @if(request()->hasAny(['search', 'method']))
                        আপনার সার্চ বা ফিল্টার শর্ত অনুযায়ী কোনো ফলাফল নেই। অনুগ্রহ করে অন্য কি-ওয়ার্ড বা ফিল্টার ট্রাই করুন।
                    @else
                        এখনো কোনো শিক্ষার্থী কোর্সটির ফি সফলভাবে পরিশোধ করে ভর্তি সম্পন্ন করেননি। নতুন পেমেন্ট সফল হওয়ার সাথে সাথে স্বয়ংক্রিয়ভাবে এখানে প্রদর্শিত হবে।
                    @endif
                </p>
                @if(request()->hasAny(['search', 'method']))
                    <div class="mt-4">
                        <a href="{{ route('admin.students') }}" class="inline-flex items-center gap-1.5 text-xs font-semibold text-emerald-700 hover:underline">
                            <i class="fa-solid fa-rotate-left"></i>
                            <span>ফিল্টার রিসেট করুন</span>
                        </a>
                    </div>
                @endif
            </div>
        @endif
    </div>

    <!-- Student Details Modal (Pure Vanilla JS - 100% resilient & no CDN dependency) -->
    <div id="studentDetailModal"
         class="hidden fixed inset-0 z-50 overflow-y-auto bg-slate-900/60 backdrop-blur-sm flex items-center justify-center p-4">

        <div class="relative bg-white rounded-3xl shadow-2xl max-w-lg w-full overflow-hidden border border-slate-200">

            <!-- Modal Header -->
            <div class="p-5 bg-gradient-to-r from-emerald-800 to-emerald-700 text-white flex items-center justify-between">
                <div>
                    <h3 id="modal-student-name" class="font-bold text-lg"></h3>
                    <p class="text-xs text-emerald-200 mt-0.5">ভর্তি ও পেমেন্ট বিস্তারিত তথ্য</p>
                </div>
                <button type="button" onclick="closeStudentModal()" class="text-emerald-200 hover:text-white p-1 rounded-lg">
                    <i class="fa-solid fa-xmark text-lg"></i>
                </button>
            </div>

            <!-- Modal Body -->
            <div class="p-6 space-y-4 max-h-[75vh] overflow-y-auto text-xs sm:text-sm">
                <!-- Contact Action Bar -->
                <div class="flex items-center gap-2 p-3 bg-emerald-50 rounded-2xl border border-emerald-100">
                    <div class="flex-1">
                        <div class="text-[11px] text-slate-500">মোবাইল নম্বর</div>
                        <div id="modal-student-phone" class="font-bold text-slate-900 font-mono text-sm"></div>
                    </div>
                    <a id="modal-wa-link" href="#" target="_blank"
                       class="inline-flex items-center gap-1.5 bg-green-500 hover:bg-green-600 text-white text-xs font-bold px-3 py-2 rounded-xl shadow-sm transition">
                        <i class="fa-brands fa-whatsapp text-sm"></i>
                        <span>WhatsApp এ মেসেজ</span>
                    </a>
                </div>

                <!-- Info Grid -->
                <div class="grid grid-cols-2 gap-3">
                    <div class="p-3 bg-slate-50 rounded-xl border border-slate-100">
                        <div class="text-[11px] text-slate-500">ইমেইল ঠিকানা</div>
                        <div id="modal-student-email" class="font-semibold text-slate-800 break-all mt-0.5"></div>
                    </div>
                    <div class="p-3 bg-slate-50 rounded-xl border border-slate-100">
                        <div class="text-[11px] text-slate-500">ভর্তিকৃত কোর্স</div>
                        <div id="modal-course-title" class="font-semibold text-slate-800 mt-0.5"></div>
                    </div>
                    <div class="p-3 bg-slate-50 rounded-xl border border-slate-100">
                        <div class="text-[11px] text-slate-500">পরিশোধিত ফি</div>
                        <div class="font-bold text-emerald-700 text-base mt-0.5">৳<span id="modal-amount"></span></div>
                    </div>
                    <div class="p-3 bg-slate-50 rounded-xl border border-slate-100">
                        <div class="text-[11px] text-slate-500">পেমেন্ট মাধ্যম</div>
                        <div id="modal-payment-method" class="font-semibold text-slate-800 uppercase mt-0.5"></div>
                    </div>
                    <div class="p-3 bg-slate-50 rounded-xl border border-slate-100 col-span-2">
                        <div class="text-[11px] text-slate-500">ট্রানজেকশন আইডি (Transaction ID)</div>
                        <div id="modal-transaction-id" class="font-mono font-bold text-slate-900 text-sm mt-0.5 select-all"></div>
                    </div>
                    <div class="p-3 bg-slate-50 rounded-xl border border-slate-100">
                        <div class="text-[11px] text-slate-500">পেমেন্ট স্ট্যাটাস</div>
                        <div class="font-bold mt-0.5 flex items-center gap-1">
                            <i class="fa-solid fa-circle-check text-xs text-green-600"></i>
                            <span id="modal-status-text">সফল (COMPLETED)</span>
                        </div>
                    </div>
                    <div class="p-3 bg-slate-50 rounded-xl border border-slate-100">
                        <div class="text-[11px] text-slate-500">ভর্তির সময়</div>
                        <div id="modal-created-at" class="font-semibold text-slate-700 mt-0.5"></div>
                    </div>
                </div>

                <!-- Notes / Remarks if any -->
                <div id="modal-notes-box" class="p-3 bg-slate-50 rounded-xl border border-slate-100 hidden">
                    <div class="text-[11px] text-slate-500">পেমেন্ট নোট / ভেরিফিকেশন তথ্য</div>
                    <div id="modal-notes" class="text-xs text-slate-700 mt-0.5"></div>
                </div>

                <!-- EPS Live Gateway Verify Action Box -->
                <div id="modal-eps-box" class="p-3.5 bg-blue-50 rounded-2xl border border-blue-200 flex items-center justify-between gap-3 hidden">
                    <div>
                        <div class="text-xs font-bold text-blue-900 flex items-center gap-1.5">
                            <i class="fa-solid fa-bolt text-blue-600"></i>
                            <span>EPS লাইভ ভেরিফিকেশন</span>
                        </div>
                        <div class="text-[11px] text-blue-700 mt-0.5">ব্যাংক/বিকাশ গেটওয়ে থেকে সরাসরি ট্রানজেকশন স্ট্যাটাস চেক করুন</div>
                    </div>
                    <form id="modal-eps-form" action="" method="POST">
                        @csrf
                        <button type="submit" class="inline-flex items-center gap-1.5 px-3 py-1.5 rounded-xl bg-blue-600 hover:bg-blue-700 text-white text-xs font-bold shadow-sm transition">
                            <i class="fa-solid fa-arrows-rotate"></i>
                            <span>EPS চেক</span>
                        </button>
                    </form>
                </div>

                <!-- Student LMS Login & Credentials Management Section -->
                <div id="modal-credentials-section" class="pt-4 border-t border-slate-200">
                    <div class="p-4 bg-emerald-50/70 border border-emerald-200 rounded-2xl space-y-3">
                        <div class="flex items-center justify-between">
                            <div class="flex items-center gap-2">
                                <div class="w-7 h-7 rounded-lg bg-emerald-600 text-white flex items-center justify-center text-xs font-bold">
                                    <i class="fa-solid fa-key"></i>
                                </div>
                                <div>
                                    <h4 class="font-bold text-slate-900 text-xs uppercase tracking-wider">স্টুডেন্ট LMS পোর্টাল লগইন ও পাসওয়ার্ড</h4>
                                    <p class="text-[11px] text-slate-500">শিক্ষার্থী লগইন লিংক: <a href="{{ url('/login') }}" target="_blank" class="text-emerald-700 underline font-semibold">{{ url('/login') }}</a></p>
                                </div>
                            </div>
                            <span id="modal-lms-status-badge" class="px-2 py-0.5 rounded-full text-[10px] font-bold"></span>
                        </div>

                        <!-- Credentials Form -->
                        <form id="modal-credentials-form" action="" method="POST" class="space-y-3 pt-1">
                            @csrf
                            <div class="grid grid-cols-1 sm:grid-cols-2 gap-2.5">
                                <div>
                                    <label class="block text-[11px] font-semibold text-slate-700 mb-1">
                                        লগইন ইউজার আইডি (User ID / Phone)
                                    </label>
                                    <div class="relative">
                                        <i class="fa-solid fa-user absolute left-3 top-2.5 text-slate-400 text-xs"></i>
                                        <input id="modal-lms-username" type="text" name="username" required
                                               placeholder="মোবাইল নম্বর বা ইউজারনেম"
                                               class="w-full pl-8 pr-3 py-1.5 text-xs bg-white border border-slate-300 rounded-xl focus:ring-2 focus:ring-emerald-500 outline-none font-mono font-medium">
                                    </div>
                                </div>
                                <div>
                                    <div class="flex items-center justify-between mb-1">
                                        <label class="block text-[11px] font-semibold text-slate-700">
                                            লগইন পাসওয়ার্ড (Password)
                                        </label>
                                        <button type="button" onclick="generateAutoPassword()"
                                                class="text-[10px] font-bold text-emerald-700 hover:text-emerald-800 underline">
                                            + অটো পাসওয়ার্ড
                                        </button>
                                    </div>
                                    <div class="relative flex items-center">
                                        <i class="fa-solid fa-lock absolute left-3 text-slate-400 text-xs"></i>
                                        <input id="modal-lms-password" type="text" name="password" required
                                               placeholder="পাসওয়ার্ড প্রদান করুন"
                                               class="w-full pl-8 pr-8 py-1.5 text-xs bg-white border border-slate-300 rounded-xl focus:ring-2 focus:ring-emerald-500 outline-none font-mono font-medium">
                                        <button type="button" onclick="togglePasswordVisibility()" class="absolute right-2.5 text-slate-400 hover:text-slate-600">
                                            <i id="pwd-eye-icon" class="fa-solid fa-eye text-xs"></i>
                                        </button>
                                    </div>
                                </div>
                            </div>

                            <div class="flex items-center gap-2 pt-1">
                                <button type="submit"
                                        class="flex-1 py-2 bg-emerald-600 hover:bg-emerald-700 text-white font-bold rounded-xl text-xs shadow-sm transition flex items-center justify-center gap-1.5">
                                    <i class="fa-solid fa-floppy-disk"></i>
                                    <span>LMS অ্যাকাউন্ট সংরক্ষণ করুন</span>
                                </button>
                                <button type="button" id="modal-copy-cred-btn" onclick="copyModalCredentials()"
                                        class="px-3 py-2 bg-slate-100 hover:bg-slate-200 text-slate-700 font-bold rounded-xl text-xs border border-slate-200 transition flex items-center gap-1"
                                        title="ইউজার আইডি ও পাসওয়ার্ড কপি করুন">
                                    <i class="fa-regular fa-copy"></i>
                                    <span>কপি</span>
                                </button>
                            </div>
                        </form>

                        <!-- Share WhatsApp Button -->
                        <div id="modal-wa-share-box" class="pt-2 border-t border-emerald-100 hidden">
                            <a id="modal-lms-wa-link" href="#" target="_blank"
                               class="w-full py-2 bg-green-600 hover:bg-green-700 text-white font-bold rounded-xl text-xs shadow-sm transition flex items-center justify-center gap-1.5">
                                <i class="fa-brands fa-whatsapp text-sm"></i>
                                <span>WhatsApp-এ আইডি ও পাসওয়ার্ড পাঠান</span>
                            </a>
                        </div>
                    </div>
                </div>

                <!-- Admin Remarks / Status Note Form -->
                <div class="pt-4 border-t border-slate-200">
                    <h4 class="font-bold text-slate-800 text-xs uppercase tracking-wider mb-2">স্ট্যাটাস ও এডমিন নোট আপডেট</h4>
                    <form id="modal-update-form" action="" method="POST" class="space-y-3">
                        @csrf
                        @method('PATCH')
                        <div>
                            <label class="block text-[11px] font-semibold text-slate-600 mb-1">পেমেন্ট ও ভর্তির স্ট্যাটাস</label>
                            <select id="modal-status-select" name="status" class="w-full px-3 py-2 text-xs bg-slate-50 border border-slate-300 rounded-xl focus:ring-2 focus:ring-emerald-500 outline-none">
                                <option value="completed">সফল (Completed) — কোর্সে ভর্তি নিশ্চিত</option>
                                <option value="pending">অপেক্ষমাণ (Pending) — এখনো যাচাই হয়নি</option>
                                <option value="cancelled">বাতিল (Cancelled) — টাকা জমা হয়নি</option>
                                <option value="failed">ব্যর্থ (Failed) — পেমেন্ট ফেইল</option>
                            </select>
                        </div>
                        <div>
                            <label class="block text-[11px] font-semibold text-slate-600 mb-1">অ্যাডমিন নোট / মন্তব্য</label>
                            <input id="modal-notes-input" type="text" name="notes" placeholder="যেমন: EPS-এ টাকা পাওয়া যায়নি / ম্যানুয়ালি যাচাইকৃত..." class="w-full px-3 py-2 text-xs bg-slate-50 border border-slate-300 rounded-xl focus:ring-2 focus:ring-emerald-500 outline-none">
                        </div>
                        <button type="submit" class="w-full py-2.5 bg-emerald-600 hover:bg-emerald-700 text-white font-bold rounded-xl text-xs shadow-sm transition">
                            স্ট্যাটাস ও নোট সংরক্ষণ করুন
                        </button>
                    </form>
                </div>
            </div>
        </div>
    </div>

</div>

@push('scripts')
<script>
function openStudentModal(id, focusLms) {
    var script = document.getElementById('student-data-' + id);
    if (!script) return;
    try {
        var data = JSON.parse(script.textContent);
        document.getElementById('modal-student-name').textContent = data.student_name || '';
        document.getElementById('modal-student-phone').textContent = data.student_phone || '';
        document.getElementById('modal-student-email').textContent = data.student_email || '';
        document.getElementById('modal-course-title').textContent = data.course_title || '';
        document.getElementById('modal-amount').textContent = data.amount || '';
        document.getElementById('modal-payment-method').textContent = (data.payment_method || '').toUpperCase();
        document.getElementById('modal-transaction-id').textContent = data.transaction_id || '';
        document.getElementById('modal-created-at').textContent = data.created_at || '';
        document.getElementById('modal-wa-link').href = data.wa_url || '#';

        var notesBox = document.getElementById('modal-notes-box');
        if (data.notes && data.notes.trim() !== '') {
            document.getElementById('modal-notes').textContent = data.notes;
            notesBox.classList.remove('hidden');
        } else {
            notesBox.classList.add('hidden');
        }

        var epsBox = document.getElementById('modal-eps-box');
        if ((data.payment_method || '').toLowerCase() === 'eps') {
            epsBox.classList.remove('hidden');
            document.getElementById('modal-eps-form').action = data.verify_eps_url || '';
        } else {
            epsBox.classList.add('hidden');
        }

        document.getElementById('modal-update-form').action = data.update_url || '';
        document.getElementById('modal-status-select').value = data.status || 'completed';
        document.getElementById('modal-notes-input').value = data.notes || '';

        var statusBadgeText = document.getElementById('modal-status-text');
        if (data.status === 'completed') {
            statusBadgeText.textContent = 'সফল (COMPLETED)';
            statusBadgeText.className = 'text-green-600 font-bold';
        } else if (data.status === 'pending') {
            statusBadgeText.textContent = 'অপেক্ষমাণ (PENDING)';
            statusBadgeText.className = 'text-amber-600 font-bold';
        } else if (data.status === 'cancelled') {
            statusBadgeText.textContent = 'বাতিল (CANCELLED)';
            statusBadgeText.className = 'text-red-600 font-bold';
        } else {
            statusBadgeText.textContent = (data.status || '').toUpperCase();
            statusBadgeText.className = 'text-slate-600 font-bold';
        }

        // Student LMS Credentials population
        var credForm = document.getElementById('modal-credentials-form');
        credForm.action = data.credentials_url || '';
        document.getElementById('modal-lms-username').value = data.lms_user_id || data.student_phone || '';
        document.getElementById('modal-lms-password').value = data.lms_password || '';

        var lmsBadge = document.getElementById('modal-lms-status-badge');
        var waShareBox = document.getElementById('modal-wa-share-box');
        var lmsWaLink = document.getElementById('modal-lms-wa-link');

        if (data.has_lms) {
            lmsBadge.textContent = 'LMS সক্রিয়';
            lmsBadge.className = 'px-2 py-0.5 rounded-full text-[10px] font-bold bg-green-100 text-green-800 border border-green-200';
            waShareBox.classList.remove('hidden');
            lmsWaLink.href = data.credentials_wa_url || '#';
        } else {
            lmsBadge.textContent = 'লগইন তৈরি বাকি';
            lmsBadge.className = 'px-2 py-0.5 rounded-full text-[10px] font-bold bg-amber-100 text-amber-800 border border-amber-200';
            waShareBox.classList.add('hidden');
            if (!data.lms_password) {
                generateAutoPassword();
            }
        }

        var modal = document.getElementById('studentDetailModal');
        modal.classList.remove('hidden');
        document.body.style.overflow = 'hidden';

        if (focusLms) {
            setTimeout(function() {
                var lmsSec = document.getElementById('modal-credentials-section');
                if (lmsSec) {
                    lmsSec.scrollIntoView({ behavior: 'smooth', block: 'center' });
                }
                var pwdField = document.getElementById('modal-lms-password');
                if (pwdField) pwdField.focus();
            }, 100);
        }
    } catch(e) {
        console.error('Failed to parse student data', e);
    }
}

function generateAutoPassword() {
    var randomNum = Math.floor(100 + Math.random() * 900);
    document.getElementById('modal-lms-password').value = 'AB2026#' + randomNum;
}

function togglePasswordVisibility() {
    var input = document.getElementById('modal-lms-password');
    var icon = document.getElementById('pwd-eye-icon');
    if (input.type === 'password') {
        input.type = 'text';
        icon.className = 'fa-solid fa-eye-slash text-xs';
    } else {
        input.type = 'password';
        icon.className = 'fa-solid fa-eye text-xs';
    }
}

function copyModalCredentials() {
    var user = document.getElementById('modal-lms-username').value;
    var pwd = document.getElementById('modal-lms-password').value;
    var loginUrl = '{{ url("/login") }}';
    var text = "স্টুডেন্ট পোর্টাল লগইন:\nওয়েবসাইট: " + loginUrl + "\nইউজার আইডি: " + user + "\nপাসওয়ার্ড: " + pwd;
    copyTrxText(text, document.getElementById('modal-copy-cred-btn'));
}

function closeStudentModal() {
    var modal = document.getElementById('studentDetailModal');
    if (modal) {
        modal.classList.add('hidden');
        document.body.style.overflow = '';
    }
}

function copyTrxText(text, btn) {
    if (navigator.clipboard && navigator.clipboard.writeText) {
        navigator.clipboard.writeText(text).then(function() {
            var icon = btn.querySelector('i');
            if (icon) {
                var orig = icon.className;
                icon.className = 'fa-solid fa-check text-emerald-600 text-xs';
                setTimeout(function() { icon.className = orig; }, 2000);
            }
        });
    } else {
        window.prompt('তথ্য কপি করুন:', text);
    }
}

document.addEventListener('DOMContentLoaded', function() {
    var modal = document.getElementById('studentDetailModal');
    if (modal) {
        modal.addEventListener('click', function(e) {
            if (e.target === modal) {
                closeStudentModal();
            }
        });
    }
    document.addEventListener('keydown', function(e) {
        if (e.key === 'Escape') {
            closeStudentModal();
        }
    });
});
</script>
@endpush
@endsection
