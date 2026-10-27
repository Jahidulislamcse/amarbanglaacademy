@extends('layouts.app')

@section('title', 'Amar Bangla Academy - আধুনিক লার্নিং ম্যানেজমেন্ট সিস্টেম (LMS)')

@section('content')

<!-- ==================== 1. HERO SECTION ==================== -->
<section class="relative overflow-hidden bg-gradient-to-b from-emerald-950 via-slate-900 to-slate-900 text-white pt-16 pb-24 sm:pt-20 sm:pb-32">
    <!-- Ambient Glow / Mesh background -->
    <div class="absolute inset-0 opacity-20 pointer-events-none">
        <div class="absolute -top-40 -left-40 w-96 h-96 bg-emerald-500 rounded-full blur-3xl"></div>
        <div class="absolute top-1/3 -right-20 w-80 h-80 bg-teal-400 rounded-full blur-3xl"></div>
        <div class="absolute -bottom-20 left-1/2 w-96 h-96 bg-amber-500 rounded-full blur-3xl"></div>
    </div>

    <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8 relative z-10">
        <div class="grid grid-cols-1 lg:grid-cols-12 gap-12 items-center">
            
            <!-- Left Column: Hero Content -->
            <div class="lg:col-span-7 space-y-6 text-center lg:text-left">
                
                <!-- Pill Badge -->
                <div class="inline-flex items-center gap-2 px-3.5 py-1.5 rounded-full bg-emerald-900/80 border border-emerald-500/30 text-emerald-300 text-xs sm:text-sm font-semibold shadow-inner">
                    <span class="w-2 h-2 rounded-full bg-emerald-400 animate-pulse"></span>
                    <span>বাংলায় ইন্ডাস্ট্রি-স্ট্যান্ডার্ড স্কিল ডেভেলপমেন্ট ও LMS</span>
                </div>

                <!-- Main Heading -->
                <h1 class="text-3xl sm:text-5xl lg:text-6xl font-extrabold tracking-tight leading-tight sm:leading-none text-white">
                    দক্ষতা অর্জন করুন <span class="text-transparent bg-clip-text bg-gradient-to-r from-emerald-400 via-teal-300 to-amber-300">মাতৃভাষায়</span>,<br>
                    গড়ে তুলুন গ্লোবাল ক্যারিয়ার
                </h1>

                <!-- Subheading -->
                <p class="text-base sm:text-lg text-slate-300 max-w-2xl mx-auto lg:mx-0 leading-relaxed font-normal">
                    আমার বাংলা একাডেমি — আন্তর্জাতিক মানের কারিকুলাম, <strong class="text-white">জুম ও গুগল মিটে সরাসরি লাইভ ক্লাস</strong>, মডিউল ভিত্তিক এক্সাম, সার্বক্ষণিক মেন্টর সাপোর্ট এবং <strong class="text-white">বিকাশ/নগদ</strong> অনলাইন পেমেন্ট সুবিধা সম্পন্ন সম্পূর্ণ সমন্বিত LMS প্ল্যাটফর্ম।
                </p>

                <!-- Core Badges / Highlights -->
                <div class="flex flex-wrap items-center justify-center lg:justify-start gap-3 pt-2 text-xs sm:text-sm text-slate-200">
                    <span class="flex items-center gap-1.5 px-3 py-1.5 rounded-lg bg-slate-800/80 border border-slate-700">
                        <i class="fa-solid fa-video text-rose-400"></i> জুম ও মিট লাইভ সেশন
                    </span>
                    <span class="flex items-center gap-1.5 px-3 py-1.5 rounded-lg bg-slate-800/80 border border-slate-700">
                        <i class="fa-solid fa-clipboard-check text-amber-400"></i> লাইভ কুইজ ও এক্সাম
                    </span>
                    <span class="flex items-center gap-1.5 px-3 py-1.5 rounded-lg bg-slate-800/80 border border-slate-700">
                        <i class="fa-solid fa-headset text-emerald-400"></i> ২৪/৭ লাইভ সাপোর্ট
                    </span>
                    <span class="flex items-center gap-1.5 px-3 py-1.5 rounded-lg bg-slate-800/80 border border-slate-700">
                        <i class="fa-solid fa-credit-card text-pink-400"></i> ইনস্ট্যান্ট বিকাশ/নগদ পেমেন্ট
                    </span>
                </div>

                <!-- Action CTA Buttons -->
                <div class="flex flex-col sm:flex-row items-center justify-center lg:justify-start gap-4 pt-4">
                    <a href="#featured-course" class="w-full sm:w-auto inline-flex items-center justify-center gap-3 px-8 py-4 rounded-xl bg-gradient-to-r from-emerald-500 to-teal-600 hover:from-emerald-600 hover:to-teal-700 text-white font-bold text-base shadow-lg shadow-emerald-500/25 hover:shadow-emerald-500/40 hover:-translate-y-0.5 transition-all duration-200">
                        <span>কোর্স বিস্তারিত ও কারিকুলাম</span>
                        <i class="fa-solid fa-arrow-down text-sm"></i>
                    </a>
                    <a href="{{ route('courses.checkout', $flagshipCourse->slug) }}" class="w-full sm:w-auto inline-flex items-center justify-center gap-2 px-7 py-4 rounded-xl bg-slate-800/90 hover:bg-slate-700/90 border border-slate-600 text-amber-300 font-bold text-base transition duration-200">
                        <i class="fa-solid fa-bolt text-amber-400"></i>
                        <span>ভর্তি নিশ্চিত করুন (৳{{ number_format($flagshipCourse->effective_price) }})</span>
                    </a>
                </div>

                <!-- Live Social Proof Stats -->
                <div class="pt-8 border-t border-slate-800 grid grid-cols-2 sm:grid-cols-4 gap-4 text-center lg:text-left">
                    <div>
                        <div class="text-2xl sm:text-3xl font-extrabold text-emerald-400">{{ $stats['total_students'] }}</div>
                        <div class="text-xs text-slate-400">নিবন্ধিত শিক্ষার্থী</div>
                    </div>
                    <div>
                        <div class="text-2xl sm:text-3xl font-extrabold text-teal-400">{{ $stats['satisfaction_rate'] }}</div>
                        <div class="text-xs text-slate-400">পজিটিভ রিভিউ</div>
                    </div>
                    <div>
                        <div class="text-2xl sm:text-3xl font-extrabold text-amber-400">{{ $stats['instructors_count'] }}</div>
                        <div class="text-xs text-slate-400">ইন্ডাস্ট্রি মেন্টর</div>
                    </div>
                    <div>
                        <div class="text-2xl sm:text-3xl font-extrabold text-rose-400">{{ $stats['active_live_batches'] }}টি</div>
                        <div class="text-xs text-slate-400">চলমান লাইভ ব্যাচ</div>
                    </div>
                </div>

            </div>

            <!-- Right Column: Hero Video / Flagship Course Card -->
            <div class="lg:col-span-5">
                <div class="relative mx-auto max-w-md lg:max-w-none">
                    
                    <!-- Decorative Badge Top Right -->
                    <div class="absolute -top-4 -right-4 z-20 bg-gradient-to-r from-amber-500 to-orange-500 text-slate-950 font-black text-xs px-3.5 py-1.5 rounded-full shadow-lg flex items-center gap-1.5 uppercase tracking-wider">
                        <i class="fa-solid fa-fire text-white animate-bounce"></i> বেস্টসেলার ব্যাচ
                    </div>

                    <!-- Card Body -->
                    <div class="rounded-2xl bg-slate-800/90 backdrop-blur border border-slate-700 p-5 shadow-2xl overflow-hidden space-y-4">
                        
                        <!-- Thumbnail with Play Overlay -->
                        <div class="relative rounded-xl overflow-hidden aspect-video bg-slate-900 group">
                            <img src="{{ $flagshipCourse->thumbnail }}" alt="{{ $flagshipCourse->title }}" class="w-full h-full object-cover group-hover:scale-105 transition-transform duration-500 opacity-90">
                            <div class="absolute inset-0 bg-gradient-to-t from-slate-950/80 via-transparent to-black/30 flex items-center justify-center">
                                <a href="#course-curriculum" class="w-16 h-16 rounded-full bg-emerald-500/90 hover:bg-emerald-400 text-white flex items-center justify-center shadow-lg transform group-hover:scale-110 transition duration-300">
                                    <i class="fa-solid fa-play text-xl ml-1"></i>
                                </a>
                            </div>
                            <span class="absolute bottom-3 left-3 bg-slate-900/90 backdrop-blur px-2.5 py-1 rounded text-xs font-semibold text-emerald-400 flex items-center gap-1.5">
                                <span class="w-2 h-2 rounded-full bg-rose-500 animate-ping"></span>
                                লাইভ ক্লাস: Zoom & Google Meet
                            </span>
                        </div>

                        <!-- Card Info -->
                        <div>
                            <div class="flex items-center justify-between text-xs text-slate-400 mb-1">
                                <span class="text-amber-400 font-semibold flex items-center gap-1">
                                    <i class="fa-solid fa-star text-amber-400"></i> 4.95 (৩,৪২০+ রিভিউ)
                                </span>
                                <span>{{ $flagshipCourse->batch_no }}</span>
                            </div>
                            <h3 class="text-lg font-bold text-white leading-snug">
                                {{ $flagshipCourse->bangla_title ?? $flagshipCourse->title }}
                            </h3>
                            <p class="text-xs text-slate-300 mt-1 line-clamp-2">
                                {{ $flagshipCourse->subtitle }}
                            </p>
                        </div>

                        <!-- Quick Specs Grid -->
                        <div class="grid grid-cols-2 gap-2 text-xs text-slate-300 bg-slate-900/60 p-3 rounded-xl border border-slate-700/60">
                            <div class="flex items-center gap-2">
                                <i class="fa-solid fa-calendar-days text-emerald-400"></i>
                                <span>শুরু: {{ $flagshipCourse->start_date ? $flagshipCourse->start_date->format('d M, Y') : 'শীঘ্রই' }}</span>
                            </div>
                            <div class="flex items-center gap-2">
                                <i class="fa-solid fa-clock text-teal-400"></i>
                                <span>সময়: {{ $flagshipCourse->total_weeks }} সপ্তাহ ({{ $flagshipCourse->total_hours }}+ ঘণ্টা)</span>
                            </div>
                            <div class="flex items-center gap-2">
                                <i class="fa-solid fa-chalkboard-user text-amber-400"></i>
                                <span>লেকচার: {{ $flagshipCourse->total_lectures }}টি লাইভ ক্লাস</span>
                            </div>
                            <div class="flex items-center gap-2">
                                <i class="fa-solid fa-award text-rose-400"></i>
                                <span>{{ $flagshipCourse->total_exams }}টি এক্সাম ও প্রজেক্ট</span>
                            </div>
                        </div>

                        <!-- Pricing & CTA inside Card -->
                        <div class="pt-2 flex items-center justify-between border-t border-slate-700">
                            <div>
                                <div class="flex items-baseline gap-2">
                                    <span class="text-2xl font-black text-amber-400">৳{{ number_format($flagshipCourse->effective_price) }}</span>
                                    @if($flagshipCourse->discount_price)
                                        <span class="text-xs text-slate-400 line-through">৳{{ number_format($flagshipCourse->regular_price) }}</span>
                                    @endif
                                </div>
                                <span class="text-[11px] text-emerald-400 font-semibold block">বিকাশ/নগদে ইনস্ট্যান্ট পেমেন্ট</span>
                            </div>
                            <a href="{{ route('courses.checkout', $flagshipCourse->slug) }}" class="px-5 py-2.5 rounded-xl bg-emerald-500 hover:bg-emerald-600 text-white font-bold text-xs shadow-md transition">
                                এখনই এনরোল করুন
                            </a>
                        </div>

                    </div>
                </div>
            </div>

        </div>
    </div>
</section>


<!-- ==================== 2. TRUST / CORPORATE ALUMNI BAR ==================== -->
<section class="bg-white border-y border-slate-200 py-6">
    <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8">
        <p class="text-center text-xs font-semibold text-slate-500 uppercase tracking-widest mb-4">
            আমাদের গ্র্যাজুয়েট ও শিক্ষার্থীরা কর্মরত রয়েছেন দেশ-বিদেশের শীর্ষ প্রতিষ্ঠানে
        </p>
        <div class="flex flex-wrap items-center justify-center gap-8 sm:gap-12 text-slate-400 text-sm font-bold opacity-80">
            <span class="flex items-center gap-2"><i class="fa-brands fa-google text-slate-600"></i> Google Alumni</span>
            <span class="flex items-center gap-2"><i class="fa-brands fa-amazon text-slate-600"></i> AWS Partner</span>
            <span class="flex items-center gap-2 text-pink-600"><i class="fa-solid fa-building"></i> bKash</span>
            <span class="flex items-center gap-2"><i class="fa-solid fa-laptop-code"></i> Brain Station 23</span>
            <span class="flex items-center gap-2 text-rose-600"><i class="fa-solid fa-motorcycle"></i> Pathao</span>
            <span class="flex items-center gap-2 text-emerald-600"><i class="fa-solid fa-cart-shopping"></i> Chaldal</span>
        </div>
    </div>
</section>


<!-- ==================== 3. FLAGSHIP LMS COURSE SHOWCASE (DEEP DIVE) ==================== -->
<section id="featured-course" class="py-16 sm:py-24 bg-slate-50 relative">
    <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8">
        
        <!-- Section Header -->
        <div class="text-center max-w-3xl mx-auto mb-16">
            <span class="text-xs font-bold text-emerald-700 uppercase tracking-widest bg-emerald-100/80 px-3 py-1 rounded-full">
                ফ্ল্যাগশিপ লাইভ বুটক্যাম্প
            </span>
            <h2 class="text-3xl sm:text-4xl font-extrabold text-slate-900 mt-3 tracking-tight">
                {{ $flagshipCourse->bangla_title ?? $flagshipCourse->title }}
            </h2>
            <p class="text-slate-600 text-base sm:text-lg mt-3 leading-relaxed">
                আন্তর্জাতিক সফটওয়্যার ইন্ডাস্ট্রির মানদণ্ড অনুযায়ী তৈরি পূর্ণাঙ্গ কারিকুলাম। সম্পূর্ণ কোর্সে পাবেন লাইভ জুম ক্লাস, নিয়মিত অনলাইন পরীক্ষা, পার্সোনাল কোড রিভিউ ও লাইভ মেন্টর সহায়তা।
            </p>
        </div>

        <!-- 3-Column Detailed Overview & Layout -->
        <div class="grid grid-cols-1 lg:grid-cols-12 gap-8 items-start">
            
            <!-- Left Main Content (Goals, Curriculum, Instructors, Live Classes, Exams, Supports) -->
            <div class="lg:col-span-8 space-y-12">
                
                <!-- A. COURSE LEARNING GOALS & OUTCOMES -->
                <div class="bg-white rounded-2xl p-6 sm:p-8 border border-slate-200/80 shadow-xs">
                    <div class="flex items-center gap-3 mb-6">
                        <div class="w-10 h-10 rounded-xl bg-emerald-100 text-emerald-700 flex items-center justify-center text-lg">
                            <i class="fa-solid fa-bullseye"></i>
                        </div>
                        <div>
                            <h3 class="text-xl font-bold text-slate-900">কোর্সের উদ্দেশ্য ও কী কী শিখবেন (Learning Goals)</h3>
                            <p class="text-xs text-slate-500">এই কোর্সটি সফলভাবে শেষ করার পর আপনি যা যা তৈরিতে পারদর্শী হবেন</p>
                        </div>
                    </div>

                    <div class="grid grid-cols-1 sm:grid-cols-2 gap-4">
                        @if($flagshipCourse->goals)
                            @foreach($flagshipCourse->goals as $goal)
                                <div class="flex items-start gap-3 p-3.5 rounded-xl bg-slate-50 border border-slate-100 hover:border-emerald-200 transition">
                                    <div class="w-6 h-6 rounded-full bg-emerald-600 text-white flex-shrink-0 flex items-center justify-center text-xs mt-0.5">
                                        <i class="fa-solid fa-check"></i>
                                    </div>
                                    <span class="text-sm font-medium text-slate-700 leading-snug">{{ $goal }}</span>
                                </div>
                            @endforeach
                        @endif
                    </div>

                    <!-- Prerequisites / Requirements -->
                    @if($flagshipCourse->requirements)
                        <div class="mt-6 pt-6 border-t border-slate-100">
                            <h4 class="text-xs font-bold text-slate-500 uppercase tracking-wider mb-3">কোর্সে অংশ নিতে যা যা প্রয়োজন (Requirements):</h4>
                            <div class="flex flex-wrap gap-2">
                                @foreach($flagshipCourse->requirements as $req)
                                    <span class="inline-flex items-center gap-1.5 px-3 py-1.5 rounded-lg bg-emerald-50 text-emerald-800 text-xs font-semibold">
                                        <i class="fa-solid fa-circle-info text-emerald-600 text-[11px]"></i> {{ $req }}
                                    </span>
                                @endforeach
                            </div>
                        </div>
                    @endif
                </div>


                <!-- B. COURSE MODULES & DETAILED CURRICULUM ACCORDION -->
                <div id="course-curriculum" class="bg-white rounded-2xl p-6 sm:p-8 border border-slate-200/80 shadow-xs" x-data="{ openModule: 1 }">
                    <div class="flex flex-col sm:flex-row sm:items-center justify-between gap-4 mb-6 pb-4 border-b border-slate-100">
                        <div class="flex items-center gap-3">
                            <div class="w-10 h-10 rounded-xl bg-teal-100 text-teal-700 flex items-center justify-center text-lg">
                                <i class="fa-solid fa-layer-group"></i>
                            </div>
                            <div>
                                <h3 class="text-xl font-bold text-slate-900">কোর্স কারিকুলাম ও মডিউল সমূহ</h3>
                                <p class="text-xs text-slate-500">{{ $flagshipCourse->modules->count() }}টি মডিউল • {{ $flagshipCourse->lessons->count() }}টি লেকচার • লাইভ জুম ও গুগল মিট ক্লাস</p>
                            </div>
                        </div>
                        <div class="flex items-center gap-2 text-xs">
                            <button @click="openModule = 'all'" class="px-3 py-1.5 rounded-lg bg-slate-100 hover:bg-slate-200 text-slate-700 font-semibold transition">সব খুলুন</button>
                            <button @click="openModule = null" class="px-3 py-1.5 rounded-lg bg-slate-100 hover:bg-slate-200 text-slate-700 font-semibold transition">সব বন্ধ করুন</button>
                        </div>
                    </div>

                    <!-- Accordion Modules List -->
                    <div class="space-y-4">
                        @foreach($flagshipCourse->modules as $module)
                            <div class="border border-slate-200 rounded-xl overflow-hidden transition-all duration-200">
                                
                                <!-- Accordion Header -->
                                <button @click="openModule = (openModule === {{ $module->id }} ? null : (openModule === 'all' ? null : {{ $module->id }}))" 
                                        type="button" 
                                        class="w-full flex items-center justify-between p-4 sm:p-5 bg-slate-50/70 hover:bg-emerald-50/60 text-left transition duration-150">
                                    <div class="flex items-center gap-3 sm:gap-4 pr-2">
                                        <span class="w-8 h-8 rounded-lg bg-emerald-600 text-white font-bold text-sm flex items-center justify-center flex-shrink-0 shadow-xs">
                                            {{ $module->order }}
                                        </span>
                                        <div>
                                            <h4 class="text-sm sm:text-base font-bold text-slate-900">
                                                {{ $module->bangla_title ?? $module->title }}
                                            </h4>
                                            <div class="flex items-center gap-3 text-xs text-slate-500 mt-1">
                                                <span><i class="fa-regular fa-clock mr-1 text-slate-400"></i>{{ $module->duration_text }}</span>
                                                <span>•</span>
                                                <span><i class="fa-solid fa-play-circle mr-1 text-emerald-600"></i>{{ $module->lessons->count() }}টি লেকচার</span>
                                                @if($module->exams->count() > 0)
                                                    <span>•</span>
                                                    <span class="text-amber-600 font-semibold"><i class="fa-solid fa-clipboard-check mr-1"></i>১টি এক্সাম</span>
                                                @endif
                                            </div>
                                        </div>
                                    </div>
                                    <div class="text-slate-400">
                                        <i class="fa-solid fa-chevron-down transition-transform duration-200" 
                                           :class="(openModule === {{ $module->id }} || openModule === 'all') ? 'rotate-180 text-emerald-600' : ''"></i>
                                    </div>
                                </button>

                                <!-- Accordion Body (Lessons) -->
                                <div x-show="openModule === {{ $module->id }} || openModule === 'all'" 
                                     x-transition:enter="transition ease-out duration-200"
                                     x-transition:enter-start="opacity-0 max-h-0"
                                     x-transition:enter-end="opacity-100 max-h-[1000px]"
                                     class="p-4 sm:p-5 bg-white border-t border-slate-100 divide-y divide-slate-100">
                                    
                                    @if($module->description)
                                        <p class="text-xs text-slate-500 italic pb-3">{{ $module->description }}</p>
                                    @endif

                                    <div class="space-y-2 pt-2">
                                        @foreach($module->lessons as $lesson)
                                            <div class="flex flex-col sm:flex-row sm:items-center justify-between p-2.5 rounded-lg hover:bg-slate-50 transition gap-2">
                                                <div class="flex items-center gap-3">
                                                    <!-- Icon by Lesson Type -->
                                                    @if($lesson->type === 'live_zoom')
                                                        <span class="w-7 h-7 rounded-md bg-blue-100 text-blue-700 flex items-center justify-center text-xs flex-shrink-0" title="Zoom লাইভ ক্লাস">
                                                            <i class="fa-solid fa-video"></i>
                                                        </span>
                                                    @elseif($lesson->type === 'live_meet')
                                                        <span class="w-7 h-7 rounded-md bg-emerald-100 text-emerald-700 flex items-center justify-center text-xs flex-shrink-0" title="Google Meet লাইভ সেশন">
                                                            <i class="fa-solid fa-users-viewfinder"></i>
                                                        </span>
                                                    @else
                                                        <span class="w-7 h-7 rounded-md bg-slate-100 text-slate-600 flex items-center justify-center text-xs flex-shrink-0" title="HD ভিডিও রেকর্ডিং">
                                                            <i class="fa-solid fa-play"></i>
                                                        </span>
                                                    @endif

                                                    <span class="text-sm font-medium text-slate-800">
                                                        {{ $lesson->title }}
                                                    </span>
                                                </div>

                                                <!-- Meta Badges -->
                                                <div class="flex items-center gap-2 self-end sm:self-auto text-xs">
                                                    @if($lesson->is_free_preview)
                                                        <span class="px-2 py-0.5 rounded bg-amber-100 text-amber-800 font-bold text-[11px]">
                                                            ফ্রি প্রিভিউ
                                                        </span>
                                                    @endif

                                                    @if($lesson->type === 'live_zoom')
                                                        <span class="px-2 py-0.5 rounded bg-blue-50 text-blue-700 font-semibold text-[11px] flex items-center gap-1">
                                                            <span class="w-1.5 h-1.5 rounded-full bg-blue-500 animate-pulse"></span> Zoom Live
                                                        </span>
                                                    @elseif($lesson->type === 'live_meet')
                                                        <span class="px-2 py-0.5 rounded bg-emerald-50 text-emerald-700 font-semibold text-[11px] flex items-center gap-1">
                                                            <span class="w-1.5 h-1.5 rounded-full bg-emerald-500 animate-pulse"></span> Google Meet
                                                        </span>
                                                    @else
                                                        <span class="text-slate-400 font-mono text-[11px]">রেকর্ডিং</span>
                                                    @endif

                                                    <span class="text-slate-400 font-mono text-[11px]">{{ $lesson->duration_minutes }} মি.</span>
                                                </div>
                                            </div>
                                        @endforeach
                                    </div>

                                    <!-- Module Exam / Quiz Badge -->
                                    @if($module->exams->count() > 0)
                                        @foreach($module->exams as $exam)
                                            <div class="mt-3 pt-3 flex items-center justify-between p-3 rounded-lg bg-amber-50/70 border border-amber-200/70 text-xs text-amber-900">
                                                <div class="flex items-center gap-2">
                                                    <i class="fa-solid fa-clipboard-question text-amber-600 text-sm"></i>
                                                    <span class="font-bold">{{ $exam->title }}</span>
                                                </div>
                                                <div class="flex items-center gap-3 font-semibold">
                                                    <span>পূর্ণমান: {{ $exam->total_marks }}</span>
                                                    <span>•</span>
                                                    <span>সময়: {{ $exam->duration_minutes }} মিনিট</span>
                                                    <span class="px-2 py-0.5 rounded bg-amber-200/80 text-amber-900 text-[10px] uppercase font-bold">লাইভ টেস্ট</span>
                                                </div>
                                            </div>
                                        @endforeach
                                    @endif

                                </div>
                            </div>
                        @endforeach
                    </div>
                </div>


                <!-- C. INSTRUCTORS & MENTORS -->
                <div id="instructors" class="bg-white rounded-2xl p-6 sm:p-8 border border-slate-200/80 shadow-xs">
                    <div class="flex items-center gap-3 mb-6">
                        <div class="w-10 h-10 rounded-xl bg-amber-100 text-amber-700 flex items-center justify-center text-lg">
                            <i class="fa-solid fa-user-tie"></i>
                        </div>
                        <div>
                            <h3 class="text-xl font-bold text-slate-900">কোর্সের ইন্সট্রাক্টর ও ফ্যাকাল্টি</h3>
                            <p class="text-xs text-slate-500">শীর্ষ স্থানীয় ইন্ডাস্ট্রি বিশেষজ্ঞ যাদের সরাসরি মেন্টরশিপে আপনি শিখবেন</p>
                        </div>
                    </div>

                    <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
                        @foreach($flagshipCourse->instructors as $instructor)
                            <div class="p-5 rounded-xl bg-slate-50 border border-slate-200/80 hover:border-emerald-300 transition flex flex-col justify-between space-y-4">
                                <div class="flex items-start gap-4">
                                    <img src="{{ $instructor->avatar }}" alt="{{ $instructor->name }}" class="w-16 h-16 rounded-full object-cover border-2 border-emerald-500 shadow-sm flex-shrink-0">
                                    <div>
                                        <div class="flex items-center gap-2">
                                            <h4 class="font-bold text-slate-900 text-base">{{ $instructor->bangla_name ?? $instructor->name }}</h4>
                                            @if($instructor->pivot && $instructor->pivot->is_lead)
                                                <span class="px-2 py-0.5 rounded text-[10px] font-bold bg-emerald-100 text-emerald-800">Lead Faculty</span>
                                            @endif
                                        </div>
                                        <p class="text-xs text-emerald-700 font-semibold">{{ $instructor->title }}</p>
                                        <p class="text-[11px] text-slate-500">{{ $instructor->organization }}</p>
                                    </div>
                                </div>

                                <p class="text-xs text-slate-600 leading-relaxed">
                                    {{ $instructor->bio }}
                                </p>

                                <div class="pt-3 border-t border-slate-200/60 flex items-center justify-between text-xs text-slate-500">
                                    <div class="flex items-center gap-1 text-amber-500 font-bold">
                                        <i class="fa-solid fa-star"></i> {{ $instructor->rating }} ({{ number_format($instructor->students_count) }}+ শিক্ষার্থী)
                                    </div>
                                    <div class="flex items-center gap-2 text-slate-400">
                                        <span class="text-slate-600 font-medium">{{ $instructor->experience_years }}+ বছরের অভিজ্ঞতা</span>
                                    </div>
                                </div>
                            </div>
                        @endforeach
                    </div>
                </div>


                <!-- D. LIVE CLASS & EXAM INTEGRATION DETAIL -->
                <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
                    
                    <!-- Zoom & Google Meet Box -->
                    <div class="bg-gradient-to-br from-blue-900 to-slate-900 text-white rounded-2xl p-6 shadow-md border border-blue-800 space-y-4">
                        <div class="w-12 h-12 rounded-xl bg-blue-600/80 flex items-center justify-center text-xl text-white">
                            <i class="fa-solid fa-video"></i>
                        </div>
                        <h4 class="text-lg font-bold">জুম ও গুগল মিটে সরাসরি লাইভ ক্লাস</h4>
                        <p class="text-xs text-blue-100/90 leading-relaxed">
                            কোনো প্রাক-রেকর্ড করা একঘেয়ে ভিডিও নয়! প্রতিটি ক্লাস সরাসরি ইন্সট্রাক্টরের সাথে স্ক্রিন শেয়ার করে অনুষ্ঠিত হয়। ক্লাসের মাঝেই সরাসরি প্রশ্ন করুন এবং ইনস্ট্যান্ট সমাধান নিন।
                        </p>
                        <ul class="text-xs text-blue-200 space-y-2">
                            <li class="flex items-center gap-2"><i class="fa-solid fa-circle-check text-blue-400"></i> ক্লাস শিডিউল: {{ $flagshipCourse->live_schedule_text }}</li>
                            <li class="flex items-center gap-2"><i class="fa-solid fa-circle-check text-blue-400"></i> ক্লাস শেষেই অটোমেটেড ফুল এইচডি রেকর্ডিং ড্যাশবোর্ডে</li>
                            <li class="flex items-center gap-2"><i class="fa-solid fa-circle-check text-blue-400"></i> ওয়ান-ক্লিক জুম ও মিট ডিরেক্ট জয়েন বোতাম</li>
                        </ul>
                        <div class="pt-2">
                            <a href="{{ route('live.classes') }}" class="text-xs font-bold text-blue-300 hover:text-white inline-flex items-center gap-1">
                                লাইভ ক্লাস রুম ইন্টারফেস দেখুন &rarr;
                            </a>
                        </div>
                    </div>

                    <!-- Online Exams & Quizzes Box -->
                    <div class="bg-gradient-to-br from-amber-900 to-slate-900 text-white rounded-2xl p-6 shadow-md border border-amber-800 space-y-4">
                        <div class="w-12 h-12 rounded-xl bg-amber-600/80 flex items-center justify-center text-xl text-white">
                            <i class="fa-solid fa-certificate"></i>
                        </div>
                        <h4 class="text-lg font-bold">অনলাইন এক্সাম ও কিউআর সার্টিফিকেট</h4>
                        <p class="text-xs text-amber-100/90 leading-relaxed">
                            প্রতিটি মডিউল শেষে রয়েছে টাইমার যুক্ত অবজেক্টিভ কুইজ এবং ইন্ডাস্ট্রি লেভেল প্র্যাক্টিক্যাল কোডিং অ্যাসাইনমেন্ট। কোর্স সম্পন্নকারীদের জন্য রয়েছে গ্লোবালি ভেরিফায়েবল সার্টিফিকেট।
                        </p>
                        <ul class="text-xs text-amber-200 space-y-2">
                            <li class="flex items-center gap-2"><i class="fa-solid fa-circle-check text-amber-400"></i> ১২টি লাইভ উইকলি কুইজ ও ৩টি বড় মক টেস্ট</li>
                            <li class="flex items-center gap-2"><i class="fa-solid fa-circle-check text-amber-400"></i> ফাইনাল ক্যাপস্টোন প্রজেক্ট ডিফেন্স ও মূল্যায়ন</li>
                            <li class="flex items-center gap-2"><i class="fa-solid fa-circle-check text-amber-400"></i> কিউআর কোড স্ক্যান করে অনলাইন সার্টিফিকেট ভেরিফিকেশন</li>
                        </ul>
                        <div class="pt-2">
                            <a href="{{ route('exams.index') }}" class="text-xs font-bold text-amber-300 hover:text-white inline-flex items-center gap-1">
                                এক্সাম পোর্টাল ডেমো দেখুন &rarr;
                            </a>
                        </div>
                    </div>

                </div>


                <!-- E. 24/7 DEDICATED SUPPORT SYSTEM -->
                <div id="course-supports" class="bg-white rounded-2xl p-6 sm:p-8 border border-slate-200/80 shadow-xs">
                    <div class="flex items-center gap-3 mb-6">
                        <div class="w-10 h-10 rounded-xl bg-rose-100 text-rose-700 flex items-center justify-center text-lg">
                            <i class="fa-solid fa-headset"></i>
                        </div>
                        <div>
                            <h3 class="text-xl font-bold text-slate-900">২৪/৭ লাইভ মেন্টর ও প্রব্লেম সলভিং সাপোর্ট</h3>
                            <p class="text-xs text-slate-500">শেখার পথে আটকে গেলে হতাশ হওয়ার কোনো সুযোগ নেই, আমরা সর্বদা পাশে আছি</p>
                        </div>
                    </div>

                    <div class="grid grid-cols-1 sm:grid-cols-2 gap-4">
                        <div class="p-4 rounded-xl bg-slate-50 border border-slate-100 space-y-2">
                            <div class="flex items-center gap-2 text-emerald-700 font-bold text-sm">
                                <i class="fa-solid fa-users text-base"></i>
                                <span>প্রতিদিন লাইভ মিট সাপোর্ট সেশন</span>
                            </div>
                            <p class="text-xs text-slate-600 leading-relaxed">
                                প্রতিদিন বিকেল ৫:০০ টা থেকে রাত ১২:০০ টা পর্যন্ত ডেডিকেটেড গুগল মিটে আমাদের মেন্টররা স্ক্রিন শেয়ার করে আপনার কোডিং বাগ ফিক্স করে দেবেন।
                            </p>
                        </div>

                        <div class="p-4 rounded-xl bg-slate-50 border border-slate-100 space-y-2">
                            <div class="flex items-center gap-2 text-indigo-700 font-bold text-sm">
                                <i class="fa-brands fa-discord text-base"></i>
                                <span>প্রাইভেট ডিসকর্ড ও টেলিগ্রাম ফোরাম</span>
                            </div>
                            <p class="text-xs text-slate-600 leading-relaxed">
                                শুধুমাত্র এনরোল করা শিক্ষার্থীদের জন্য স্পেশাল প্রাইভেট চ্যানেল। সেখানে ইন্সট্রাক্টর, মেন্টর ও সহপাঠীদের সাথে ২৪ ঘণ্টা আলোচনা ও সমস্যা শেয়ারের সুযোগ।
                            </p>
                        </div>

                        <div class="p-4 rounded-xl bg-slate-50 border border-slate-100 space-y-2">
                            <div class="flex items-center gap-2 text-blue-700 font-bold text-sm">
                                <i class="fa-solid fa-code-pull-request text-base"></i>
                                <span>২৪ ঘণ্টায় পার্সোনাল কোড রিভিউ</span>
                            </div>
                            <p class="text-xs text-slate-600 leading-relaxed">
                                আপনার গিটহাব রিপোজিটরির কোড রিভিউ করে কমেন্ট সহ পিআর (Pull Request) ফিডব্যাক দেওয়া হবে ইন্ডাস্ট্রি কোডিং স্ট্যান্ডার্ড অনুযায়ী।
                            </p>
                        </div>

                        <div class="p-4 rounded-xl bg-slate-50 border border-slate-100 space-y-2">
                            <div class="flex items-center gap-2 text-amber-700 font-bold text-sm">
                                <i class="fa-solid fa-briefcase text-base"></i>
                                <span>জব প্লেসমেন্ট ও রেজুমে সাপোর্ট</span>
                            </div>
                            <p class="text-xs text-slate-600 leading-relaxed">
                                কোর্স শেষে মক টেকনিক্যাল ইন্টারভিউ নেওয়া হবে এবং পার্টনার কোম্পানিতে সরাসরি ইন্টার্ন ও জুনিয়র ডেভেলপার পজিশনে সিভি পাঠানো হবে।
                            </p>
                        </div>
                    </div>
                </div>

            </div>


            <!-- Right Sidebar: STICKY PRICING & ENROLLMENT CARD -->
            <div id="fees-payment" class="lg:col-span-4 sticky top-28 space-y-6">
                
                <div class="bg-white rounded-2xl border-2 border-emerald-500 shadow-xl p-6 sm:p-7 space-y-6">
                    
                    <!-- Pricing Header -->
                    <div class="text-center pb-4 border-b border-slate-100">
                        <span class="inline-block px-3 py-1 rounded-full text-xs font-bold bg-amber-100 text-amber-800 mb-2">
                            🔥 ভর্তি চলছে • লিমিটেড সিট ({{ $flagshipCourse->batch_no }})
                        </span>
                        
                        <div class="flex items-baseline justify-center gap-3">
                            <span class="text-4xl font-black text-slate-900">৳{{ number_format($flagshipCourse->effective_price) }}</span>
                            @if($flagshipCourse->discount_price)
                                <span class="text-base text-slate-400 line-through">৳{{ number_format($flagshipCourse->regular_price) }}</span>
                                <span class="text-xs font-bold text-rose-600 bg-rose-50 px-2 py-0.5 rounded">
                                    {{ $flagshipCourse->discount_percentage }}% ছাড়
                                </span>
                            @endif
                        </div>
                        <p class="text-xs text-slate-500 mt-1">এককালীন ফি • কোনো গোপন চার্জ নেই • লাইফটাইম অ্যাক্সেস</p>
                    </div>

                    <!-- Features checklist in card -->
                    <div class="space-y-3 text-xs text-slate-700">
                        <div class="flex items-center gap-2.5">
                            <i class="fa-solid fa-circle-check text-emerald-600 text-sm"></i>
                            <span class="font-medium"><strong>{{ $flagshipCourse->total_lectures }}টি লাইভ ক্লাস</strong> (Zoom ও Google Meet)</span>
                        </div>
                        <div class="flex items-center gap-2.5">
                            <i class="fa-solid fa-circle-check text-emerald-600 text-sm"></i>
                            <span class="font-medium"><strong>{{ $flagshipCourse->total_exams }}টি অনলাইন পরীক্ষা</strong> ও প্র্যাক্টিক্যাল কুইজ</span>
                        </div>
                        <div class="flex items-center gap-2.5">
                            <i class="fa-solid fa-circle-check text-emerald-600 text-sm"></i>
                            <span class="font-medium"><strong>{{ $flagshipCourse->total_projects }}টি রিয়েল-ওয়ার্ল্ড প্রজেক্ট</strong> (LMS, ই-কমার্স)</span>
                        </div>
                        <div class="flex items-center gap-2.5">
                            <i class="fa-solid fa-circle-check text-emerald-600 text-sm"></i>
                            <span class="font-medium">প্রতিদিন লাইভ মেন্টরশিপ ও স্ক্রিন শেয়ারিং</span>
                        </div>
                        <div class="flex items-center gap-2.5">
                            <i class="fa-solid fa-circle-check text-emerald-600 text-sm"></i>
                            <span class="font-medium">ভেরিফায়েবল সার্টিফিকেট ও জব সাপোর্ট</span>
                        </div>
                    </div>

                    <!-- Online Payment Supported -->
                    <div class="p-3.5 rounded-xl bg-slate-50 border border-slate-200/80 text-center space-y-2">
                        <span class="text-xs font-bold text-slate-600 block">ইনস্ট্যান্ট পেমেন্ট মেথড:</span>
                        <div class="flex items-center justify-center gap-2">
                            <span class="px-2.5 py-1 rounded bg-pink-600 text-white font-bold text-xs">বিকাশ</span>
                            <span class="px-2.5 py-1 rounded bg-orange-500 text-white font-bold text-xs">নগদ</span>
                            <span class="px-2.5 py-1 rounded bg-purple-600 text-white font-bold text-xs">রকেট</span>
                            <span class="px-2.5 py-1 rounded bg-blue-600 text-white font-bold text-xs">কার্ড</span>
                        </div>
                        <span class="text-[11px] text-slate-400 block">পেমেন্টের সাথে সাথেই স্বয়ংক্রিয়ভাবে অ্যাক্সেস ওপেন হবে</span>
                    </div>

                    <!-- Big CTA Button -->
                    <div class="space-y-3">
                        <a href="{{ route('courses.checkout', $flagshipCourse->slug) }}" class="w-full flex items-center justify-center gap-2 py-4 rounded-xl bg-gradient-to-r from-emerald-600 to-teal-600 hover:from-emerald-700 hover:to-teal-700 text-white font-extrabold text-base shadow-lg shadow-emerald-600/30 hover:shadow-emerald-600/50 hover:scale-[1.02] transition-all duration-200">
                            <span>ভর্তি নিশ্চিত করুন (৳{{ number_format($flagshipCourse->effective_price) }})</span>
                            <i class="fa-solid fa-arrow-right text-sm"></i>
                        </a>

                        <a href="https://wa.me/8801700000000" target="_blank" class="w-full flex items-center justify-center gap-2 py-3 rounded-xl border border-slate-300 hover:bg-slate-50 text-slate-700 font-bold text-xs transition">
                            <i class="fa-brands fa-whatsapp text-emerald-600 text-sm"></i>
                            <span>হোয়াটসঅ্যাপে কাউন্সেলিং নিন</span>
                        </a>
                    </div>

                    <!-- Money back guarantee notice -->
                    <div class="flex items-center justify-center gap-2 text-slate-400 text-xs text-center">
                        <i class="fa-solid fa-shield-halved text-emerald-600"></i>
                        <span>৭ দিনের মানিব্যাক গ্যারান্টি পলিসি</span>
                    </div>

                </div>

                <!-- Instructor mini card -->
                <div class="bg-white rounded-2xl border border-slate-200/80 p-5 shadow-xs space-y-3">
                    <h5 class="text-xs font-bold text-slate-500 uppercase tracking-wider">কোর্স ইন্সট্রাক্টর:</h5>
                    @if($lead = $flagshipCourse->leadInstructor())
                        <div class="flex items-center gap-3">
                            <img src="{{ $lead->avatar }}" alt="{{ $lead->name }}" class="w-12 h-12 rounded-full object-cover border border-emerald-400">
                            <div>
                                <h6 class="text-sm font-bold text-slate-900">{{ $lead->bangla_name ?? $lead->name }}</h6>
                                <p class="text-xs text-slate-500 leading-tight">{{ $lead->title }}</p>
                            </div>
                        </div>
                    @endif
                </div>

            </div>

        </div>

    </div>
</section>


<!-- ==================== 4. ALL COURSES EXPLORER ==================== -->
<section id="all-courses" class="py-16 sm:py-24 bg-white border-t border-slate-200">
    <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8">
        
        <div class="flex flex-col md:flex-row md:items-end justify-between gap-4 mb-12">
            <div>
                <span class="text-xs font-bold text-emerald-700 uppercase tracking-widest bg-emerald-100/80 px-3 py-1 rounded-full">
                    কোর্স ক্যাটালগ
                </span>
                <h2 class="text-3xl font-extrabold text-slate-900 mt-2">আমাদের অন্যান্য লাইভ কোর্সসমূহ</h2>
                <p class="text-slate-600 text-sm mt-1">আপনার আগ্রহ অনুযায়ী সেরা কোর্সটি বেছে নিন এবং লাইভ ক্লাসে অংশ নিন</p>
            </div>
        </div>

        <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-3 gap-8">
            @foreach($courses as $course)
                <div class="bg-white rounded-2xl border border-slate-200 overflow-hidden shadow-xs hover:shadow-xl hover:-translate-y-1 transition-all duration-300 flex flex-col justify-between">
                    <div>
                        <!-- Thumbnail -->
                        <div class="relative aspect-video bg-slate-100 overflow-hidden">
                            <img src="{{ $course->thumbnail }}" alt="{{ $course->title }}" class="w-full h-full object-cover">
                            <span class="absolute top-3 left-3 px-2.5 py-1 rounded-md text-[11px] font-bold bg-slate-900/80 backdrop-blur text-emerald-400">
                                {{ $course->category }}
                            </span>
                            <span class="absolute bottom-3 right-3 px-2 py-0.5 rounded text-[10px] font-bold bg-rose-600 text-white flex items-center gap-1">
                                <span class="w-1.5 h-1.5 rounded-full bg-white animate-ping"></span> লাইভ ক্লাস
                            </span>
                        </div>

                        <!-- Content -->
                        <div class="p-6 space-y-3">
                            <div class="flex items-center justify-between text-xs text-slate-500">
                                <span class="font-semibold text-emerald-700">{{ $course->batch_no }}</span>
                                <span><i class="fa-regular fa-clock mr-1"></i>{{ $course->total_weeks }} সপ্তাহ</span>
                            </div>

                            <h3 class="text-lg font-bold text-slate-900 hover:text-emerald-700 transition leading-snug">
                                <a href="{{ route('courses.show', $course->slug) }}">
                                    {{ $course->bangla_title ?? $course->title }}
                                </a>
                            </h3>

                            <p class="text-xs text-slate-600 line-clamp-2 leading-relaxed">
                                {{ $course->subtitle }}
                            </p>

                            <!-- Features Pill -->
                            <div class="pt-2 flex flex-wrap gap-1.5 text-[11px] text-slate-600">
                                <span class="px-2 py-0.5 rounded bg-slate-100"><i class="fa-solid fa-video mr-1 text-blue-600"></i>{{ $course->live_platform }}</span>
                                <span class="px-2 py-0.5 rounded bg-slate-100"><i class="fa-solid fa-award mr-1 text-amber-500"></i>সার্টিফিকেট</span>
                            </div>
                        </div>
                    </div>

                    <!-- Bottom Pricing & Action -->
                    <div class="p-6 pt-0 border-t border-slate-100 mt-4 flex items-center justify-between">
                        <div>
                            <span class="text-xs text-slate-400 block">কোর্স ফি</span>
                            <div class="flex items-baseline gap-1.5">
                                <span class="text-xl font-black text-slate-900">৳{{ number_format($course->effective_price) }}</span>
                                @if($course->discount_price)
                                    <span class="text-xs text-slate-400 line-through">৳{{ number_format($course->regular_price) }}</span>
                                @endif
                            </div>
                        </div>
                        <a href="{{ route('courses.show', $course->slug) }}" class="px-4 py-2 rounded-xl bg-emerald-600 hover:bg-emerald-700 text-white font-bold text-xs transition">
                            বিস্তারিত দেখুন
                        </a>
                    </div>
                </div>
            @endforeach
        </div>

    </div>
</section>


<!-- ==================== 5. WHY AMAR BANGLA ACADEMY (6 PILLARS) ==================== -->
<section class="py-16 sm:py-24 bg-slate-900 text-white relative overflow-hidden">
    <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8 relative z-10">
        
        <div class="text-center max-w-2xl mx-auto mb-16">
            <span class="text-xs font-bold text-emerald-400 uppercase tracking-widest bg-emerald-950/80 px-3 py-1 rounded-full border border-emerald-800">
                আমাদের বিশেষত্ব
            </span>
            <h2 class="text-3xl sm:text-4xl font-extrabold mt-3">কেন আমার বাংলা একাডেমিতে শিখবেন?</h2>
            <p class="text-slate-400 text-sm sm:text-base mt-2">অন্যান্য প্রথাগত কোর্সের তুলনায় আমাদের প্ল্যাটফর্মের স্বাতন্ত্র্য</p>
        </div>

        <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-3 gap-8">
            
            <div class="p-6 rounded-2xl bg-slate-800/80 border border-slate-700/80 space-y-3">
                <div class="w-12 h-12 rounded-xl bg-emerald-600/30 text-emerald-400 flex items-center justify-center text-xl">
                    <i class="fa-solid fa-headset"></i>
                </div>
                <h3 class="text-lg font-bold">প্রতিদিন লাইভ গুগল মিট সাপোর্ট</h3>
                <p class="text-xs text-slate-400 leading-relaxed">
                    সপ্তাহের ৭ দিন বিকেল থেকে মধ্যরাত পর্যন্ত গুগল মিট খোলা থাকে। যেকোনো এরর বা সমস্যা স্ক্রিন শেয়ার করে রিয়েল-টাইম সমাধান নিন।
                </p>
            </div>

            <div class="p-6 rounded-2xl bg-slate-800/80 border border-slate-700/80 space-y-3">
                <div class="w-12 h-12 rounded-xl bg-blue-600/30 text-blue-400 flex items-center justify-center text-xl">
                    <i class="fa-solid fa-video"></i>
                </div>
                <h3 class="text-lg font-bold">ইন্টারঅ্যাক্টিভ জুম লাইভ ক্লাস</h3>
                <p class="text-xs text-slate-400 leading-relaxed">
                    শুধুমাত্র প্যাসিভ ভিডিও নয়, ইন্সট্রাক্টরের সাথে সরাসরি কথা বলে, প্রশ্ন করে কনসেপ্ট একদম পরিষ্কার করে নেওয়ার সুযোগ।
                </p>
            </div>

            <div class="p-6 rounded-2xl bg-slate-800/80 border border-slate-700/80 space-y-3">
                <div class="w-12 h-12 rounded-xl bg-amber-600/30 text-amber-400 flex items-center justify-center text-xl">
                    <i class="fa-solid fa-clipboard-check"></i>
                </div>
                <h3 class="text-lg font-bold">রিয়েল-টাইম এক্সাম ও কুইজ</h3>
                <p class="text-xs text-slate-400 leading-relaxed">
                    প্রতিটি উইকের লেকচার শেষে অনলাইন পরীক্ষা দিয়ে নিজের অগ্রগতি যাচাই করার সুবিধা এবং লিডারবোর্ড ট্র্যাকিং।
                </p>
            </div>

            <div class="p-6 rounded-2xl bg-slate-800/80 border border-slate-700/80 space-y-3">
                <div class="w-12 h-12 rounded-xl bg-pink-600/30 text-pink-400 flex items-center justify-center text-xl">
                    <i class="fa-solid fa-wallet"></i>
                </div>
                <h3 class="text-lg font-bold">সহজ বিকাশ ও নগদ পেমেন্ট</h3>
                <p class="text-xs text-slate-400 leading-relaxed">
                    কোনো আন্তর্জাতিক ক্রেডিট কার্ডের ঝামেলা নেই। দেশের যেকোনো প্রান্ত থেকে বিকাশ, নগদ, রকেট দিয়ে সেকেন্ডে এনরোলমেন্ট।
                </p>
            </div>

            <div class="p-6 rounded-2xl bg-slate-800/80 border border-slate-700/80 space-y-3">
                <div class="w-12 h-12 rounded-xl bg-teal-600/30 text-teal-400 flex items-center justify-center text-xl">
                    <i class="fa-solid fa-cloud-arrow-up"></i>
                </div>
                <h3 class="text-lg font-bold">cPanel ও ক্লাউড ডেপ্লয়মেন্ট</h3>
                <p class="text-xs text-slate-400 leading-relaxed">
                    প্রজেক্ট শুধু লোকালহোস্টে নয়, সরাসরি সিপ্যানেল ও ক্লাউড লাইভ সার্ভারে ডিপ্লয় করে পাবলিকলি শোকেস করার পূর্ণাঙ্গ গাইডলাইন।
                </p>
            </div>

            <div class="p-6 rounded-2xl bg-slate-800/80 border border-slate-700/80 space-y-3">
                <div class="w-12 h-12 rounded-xl bg-rose-600/30 text-rose-400 flex items-center justify-center text-xl">
                    <i class="fa-solid fa-award"></i>
                </div>
                <h3 class="text-lg font-bold">ভেরিফায়েবল ডিজিটাল সার্টিফিকেট</h3>
                <p class="text-xs text-slate-400 leading-relaxed">
                    কোর্স ও প্রজেক্ট শেষ করার পর কিউআর কোড যুক্ত আন্তর্জাতিক ভেরিফায়েবল ডিজিটাল সার্টিফিকেট, যা লিংকডইনে সরাসরি যুক্ত করা যায়।
                </p>
            </div>

        </div>

    </div>
</section>


<!-- ==================== 6. FREQUENTLY ASKED QUESTIONS (FAQ) ==================== -->
<section class="py-16 sm:py-24 bg-white" x-data="{ openFaq: 1 }">
    <div class="max-w-4xl mx-auto px-4 sm:px-6 lg:px-8">
        
        <div class="text-center mb-12">
            <span class="text-xs font-bold text-emerald-700 uppercase tracking-widest bg-emerald-100/80 px-3 py-1 rounded-full">
                সচরাচর জিজ্ঞাসা
            </span>
            <h2 class="text-3xl font-extrabold text-slate-900 mt-2">আপনার মনে কি কোনো প্রশ্ন আছে?</h2>
            <p class="text-slate-500 text-sm mt-1">ভর্তি সংক্রান্ত যেকোনো প্রশ্নের সহজ উত্তর নিচে জেনে নিন</p>
        </div>

        <div class="space-y-4">
            
            <div class="border border-slate-200 rounded-xl overflow-hidden">
                <button @click="openFaq = (openFaq === 1 ? null : 1)" class="w-full p-5 text-left bg-slate-50 hover:bg-emerald-50/50 flex items-center justify-between font-bold text-slate-900 text-sm sm:text-base transition">
                    <span>লাইভ ক্লাস কীভাবে হবে এবং ক্লাসের লিংক কোথায় পাব?</span>
                    <i class="fa-solid fa-chevron-down text-slate-400 text-xs transition-transform" :class="openFaq === 1 ? 'rotate-180 text-emerald-600' : ''"></i>
                </button>
                <div x-show="openFaq === 1" class="p-5 text-xs sm:text-sm text-slate-600 bg-white border-t border-slate-100 leading-relaxed">
                    লাইভ ক্লাসগুলো Zoom এবং Google Meet এর মাধ্যমে সরাসরি ইন্টারঅ্যাক্টিভ পদ্ধতিতে অনুষ্ঠিত হবে। কোর্সটিতে এনরোল করার সাথে সাথে আপনার স্টুডেন্ট ড্যাশবোর্ডে "লাইভ ক্লাসরুম" ট্যাব সক্রিয় হবে, যেখানে প্রতি ক্লাসের সরাসরি ওয়ান-ক্লিক লিংক, মিটিং আইডি ও পাসকোড প্রদর্শিত হবে। এছাড়াও প্রতি ক্লাসের আগে ইমেইল ও এসএমএস নোটিফিকেশন পাবেন।
                </div>
            </div>

            <div class="border border-slate-200 rounded-xl overflow-hidden">
                <button @click="openFaq = (openFaq === 2 ? null : 2)" class="w-full p-5 text-left bg-slate-50 hover:bg-emerald-50/50 flex items-center justify-between font-bold text-slate-900 text-sm sm:text-base transition">
                    <span>লাইভ ক্লাস মিস হলে কি রেকর্ডিং পাওয়া যাবে?</span>
                    <i class="fa-solid fa-chevron-down text-slate-400 text-xs transition-transform" :class="openFaq === 2 ? 'rotate-180 text-emerald-600' : ''"></i>
                </button>
                <div x-show="openFaq === 2" class="p-5 text-xs sm:text-sm text-slate-600 bg-white border-t border-slate-100 leading-relaxed">
                    হ্যাঁ, অবশ্যই! প্রতিটি লাইভ ক্লাস শেষ হওয়ার সর্বোচ্চ ২ ঘণ্টার মধ্যে সম্পূর্ণ ক্লাসটির ফুল এইচডি রেকর্ডিং আপনার স্টুডেন্ট পোর্টালের নির্দিষ্ট মডিউলে আপলোড করা হবে। আপনি যেকোনো সময় যতবার ইচ্ছা ক্লাস রেকর্ডিং দেখতে পারবেন এবং এই রেকর্ডিংয়ের লাইফটাইম অ্যাক্সেস আপনার থাকবে।
                </div>
            </div>

            <div class="border border-slate-200 rounded-xl overflow-hidden">
                <button @click="openFaq = (openFaq === 3 ? null : 3)" class="w-full p-5 text-left bg-slate-50 hover:bg-emerald-50/50 flex items-center justify-between font-bold text-slate-900 text-sm sm:text-base transition">
                    <span>বিকাশ বা নগদ দিয়ে পেমেন্ট করার পর কী করতে হবে?</span>
                    <i class="fa-solid fa-chevron-down text-slate-400 text-xs transition-transform" :class="openFaq === 3 ? 'rotate-180 text-emerald-600' : ''"></i>
                </button>
                <div x-show="openFaq === 3" class="p-5 text-xs sm:text-sm text-slate-600 bg-white border-t border-slate-100 leading-relaxed">
                    আমাদের অনলাইন পেমেন্ট সিস্টেম সম্পূর্ণ স্বয়ংক্রিয়। চেকআউট পেজে গিয়ে বিকাশ, নগদ বা রকেট নির্বাচন করে পেমেন্ট সম্পূর্ণ করা মাত্রই সিস্টেম স্বয়ংক্রিয়ভাবে আপনার এনরোলমেন্ট কনফার্ম করবে এবং স্ক্রিনে লাইভ ক্লাস লিংক ও প্রাইভেট ডিসকর্ড কমিউনিটির ইনভাইটেশন লিংক শো করবে।
                </div>
            </div>

            <div class="border border-slate-200 rounded-xl overflow-hidden">
                <button @click="openFaq = (openFaq === 4 ? null : 4)" class="w-full p-5 text-left bg-slate-50 hover:bg-emerald-50/50 flex items-center justify-between font-bold text-slate-900 text-sm sm:text-base transition">
                    <span>অনলাইন এক্সাম এবং সার্টিফিকেট সিস্টেম কীভাবে কাজ করে?</span>
                    <i class="fa-solid fa-chevron-down text-slate-400 text-xs transition-transform" :class="openFaq === 4 ? 'rotate-180 text-emerald-600' : ''"></i>
                </button>
                <div x-show="openFaq === 4" class="p-5 text-xs sm:text-sm text-slate-600 bg-white border-t border-slate-100 leading-relaxed">
                    প্রতিটি মডিউল শেষে অনলাইন কুইজ এবং মিড-টার্ম ও ফাইনাল প্র্যাক্টিক্যাল কোডিং এক্সাম অনুষ্ঠিত হয়। পরীক্ষায় উত্তীর্ণ হলে এবং ফাইনাল প্রজেক্ট সফলভাবে জমা দিলে আপনি পাবেন ইউনিক আইডি ও কিউআর কোড সম্বলিত অফিসিয়াল সার্টিফিকেট, যা সরাসরি লিংকডইন বা সিভিতে যুক্ত করতে পারবেন।
                </div>
            </div>

        </div>

    </div>
</section>


<!-- ==================== 7. FINAL CALL TO ACTION (CTA) ==================== -->
<section class="py-16 bg-gradient-to-r from-emerald-800 to-teal-800 text-white relative">
    <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8 text-center space-y-6">
        <h2 class="text-3xl sm:text-4xl font-extrabold tracking-tight">
            ক্যারিয়ারে এক ধাপ এগিয়ে যেতে আজই এনরোল করুন
        </h2>
        <p class="text-emerald-100 text-base max-w-2xl mx-auto">
            সীমিত আসনের নতুন লাইভ ব্যাচে ৬০% স্কলারশিপ ছাড়ের সুযোগ হাতছাড়া করবেন না।
        </p>
        <div class="flex flex-col sm:flex-row items-center justify-center gap-4 pt-2">
            <a href="{{ route('courses.checkout', $flagshipCourse->slug) }}" class="w-full sm:w-auto px-8 py-4 rounded-xl bg-amber-400 hover:bg-amber-500 text-slate-950 font-extrabold text-base shadow-lg transition">
                ভর্তি নিশ্চিত করুন (৳{{ number_format($flagshipCourse->effective_price) }})
            </a>
            <a href="tel:+8801700000000" class="w-full sm:w-auto px-6 py-4 rounded-xl bg-emerald-900/60 hover:bg-emerald-900 border border-emerald-600 text-white font-bold text-base transition flex items-center justify-center gap-2">
                <i class="fa-solid fa-phone"></i>
                <span>সরাসরি কথা বলুন: +৮৮০ ১৭০০-০০০০০০</span>
            </a>
        </div>
    </div>
</section>

@endsection
