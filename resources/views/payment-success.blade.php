@extends('layouts.app')

@section('title', 'ভর্তি সফল হয়েছে - Amar Bangla Academy')

@section('content')
<div class="py-16 bg-slate-50">
    <div class="max-w-3xl mx-auto px-4 sm:px-6 lg:px-8">
        
        <div class="bg-white rounded-3xl border border-slate-200/80 shadow-xl overflow-hidden">
            
            <!-- Green Celebration Banner -->
            <div class="bg-gradient-to-r from-emerald-600 to-teal-600 text-white p-8 text-center space-y-3">
                <div class="w-20 h-20 rounded-full bg-white text-emerald-600 flex items-center justify-center text-3xl mx-auto shadow-lg animate-bounce">
                    <i class="fa-solid fa-check"></i>
                </div>
                <h1 class="text-2xl sm:text-3xl font-black">অভিনন্দন, আপনার ভর্তি নিশ্চিত হয়েছে!</h1>
                <p class="text-emerald-100 text-sm max-w-md mx-auto">
                    আমার বাংলা একাডেমির লাইভ ক্লাসরুমে আপনাকে স্বাগতম। আপনার পেমেন্ট সফলভাবে সংরক্ষিত হয়েছে।
                </p>
            </div>

            <!-- Receipt & Details -->
            <div class="p-6 sm:p-8 space-y-6">
                
                <!-- Receipt Card -->
                <div class="bg-slate-50 rounded-2xl p-5 border border-slate-200/80 space-y-3 text-xs">
                    <div class="flex items-center justify-between pb-3 border-b border-slate-200 font-bold text-slate-800 text-sm">
                        <span>পেমেন্ট ইনভয়েস / রশিদ</span>
                        <span class="text-emerald-700 font-mono">TrxID: {{ $payment->transaction_id }}</span>
                    </div>

                    <div class="grid grid-cols-2 gap-3 text-slate-600">
                        <div>
                            <span class="block text-slate-400">কোর্সের নাম:</span>
                            <strong class="text-slate-800">{{ $payment->course->bangla_title ?? $payment->course->title }}</strong>
                        </div>
                        <div>
                            <span class="block text-slate-400">ব্যাচ:</span>
                            <strong class="text-slate-800">{{ $payment->course->batch_no }}</strong>
                        </div>
                        <div>
                            <span class="block text-slate-400">শিক্ষার্থীর নাম:</span>
                            <strong class="text-slate-800">{{ $payment->student_name }}</strong>
                        </div>
                        <div>
                            <span class="block text-slate-400">যোগাযোগের ইমেইল:</span>
                            <strong class="text-slate-800">{{ $payment->student_email }}</strong>
                        </div>
                        <div>
                            <span class="block text-slate-400">পেমেন্ট মেথড:</span>
                            <strong class="uppercase text-slate-800">{{ $payment->payment_method === 'eps' ? 'EPS অনলাইন গেটওয়ে (স্বয়ংক্রিয়)' : strtoupper($payment->payment_method) }}</strong>
                        </div>
                        <div>
                            <span class="block text-slate-400">পরিশোধিত অর্থ:</span>
                            <strong class="text-emerald-700 text-sm font-bold">৳{{ number_format($payment->amount) }}</strong>
                        </div>
                    </div>
                </div>

                <!-- Live Zoom / Google Meet Access Box -->
                <div class="rounded-2xl bg-gradient-to-br from-blue-900 to-slate-900 text-white p-6 space-y-4">
                    <div class="flex items-center justify-between">
                        <span class="px-3 py-1 rounded-full text-xs font-bold bg-blue-700 text-blue-100 flex items-center gap-1.5">
                            <span class="w-2 h-2 rounded-full bg-rose-400 animate-ping"></span> লাইভ ক্লাস অ্যাক্সেস লিঙ্ক
                        </span>
                        <span class="text-xs text-blue-200 font-mono">{{ $payment->course->live_platform }}</span>
                    </div>

                    <h3 class="text-lg font-bold">আপনার লাইভ ক্লাসরুমের তথ্য</h3>
                    
                    <div class="p-4 rounded-xl bg-slate-800/80 border border-blue-700/60 space-y-2 text-xs">
                        <div class="flex items-center justify-between">
                            <span class="text-slate-400">মিটিং আইডি (Zoom):</span>
                            <span class="font-mono font-bold text-blue-300">987 654 3210</span>
                        </div>
                        <div class="flex items-center justify-between">
                            <span class="text-slate-400">পাসকোড:</span>
                            <span class="font-mono font-bold text-amber-300">AMAR2026</span>
                        </div>
                        <div class="flex items-center justify-between">
                            <span class="text-slate-400">ক্লাস শিডিউল:</span>
                            <span class="font-bold text-slate-200">{{ $payment->course->live_schedule_text }}</span>
                        </div>
                    </div>

                    <div class="flex flex-col sm:flex-row gap-3 pt-2">
                        <a href="https://zoom.us" target="_blank" class="flex-1 py-3 px-4 rounded-xl bg-blue-600 hover:bg-blue-500 text-white font-bold text-xs text-center shadow transition flex items-center justify-center gap-2">
                            <i class="fa-solid fa-video"></i>
                            <span>সরাসরি জুম ক্লাসে যুক্ত হন</span>
                        </a>
                        <a href="https://meet.google.com" target="_blank" class="flex-1 py-3 px-4 rounded-xl bg-emerald-600 hover:bg-emerald-500 text-white font-bold text-xs text-center shadow transition flex items-center justify-center gap-2">
                            <i class="fa-solid fa-users-viewfinder"></i>
                            <span>গুগল মিট রুম ওপেন করুন</span>
                        </a>
                    </div>
                </div>

                <!-- Next Steps -->
                <div class="space-y-3 pt-2">
                    <h4 class="text-sm font-bold text-slate-800">পরবর্তী গুরুত্বপূর্ণ পদক্ষেপসমূহ:</h4>
                    <div class="grid grid-cols-1 sm:grid-cols-2 gap-3 text-xs">
                        <a href="https://discord.com" target="_blank" class="p-3.5 rounded-xl border border-slate-200 hover:border-indigo-400 hover:bg-indigo-50/30 transition flex items-center gap-3">
                            <i class="fa-brands fa-discord text-indigo-600 text-xl"></i>
                            <div>
                                <span class="font-bold text-slate-900 block">ডিসকর্ড কমিউনিটিতে যুক্ত হন</span>
                                <span class="text-slate-500 text-[11px]">২৪/৭ ডাউট সলভিং ও নোটিশ</span>
                            </div>
                        </a>

                        <a href="{{ route('home') }}#course-curriculum" class="p-3.5 rounded-xl border border-slate-200 hover:border-emerald-400 hover:bg-emerald-50/30 transition flex items-center gap-3">
                            <i class="fa-solid fa-book-open text-emerald-600 text-xl"></i>
                            <div>
                                <span class="font-bold text-slate-900 block">কোর্স কারিকুলাম দেখুন</span>
                                <span class="text-slate-500 text-[11px]">লেকচার ও প্রজেক্ট রুটিন</span>
                            </div>
                        </a>
                    </div>
                </div>

                <div class="pt-4 border-t border-slate-200 flex justify-between items-center text-xs">
                    <a href="{{ route('home') }}" class="text-emerald-700 font-bold hover:underline flex items-center gap-1">
                        <i class="fa-solid fa-arrow-left"></i> হোমপেজে ফিরে যান
                    </a>
                    <button onclick="window.print()" class="px-3.5 py-2 rounded-lg bg-slate-100 hover:bg-slate-200 text-slate-700 font-semibold transition">
                        <i class="fa-solid fa-print mr-1"></i> রশিদ প্রিন্ট করুন
                    </button>
                </div>

            </div>

        </div>

    </div>
</div>
@endsection
