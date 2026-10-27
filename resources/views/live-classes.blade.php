@extends('layouts.app')

@section('title', 'লাইভ ক্লাস পোর্টাল (Zoom & Google Meet) - Amar Bangla Academy')

@section('content')
<div class="bg-slate-50 py-12">
    <div class="max-w-6xl mx-auto px-4 sm:px-6 lg:px-8 space-y-10">
        
        <!-- Header -->
        <div class="text-center max-w-2xl mx-auto space-y-3">
            <span class="inline-flex items-center gap-1.5 px-3 py-1 rounded-full text-xs font-bold bg-rose-100 text-rose-800">
                <span class="w-2 h-2 rounded-full bg-rose-600 animate-ping"></span>
                <span>লাইভ ইন্টারঅ্যাক্টিভ ক্লাসরুম</span>
            </span>
            <h1 class="text-3xl font-extrabold text-slate-900">
                জুম ও গুগল মিট লাইভ ক্লাস শিডিউল
            </h1>
            <p class="text-slate-600 text-sm">
                সকল কোর্সের চলমান এবং আসন্ন লাইভ ক্লাসের রুটিন ও সরাসরি জয়েনিং লিঙ্ক
            </p>
        </div>

        <!-- Live Platform Highlights -->
        <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
            
            <div class="bg-gradient-to-r from-blue-900 to-indigo-950 text-white rounded-2xl p-6 border border-blue-800 space-y-3">
                <div class="flex items-center gap-3">
                    <span class="w-10 h-10 rounded-xl bg-blue-600 flex items-center justify-center text-lg">
                        <i class="fa-solid fa-video"></i>
                    </span>
                    <div>
                        <h3 class="font-bold text-base">Zoom লাইভ ক্লাসরুম</h3>
                        <p class="text-xs text-blue-200">হাই-কোয়ালিটি স্ক্রিন শেয়ারিং ও রিয়েল-টাইম অডিও/ভিডিও প্রশ্নোত্তর</p>
                    </div>
                </div>
                <p class="text-xs text-blue-100 leading-relaxed">
                    প্রতিটি সেশনে ইন্সট্রাক্টর সরাসরি কোড লিখে শেখান। ক্লাসের মাঝেই হ্যান্ড রেইজ করে কথা বলতে পারবেন।
                </p>
            </div>

            <div class="bg-gradient-to-r from-emerald-900 to-teal-950 text-white rounded-2xl p-6 border border-emerald-800 space-y-3">
                <div class="flex items-center gap-3">
                    <span class="w-10 h-10 rounded-xl bg-emerald-600 flex items-center justify-center text-lg">
                        <i class="fa-solid fa-users-viewfinder"></i>
                    </span>
                    <div>
                        <h3 class="font-bold text-base">Google Meet ডাউট সলভিং রুম</h3>
                        <p class="text-xs text-emerald-200">প্রতিদিন বিকেল ৫টা থেকে রাত ১২টা পর্যন্ত ওয়ান-অন-ওয়ান সাপোর্ট</p>
                    </div>
                </div>
                <p class="text-xs text-emerald-100 leading-relaxed">
                    কোডিংয়ের যেকোনো এরর বা সমস্যা সমাধানে গুগল মিটে সরাসরি মেন্টরের সাথে স্ক্রিন শেয়ার করুন।
                </p>
            </div>

        </div>

        <!-- Courses Live Classes Schedule Table -->
        <div class="space-y-6">
            <h2 class="text-xl font-bold text-slate-900">কোর্স ভিত্তিক লাইভ ক্লাসসমূহ:</h2>

            @foreach($courses as $course)
                <div class="bg-white rounded-2xl border border-slate-200 p-6 space-y-4">
                    <div class="flex flex-col sm:flex-row sm:items-center justify-between pb-3 border-b border-slate-100 gap-2">
                        <div>
                            <span class="text-xs text-emerald-700 font-bold block">{{ $course->batch_no }}</span>
                            <h3 class="text-lg font-bold text-slate-900">{{ $course->bangla_title ?? $course->title }}</h3>
                        </div>
                        <div class="text-xs text-slate-500 flex items-center gap-2">
                            <span class="px-2.5 py-1 rounded bg-slate-100 font-semibold"><i class="fa-solid fa-clock mr-1 text-slate-400"></i>{{ $course->live_schedule_text }}</span>
                            <a href="{{ route('courses.checkout', $course->slug) }}" class="px-3 py-1.5 rounded-lg bg-emerald-600 hover:bg-emerald-700 text-white font-bold transition">
                                এনরোল করুন
                            </a>
                        </div>
                    </div>

                    <div class="divide-y divide-slate-100 text-xs">
                        @forelse($course->modules as $mod)
                            @foreach($mod->lessons->whereIn('type', ['live_zoom', 'live_meet']) as $les)
                                <div class="py-3 flex flex-col sm:flex-row sm:items-center justify-between gap-3">
                                    <div class="flex items-center gap-3">
                                        @if($les->type === 'live_zoom')
                                            <span class="px-2 py-1 rounded bg-blue-100 text-blue-800 font-bold text-[10px] uppercase">Zoom Live</span>
                                        @else
                                            <span class="px-2 py-1 rounded bg-emerald-100 text-emerald-800 font-bold text-[10px] uppercase">Google Meet</span>
                                        @endif
                                        <span class="font-medium text-slate-800 text-sm">{{ $les->title }}</span>
                                    </div>

                                    <div class="flex items-center gap-3">
                                        <span class="text-slate-400"><i class="fa-regular fa-clock mr-1"></i>{{ $les->duration_minutes }} মিনিট</span>
                                        <a href="{{ route('courses.checkout', $course->slug) }}" class="px-3 py-1.5 rounded-lg bg-slate-800 hover:bg-slate-900 text-amber-300 font-bold transition flex items-center gap-1.5">
                                            <i class="fa-solid fa-arrow-up-right-from-square text-[10px]"></i>
                                            <span>জয়েন রুম</span>
                                        </a>
                                    </div>
                                </div>
                            @endforeach
                        @empty
                            <p class="text-slate-400 py-2">কোনো লাইভ ক্লাস নেই।</p>
                        @endforelse
                    </div>
                </div>
            @endforeach
        </div>

    </div>
</div>
@endsection
