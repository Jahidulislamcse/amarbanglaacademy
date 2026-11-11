@extends('layouts.app')

@section('title', 'অনলাইন পেমেন্ট ও ভর্তি - ' . ($course->bangla_title ?? $course->title))

@section('content')
@php
    $checkoutBatch = $activeBatch ?? $displayBatch;
    $checkoutIsOpen = isset($isEnrollmentOpen) ? (bool) $isEnrollmentOpen : !is_null($activeBatch);
    $checkoutBatchList = ($openBatches ?? collect())->map(function ($b) use ($course) {
        $reg = (int) ($b->effective_regular_price ?? $course->regular_price ?? 3000);
        $sale = (int) ($b->effective_price ?? $course->effective_price ?? 999);
        return [
            'id' => $b->id,
            'name' => $b->name,
            'start_date' => $b->formatted_start_date,
            'schedule' => $b->formatted_schedule,
            'offer_label' => $b->offer_label ?: ($b->name . ' বিশেষ ছাড়'),
            'regular_price' => $reg,
            'effective_price' => $sale,
            'discount_amount' => max(0, $reg - $sale),
            'seats_remaining' => $b->seats_remaining,
        ];
    })->values();
    $initialBatch = $checkoutBatchList->firstWhere('id', $checkoutBatch?->id) ?? $checkoutBatchList->first() ?? [
        'id' => null,
        'name' => $checkoutBatch?->name ?? ($course->batch_no ?? '১ম ব্যাচ'),
        'start_date' => $checkoutBatch?->formatted_start_date ?? 'শীঘ্রই ঘোষণা হবে',
        'schedule' => $checkoutBatch?->formatted_schedule ?? ($course->live_schedule_text ?? 'বৃহস্পতি, শুক্র ও শনি (রাত ৯টা)'),
        'offer_label' => ($checkoutBatch?->offer_label) ?: (($checkoutBatch?->name ?? 'চলমান ব্যাচ') . ' বিশেষ ছাড়'),
        'regular_price' => (int) ($checkoutBatch?->effective_regular_price ?? $course->regular_price ?? 3000),
        'effective_price' => (int) ($checkoutBatch?->effective_price ?? $course->effective_price ?? 999),
        'discount_amount' => max(0, (int) ($checkoutBatch?->effective_regular_price ?? $course->regular_price ?? 3000) - (int) ($checkoutBatch?->effective_price ?? $course->effective_price ?? 999)),
        'seats_remaining' => $checkoutBatch?->seats_remaining ?? 0,
    ];
@endphp

@if($checkoutIsOpen)
<script>
    function checkoutPageData() {
        const batches = @json($checkoutBatchList);
        const fallback = @json($initialBatch);
        return {
            isSubmitting: false,
            selectedBatchId: @json($initialBatch['id']),
            get selectedBatch() {
                return batches.find(b => Number(b.id) === Number(this.selectedBatchId)) || batches[0] || fallback;
            },
            formatMoney(num) {
                return Number(num || 0).toLocaleString('en-US');
            },
            handleSubmit(e) {
                if (this.isSubmitting) {
                    e.preventDefault();
                    return false;
                }
                this.isSubmitting = true;
            }
        };
    }
</script>
@endif

<div class="bg-slate-100 py-10 sm:py-14" @if($checkoutIsOpen) x-data="checkoutPageData()" @endif>
    <div class="max-w-4xl mx-auto px-4 sm:px-6 lg:px-8">

        @if(!$checkoutIsOpen)
            <!-- Enrollment Closed Card -->
            <div class="max-w-2xl mx-auto bg-white rounded-3xl border border-slate-200 shadow-xl overflow-hidden">
                <div class="bg-gradient-to-r from-slate-900 via-rose-950 to-slate-900 text-white p-7 sm:p-9 text-center space-y-3">
                    <div class="w-16 h-16 rounded-2xl bg-rose-500/20 border border-rose-400/40 text-rose-300 flex items-center justify-center text-2xl mx-auto shadow-lg">
                        <i class="fa-solid fa-lock"></i>
                    </div>
                    <span class="inline-flex items-center gap-1.5 px-3 py-1 rounded-full text-xs font-black bg-rose-600 text-white">
                        {{ $initialBatch['name'] }} — অনলাইন ভর্তি বন্ধ
                    </span>
                    <h1 class="text-2xl sm:text-3xl font-extrabold leading-tight">
                        বর্তমানে অনলাইন ভর্তি কার্যক্রম বন্ধ রয়েছে
                    </h1>
                    <p class="text-xs sm:text-sm text-slate-300 max-w-lg mx-auto leading-relaxed">
                        <strong>{{ $course->bangla_title ?? $course->title }}</strong> কোর্সের <strong>{{ $initialBatch['name'] }}</strong>-এর নির্ধারিত আসন পূর্ণ হওয়ায় নতুন অনলাইন পেমেন্ট ও ভর্তি সাময়িকভাবে বন্ধ রাখা হয়েছে।
                    </p>
                </div>

                <div class="p-6 sm:p-8 space-y-6">
                    <div class="bg-slate-50 rounded-2xl p-4 sm:p-5 border border-slate-200 space-y-2.5 text-xs">
                        <div class="flex items-center justify-between">
                            <span class="text-slate-500">কোর্স:</span>
                            <strong class="text-slate-900">{{ $course->bangla_title ?? $course->title }}</strong>
                        </div>
                        <div class="flex items-center justify-between">
                            <span class="text-slate-500">সর্বশেষ ব্যাচ:</span>
                            <strong class="text-slate-900">{{ $initialBatch['name'] }} (শুরু: {{ $initialBatch['start_date'] }})</strong>
                        </div>
                        <div class="flex items-center justify-between">
                            <span class="text-slate-500">সাপ্তাহিক ক্লাস রুটিন:</span>
                            <strong class="text-slate-900">{{ $initialBatch['schedule'] }}</strong>
                        </div>
                        <div class="flex items-center justify-between">
                            <span class="text-slate-500">কারিকুলাম:</span>
                            <strong class="text-emerald-700">১২টি বিশেষায়িত মডিউল ও লাইভ ক্লাস</strong>
                        </div>
                    </div>

                    <div class="p-4 rounded-2xl bg-emerald-50 border border-emerald-200 flex flex-col sm:flex-row sm:items-center justify-between gap-4">
                        <div class="space-y-1">
                            <h3 class="font-bold text-sm text-emerald-950">পরবর্তী ব্যাচে ভর্তি হতে চান?</h3>
                            <p class="text-xs text-emerald-800">
                                নতুন ব্যাচ শুরুর তারিখ ও আসন বুকিংয়ের তথ্য সবার আগে জানতে WhatsApp-এ মেসেজ দিন।
                            </p>
                        </div>
                        <a href="https://wa.me/8801332549549?text={{ urlencode('আসসালামু আলাইকুম, আমি আমার বাংলা একাডেমীর পরবর্তী ব্যাচে ভর্তি হতে আগ্রহী।') }}" target="_blank"
                           class="inline-flex items-center justify-center gap-2 px-5 py-3 rounded-xl bg-emerald-600 hover:bg-emerald-700 text-white font-bold text-xs sm:text-sm shadow-sm transition shrink-0">
                            <i class="fa-brands fa-whatsapp text-base"></i>
                            <span>WhatsApp (01332-549549)</span>
                        </a>
                    </div>

                    <div class="flex flex-col sm:flex-row items-center justify-between gap-3 pt-2 border-t border-slate-100 text-xs">
                        <a href="{{ route('home') }}" class="inline-flex items-center gap-1.5 font-bold text-slate-700 hover:text-emerald-700 transition">
                            <i class="fa-solid fa-arrow-left"></i>
                            <span>মূল ওয়েবসাইটে ফিরে যান</span>
                        </a>
                        <a href="{{ route('login') }}" class="inline-flex items-center gap-1.5 font-bold text-emerald-700 hover:text-emerald-800 transition">
                            <i class="fa-solid fa-user-graduate"></i>
                            <span>ইতিমধ্যে ভর্তিকৃত শিক্ষার্থী? লগইন করুন &rarr;</span>
                        </a>
                    </div>
                </div>
            </div>
        @else
        
        <!-- Header -->
        <div class="text-center mb-8">
            <span class="inline-flex items-center gap-1.5 text-xs font-bold text-emerald-800 uppercase tracking-wider bg-emerald-100 border border-emerald-200 px-3.5 py-1 rounded-full">
                <i class="fa-solid fa-shield-halved text-emerald-600"></i> নিরাপদ অনলাইন পেমেন্ট ও ইনস্ট্যান্ট ভর্তি
            </span>
            <h1 class="text-2xl sm:text-3xl font-extrabold text-slate-900 mt-2">
                ভর্তি নিশ্চিতকরণ ও ফি পরিশোধ
            </h1>
            <p class="text-xs sm:text-sm text-slate-600 mt-1">
                EPS অনলাইন পেমেন্ট গেটওয়ের মাধ্যমে বিকাশ, নগদ, রকেট অথবা কার্ড দিয়ে দ্রুত পেমেন্ট সম্পন্ন করুন
            </p>
        </div>

        @if(session('eps_fallback'))
        <!-- EPS Fallback Emergency Card (Shown when EPS fails or times out) -->
        <div class="mb-8 p-5 rounded-2xl bg-gradient-to-r from-amber-50 to-orange-50 border-2 border-amber-400 text-amber-950 shadow-md">
            <div class="flex items-start gap-3.5">
                <div class="w-11 h-11 rounded-2xl bg-amber-500 text-white flex items-center justify-center text-xl shrink-0 shadow-sm">
                    <i class="fa-solid fa-triangle-exclamation"></i>
                </div>
                <div class="flex-1 space-y-2.5">
                    <div>
                        <h3 class="font-black text-base sm:text-lg text-amber-950">
                            EPS অনলাইন গেটওয়েতে সাময়িক সংযোগ সমস্যা হয়েছে?
                        </h3>
                        <p class="text-xs text-amber-900 mt-0.5 leading-relaxed">
                            চিন্তার কোনো কারণ নেই! আপনি সরাসরি নিচের <strong>বিকাশ</strong> অথবা <strong>নগদ</strong> পার্সোনাল নম্বরে কোর্স ফি (<strong x-text="'৳' + formatMoney(selectedBatch.effective_price)">৳{{ number_format($initialBatch['effective_price']) }}</strong>) Send Money করে আমাদের WhatsApp-এ TrxID পাঠিয়ে দিন। আমরা সাথে সাথে আপনার ভর্তি কনফার্ম করে দেব।
                        </p>
                    </div>

                    <div class="grid grid-cols-1 sm:grid-cols-2 gap-3 pt-1">
                        <!-- bKash Personal -->
                        <div class="bg-white p-3 rounded-xl border border-pink-300 flex items-center justify-between shadow-xs">
                            <div>
                                <span class="text-xs font-bold text-pink-700 flex items-center gap-1">
                                    <span class="w-2 h-2 rounded-full bg-pink-600"></span> বিকাশ (Personal)
                                </span>
                                <div class="font-mono font-black text-slate-900 text-base sm:text-lg select-all">01813-753610</div>
                            </div>
                            <button type="button" onclick="navigator.clipboard.writeText('01813753610'); alert('বিকাশ নম্বর কপি হয়েছে: 01813753610');"
                                    class="px-3 py-1.5 bg-pink-50 hover:bg-pink-100 text-pink-700 text-xs font-bold rounded-lg border border-pink-200 transition">
                                কপি
                            </button>
                        </div>

                        <!-- Nagad Personal -->
                        <div class="bg-white p-3 rounded-xl border border-orange-300 flex items-center justify-between shadow-xs">
                            <div>
                                <span class="text-xs font-bold text-orange-700 flex items-center gap-1">
                                    <span class="w-2 h-2 rounded-full bg-orange-600"></span> নগদ (Personal)
                                </span>
                                <div class="font-mono font-black text-slate-900 text-base sm:text-lg select-all">01712-168109</div>
                            </div>
                            <button type="button" onclick="navigator.clipboard.writeText('01712168109'); alert('নগদ নম্বর কপি হয়েছে: 01712168109');"
                                    class="px-3 py-1.5 bg-orange-50 hover:bg-orange-100 text-orange-700 text-xs font-bold rounded-lg border border-orange-200 transition">
                                কপি
                            </button>
                        </div>
                    </div>

                    <div class="pt-1 flex flex-col sm:flex-row items-stretch sm:items-center gap-2">
                        <a href="https://wa.me/8801332549549?text={{ urlencode('আসসালামু আলাইকুম, আমি আমার বাংলা একাডেমীর সাংবাদিকতা কোর্সে ভর্তি হতে চাই। EPS গেটওয়েতে সমস্যা হওয়ায় বিকাশ/নগদে সেন্ড মানি করেছি।') }}"
                           target="_blank"
                           class="inline-flex items-center justify-center gap-2 bg-green-600 hover:bg-green-700 text-white font-bold text-xs sm:text-sm px-4 py-2.5 rounded-xl shadow-sm transition">
                            <i class="fa-brands fa-whatsapp text-lg"></i>
                            <span>টাকা পাঠিয়ে সরাসরি WhatsApp-এ TrxID পাঠান (01332-549549)</span>
                        </a>
                    </div>
                </div>
            </div>
        </div>
        @endif

        <!-- Checkout Form (Submits to EPS Online Payment Gateway) -->
        <form action="{{ route('eps.pay', $course->slug) }}" 
              method="POST" 
              @submit="handleSubmit($event)"
              class="grid grid-cols-1 md:grid-cols-12 gap-8 items-start">
            @csrf
            <input type="hidden" name="batch_id" :value="selectedBatchId">

            <!-- Left: Student Info & Payment Selector (7 cols) -->
            <div class="md:col-span-7 space-y-6">

                @if($checkoutBatchList->count() > 1)
                <!-- 0. Batch Selector (Shown when multiple batches are open simultaneously) -->
                <div class="bg-white rounded-2xl p-5 sm:p-6 border border-emerald-200 shadow-sm space-y-3">
                    <h3 class="font-bold text-base text-slate-900 flex items-center gap-2 pb-2 border-b border-slate-100">
                        <span class="w-6 h-6 rounded-full bg-emerald-600 text-white text-xs font-black flex items-center justify-center"><i class="fa-solid fa-layer-group text-[10px]"></i></span>
                        <span>ব্যাচ নির্বাচন করুন</span>
                    </h3>
                    <div class="grid grid-cols-1 sm:grid-cols-2 gap-3 text-xs">
                        @foreach($checkoutBatchList as $bOption)
                            <label @click="selectedBatchId = {{ $bOption['id'] }}"
                                   :class="Number(selectedBatchId) === {{ $bOption['id'] }} ? 'border-emerald-600 bg-emerald-50/70 ring-1 ring-emerald-500' : 'border-slate-200 hover:border-slate-300 bg-slate-50/40'"
                                   class="cursor-pointer rounded-xl border-2 p-3.5 flex flex-col justify-between gap-1.5 transition">
                                <div class="flex items-center justify-between gap-2">
                                    <span class="font-bold text-sm text-slate-900">{{ $bOption['name'] }}</span>
                                    <span class="font-black text-emerald-700">৳{{ number_format($bOption['effective_price']) }}</span>
                                </div>
                                <div class="text-[11px] text-slate-600">শুরু: {{ $bOption['start_date'] }}</div>
                                <div class="text-[10px] text-emerald-700 font-semibold">বাকি আসন: {{ $bOption['seats_remaining'] }}টি</div>
                            </label>
                        @endforeach
                    </div>
                </div>
                @endif
                
                <!-- 1. Student Details -->
                <div class="bg-white rounded-2xl p-5 sm:p-6 border border-slate-200 shadow-sm space-y-4">
                    <h3 class="font-bold text-base text-slate-900 flex items-center gap-2 pb-3 border-b border-slate-100">
                        <span class="w-6 h-6 rounded-full bg-emerald-600 text-white text-xs font-black flex items-center justify-center">১</span>
                        <span>আপনার তথ্য (শিক্ষার্থী)</span>
                    </h3>

                    <div class="space-y-3.5 text-xs">
                        <div>
                            <label class="block font-semibold text-slate-700 mb-1">পূর্ণ নাম <span class="text-rose-500">*</span></label>
                            <input type="text" name="student_name" required value="{{ old('student_name') }}" placeholder="যেমন: মো: রহিম উদ্দিন" 
                                   class="w-full px-3.5 py-2.5 rounded-xl border border-slate-300 focus:outline-none focus:ring-2 focus:ring-emerald-500 text-sm">
                            @error('student_name')
                                <p class="text-rose-600 text-[11px] mt-1">{{ $message }}</p>
                            @enderror
                        </div>

                        <div>
                            <label class="block font-semibold text-slate-700 mb-1">ইমেইল অ্যাড্রেস <span class="text-rose-500">*</span> (এখানে ক্লাসের জুম/মিট লিংক পাঠানো হবে)</label>
                            <input type="email" name="student_email" required value="{{ old('student_email') }}" placeholder="rahim@example.com" 
                                   class="w-full px-3.5 py-2.5 rounded-xl border border-slate-300 focus:outline-none focus:ring-2 focus:ring-emerald-500 text-sm">
                            @error('student_email')
                                <p class="text-rose-600 text-[11px] mt-1">{{ $message }}</p>
                            @enderror
                        </div>

                        <div>
                            <label class="block font-semibold text-slate-700 mb-1">মোবাইল নম্বর <span class="text-rose-500">*</span> (হোয়াটসঅ্যাপ ও কনফার্মেশন এসএমএস)</label>
                            <input type="tel" name="student_phone" required value="{{ old('student_phone') }}" placeholder="01XXXXXXXXX" 
                                   class="w-full px-3.5 py-2.5 rounded-xl border border-slate-300 focus:outline-none focus:ring-2 focus:ring-emerald-500 text-sm">
                            @error('student_phone')
                                <p class="text-rose-600 text-[11px] mt-1">{{ $message }}</p>
                            @enderror
                        </div>
                    </div>
                </div>

                <!-- 2. Payment Gateway Section (Official EPS Gateway) -->
                <div class="bg-white rounded-2xl p-5 sm:p-6 border border-slate-200 shadow-sm space-y-4">
                    <h3 class="font-bold text-base text-slate-900 flex items-center gap-2 pb-3 border-b border-slate-100">
                        <span class="w-6 h-6 rounded-full bg-emerald-600 text-white text-xs font-black flex items-center justify-center">২</span>
                        <span>পেমেন্ট গেটওয়ে</span>
                    </h3>

                    <!-- EPS Online Gateway Card -->
                    <div class="p-4 rounded-xl border-2 border-emerald-600 bg-emerald-50/40 shadow-xs">
                        <div class="flex items-start justify-between gap-3">
                            <div class="flex items-center gap-3">
                                <div class="w-10 h-10 rounded-xl bg-emerald-600 text-white flex items-center justify-center text-lg shrink-0">
                                    <i class="fa-solid fa-bolt"></i>
                                </div>
                                <div>
                                    <div class="flex items-center gap-2 flex-wrap">
                                        <span class="font-bold text-sm text-slate-900">EPS অনলাইন পেমেন্ট (স্বয়ংক্রিয় ও নিরাপদ)</span>
                                        <span class="text-[10px] font-extrabold uppercase bg-emerald-600 text-white px-2 py-0.5 rounded-full">অফিসিয়াল গেটওয়ে</span>
                                    </div>
                                    <p class="text-xs text-slate-600 mt-1">
                                        বিকাশ, নগদ, রকেট, উপাই অথবা যেকোনো কার্ড দিয়ে সাথে সাথে ইনস্ট্যান্ট পেমেন্ট সম্পন্ন করুন।
                                    </p>
                                </div>
                            </div>
                        </div>

                        <!-- Supported Gateways Badge Row -->
                        <div class="mt-3 pt-3 border-t border-emerald-200/60 flex items-center gap-2 flex-wrap text-xs">
                            <span class="px-2.5 py-1 rounded-md bg-pink-100 text-pink-800 font-bold text-[11px] flex items-center gap-1">
                                <span class="w-2 h-2 rounded-full bg-pink-600"></span> বিকাশ (bKash)
                            </span>
                            <span class="px-2.5 py-1 rounded-md bg-orange-100 text-orange-800 font-bold text-[11px] flex items-center gap-1">
                                <span class="w-2 h-2 rounded-full bg-orange-600"></span> নগদ (Nagad)
                            </span>
                            <span class="px-2.5 py-1 rounded-md bg-purple-100 text-purple-800 font-bold text-[11px] flex items-center gap-1">
                                <span class="w-2 h-2 rounded-full bg-purple-600"></span> রকেট (Rocket)
                            </span>
                            <span class="px-2.5 py-1 rounded-md bg-yellow-100 text-yellow-900 font-bold text-[11px] flex items-center gap-1">
                                <span class="w-2 h-2 rounded-full bg-yellow-600"></span> উপায় (Upay)
                            </span>
                            <span class="px-2.5 py-1 rounded-md bg-blue-100 text-blue-800 font-bold text-[11px] flex items-center gap-1">
                                <i class="fa-solid fa-credit-card"></i> ভিসা / মাস্টারকার্ড
                            </span>
                        </div>
                    </div>

                    <!-- Payment Instruction Notice -->
                    <div class="p-3.5 bg-slate-50 rounded-xl border border-slate-200 text-xs text-slate-600 flex items-start gap-2.5">
                        <i class="fa-solid fa-circle-info text-emerald-600 text-sm mt-0.5 shrink-0"></i>
                        <span>নিচের বাটনে ক্লিক করলে আপনাকে নিরাপদ <strong>EPS পেমেন্ট গেটওয়েতে</strong> নিয়ে যাওয়া হবে। সেখানে আপনার পছন্দের মাধ্যমে (বিকাশ/নগদ/কার্ড) পেমেন্ট সম্পন্ন করলেই কোর্সটিতে সরাসরি ভর্তি নিশ্চিত হয়ে যাবে।</span>
                    </div>

                    <!-- Submit / Pay Now Button -->
                    <button type="submit" 
                            :disabled="isSubmitting"
                            :class="isSubmitting ? 'opacity-75 cursor-not-allowed pointer-events-none' : 'hover:from-emerald-700 hover:to-teal-800 hover:shadow-emerald-600/50'"
                            class="w-full py-4 rounded-xl bg-gradient-to-r from-emerald-600 via-teal-600 to-emerald-700 text-white font-black text-sm sm:text-base shadow-lg shadow-emerald-600/30 transition duration-200 flex items-center justify-center gap-2">
                        <span x-show="!isSubmitting" class="inline-flex items-center justify-center gap-2">
                            <i class="fa-solid fa-lock text-sm"></i>
                            <span>নিরাপদ EPS গেটওয়েতে পেমেন্ট করুন (৳<span x-text="formatMoney(selectedBatch.effective_price)">{{ number_format($initialBatch['effective_price']) }}</span>)</span>
                            <i class="fa-solid fa-arrow-right text-xs"></i>
                        </span>
                        <span x-show="isSubmitting" class="inline-flex items-center justify-center gap-2" style="display: none;">
                            <i class="fa-solid fa-circle-notch fa-spin text-base"></i>
                            <span>অনুগ্রহ করে অপেক্ষা করুন, EPS গেটওয়েতে সংযোগ তৈরি হচ্ছে...</span>
                        </span>
                    </button>

                    <div class="flex items-center justify-center gap-4 text-[11px] text-slate-500 pt-1">
                        <span class="flex items-center gap-1">
                            <i class="fa-solid fa-shield-check text-emerald-600"></i> ১০০% নিরাপদ
                        </span>
                        <span class="flex items-center gap-1">
                            <i class="fa-solid fa-bolt text-amber-500"></i> তাৎক্ষণিক অ্যাক্টিভেশন
                        </span>
                        <span class="flex items-center gap-1">
                            <i class="fa-solid fa-certificate text-teal-600"></i> সনদপত্র অন্তর্ভুক্ত
                        </span>
                    </div>

                    <!-- Emergency / Alternative Send Money Card (If EPS fails) -->
                    <div class="mt-4 p-4 rounded-2xl bg-amber-50/80 border border-amber-300 text-xs text-amber-950 space-y-3">
                        <div class="flex items-center justify-between">
                            <span class="font-bold flex items-center gap-1.5 text-amber-900 text-xs sm:text-sm">
                                <i class="fa-solid fa-circle-question text-amber-600"></i>
                                <span>EPS গেটওয়েতে সমস্যা হলে বিকল্প উপায়</span>
                            </span>
                            <span class="text-[10px] bg-amber-200 text-amber-900 font-bold px-2 py-0.5 rounded-full">বিকল্প পদ্ধতি</span>
                        </div>
                        <p class="text-[11px] text-amber-900 leading-relaxed">
                            কোনো কারণে EPS অনলাইন গেটওয়েতে সমস্যা হলে আপনি সরাসরি নিচের নম্বরে <strong>Send Money</strong> করে WhatsApp-এ যোগাযোগ করতে পারেন:
                        </p>
                        
                        <div class="grid grid-cols-1 sm:grid-cols-2 gap-2.5">
                            <!-- bKash Personal -->
                            <div class="bg-white p-2.5 rounded-xl border border-pink-200 flex items-center justify-between shadow-xs">
                                <div>
                                    <div class="text-[10px] font-bold text-pink-700 flex items-center gap-1">
                                        <span class="w-1.5 h-1.5 rounded-full bg-pink-600"></span> বিকাশ (Personal)
                                    </div>
                                    <div class="font-mono font-bold text-slate-900 text-sm mt-0.5 select-all">01813-753610</div>
                                </div>
                                <button type="button" onclick="navigator.clipboard.writeText('01813753610'); alert('বিকাশ নম্বর কপি হয়েছে: 01813753610');"
                                        class="px-2 py-1 rounded bg-pink-50 hover:bg-pink-100 text-pink-700 text-[10px] font-bold border border-pink-200 transition">
                                    কপি
                                </button>
                            </div>

                            <!-- Nagad Personal -->
                            <div class="bg-white p-2.5 rounded-xl border border-orange-200 flex items-center justify-between shadow-xs">
                                <div>
                                    <div class="text-[10px] font-bold text-orange-700 flex items-center gap-1">
                                        <span class="w-1.5 h-1.5 rounded-full bg-orange-600"></span> নগদ (Personal)
                                    </div>
                                    <div class="font-mono font-bold text-slate-900 text-sm mt-0.5 select-all">01712-168109</div>
                                </div>
                                <button type="button" onclick="navigator.clipboard.writeText('01712168109'); alert('নগদ নম্বর কপি হয়েছে: 01712168109');"
                                        class="px-2 py-1 rounded bg-orange-50 hover:bg-orange-100 text-orange-700 text-[10px] font-bold border border-orange-200 transition">
                                    কপি
                                </button>
                            </div>
                        </div>

                        <div class="pt-1 flex flex-col sm:flex-row items-stretch sm:items-center justify-between gap-2 bg-white/80 p-2.5 rounded-xl border border-amber-200">
                            <span class="text-[11px] text-amber-900 font-semibold">টাকা পাঠিয়ে TrxID WhatsApp-এ দিন:</span>
                            <a href="https://wa.me/8801332549549?text={{ urlencode('আসসালামু আলাইকুম, আমি আমার বাংলা একাডেমীর সাংবাদিকতা কোর্সে ভর্তি হতে চাই। EPS গেটওয়েতে সমস্যা হওয়ায় বিকাশ/নগদে সেন্ড মানি করেছি।') }}"
                               target="_blank"
                               class="inline-flex items-center justify-center gap-1.5 bg-green-600 hover:bg-green-700 text-white font-bold text-xs px-3 py-1.5 rounded-lg shadow-sm transition">
                                <i class="fa-brands fa-whatsapp text-sm"></i>
                                <span>WhatsApp (01332-549549)</span>
                            </a>
                        </div>
                    </div>

                </div>

            </div>

            <!-- Right: Order Summary (5 cols) -->
            <div class="md:col-span-5 space-y-4">
                <div class="bg-white rounded-2xl p-5 sm:p-6 border border-slate-200 shadow-sm space-y-4">
                    <h3 class="font-bold text-base text-slate-900 pb-3 border-b border-slate-100 flex items-center justify-between">
                        <span>অর্ডার বিবরণী</span>
                        <span class="text-xs font-semibold px-2.5 py-0.5 rounded-full bg-emerald-100 text-emerald-800" x-text="selectedBatch.name">{{ $initialBatch['name'] }}</span>
                    </h3>

                    <div>
                        <h4 class="font-extrabold text-sm text-slate-900 leading-snug">
                            {{ $course->bangla_title ?? $course->title }}
                        </h4>
                        <p class="text-xs text-slate-500 mt-1">
                            সাংবাদিকতা, আইন ও ক্যামেরার সামনে উপস্থাপনা
                        </p>
                    </div>

                    <div class="space-y-2.5 text-xs text-slate-600 pt-3 border-t border-slate-100">
                        <div class="flex justify-between">
                            <span>নির্বাচিত ব্যাচ:</span>
                            <span class="font-bold text-emerald-800" x-text="selectedBatch.name">{{ $initialBatch['name'] }}</span>
                        </div>
                        <div class="flex justify-between">
                            <span>ক্লাস শুরু:</span>
                            <span class="font-bold text-slate-800" x-text="selectedBatch.start_date">{{ $initialBatch['start_date'] }}</span>
                        </div>
                        <div class="flex justify-between">
                            <span>ক্লাসের সময়:</span>
                            <span class="font-bold text-slate-800" x-text="selectedBatch.schedule">{{ $initialBatch['schedule'] }}</span>
                        </div>
                        <div class="flex justify-between">
                            <span>মোট ক্লাস:</span>
                            <span class="font-bold text-slate-800">১২টি অনলাইন লাইভ ক্লাস (১ মাস)</span>
                        </div>
                        <div class="flex justify-between">
                            <span>প্ল্যাটফর্ম:</span>
                            <span class="font-bold text-emerald-700">Zoom & Google Meet</span>
                        </div>
                        <div class="flex justify-between">
                            <span>ক্লাস রেকর্ডিং ও সাপোর্ট:</span>
                            <span class="font-bold text-emerald-700">আজীবন অ্যাক্সেস</span>
                        </div>
                    </div>

                    <!-- Fee calculation -->
                    <div class="pt-3 border-t border-slate-100 space-y-2 text-xs">
                        <div class="flex justify-between text-slate-500" x-show="selectedBatch.regular_price > selectedBatch.effective_price">
                            <span>নিয়মিত কোর্স ফি:</span>
                            <span class="line-through text-slate-400 font-semibold">৳<span x-text="formatMoney(selectedBatch.regular_price)">{{ number_format($initialBatch['regular_price']) }}</span></span>
                        </div>
                        <div class="flex justify-between text-rose-600 font-bold bg-rose-50 px-2 py-1 rounded-md" x-show="selectedBatch.discount_amount > 0">
                            <span x-text="selectedBatch.offer_label + ':'">{{ $initialBatch['offer_label'] }}:</span>
                            <span>- ৳<span x-text="formatMoney(selectedBatch.discount_amount)">{{ number_format($initialBatch['discount_amount']) }}</span></span>
                        </div>
                        <div class="flex justify-between text-lg font-black text-slate-900 pt-2 border-t border-dashed border-slate-200">
                            <span>সর্বমোট প্রদেয় ফি:</span>
                            <span class="text-emerald-700 text-xl font-extrabold">৳<span x-text="formatMoney(selectedBatch.effective_price)">{{ number_format($initialBatch['effective_price']) }}</span></span>
                        </div>
                    </div>

                </div>

                <!-- Support Box -->
                <div class="p-4 rounded-xl bg-gradient-to-br from-emerald-50 to-teal-50 border border-emerald-200 text-xs text-emerald-950 space-y-2 shadow-xs">
                    <span class="font-bold flex items-center gap-1.5 text-emerald-900 text-sm">
                        <i class="fa-solid fa-headset text-emerald-600"></i> ভর্তি বা পেমেন্টে কোনো জিজ্ঞাসা?
                    </span>
                    <p class="text-slate-600 leading-relaxed text-[11px]">
                        সরাসরি আমাদের হেল্পলাইনে কথা বলতে কল বা হোয়াটসঅ্যাপ করুন:
                    </p>
                    <a href="https://wa.me/8801332549549" target="_blank" class="inline-flex items-center gap-1.5 text-emerald-700 font-bold hover:underline">
                        <i class="fa-brands fa-whatsapp text-emerald-600 text-sm"></i> ০১৩৩২-৫৪৯৫৪৯ (01332-549549)
                    </a>
                    <div class="pt-2 border-t border-emerald-200/60 text-[11px] text-slate-700 space-y-1">
                        <div class="font-bold text-slate-800 text-[11px]">EPS গেটওয়েতে সমস্যা হলে সেন্ড মানি করুন:</div>
                        <div class="flex items-center justify-between">
                            <span><strong class="text-pink-700">বিকাশ (Personal):</strong> 01813-753610</span>
                        </div>
                        <div class="flex items-center justify-between">
                            <span><strong class="text-orange-700">নগদ (Personal):</strong> 01712-168109</span>
                        </div>
                    </div>
                </div>
            </div>

        </form>

        @endif

    </div>
</div>
@endsection
