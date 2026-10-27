@extends('layouts.app')

@section('title', 'অনলাইন পেমেন্ট ও ভর্তি - ' . ($course->bangla_title ?? $course->title))

@section('content')
<div class="bg-slate-100 py-12" x-data="{ 
    paymentMethod: 'bkash',
    senderNumber: '',
    transactionId: '',
    simulateAutoTrx() {
        if(!this.transactionId) {
            this.transactionId = this.paymentMethod.toUpperCase().substring(0,2) + Math.floor(10000000 + Math.random() * 90000000);
        }
    }
}">
    <div class="max-w-4xl mx-auto px-4 sm:px-6 lg:px-8">
        
        <!-- Header -->
        <div class="text-center mb-8">
            <span class="text-xs font-bold text-emerald-700 uppercase tracking-widest bg-emerald-100 px-3 py-1 rounded-full">
                নিরাপদ অনলাইন পেমেন্ট গেটওয়ে
            </span>
            <h1 class="text-2xl sm:text-3xl font-extrabold text-slate-900 mt-2">
                ভর্তি প্রক্রিয়া ও ফি পরিশোধ
            </h1>
            <p class="text-xs sm:text-sm text-slate-500 mt-1">
                বিকাশ, নগদ, রকেট অথবা কার্ডের মাধ্যমে ইনস্ট্যান্ট ফি পরিশোধ করে ক্লাসে যুক্ত হন
            </p>
        </div>

        <form action="{{ route('courses.enroll', $course->slug) }}" method="POST" class="grid grid-cols-1 md:grid-cols-12 gap-8 items-start">
            @csrf

            <!-- Left: Student Info & Payment Selector (8 cols) -->
            <div class="md:col-span-7 space-y-6">
                
                <!-- 1. Student Details -->
                <div class="bg-white rounded-2xl p-6 border border-slate-200/80 shadow-xs space-y-4">
                    <h3 class="font-bold text-base text-slate-900 flex items-center gap-2">
                        <span class="w-6 h-6 rounded-full bg-emerald-600 text-white text-xs flex items-center justify-center">১</span>
                        <span>আপনার তথ্য (শিক্ষার্থী)</span>
                    </h3>

                    <div class="space-y-3 text-xs">
                        <div>
                            <label class="block font-semibold text-slate-700 mb-1">পূর্ণ নাম <span class="text-rose-500">*</span></label>
                            <input type="text" name="student_name" required placeholder="যেমন: মো: রহিম উদ্দিন" 
                                   class="w-full px-3.5 py-2.5 rounded-xl border border-slate-300 focus:outline-none focus:ring-2 focus:ring-emerald-500 text-sm">
                        </div>

                        <div>
                            <label class="block font-semibold text-slate-700 mb-1">ইমেইল অ্যাড্রেস <span class="text-rose-500">*</span> (ক্লাসের লিংক পাঠানো হবে)</label>
                            <input type="email" name="student_email" required placeholder="rahim@example.com" 
                                   class="w-full px-3.5 py-2.5 rounded-xl border border-slate-300 focus:outline-none focus:ring-2 focus:ring-emerald-500 text-sm">
                        </div>

                        <div>
                            <label class="block font-semibold text-slate-700 mb-1">মোবাইল নম্বর <span class="text-rose-500">*</span> (হোয়াটসঅ্যাপ ও এসএমএস নোটিফিকেশন)</label>
                            <input type="tel" name="student_phone" required placeholder="017XXXXXXXX" 
                                   class="w-full px-3.5 py-2.5 rounded-xl border border-slate-300 focus:outline-none focus:ring-2 focus:ring-emerald-500 text-sm">
                        </div>
                    </div>
                </div>

                <!-- 2. Payment Method Selector -->
                <div class="bg-white rounded-2xl p-6 border border-slate-200/80 shadow-xs space-y-4">
                    <h3 class="font-bold text-base text-slate-900 flex items-center gap-2">
                        <span class="w-6 h-6 rounded-full bg-emerald-600 text-white text-xs flex items-center justify-center">২</span>
                        <span>পেমেন্ট মেথড নির্বাচন করুন</span>
                    </h3>

                    <!-- Radio Buttons Cards -->
                    <div class="grid grid-cols-2 sm:grid-cols-4 gap-3">
                        
                        <!-- bKash -->
                        <label @click="paymentMethod = 'bkash'" 
                               class="cursor-pointer border-2 rounded-xl p-3 text-center transition flex flex-col items-center justify-center gap-1.5"
                               :class="paymentMethod === 'bkash' ? 'border-pink-600 bg-pink-50/50' : 'border-slate-200 hover:border-slate-300'">
                            <input type="radio" name="payment_method" value="bkash" class="sr-only" checked>
                            <span class="w-8 h-8 rounded-lg bg-pink-600 text-white flex items-center justify-center text-xs font-black">
                                bK
                            </span>
                            <span class="text-xs font-bold text-slate-800">বিকাশ</span>
                            <span class="text-[10px] text-pink-700 font-semibold">ইনস্ট্যান্ট</span>
                        </label>

                        <!-- Nagad -->
                        <label @click="paymentMethod = 'nagad'" 
                               class="cursor-pointer border-2 rounded-xl p-3 text-center transition flex flex-col items-center justify-center gap-1.5"
                               :class="paymentMethod === 'nagad' ? 'border-orange-500 bg-orange-50/50' : 'border-slate-200 hover:border-slate-300'">
                            <input type="radio" name="payment_method" value="nagad" class="sr-only">
                            <span class="w-8 h-8 rounded-lg bg-orange-500 text-white flex items-center justify-center text-xs font-black">
                                NG
                            </span>
                            <span class="text-xs font-bold text-slate-800">নগদ</span>
                            <span class="text-[10px] text-orange-600 font-semibold">চার্জ ফ্রি</span>
                        </label>

                        <!-- Rocket -->
                        <label @click="paymentMethod = 'rocket'" 
                               class="cursor-pointer border-2 rounded-xl p-3 text-center transition flex flex-col items-center justify-center gap-1.5"
                               :class="paymentMethod === 'rocket' ? 'border-purple-600 bg-purple-50/50' : 'border-slate-200 hover:border-slate-300'">
                            <input type="radio" name="payment_method" value="rocket" class="sr-only">
                            <span class="w-8 h-8 rounded-lg bg-purple-600 text-white flex items-center justify-center text-xs font-black">
                                RK
                            </span>
                            <span class="text-xs font-bold text-slate-800">রকেট</span>
                            <span class="text-[10px] text-purple-700 font-semibold">DBBL</span>
                        </label>

                        <!-- Card / SSL -->
                        <label @click="paymentMethod = 'card'" 
                               class="cursor-pointer border-2 rounded-xl p-3 text-center transition flex flex-col items-center justify-center gap-1.5"
                               :class="paymentMethod === 'card' ? 'border-blue-600 bg-blue-50/50' : 'border-slate-200 hover:border-slate-300'">
                            <input type="radio" name="payment_method" value="card" class="sr-only">
                            <span class="w-8 h-8 rounded-lg bg-blue-600 text-white flex items-center justify-center text-xs font-black">
                                <i class="fa-solid fa-credit-card"></i>
                            </span>
                            <span class="text-xs font-bold text-slate-800">কার্ড</span>
                            <span class="text-[10px] text-blue-700 font-semibold">Visa/Master</span>
                        </label>

                    </div>

                    <!-- Dynamic Instructions Based on Selected Gateway -->
                    <div class="p-4 rounded-xl bg-slate-50 border border-slate-200 text-xs text-slate-700 space-y-3">
                        <div class="flex items-center justify-between font-bold text-slate-800">
                            <span x-text="paymentMethod === 'bkash' ? 'বিকাশ পেমেন্ট নির্দেশনা:' : (paymentMethod === 'nagad' ? 'নগদ পেমেন্ট নির্দেশনা:' : (paymentMethod === 'rocket' ? 'রকেট পেমেন্ট নির্দেশনা:' : 'কার্ড পেমেন্ট নির্দেশনা:'))"></span>
                            <button type="button" @click="simulateAutoTrx()" class="text-[11px] text-emerald-700 hover:underline font-semibold">
                                <i class="fa-solid fa-wand-magic-sparkles mr-1"></i> ডেমো TrxID বসান
                            </button>
                        </div>

                        <ol class="list-decimal list-inside space-y-1.5 text-slate-600 leading-relaxed">
                            <li>আপনার বিকাশ/নগদ অ্যাপে যান এবং <strong>'Make Payment'</strong> অথবা <strong>'Send Money'</strong> অপশনে ক্লিক করুন।</li>
                            <li>আমাদের মার্চেন্ট নম্বর: <strong class="text-emerald-700 select-all font-mono text-sm">01712-345678</strong></li>
                            <li>মোট টাকার পরিমাণ: <strong class="text-slate-900 font-bold">৳{{ number_format($course->effective_price) }}</strong> লিখুন।</li>
                            <li>রেফারেন্স হিসেবে আপনার নাম লিখুন এবং পেমেন্ট সম্পন্ন করুন।</li>
                            <li>পেমেন্ট শেষে পাওয়া <strong>Transaction ID (TrxID)</strong> টি নিচের বক্সে লিখে সাবমিট করুন।</li>
                        </ol>

                        <div class="pt-2 grid grid-cols-1 sm:grid-cols-2 gap-3">
                            <div>
                                <label class="block font-semibold text-slate-700 mb-1">প্রেরকের বিকাশ/নগদ নম্বর</label>
                                <input type="text" name="sender_phone" x-model="senderNumber" placeholder="01XXXXXXXXX" 
                                       class="w-full px-3 py-2 rounded-lg border border-slate-300 focus:outline-none focus:ring-1 focus:ring-emerald-500 text-xs">
                            </div>
                            <div>
                                <label class="block font-semibold text-slate-700 mb-1">Transaction ID (TrxID)</label>
                                <input type="text" name="transaction_id" x-model="transactionId" placeholder="যেমন: BK98765432" 
                                       class="w-full px-3 py-2 rounded-lg border border-slate-300 focus:outline-none focus:ring-1 focus:ring-emerald-500 text-xs font-mono uppercase">
                            </div>
                        </div>
                    </div>

                    <!-- Submit Button -->
                    <button type="submit" class="w-full py-4 rounded-xl bg-gradient-to-r from-emerald-600 to-teal-600 hover:from-emerald-700 hover:to-teal-700 text-white font-extrabold text-sm sm:text-base shadow-lg shadow-emerald-600/30 hover:shadow-emerald-600/50 transition duration-200 flex items-center justify-center gap-2">
                        <i class="fa-solid fa-lock text-sm"></i>
                        <span>ভর্তি নিশ্চিত করুন ও ক্লাসে জয়েন করুন (৳{{ number_format($course->effective_price) }})</span>
                    </button>

                    <p class="text-center text-[11px] text-slate-400">
                        <i class="fa-solid fa-shield-halved text-emerald-600"></i> আপনার ডেটা ও পেমেন্ট সম্পূর্ণ 256-bit SSL দ্বারা এনক্রিপ্টেড ও সুরক্ষিত।
                    </p>

                </div>

            </div>

            <!-- Right: Order Summary (5 cols) -->
            <div class="md:col-span-5 space-y-4">
                <div class="bg-white rounded-2xl p-6 border border-slate-200/80 shadow-xs space-y-4">
                    <h3 class="font-bold text-base text-slate-900 pb-3 border-b border-slate-100">
                        অর্ডার সামারি
                    </h3>

                    <div class="flex items-start gap-3">
                        <img src="{{ $course->thumbnail }}" alt="{{ $course->title }}" class="w-20 h-14 object-cover rounded-lg border border-slate-200 flex-shrink-0">
                        <div>
                            <h4 class="font-bold text-xs text-slate-900 leading-snug">
                                {{ $course->bangla_title ?? $course->title }}
                            </h4>
                            <span class="text-[11px] text-emerald-700 font-semibold block mt-0.5">{{ $course->batch_no }}</span>
                        </div>
                    </div>

                    <div class="space-y-2 text-xs text-slate-600 pt-2 border-t border-slate-100">
                        <div class="flex justify-between">
                            <span>লাইভ ক্লাস প্ল্যাটফর্ম:</span>
                            <span class="font-bold text-slate-800">{{ $course->live_platform }}</span>
                        </div>
                        <div class="flex justify-between">
                            <span>কোর্স ব্যাপ্তিকাল:</span>
                            <span class="font-bold text-slate-800">{{ $course->total_weeks }} সপ্তাহ ({{ $course->total_hours }}+ ঘণ্টা)</span>
                        </div>
                        <div class="flex justify-between">
                            <span>লাইভ লেকচার সংখ্যা:</span>
                            <span class="font-bold text-slate-800">{{ $course->total_lectures }}টি</span>
                        </div>
                        <div class="flex justify-between">
                            <span>এক্সাম ও সার্টিফিকেট:</span>
                            <span class="font-bold text-emerald-700">অন্তর্ভুক্ত</span>
                        </div>
                    </div>

                    <!-- Fee calculation -->
                    <div class="pt-3 border-t border-slate-100 space-y-1.5 text-xs">
                        <div class="flex justify-between text-slate-500">
                            <span>নিয়মিত ফি:</span>
                            <span class="line-through">৳{{ number_format($course->regular_price) }}</span>
                        </div>
                        @if($course->discount_price)
                            <div class="flex justify-between text-rose-600 font-semibold">
                                <span>স্কলারশিপ ছাড় ({{ $course->discount_percentage }}%):</span>
                                <span>- ৳{{ number_format($course->regular_price - $course->discount_price) }}</span>
                            </div>
                        @endif
                        <div class="flex justify-between text-base font-black text-slate-900 pt-2 border-t border-dashed border-slate-200">
                            <span>সর্বমোট প্রদেয়:</span>
                            <span class="text-emerald-700">৳{{ number_format($course->effective_price) }}</span>
                        </div>
                    </div>

                </div>

                <!-- Support Box -->
                <div class="p-4 rounded-xl bg-emerald-50 border border-emerald-200 text-xs text-emerald-900 space-y-1">
                    <span class="font-bold flex items-center gap-1.5">
                        <i class="fa-solid fa-headset text-emerald-700"></i> পেমেন্টে কোনো সমস্যা হচ্ছে?
                    </span>
                    <p class="text-slate-600">আমাদের হেল্পলাইনে সরাসরি ফোন করুন: <strong class="text-emerald-800">+৮৮০ ১৭০০-০০০০০০</strong></p>
                </div>
            </div>

        </form>

    </div>
</div>
@endsection
