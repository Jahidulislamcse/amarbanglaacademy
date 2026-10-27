@extends('layouts.app')

@section('title', 'অনলাইন পেমেন্ট ও ভর্তি - ' . ($course->bangla_title ?? $course->title))

@section('content')
<div class="bg-slate-100 py-10 sm:py-14" x-data="{ 
    paymentMode: 'eps', // 'eps' or 'manual'
    manualMethod: 'bkash',
    senderNumber: '',
    transactionId: '',
    simulateAutoTrx() {
        if(!this.transactionId) {
            this.transactionId = this.manualMethod.toUpperCase().substring(0,2) + Math.floor(10000000 + Math.random() * 90000000);
        }
    }
}">
    <div class="max-w-4xl mx-auto px-4 sm:px-6 lg:px-8">
        
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

        <!-- Checkout Form (Dynamic action based on paymentMode) -->
        <form :action="paymentMode === 'eps' ? '{{ route('eps.pay', $course->slug) }}' : '{{ route('courses.enroll', $course->slug) }}'" 
              method="POST" 
              class="grid grid-cols-1 md:grid-cols-12 gap-8 items-start">
            @csrf

            <!-- Left: Student Info & Payment Selector (7 cols) -->
            <div class="md:col-span-7 space-y-6">
                
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

                <!-- 2. Payment Gateway Mode Selector -->
                <div class="bg-white rounded-2xl p-5 sm:p-6 border border-slate-200 shadow-sm space-y-4">
                    <h3 class="font-bold text-base text-slate-900 flex items-center gap-2 pb-3 border-b border-slate-100">
                        <span class="w-6 h-6 rounded-full bg-emerald-600 text-white text-xs font-black flex items-center justify-center">২</span>
                        <span>পেমেন্ট মেথড ও পদ্ধতি নির্বাচন করুন</span>
                    </h3>

                    <!-- Payment Mode Switcher Tabs -->
                    <div class="space-y-3">
                        
                        <!-- Option A: EPS Online Gateway (Recommended) -->
                        <label @click="paymentMode = 'eps'" 
                               class="block cursor-pointer p-4 rounded-xl border-2 transition relative"
                               :class="paymentMode === 'eps' ? 'border-emerald-600 bg-emerald-50/50 shadow-xs' : 'border-slate-200 hover:border-slate-300 bg-white'">
                            <div class="flex items-start justify-between gap-3">
                                <div class="flex items-center gap-3">
                                    <input type="radio" name="payment_mode_radio" value="eps" :checked="paymentMode === 'eps'" class="mt-0.5 text-emerald-600 focus:ring-emerald-500 h-4 w-4">
                                    <div>
                                        <div class="flex items-center gap-2 flex-wrap">
                                            <span class="font-bold text-sm text-slate-900">EPS অনলাইন পেমেন্ট (স্বয়ংক্রিয়)</span>
                                            <span class="text-[10px] font-extrabold uppercase bg-emerald-600 text-white px-2 py-0.5 rounded-full">দ্রুত ও নির্ভরযোগ্য</span>
                                        </div>
                                        <p class="text-xs text-slate-600 mt-0.5">
                                            বিকাশ, নগদ, রকেট, উপাই, ডেবিট/ক্রেডিট কার্ড দিয়ে সাথে সাথে ইনস্ট্যান্ট পেমেন্ট
                                        </p>
                                    </div>
                                </div>
                            </div>

                            <!-- Supported Gateways Badge Row -->
                            <div class="mt-3 pt-3 border-t border-emerald-100/80 flex items-center gap-2 flex-wrap text-xs">
                                <span class="px-2.5 py-1 rounded-md bg-pink-100 text-pink-800 font-bold text-[11px] flex items-center gap-1">
                                    <span class="w-2 h-2 rounded-full bg-pink-600"></span> বিকাশ
                                </span>
                                <span class="px-2.5 py-1 rounded-md bg-orange-100 text-orange-800 font-bold text-[11px] flex items-center gap-1">
                                    <span class="w-2 h-2 rounded-full bg-orange-600"></span> নগদ
                                </span>
                                <span class="px-2.5 py-1 rounded-md bg-purple-100 text-purple-800 font-bold text-[11px] flex items-center gap-1">
                                    <span class="w-2 h-2 rounded-full bg-purple-600"></span> রকেট
                                </span>
                                <span class="px-2.5 py-1 rounded-md bg-blue-100 text-blue-800 font-bold text-[11px] flex items-center gap-1">
                                    <i class="fa-solid fa-credit-card"></i> ভিসা / মাস্টারকার্ড
                                </span>
                            </div>
                        </label>

                        <!-- Option B: Manual Send Money & TrxID Entry -->
                        <label @click="paymentMode = 'manual'" 
                               class="block cursor-pointer p-4 rounded-xl border-2 transition"
                               :class="paymentMode === 'manual' ? 'border-emerald-600 bg-emerald-50/50 shadow-xs' : 'border-slate-200 hover:border-slate-300 bg-white'">
                            <div class="flex items-center gap-3">
                                <input type="radio" name="payment_mode_radio" value="manual" :checked="paymentMode === 'manual'" class="text-emerald-600 focus:ring-emerald-500 h-4 w-4">
                                <div>
                                    <span class="font-bold text-sm text-slate-900 block">ম্যানুয়াল পেমেন্ট (TrxID সাবমিশন)</span>
                                    <p class="text-xs text-slate-500 mt-0.5">
                                        আপনি যদি আগেই টাকা পাঠিয়ে থাকেন তবে TrxID দিয়ে সাবমিট করুন
                                    </p>
                                </div>
                            </div>
                        </label>

                    </div>

                    <!-- Dynamic Manual Payment Fields (shown only when paymentMode === 'manual') -->
                    <div x-show="paymentMode === 'manual'" x-transition class="p-4 rounded-xl bg-slate-50 border border-slate-200 text-xs text-slate-700 space-y-4">
                        
                        <!-- Manual Method Sub-selector -->
                        <div class="grid grid-cols-3 gap-2">
                            <label @click="manualMethod = 'bkash'" 
                                   class="cursor-pointer border rounded-lg p-2 text-center text-xs font-bold transition"
                                   :class="manualMethod === 'bkash' ? 'border-pink-600 bg-pink-100/60 text-pink-800' : 'border-slate-200 bg-white text-slate-700'">
                                <input type="radio" name="payment_method" value="bkash" class="sr-only" :checked="manualMethod === 'bkash'">
                                বিকাশ (Personal)
                            </label>
                            <label @click="manualMethod = 'nagad'" 
                                   class="cursor-pointer border rounded-lg p-2 text-center text-xs font-bold transition"
                                   :class="manualMethod === 'nagad' ? 'border-orange-500 bg-orange-100/60 text-orange-800' : 'border-slate-200 bg-white text-slate-700'">
                                <input type="radio" name="payment_method" value="nagad" class="sr-only" :checked="manualMethod === 'nagad'">
                                নগদ (Personal)
                            </label>
                            <label @click="manualMethod = 'rocket'" 
                                   class="cursor-pointer border rounded-lg p-2 text-center text-xs font-bold transition"
                                   :class="manualMethod === 'rocket' ? 'border-purple-600 bg-purple-100/60 text-purple-800' : 'border-slate-200 bg-white text-slate-700'">
                                <input type="radio" name="payment_method" value="rocket" class="sr-only" :checked="manualMethod === 'rocket'">
                                রকেট (Personal)
                            </label>
                        </div>

                        <!-- Manual Number & Instructions -->
                        <div class="p-3 bg-white rounded-lg border border-slate-200 space-y-2">
                            <div class="flex items-center justify-between font-bold text-slate-800">
                                <span>টাকা পাঠানোর নম্বর:</span>
                                <strong class="text-emerald-700 text-sm select-all font-mono">01813-753610</strong>
                            </div>
                            <p class="text-[11px] text-slate-600 leading-relaxed">
                                'Send Money' করে প্রাপ্ত <strong>Transaction ID (TrxID)</strong> নিচে লিখুন।
                            </p>
                        </div>

                        <div class="grid grid-cols-1 sm:grid-cols-2 gap-3">
                            <div>
                                <label class="block font-semibold text-slate-700 mb-1">প্রেরকের মোবাইল নম্বর</label>
                                <input type="text" name="sender_phone" x-model="senderNumber" placeholder="01XXXXXXXXX" 
                                       class="w-full px-3 py-2 rounded-lg border border-slate-300 focus:outline-none focus:ring-1 focus:ring-emerald-500 text-xs">
                            </div>
                            <div>
                                <div class="flex items-center justify-between mb-1">
                                    <label class="block font-semibold text-slate-700">Transaction ID (TrxID)</label>
                                    <button type="button" @click="simulateAutoTrx()" class="text-[10px] text-emerald-700 hover:underline font-semibold">
                                        ডেমো TrxID
                                    </button>
                                </div>
                                <input type="text" name="transaction_id" x-model="transactionId" placeholder="যেমন: 9J7X298" 
                                       class="w-full px-3 py-2 rounded-lg border border-slate-300 focus:outline-none focus:ring-1 focus:ring-emerald-500 text-xs font-mono uppercase">
                            </div>
                        </div>
                    </div>

                    <!-- Submit / Pay Now Button -->
                    <button type="submit" 
                            class="w-full py-4 rounded-xl bg-gradient-to-r from-emerald-600 via-teal-600 to-emerald-700 hover:from-emerald-700 hover:to-teal-800 text-white font-black text-sm sm:text-base shadow-lg shadow-emerald-600/30 hover:shadow-emerald-600/50 transition duration-200 flex items-center justify-center gap-2">
                        <i class="fa-solid fa-lock text-sm"></i>
                        <span x-text="paymentMode === 'eps' ? 'EPS অনলাইন গেটওয়েতে পেমেন্ট করুন (৳{{ number_format($course->effective_price) }})' : 'ভর্তি নিশ্চিত করুন (৳{{ number_format($course->effective_price) }})'"></span>
                        <i class="fa-solid fa-arrow-right text-xs"></i>
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

                </div>

            </div>

            <!-- Right: Order Summary (5 cols) -->
            <div class="md:col-span-5 space-y-4">
                <div class="bg-white rounded-2xl p-5 sm:p-6 border border-slate-200 shadow-sm space-y-4">
                    <h3 class="font-bold text-base text-slate-900 pb-3 border-b border-slate-100 flex items-center justify-between">
                        <span>অর্ডার বিবরণী</span>
                        <span class="text-xs font-semibold px-2.5 py-0.5 rounded-full bg-emerald-100 text-emerald-800">১ম ব্যাচ</span>
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
                            <span>ক্লাস শুরু:</span>
                            <span class="font-bold text-slate-800">৫ নভেম্বর ২০২৬</span>
                        </div>
                        <div class="flex justify-between">
                            <span>ক্লাসের সময়:</span>
                            <span class="font-bold text-slate-800">বৃহস্পতি, শুক্র ও শনি (রাত ৯টা)</span>
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
                        <div class="flex justify-between text-slate-500">
                            <span>নিয়মিত কোর্স ফি:</span>
                            <span class="line-through text-slate-400 font-semibold">৳{{ number_format($course->regular_price) }}</span>
                        </div>
                        @if($course->discount_price)
                            <div class="flex justify-between text-rose-600 font-bold bg-rose-50 px-2 py-1 rounded-md">
                                <span>১ম ব্যাচের বিশেষ ছাড় (প্রথম ৫০ জন):</span>
                                <span>- ৳{{ number_format($course->regular_price - $course->discount_price) }}</span>
                            </div>
                        @endif
                        <div class="flex justify-between text-lg font-black text-slate-900 pt-2 border-t border-dashed border-slate-200">
                            <span>সর্বমোট প্রদেয় ফি:</span>
                            <span class="text-emerald-700 text-xl font-extrabold">৳{{ number_format($course->effective_price) }}</span>
                        </div>
                    </div>

                </div>

                <!-- Support Box -->
                <div class="p-4 rounded-xl bg-gradient-to-br from-emerald-50 to-teal-50 border border-emerald-200 text-xs text-emerald-950 space-y-1.5 shadow-xs">
                    <span class="font-bold flex items-center gap-1.5 text-emerald-900 text-sm">
                        <i class="fa-solid fa-headset text-emerald-600"></i> ভর্তি বা পেমেন্টে কোনো জিজ্ঞাসা?
                    </span>
                    <p class="text-slate-600 leading-relaxed">
                        সরাসরি আমাদের হেল্পলাইনে কথা বলতে কল বা হোয়াটসঅ্যাপ করুন:
                    </p>
                    <a href="https://wa.me/8801700000000" target="_blank" class="inline-flex items-center gap-1.5 text-emerald-700 font-bold hover:underline">
                        <i class="fa-brands fa-whatsapp text-emerald-600 text-sm"></i> +৮৮০ ১৭০০-০০০০০০ (হোয়াটসঅ্যাপ)
                    </a>
                </div>
            </div>

        </form>

    </div>
</div>
@endsection
