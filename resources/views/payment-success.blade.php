@extends('layouts.app')

@section('title', 'ভর্তি সফল হয়েছে - Amar Bangla Academy')

@section('content')
<div class="py-16 bg-slate-50">
    <div class="max-w-3xl mx-auto px-4 sm:px-6 lg:px-8">
        <div class="bg-white rounded-3xl border border-slate-200/80 shadow-xl overflow-hidden">

            <!-- Green Celebration Banner -->
            <div class="bg-gradient-to-r from-emerald-600 to-teal-600 text-white p-8 text-center space-y-3">
                <div class="w-20 h-20 rounded-full bg-white text-emerald-600 flex items-center justify-center text-3xl mx-auto shadow-lg animate-bounce">
                    <i class="fa-solid fa-check"></i>
                </div>
                <h1 class="text-2xl sm:text-3xl font-black">অভিনন্দন, আপনার ভর্তি নিশ্চিত হয়েছে!</h1>
                <p class="text-emerald-100 text-sm max-w-md mx-auto">
                    আমার বাংলা একাডেমির লাইভ ক্লাসরুমে আপনাকে স্বাগতম। আপনার পেমেন্ট সফলভাবে সংরক্ষিত হয়েছে।
                </p>
            </div>

            <!-- Receipt & Details -->
            <div class="p-6 sm:p-8 space-y-6">

                <!-- Receipt Card -->
                <div class="bg-slate-50 rounded-2xl p-5 border border-slate-200/80 space-y-3 text-xs">
                    <div class="flex items-center justify-between pb-3 border-b border-slate-200 font-bold text-slate-800 text-sm">
                        <span>পেমেন্ট ইনভয়েস / রশিদ</span>
                        <span class="text-emerald-700 font-mono">TrxID: {{ $payment->transaction_id }}</span>
                    </div>
                    <div class="grid grid-cols-2 gap-3 text-slate-600">
                        <div><span class="block text-slate-400">কোর্সের নাম:</span><strong class="text-slate-800">{{ $payment->course->bangla_title ?? $payment->course->title }}</strong></div>
                        <div><span class="block text-slate-400">ব্যাচ:</span><strong class="text-slate-800">{{ $payment->course->batch_no }}</strong></div>
                        <div><span class="block text-slate-400">শিক্ষার্থীর নাম:</span><strong class="text-slate-800">{{ $payment->student_name }}</strong></div>
                        <div><span class="block text-slate-400">যোগাযোগের ইমেইল:</span><strong class="text-slate-800">{{ $payment->student_email }}</strong></div>
                        <div><span class="block text-slate-400">পেমেন্ট মেথড:</span><strong class="uppercase text-slate-800">{{ $payment->payment_method === 'eps' ? 'EPS অনলাইন গেটওয়ে (স্বয়ংক্রিয়)' : strtoupper($payment->payment_method) }}</strong></div>
                        <div><span class="block text-slate-400">পরিশোধিত অর্থ:</span><strong class="text-emerald-700 text-sm font-bold">৳{{ number_format($payment->amount) }}</strong></div>
                    </div>
                </div>

                <!-- WhatsApp Group Join CTA - Most Important Next Step -->
                <div class="rounded-2xl bg-gradient-to-br from-green-600 via-green-700 to-emerald-800 text-white p-6 shadow-xl shadow-green-700/25 relative overflow-hidden">
                    <div class="absolute top-0 right-0 w-36 h-36 bg-white/5 rounded-full -translate-y-1/2 translate-x-1/2 pointer-events-none"></div>
                    <div class="absolute bottom-0 left-0 w-24 h-24 bg-black/10 rounded-full translate-y-1/2 -translate-x-1/2 pointer-events-none"></div>
                    <div class="relative z-10 flex flex-col sm:flex-row items-start sm:items-center gap-4">
                        <div class="w-16 h-16 rounded-2xl bg-white/15 border border-white/20 flex items-center justify-center shrink-0">
                            <i class="fa-brands fa-whatsapp text-4xl text-white"></i>
                        </div>
                        <div class="flex-1">
                            <span class="inline-block text-xs font-bold bg-amber-400 text-amber-900 px-2.5 py-0.5 rounded-full animate-pulse">
                                ⭐ সবচেয়ে গুরুত্বপূর্ণ পদক্ষেপ!
                            </span>
                            <h3 class="text-xl sm:text-2xl font-black mt-1.5 leading-tight">এখনই WhatsApp গ্রুপে যুক্ত হন</h3>
                            <p class="text-green-100 text-xs sm:text-sm mt-1 leading-relaxed">
                                ক্লাসের লিঙ্ক, শিডিউল, নোটিশ ও সমস্ত গুরুত্বপূর্ণ আপডেট এই WhatsApp গ্রুপে পাঠানো হবে।
                                গ্রুপে না থাকলে কোনো তথ্য মিস হতে পারে।
                            </p>
                        </div>
                    </div>
                    <div class="relative z-10 mt-5">
                        <a href="https://chat.whatsapp.com/EeM7caaCmE79NR0BFdrwWh?s=sh&p=a&iam=2"
                           target="_blank" rel="noopener noreferrer" id="wa-group-join-btn"
                           class="w-full flex items-center justify-center gap-3 py-4 px-5 rounded-2xl bg-white text-green-700 font-black text-base sm:text-lg shadow-xl hover:bg-green-50 transition-all duration-200 transform hover:scale-[1.02] active:scale-[0.99] group border-2 border-white/80">
                            <i class="fa-brands fa-whatsapp text-2xl text-green-500 group-hover:scale-110 transition-transform"></i>
                            <span>WhatsApp গ্রুপে যুক্ত হন</span>
                            <i class="fa-solid fa-arrow-right text-sm group-hover:translate-x-1 transition-transform"></i>
                        </a>
                        <p class="text-center text-green-200 text-[11px] mt-2.5 flex items-center justify-center gap-1">
                            <i class="fa-solid fa-lock text-[10px]"></i>
                            <span>শুধুমাত্র ভর্তিকৃত শিক্ষার্থীদের জন্য নিরাপদ প্রাইভেট গ্রুপ &bull; সাপোর্ট: 01332-549549</span>
                        </p>
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
                            <i class="fa-solid fa-video"></i><span>সরাসরি জুম ক্লাসে যুক্ত হন</span>
                        </a>
                        <a href="https://meet.google.com" target="_blank" class="flex-1 py-3 px-4 rounded-xl bg-emerald-600 hover:bg-emerald-500 text-white font-bold text-xs text-center shadow transition flex items-center justify-center gap-2">
                            <i class="fa-solid fa-users-viewfinder"></i><span>গুগল মিট রুম ওপেন করুন</span>
                        </a>
                    </div>
                </div>

                <!-- Next Steps -->
                <div class="space-y-3 pt-2">
                    <h4 class="text-sm font-bold text-slate-800">পরবর্তী গুরুত্বপূর্ণ পদক্ষেপসমূহ:</h4>
                    <div class="grid grid-cols-1 sm:grid-cols-2 gap-3 text-xs">
                        <a href="https://chat.whatsapp.com/EeM7caaCmE79NR0BFdrwWh?s=sh&p=a&iam=2" target="_blank" rel="noopener noreferrer"
                           class="p-3.5 rounded-xl border border-green-200 hover:border-green-400 bg-green-50/50 hover:bg-green-50 transition flex items-center gap-3">
                            <i class="fa-brands fa-whatsapp text-green-600 text-xl"></i>
                            <div>
                                <span class="font-bold text-slate-900 block">WhatsApp গ্রুপে যুক্ত হন</span>
                                <span class="text-slate-500 text-[11px]">ক্লাস নোটিশ, আপডেট ও সাপোর্ট পেতে</span>
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
