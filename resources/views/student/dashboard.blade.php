<!DOCTYPE html>
<html lang="bn" class="scroll-smooth">
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <meta name="csrf-token" content="{{ csrf_token() }}">

    <title>স্টুডেন্ট এলএমএস পোর্টাল - আমার বাংলা একাডেমি</title>

    <!-- Google Fonts: Hind Siliguri & Inter -->
    <link rel="preconnect" href="https://fonts.googleapis.com">
    <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
    <link href="https://fonts.googleapis.com/css2?family=Hind+Siliguri:wght@400;500;600;700&family=Inter:wght@400;500;600;700&display=swap" rel="stylesheet">

    <!-- Font Awesome Icons -->
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.5.1/css/all.min.css">

    <!-- Tailwind CSS CDN -->
    <script src="https://cdn.tailwindcss.com"></script>
    <script>
        tailwind.config = {
            theme: {
                extend: {
                    colors: {
                        emerald: {
                            50: '#ecfdf5',
                            100: '#d1fae5',
                            500: '#10b981',
                            600: '#059669',
                            700: '#047857',
                            800: '#065f46',
                            900: '#064e3b',
                        }
                    },
                    fontFamily: {
                        sans: ['"Hind Siliguri"', '"Inter"', 'sans-serif'],
                        bangla: ['"Hind Siliguri"', 'sans-serif'],
                    }
                }
            }
        }
    </script>

    <!-- Alpine.js -->
    <script defer src="https://cdn.jsdelivr.net/npm/alpinejs@3.14.3/dist/cdn.min.js"></script>

    <style>
        body { font-family: 'Hind Siliguri', 'Inter', sans-serif; }
    </style>
</head>
<body class="bg-slate-100 text-slate-800 antialiased font-bangla min-h-screen flex flex-col" x-data="{
    activeTab: 'live',
    openModule: 1
}">

    <!-- Top Student Header -->
    <header class="bg-white border-b border-slate-200 sticky top-0 z-40 shadow-sm">
        <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8">
            <div class="flex items-center justify-between h-16 sm:h-20">
                
                <!-- Brand & Portal Badge -->
                <div class="flex items-center gap-3">
                    <a href="{{ route('student.dashboard') }}" class="flex items-center gap-3 group">
                        <div class="w-10 h-10 sm:w-11 sm:h-11 rounded-2xl bg-gradient-to-tr from-emerald-700 to-emerald-500 text-white flex items-center justify-center font-bold text-xl shadow-sm">
                            আ
                        </div>
                        <div class="leading-tight">
                            <span class="text-base sm:text-lg font-bold text-slate-900 group-hover:text-emerald-700 transition">আমার বাংলা একাডেমি</span>
                            <div class="flex items-center gap-1.5">
                                <span class="inline-block w-2 h-2 rounded-full bg-emerald-500 animate-pulse"></span>
                                <span class="text-xs font-semibold text-emerald-700 bg-emerald-50 px-2 py-0.5 rounded-full border border-emerald-200">শিক্ষার্থী পোর্টাল</span>
                            </div>
                        </div>
                    </a>
                </div>

                <!-- Right Actions: Student Info & Logout -->
                <div class="flex items-center gap-2 sm:gap-4">
                    @if($course?->live_meeting_url)
                        <a href="{{ route('student.live-class') }}" target="_blank"
                           class="hidden sm:inline-flex items-center gap-2 bg-rose-600 hover:bg-rose-700 text-white text-xs font-bold px-3.5 py-2 rounded-xl shadow-sm animate-pulse transition">
                            <i class="fa-solid fa-video"></i>
                            <span>লাইভ ক্লাসে যোগ দিন</span>
                        </a>
                    @endif

                    <!-- User Card -->
                    <div class="flex items-center gap-2.5 bg-slate-50 border border-slate-200 px-3 py-1.5 rounded-xl">
                        <div class="w-8 h-8 rounded-full bg-emerald-600 text-white flex items-center justify-center text-xs font-bold">
                            {{ strtoupper(substr($user->name, 0, 1)) }}
                        </div>
                        <div class="hidden md:flex flex-col text-left leading-tight">
                            <span class="text-xs font-bold text-slate-900">{{ $user->name }}</span>
                            <span class="text-[10px] text-slate-500 font-mono">{{ $user->phone ?? $user->email }}</span>
                        </div>
                    </div>

                    <!-- Logout Button -->
                    <form method="POST" action="{{ route('student.logout') }}" class="inline">
                        @csrf
                        <button type="submit" title="লগআউট" class="inline-flex items-center gap-1.5 text-xs font-semibold text-red-600 hover:text-red-700 bg-red-50 hover:bg-red-100 border border-red-200 px-3 py-2 rounded-xl transition">
                            <i class="fa-solid fa-right-from-bracket"></i>
                            <span class="hidden sm:inline">লগআউট</span>
                        </button>
                    </form>
                </div>

            </div>
        </div>
    </header>

    <!-- Flash Message Container -->
    <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8 mt-4 w-full">
        @if(session('success'))
            <div class="p-4 rounded-2xl bg-emerald-50 border border-emerald-200 text-emerald-800 flex items-center justify-between shadow-xs mb-3">
                <div class="flex items-center gap-2.5">
                    <i class="fa-solid fa-circle-check text-emerald-600 text-lg"></i>
                    <span class="text-xs sm:text-sm font-semibold">{{ session('success') }}</span>
                </div>
                <button onclick="this.parentElement.remove()" class="text-emerald-500 hover:text-emerald-700 p-1">
                    <i class="fa-solid fa-xmark"></i>
                </button>
            </div>
        @endif

        @if(session('info'))
            <div class="p-4 rounded-2xl bg-blue-50 border border-blue-200 text-blue-800 flex items-center justify-between shadow-xs mb-3">
                <div class="flex items-center gap-2.5">
                    <i class="fa-solid fa-circle-info text-blue-600 text-lg"></i>
                    <span class="text-xs sm:text-sm font-semibold">{{ session('info') }}</span>
                </div>
                <button onclick="this.parentElement.remove()" class="text-blue-500 hover:text-blue-700 p-1">
                    <i class="fa-solid fa-xmark"></i>
                </button>
            </div>
        @endif

        @if(session('error'))
            <div class="p-4 rounded-2xl bg-rose-50 border border-rose-200 text-rose-800 flex items-center justify-between shadow-xs mb-3">
                <div class="flex items-center gap-2.5">
                    <i class="fa-solid fa-circle-exclamation text-rose-600 text-lg"></i>
                    <span class="text-xs sm:text-sm font-semibold">{{ session('error') }}</span>
                </div>
                <button onclick="this.parentElement.remove()" class="text-rose-500 hover:text-rose-700 p-1">
                    <i class="fa-solid fa-xmark"></i>
                </button>
            </div>
        @endif
    </div>

    <!-- Main Content Container -->
    <main class="flex-1 max-w-7xl mx-auto px-4 sm:px-6 lg:px-8 py-5 sm:py-7 w-full space-y-6">

        <!-- 1. LIVE CLASSROOM HERO SECTION (Direct Meeting Redirect Feature) -->
        <div class="bg-gradient-to-br from-slate-900 via-emerald-950 to-slate-950 rounded-3xl p-6 sm:p-8 text-white shadow-xl relative overflow-hidden border border-emerald-900/60">
            <!-- Decorative Background Accent -->
            <div class="absolute -right-16 -top-16 w-64 h-64 bg-emerald-500/10 rounded-full blur-3xl pointer-events-none"></div>

            <div class="relative z-10 grid grid-cols-1 lg:grid-cols-12 gap-6 items-center">
                <!-- Left text -->
                <div class="lg:col-span-8 space-y-3">
                    <div class="flex items-center gap-2 flex-wrap">
                        @if($course?->is_live_now)
                            <span class="inline-flex items-center gap-1.5 px-3 py-1 rounded-full text-xs font-black bg-rose-600 text-white animate-pulse shadow-md shadow-rose-600/30">
                                <span class="w-2 h-2 rounded-full bg-white"></span>
                                লাইভ ক্লাস চলছে (LIVE NOW)
                            </span>
                        @else
                            <span class="inline-flex items-center gap-1.5 px-3 py-1 rounded-full text-xs font-bold bg-amber-500/20 text-amber-300 border border-amber-500/30">
                                <i class="fa-solid fa-clock text-xs"></i>
                                শিডিউল করা লাইভ ক্লাস
                            </span>
                        @endif
                        <span class="text-xs text-slate-300 font-medium">প্ল্যাটফর্ম: {{ $course?->live_platform ?? 'Zoom & Google Meet' }}</span>
                    </div>

                    <h2 class="text-xl sm:text-2xl lg:text-3xl font-extrabold leading-snug">
                        {{ $course?->live_class_topic ?? ($course?->bangla_title ?? 'সাংবাদিকতা, আইন ও উপস্থাপনা') }}
                    </h2>

                    <!-- Prominent Class Schedule Box when not live -->
                    @if(!$course?->is_live_now)
                        <div class="inline-flex items-center gap-3 p-3 sm:px-4 sm:py-2.5 bg-amber-500/10 border border-amber-500/30 rounded-2xl text-amber-200">
                            <div class="w-9 h-9 rounded-xl bg-amber-500/20 text-amber-300 flex items-center justify-center text-base shrink-0">
                                <i class="fa-regular fa-calendar-check"></i>
                            </div>
                            <div>
                                <span class="text-[11px] text-amber-300/80 block font-medium">পরবর্তী ক্লাসের সময়সূচি:</span>
                                <strong class="text-sm sm:text-base text-amber-200 font-bold block">
                                    {{ $course?->live_schedule_text ?? 'প্রতি বৃহস্পতি, শুক্র ও শনি রাত ৯:০০ টা' }}
                                </strong>
                            </div>
                        </div>
                    @endif

                    <p class="text-xs sm:text-sm text-slate-300 leading-relaxed max-w-2xl">
                        @if($course?->is_live_now)
                            ক্লাস শুরু হয়েছে! নিচের বাটনে ক্লিক করে সরাসরি Zoom বা Google Meet-এ যুক্ত হতে পারবেন। কোনো পাসকোড লাগলে তা নিচে দেওয়া আছে।
                        @else
                            নির্ধারিত ক্লাসের সময়ে এডমিন থেকে ক্লাস শুরু করা হলে নিচের বাটনটি স্বয়ংক্রিয়ভাবে সক্রিয় হবে এবং আপনি সরাসরি ক্লাসে যোগ দিতে পারবেন।
                        @endif
                    </p>

                    <!-- Meeting Details Badges if available when live -->
                    @if($course?->is_live_now && ($course?->live_meeting_id || $course?->live_meeting_passcode))
                        <div class="flex items-center gap-3 pt-1 text-xs font-mono flex-wrap">
                            @if($course->live_meeting_id)
                                <div class="bg-white/10 px-3 py-1 rounded-xl border border-white/15 flex items-center gap-2">
                                    <span class="text-slate-400 font-sans">মিটিং আইডি:</span>
                                    <strong class="text-white select-all">{{ $course->live_meeting_id }}</strong>
                                </div>
                            @endif
                            @if($course->live_meeting_passcode)
                                <div class="bg-white/10 px-3 py-1 rounded-xl border border-white/15 flex items-center gap-2">
                                    <span class="text-slate-400 font-sans">পাসকোড:</span>
                                    <strong class="text-white select-all">{{ $course->live_meeting_passcode }}</strong>
                                </div>
                            @endif
                        </div>
                    @endif
                </div>

                <!-- Right Big Action Button -->
                <div class="lg:col-span-4 flex flex-col items-stretch sm:items-start lg:items-end justify-center">
                    @if($course?->is_live_now)
                        <!-- Active Button when class is Live Now -->
                        <a href="{{ route('student.live-class') }}" target="_blank"
                           class="w-full sm:w-auto inline-flex items-center justify-center gap-2.5 bg-gradient-to-r from-emerald-500 to-teal-500 hover:from-emerald-600 hover:to-teal-600 text-white font-black text-sm sm:text-base px-6 py-4 rounded-2xl shadow-xl shadow-emerald-500/30 transform hover:-translate-y-0.5 active:scale-95 transition">
                            <span class="w-2.5 h-2.5 rounded-full bg-white animate-ping"></span>
                            <i class="fa-solid fa-video text-lg"></i>
                            <span>সরাসরি ক্লাসে যোগ দিন</span>
                            <i class="fa-solid fa-arrow-up-right-from-square text-xs"></i>
                        </a>
                        <span class="text-[11px] text-emerald-300 font-semibold mt-2 text-center lg:text-right w-full">
                            এখন লাইভ চলছে — এক ক্লিকে ক্লাসে যোগ দিন
                        </span>
                    @else
                        <!-- Disabled Button before Scheduled Time / when not live -->
                        <div class="w-full sm:w-auto inline-flex items-center justify-center gap-2.5 bg-slate-800/90 text-slate-400 font-bold text-sm sm:text-base px-6 py-4 rounded-2xl border border-slate-700/80 cursor-not-allowed select-none shadow-inner">
                            <i class="fa-solid fa-lock text-slate-500"></i>
                            <span>ক্লাস শুরুর সময়ে লিংক সক্রিয় হবে</span>
                        </div>
                        <div class="w-full text-center lg:text-right mt-2 space-y-0.5">
                            <span class="text-[11px] text-amber-300 font-semibold block">
                                <i class="fa-solid fa-clock text-[10px] mr-1"></i>
                                শিডিউল: {{ $course?->live_schedule_text ?? 'রাত ৯:০০ টা' }}
                            </span>
                            <span class="text-[10px] text-slate-400 block">
                                নির্ধারিত সময়ে বাটনটি সক্রিয় হবে
                            </span>
                        </div>
                    @endif
                </div>
            </div>
        </div>

        <!-- 2. STUDENT STATS & BATCH INFO -->
        <div class="grid grid-cols-2 lg:grid-cols-4 gap-3 sm:gap-4">
            <!-- Enrolled Course -->
            <div class="bg-white p-4 rounded-2xl border border-slate-200 shadow-sm flex items-center gap-3">
                <div class="w-11 h-11 rounded-xl bg-emerald-50 text-emerald-600 flex items-center justify-center text-lg shrink-0">
                    <i class="fa-solid fa-book-open-reader"></i>
                </div>
                <div class="min-w-0">
                    <div class="text-[11px] font-semibold text-slate-500">ভর্তিকৃত কোর্স</div>
                    <div class="text-xs sm:text-sm font-bold text-slate-900 truncate" title="{{ $course?->bangla_title }}">
                        {{ $course?->bangla_title ?? 'সাংবাদিকতা, আইন ও উপস্থাপনা' }}
                    </div>
                </div>
            </div>

            <!-- Batch & Schedule -->
            <div class="bg-white p-4 rounded-2xl border border-slate-200 shadow-sm flex items-center gap-3">
                <div class="w-11 h-11 rounded-xl bg-blue-50 text-blue-600 flex items-center justify-center text-lg shrink-0">
                    <i class="fa-solid fa-calendar-days"></i>
                </div>
                <div class="min-w-0">
                    <div class="text-[11px] font-semibold text-slate-500">ব্যাচ নম্বর</div>
                    <div class="text-xs sm:text-sm font-bold text-slate-900">
                        {{ $course?->batch_no ?? '১ম ব্যাচ' }} (শুরু: ৫ নভেম্বর)
                    </div>
                </div>
            </div>

            <!-- Total Classes -->
            <div class="bg-white p-4 rounded-2xl border border-slate-200 shadow-sm flex items-center gap-3">
                <div class="w-11 h-11 rounded-xl bg-purple-50 text-purple-600 flex items-center justify-center text-lg shrink-0">
                    <i class="fa-solid fa-chalkboard-user"></i>
                </div>
                <div class="min-w-0">
                    <div class="text-[11px] font-semibold text-slate-500">মোট ক্লাস</div>
                    <div class="text-xs sm:text-sm font-bold text-slate-900">
                        ১২টি লাইভ ক্লাস (১ মাস)
                    </div>
                </div>
            </div>

            <!-- Enrollment Status -->
            <div class="bg-white p-4 rounded-2xl border border-slate-200 shadow-sm flex items-center gap-3">
                <div class="w-11 h-11 rounded-xl bg-green-50 text-green-600 flex items-center justify-center text-lg shrink-0">
                    <i class="fa-solid fa-circle-check"></i>
                </div>
                <div class="min-w-0">
                    <div class="text-[11px] font-semibold text-slate-500">ভর্তি স্ট্যাটাস</div>
                    <div class="text-xs sm:text-sm font-bold text-green-700 flex items-center gap-1">
                        <span>সক্রিয় শিক্ষার্থী</span>
                    </div>
                </div>
            </div>
        </div>

        <!-- 3. OFFICIAL WHATSAPP BATCH GROUP CARD -->
        <div class="p-5 rounded-2xl bg-gradient-to-r from-emerald-50 via-teal-50 to-green-50 border border-emerald-200 shadow-xs flex flex-col sm:flex-row items-start sm:items-center justify-between gap-4">
            <div class="flex items-center gap-3.5">
                <div class="w-12 h-12 rounded-2xl bg-green-500 text-white flex items-center justify-center text-2xl shadow-sm shrink-0">
                    <i class="fa-brands fa-whatsapp"></i>
                </div>
                <div>
                    <h3 class="font-extrabold text-sm sm:text-base text-slate-900">
                        ১ম ব্যাচের অফিসিয়াল WhatsApp গ্রুপে যুক্ত থাকুন
                    </h3>
                    <p class="text-xs text-slate-600 mt-0.5">
                        সকল জরুরি নোটিশ, ক্লাসের লিংক, রেকর্ডিং এবং ট্রেইনারদের সাথে সরাসরি প্রশ্নোত্তর এই গ্রুপে পরিচালিত হয়।
                    </p>
                </div>
            </div>
            <a href="https://chat.whatsapp.com/EeM7caaCmE79NR0BFdrwWh?s=sh&p=a&iam=2" target="_blank"
               class="inline-flex items-center gap-2 bg-green-600 hover:bg-green-700 text-white font-bold text-xs sm:text-sm px-4 py-2.5 rounded-xl shadow-sm shrink-0 transition">
                <i class="fa-brands fa-whatsapp text-base"></i>
                <span>গ্রুপে যুক্ত হন</span>
            </a>
        </div>

        <!-- 4. COURSE CURRICULUM & MODULE ACCORDION (12 MODULES) -->
        <div class="bg-white rounded-3xl border border-slate-200 shadow-sm p-6 sm:p-7 space-y-4">
            <div class="flex flex-col sm:flex-row sm:items-center justify-between gap-2 pb-4 border-b border-slate-100">
                <div>
                    <h3 class="font-bold text-lg text-slate-900 flex items-center gap-2">
                        <i class="fa-solid fa-list-check text-emerald-600"></i>
                        <span>কোর্স কারিকুলাম ও মডিউলসমূহ (১২টি লেকচার)</span>
                    </h3>
                    <p class="text-xs text-slate-500 mt-0.5">
                        সাংবাদিকতা, আইন ও উপস্থাপনা কোর্সের সম্পূর্ণ সিলেবাস ও পাঠ্যসূচি
                    </p>
                </div>
                <span class="text-xs font-semibold px-3 py-1 rounded-full bg-slate-100 text-slate-700 self-start sm:self-auto">
                    মোট ১২টি লাইভ ক্লাস
                </span>
            </div>

            <!-- Modules List -->
            @php
                $defaultModules = [
                    ['order' => 1, 'title' => 'সাংবাদিকতার পরিচিতি ও মৌলিক ধারণা', 'duration' => '১ম ক্লাস • ২ ঘণ্টা', 'desc' => 'সংবাদের সংজ্ঞা, উপাদান, সংবাদের মূল্যবোধ (News Values), সাংবাদিকতার মৌলিক নীতিমালা ও সামাজিক দায়বদ্ধতা।'],
                    ['order' => 2, 'title' => 'সংবাদ তৈরি, কাঠামো ও রিভার্স পিরামিড পদ্ধতি', 'duration' => '২য় ক্লাস • ২ ঘণ্টা', 'desc' => '5W1H ফর্মুলা, কার্যকর ইন্ট্রো ও লিড লেখার কৌশল, ইনভার্টেড পিরামিড কাঠামো, সংবাদ সোর্স যাচাই ও এডিটিং।'],
                    ['order' => 3, 'title' => 'অনুসন্ধানী সাংবাদিকতা (Investigative Journalism)', 'duration' => '৩য় ক্লাস • ২ ঘণ্টা', 'desc' => 'অনুসন্ধানী রিপোর্টের বিষয় নির্বাচন, গোপন তথ্য সংগ্রহ, ডকুমেন্ট বিশ্লেষণ, ইন্টারভিউ কৌশল ও ব্যক্তিগত ঝুঁকি মোকাবেলা।'],
                    ['order' => 4, 'title' => 'তথ্য অধিকার আইন (RTI) ও সাংবাদিকদের অধিকার', 'duration' => '৪র্থ ক্লাস • ২ ঘণ্টা', 'desc' => 'তথ্য অধিকার আইন ২০০৯ ব্যবহার করে সরকারি/বেসরকারি দফতর থেকে তথ্য প্রাপ্তির আবেদন ফরম ও আইনি অধিকার।'],
                    ['order' => 5, 'title' => 'ডিজিটাল নিরাপত্তা ও সাইবার আইন', 'duration' => '৫ম ক্লাস • ২ ঘণ্টা', 'desc' => 'সাইবার সিকিউরিটি আইন, মানহানি মামলা, ডিজিটাল কন্টেন্টের আইনি সীমানা ও সামাজিক যোগাযোগ মাধ্যমে সাংবাদিকতা নীতি।'],
                    ['order' => 6, 'title' => 'সাক্ষাৎকার গ্রহণ কৌশল ও সোর্স রক্ষা', 'duration' => '৬ষ্ঠ ক্লাস • ২ ঘণ্টা', 'desc' => 'কঠিন প্রশ্ন করার কায়দা, অন-দ্য-রেকর্ড বনাম অফ-দ্য-রেকর্ড নিয়ম, সোর্সের নিরাপত্তা নিশ্চিতকরণ ও বিশ্বাসযোগ্যতা রক্ষা।'],
                    ['order' => 7, 'title' => 'টিভি ও ডিজিটাল নিউজে ক্যামেরার সামনে উপস্থাপনা', 'duration' => '৭ম ক্লাস • ২ ঘণ্টা', 'desc' => 'ক্যামেরা ফেস করার আত্মবিশ্বাস, বডি ল্যাঙ্গুয়েজ, চোখের দৃষ্টি (Eye Contact), টেলিপ্রম্পটার ব্যবহারের কৌশল।'],
                    ['order' => 8, 'title' => 'বাচনভঙ্গি, ভয়েস মড্যুলেশন ও উচ্চারণ শুদ্ধতা', 'duration' => '৮ম ক্লাস • ২ ঘণ্টা', 'desc' => 'প্রমিত বাংলা উচ্চারণ, কণ্ঠস্বরের ওঠানামা (Voice Modulation), পিচ কন্ট্রোল, নিঃশ্বাস নিয়ন্ত্রণ ও জড়তা কাটানোর অনুশীলন।'],
                    ['order' => 9, 'title' => 'নিউজ বুলেটিন ও লাইভ রিপোর্টিং কৌশল', 'duration' => '৯ম ক্লাস • ২ ঘণ্টা', 'desc' => 'অন-স্পট লাইভ রিপোর্টিং, ব্রেকিং নিউজ হ্যান্ডলিং, স্টুডিও উপস্থাপক ও ফিল্ড রিপোর্টারের কার্যকর সমন্বয়।'],
                    ['order' => 10, 'title' => 'মোবাইল জার্নালিজম (MoJo) ও ভিডিও এডিটিং', 'duration' => '১০ম ক্লাস • ২ ঘণ্টা', 'desc' => 'স্মার্টফোনে পেশাদার ভিডিও ধারণ, ক্যাপকাট/মোবাইল অ্যাপে ভিডিও এডিটিং, সাবটাইটেল ও ফেসবুক/ইউটিউবে প্রকাশ।'],
                    ['order' => 11, 'title' => 'ফ্যাক্ট চেকিং ও ভুয়া তথ্য (Fake News) যাচাই', 'duration' => '১১তম ক্লাস • ২ ঘণ্টা', 'desc' => 'গুগল রিভার্স ইমেজ সার্চ, ভিডিও ভেরিফিকেশন টুলস, ডিপফেক ও সোশ্যাল মিডিয়া প্রপাগান্ডা শনাক্তকরণ।'],
                    ['order' => 12, 'title' => 'কোর্স সমাপনী, প্র্যাকটিক্যাল অ্যাসাইনমেন্ট ও সার্টিফিকেশন', 'duration' => '১২তম ক্লাস • ২ ঘণ্টা', 'desc' => 'চূড়ান্ত ব্যবহারিক উপস্থাপনা পরীক্ষা, অ্যাসাইনমেন্ট মূল্যায়ন, ক্যারিয়ার গাইডলাইন ও সার্টিফিকেট সংক্রান্ত নির্দেশনা।'],
                ];
            @endphp

            <div class="space-y-3">
                @foreach($defaultModules as $mod)
                    <div class="border border-slate-200 rounded-2xl overflow-hidden transition"
                         :class="openModule === {{ $mod['order'] }} ? 'border-emerald-500 bg-emerald-50/20 shadow-xs' : 'bg-white hover:border-slate-300'">
                        <button type="button"
                                @click="openModule = (openModule === {{ $mod['order'] }} ? null : {{ $mod['order'] }})"
                                class="w-full p-4 text-left flex items-center justify-between gap-3">
                            <div class="flex items-center gap-3">
                                <span class="w-8 h-8 rounded-xl flex items-center justify-center font-bold text-xs"
                                      :class="openModule === {{ $mod['order'] }} ? 'bg-emerald-600 text-white' : 'bg-slate-100 text-slate-700'">
                                    {{ $mod['order'] }}
                                </span>
                                <div>
                                    <h4 class="font-bold text-sm text-slate-900">{{ $mod['title'] }}</h4>
                                    <span class="text-[11px] text-slate-500">{{ $mod['duration'] }}</span>
                                </div>
                            </div>
                            <i class="fa-solid fa-chevron-down text-xs text-slate-400 transition-transform duration-200"
                               :class="openModule === {{ $mod['order'] }} ? 'rotate-180 text-emerald-600' : ''"></i>
                        </button>
                        <div x-show="openModule === {{ $mod['order'] }}" x-collapse class="px-4 pb-4 pt-1 text-xs text-slate-600 border-t border-slate-100">
                            <p class="leading-relaxed">{{ $mod['desc'] }}</p>
                            <div class="mt-3 flex items-center gap-2">
                                <span class="inline-flex items-center gap-1 text-[11px] font-semibold text-emerald-700 bg-emerald-50 px-2 py-0.5 rounded border border-emerald-200">
                                    <i class="fa-solid fa-video text-[10px]"></i> লাইভ ক্লাস অন্তর্ভুক্ত
                                </span>
                                <span class="inline-flex items-center gap-1 text-[11px] font-semibold text-slate-600 bg-slate-100 px-2 py-0.5 rounded">
                                    <i class="fa-solid fa-file-lines text-[10px]"></i> লেকচার নোট দেওয়া হবে
                                </span>
                            </div>
                        </div>
                    </div>
                @endforeach
            </div>
        </div>

        <!-- 5. STUDENT SUPPORT & GUIDELINES -->
        <div class="grid grid-cols-1 md:grid-cols-2 gap-4">
            <!-- Guidelines -->
            <div class="bg-white p-6 rounded-3xl border border-slate-200 shadow-sm space-y-3">
                <h4 class="font-bold text-sm text-slate-900 flex items-center gap-2">
                    <i class="fa-solid fa-circle-info text-emerald-600"></i>
                    <span>অনলাইন ক্লাসের গুরুত্বপূর্ণ নির্দেশনাবলী</span>
                </h4>
                <ul class="text-xs text-slate-600 space-y-2 list-disc list-inside leading-relaxed">
                    <li>ক্লাস শুরুর ৫ মিনিট আগেই জুম/মিট লিংকে জয়েন করুন।</li>
                    <li>ক্লাস চলাকালীন অডিও মিউট রাখুন; প্রশ্ন করার সময় Hand Raise করুন।</li>
                    <li>উচ্চারণ ও উপস্থাপনা অনুশীলনের সময় ক্যামেরা অন রাখা বাধ্যতামূলক।</li>
                    <li>সনদপত্র (Certificate) প্রাপ্তির জন্য কমপক্ষে ৮০% উপস্থিতি ও সমাপনী অ্যাসাইনমেন্ট জমা দেওয়া আবশ্যক।</li>
                </ul>
            </div>

            <!-- Helpline -->
            <div class="bg-white p-6 rounded-3xl border border-slate-200 shadow-sm space-y-3">
                <h4 class="font-bold text-sm text-slate-900 flex items-center gap-2">
                    <i class="fa-solid fa-headset text-emerald-600"></i>
                    <span>যেকোনো প্রয়োজনে শিক্ষার্থী সাপোর্ট</span>
                </h4>
                <p class="text-xs text-slate-600 leading-relaxed">
                    লগইন সংক্রান্ত কোনো অসুবিধা হলে বা ক্লাসের লিংক না পেলে যেকোনো সময় সরাসরি আমাদের হেল্পলাইনে কল বা হোয়াটসঅ্যাপ করতে পারেন:
                </p>
                <div class="pt-1 flex items-center gap-2">
                    <a href="https://wa.me/8801332549549" target="_blank"
                       class="inline-flex items-center gap-2 bg-green-500 hover:bg-green-600 text-white font-bold text-xs px-3.5 py-2 rounded-xl shadow-xs transition">
                        <i class="fa-brands fa-whatsapp text-sm"></i>
                        <span>হোয়াটসঅ্যাপ: ০১৩৩২-৫৪৯৫৪৯</span>
                    </a>
                    <a href="tel:01332549549"
                       class="inline-flex items-center gap-1.5 bg-slate-100 hover:bg-slate-200 text-slate-700 font-semibold text-xs px-3.5 py-2 rounded-xl border border-slate-200 transition">
                        <i class="fa-solid fa-phone text-xs"></i>
                        <span>কল করুন</span>
                    </a>
                </div>
            </div>
        </div>

    </main>

    <!-- Footer -->
    <footer class="bg-white border-t border-slate-200 py-4 text-center text-xs text-slate-500 mt-auto">
        <div class="max-w-7xl mx-auto px-4">
            আমার বাংলা একাডেমি শিক্ষার্থী পোর্টাল &copy; {{ date('Y') }} । সর্বস্বত্ব সংরক্ষিত।
        </div>
    </footer>

</body>
</html>
