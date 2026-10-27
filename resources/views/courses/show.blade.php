@extends('layouts.app')

@section('title', ($course->bangla_title ?? $course->title) . ' - Amar Bangla Academy')

@section('content')
<!-- Course Header Hero -->
<section class="bg-gradient-to-b from-emerald-950 via-slate-900 to-slate-900 text-white py-12 sm:py-16">
    <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8">
        <div class="grid grid-cols-1 lg:grid-cols-12 gap-8 items-center">
            
            <div class="lg:col-span-8 space-y-4">
                <div class="flex flex-wrap items-center gap-2">
                    <span class="px-3 py-1 rounded-full text-xs font-bold bg-emerald-800 text-emerald-200">
                        {{ $course->category }}
                    </span>
                    <span class="px-3 py-1 rounded-full text-xs font-bold bg-rose-900 text-rose-200 flex items-center gap-1">
                        <span class="w-1.5 h-1.5 rounded-full bg-rose-400 animate-pulse"></span> {{ $course->live_platform }}
                    </span>
                    <span class="px-3 py-1 rounded-full text-xs font-bold bg-slate-800 text-slate-300">
                        {{ $course->batch_no }}
                    </span>
                </div>

                <h1 class="text-2xl sm:text-4xl font-extrabold text-white leading-tight">
                    {{ $course->bangla_title ?? $course->title }}
                </h1>

                <p class="text-sm sm:text-base text-slate-300 leading-relaxed">
                    {{ $course->subtitle }}
                </p>

                <!-- Instructors row -->
                <div class="pt-2 flex flex-wrap items-center gap-4 text-xs text-slate-300">
                    @foreach($course->instructors as $inst)
                        <div class="flex items-center gap-2">
                            <img src="{{ $inst->avatar }}" alt="{{ $inst->name }}" class="w-8 h-8 rounded-full object-cover border border-emerald-400">
                            <div>
                                <span class="font-bold text-white block">{{ $inst->bangla_name ?? $inst->name }}</span>
                                <span class="text-slate-400 text-[11px]">{{ $inst->title }}</span>
                            </div>
                        </div>
                    @endforeach
                </div>

                <!-- Stats summary -->
                <div class="flex flex-wrap gap-4 pt-3 text-xs text-slate-300 border-t border-slate-800">
                    <span><i class="fa-regular fa-clock mr-1 text-emerald-400"></i> {{ $course->total_weeks }} সপ্তাহ ({{ $course->total_hours }}+ ঘণ্টা)</span>
                    <span><i class="fa-solid fa-play-circle mr-1 text-teal-400"></i> {{ $course->total_lectures }}টি লাইভ লেকচার</span>
                    <span><i class="fa-solid fa-clipboard-check mr-1 text-amber-400"></i> {{ $course->total_exams }}টি অনলাইন পরীক্ষা</span>
                    <span><i class="fa-solid fa-award mr-1 text-rose-400"></i> ডিজিটাল সার্টিফিকেট</span>
                </div>
            </div>

            <div class="lg:col-span-4">
                <div class="bg-white rounded-2xl p-6 text-slate-900 shadow-2xl border border-slate-200 space-y-5">
                    <div class="relative aspect-video rounded-xl overflow-hidden bg-slate-100">
                        <img src="{{ $course->thumbnail }}" alt="{{ $course->title }}" class="w-full h-full object-cover">
                    </div>

                    <div class="flex items-baseline justify-between">
                        <div>
                            <span class="text-xs text-slate-400 block">কোর্স ফি</span>
                            <div class="flex items-baseline gap-2">
                                <span class="text-3xl font-black text-emerald-700">৳{{ number_format($course->effective_price) }}</span>
                                @if($course->discount_price)
                                    <span class="text-sm text-slate-400 line-through">৳{{ number_format($course->regular_price) }}</span>
                                @endif
                            </div>
                        </div>
                        @if($course->discount_percentage)
                            <span class="px-2.5 py-1 rounded-lg text-xs font-bold bg-rose-100 text-rose-700">
                                {{ $course->discount_percentage }}% ছাড়
                            </span>
                        @endif
                    </div>

                    <a href="{{ route('courses.checkout', $course->slug) }}" class="w-full flex items-center justify-center gap-2 py-3.5 rounded-xl bg-gradient-to-r from-emerald-600 to-teal-600 hover:from-emerald-700 hover:to-teal-700 text-white font-bold text-sm shadow-md transition">
                        <span>ভর্তি নিশ্চিত করুন (বিকাশ / নগদ)</span>
                        <i class="fa-solid fa-arrow-right text-xs"></i>
                    </a>

                    <div class="text-center text-xs text-slate-500">
                        <i class="fa-solid fa-shield-halved text-emerald-600 mr-1"></i> ৭ দিনের মানিব্যাক গ্যারান্টি
                    </div>
                </div>
            </div>

        </div>
    </div>
</section>

<!-- Course Details Content -->
<section class="py-16 bg-slate-50">
    <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8">
        <div class="grid grid-cols-1 lg:grid-cols-12 gap-8">
            
            <div class="lg:col-span-8 space-y-10">
                <!-- Description -->
                <div class="bg-white rounded-2xl p-6 sm:p-8 border border-slate-200">
                    <h3 class="text-xl font-bold text-slate-900 mb-4">কোর্স সম্পর্কে বিস্তারিত</h3>
                    <p class="text-sm text-slate-700 leading-relaxed whitespace-pre-line">{{ $course->description }}</p>
                </div>

                <!-- Learning Goals -->
                @if($course->goals)
                    <div class="bg-white rounded-2xl p-6 sm:p-8 border border-slate-200">
                        <h3 class="text-xl font-bold text-slate-900 mb-4">এই কোর্স থেকে আপনি কী কী শিখবেন</h3>
                        <div class="grid grid-cols-1 sm:grid-cols-2 gap-3">
                            @foreach($course->goals as $goal)
                                <div class="flex items-start gap-2.5 p-3 rounded-lg bg-emerald-50/50 text-xs text-slate-800">
                                    <i class="fa-solid fa-check text-emerald-600 mt-0.5"></i>
                                    <span>{{ $goal }}</span>
                                </div>
                            @endforeach
                        </div>
                    </div>
                @endif

                <!-- Curriculum Modules -->
                <div class="bg-white rounded-2xl p-6 sm:p-8 border border-slate-200" x-data="{ openMod: 1 }">
                    <h3 class="text-xl font-bold text-slate-900 mb-6">কোর্স কারিকুলাম ও মডিউল</h3>
                    <div class="space-y-3">
                        @foreach($course->modules as $mod)
                            <div class="border border-slate-200 rounded-xl overflow-hidden">
                                <button @click="openMod = (openMod === {{ $mod->id }} ? null : {{ $mod->id }})" class="w-full p-4 bg-slate-50 flex items-center justify-between text-left hover:bg-emerald-50/40 transition">
                                    <span class="font-bold text-sm text-slate-900">{{ $mod->bangla_title ?? $mod->title }}</span>
                                    <i class="fa-solid fa-chevron-down text-xs text-slate-400 transition-transform" :class="openMod === {{ $mod->id }} ? 'rotate-180 text-emerald-600' : ''"></i>
                                </button>
                                <div x-show="openMod === {{ $mod->id }}" class="p-4 bg-white divide-y divide-slate-100 text-xs">
                                    @foreach($mod->lessons as $les)
                                        <div class="py-2.5 flex items-center justify-between">
                                            <span class="flex items-center gap-2 text-slate-700 font-medium">
                                                <i class="fa-solid fa-circle-play text-emerald-600"></i> {{ $les->title }}
                                            </span>
                                            <span class="text-slate-400">{{ $les->duration_minutes }} মিনিট</span>
                                        </div>
                                    @endforeach
                                </div>
                            </div>
                        @endforeach
                    </div>
                </div>
            </div>

            <!-- Sidebar -->
            <div class="lg:col-span-4 space-y-6">
                <div class="bg-white rounded-2xl p-6 border border-slate-200 space-y-4">
                    <h4 class="font-bold text-base text-slate-900">কোর্সের সুবিধাসমূহ</h4>
                    <ul class="text-xs text-slate-600 space-y-2.5">
                        <li class="flex items-center gap-2"><i class="fa-solid fa-circle-check text-emerald-600"></i> জুম ও গুগল মিটে সরাসরি ইন্টারঅ্যাকশন</li>
                        <li class="flex items-center gap-2"><i class="fa-solid fa-circle-check text-emerald-600"></i> প্রতিদিন ২ ঘণ্টা ডেডিকেটেড গুগল মিট সাপোর্ট</li>
                        <li class="flex items-center gap-2"><i class="fa-solid fa-circle-check text-emerald-600"></i> প্রতি মডিউলে অনলাইন পরীক্ষা ও কুইজ</li>
                        <li class="flex items-center gap-2"><i class="fa-solid fa-circle-check text-emerald-600"></i> ভেরিফায়েবল সার্টিফিকেট</li>
                        <li class="flex items-center gap-2"><i class="fa-solid fa-circle-check text-emerald-600"></i> লাইফটাইম কোর্স রেকর্ডিং অ্যাক্সেস</li>
                    </ul>
                    <a href="{{ route('courses.checkout', $course->slug) }}" class="w-full block text-center py-3 rounded-xl bg-emerald-600 text-white font-bold text-xs shadow hover:bg-emerald-700 transition">
                        এনরোল করুন
                    </a>
                </div>
            </div>

        </div>
    </div>
</section>
@endsection
