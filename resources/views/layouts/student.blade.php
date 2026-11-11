<!DOCTYPE html>
<html lang="bn" class="scroll-smooth">
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1, viewport-fit=cover">
    <meta name="csrf-token" content="{{ csrf_token() }}">

    <title>@yield('title', 'স্টুডেন্ট এলএমএস পোর্টাল - আমার বাংলা একাডেমি')</title>

    <!-- Favicon -->
    <link rel="icon" type="image/png" href="{{ asset('images/favicon.png') }}">
    <link rel="shortcut icon" href="{{ asset('favicon.ico') }}">
    <link rel="apple-touch-icon" href="{{ asset('images/favicon.png') }}">

    <!-- Google Fonts: Hind Siliguri & Inter -->
    <link rel="preconnect" href="https://fonts.googleapis.com">
    <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
    <link href="https://fonts.googleapis.com/css2?family=Hind+Siliguri:wght@400;500;600;700;800&family=Inter:wght@400;500;600;700;800&display=swap" rel="stylesheet">

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
                            950: '#022c22',
                        },
                        amber: {
                            400: '#fbbf24',
                            500: '#f59e0b',
                            600: '#d97706',
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
        [x-cloak] { display: none !important; }
    </style>
    @stack('styles')
</head>
<body class="bg-slate-100 text-slate-800 antialiased font-bangla min-h-screen flex"
      x-data="studentPortalShell()"
      @keydown.escape.window="sidebarOpen = false; profileOpen = false"
      :class="{ 'overflow-hidden lg:overflow-auto': sidebarOpen }">

    @php
        $portalUser = $user ?? auth()->user();
        $portalCourse = $course ?? null;
        $portalEnrollment = $enrollment ?? null;
        $portalBatch = $studentBatch ?? $portalEnrollment?->batch ?? null;
        $portalActiveMeeting = $currentMeeting ?? $portalCourse?->currentOrNextMeeting($portalEnrollment?->batch_id);
        $portalBatchName = $portalBatch?->name ?? $portalCourse?->batch_no ?? '১ম ব্যাচ';
        $portalIsLive = isset($isLiveNow)
            ? (bool) $isLiveNow
            : (($portalActiveMeeting && $portalActiveMeeting->is_live_now) || ($portalCourse?->is_live_now));
        $portalSchedule = $liveSchedule
            ?? $portalActiveMeeting?->formatted_schedule
            ?? $portalBatch?->formatted_schedule
            ?? $portalCourse?->live_schedule_text
            ?? 'রাত ৯:০০ টা';
        $portalMeetingsCount = isset($scheduledMeetings) ? count($scheduledMeetings) : 0;
        $portalRecordingsCount = isset($recordings) ? count($recordings) : 0;
        $portalAttendancePct = $attendanceStats['percentage'] ?? 100;
        $portalIsDashboard = request()->routeIs('student.dashboard');
        $portalDashUrl = route('student.dashboard');
        $portalAvatarInitial = $portalUser && !empty($portalUser->name)
            ? mb_strtoupper(mb_substr($portalUser->name, 0, 1, 'UTF-8'), 'UTF-8')
            : 'শ';
    @endphp

    <!-- Backdrop for Mobile Sidebar Drawer -->
    <div x-show="sidebarOpen"
         x-cloak
         x-transition:enter="transition-opacity ease-out duration-200"
         x-transition:enter-start="opacity-0"
         x-transition:enter-end="opacity-100"
         x-transition:leave="transition-opacity ease-in duration-150"
         x-transition:leave-start="opacity-100"
         x-transition:leave-end="opacity-0"
         @click="sidebarOpen = false"
         class="fixed inset-0 bg-slate-950/70 backdrop-blur-sm z-40 lg:hidden"></div>

    <!-- ==================== FORMAL STUDENT SIDEBAR (DESKTOP & MOBILE DRAWER) ==================== -->
    <aside :class="sidebarOpen ? 'translate-x-0' : '-translate-x-full lg:translate-x-0'"
           class="fixed inset-y-0 left-0 z-50 w-72 max-w-[85vw] bg-gradient-to-b from-slate-950 via-slate-900 to-emerald-950/95 text-slate-300 border-r border-slate-800/90 flex flex-col transition-transform duration-300 ease-out shrink-0 shadow-2xl lg:shadow-none">

        <!-- Sidebar Brand Header -->
        <div class="h-16 lg:h-[72px] px-4 flex items-center justify-between gap-2 border-b border-slate-800/80 shrink-0">
            <a href="{{ route('student.dashboard') }}" @click="sidebarOpen = false" class="flex items-center gap-2.5 group min-w-0">
                <div class="bg-white rounded-xl px-2 py-1 shadow-sm shrink-0 group-hover:scale-[1.02] transition-transform">
                    <img src="{{ asset('images/AmarBanglaAcademy.png') }}"
                         alt="আমার বাংলা একাডেমি"
                         class="h-7 sm:h-8 w-auto object-contain">
                </div>
                <div class="leading-tight min-w-0">
                    <span class="sr-only">আমার বাংলা একাডেমি</span>
                    <span class="text-[10px] font-bold text-emerald-400 uppercase tracking-wider flex items-center gap-1">
                        <span class="w-1.5 h-1.5 rounded-full bg-emerald-400 animate-pulse shrink-0"></span>
                        <span class="truncate">Student LMS Portal</span>
                    </span>
                </div>
            </a>
            <button type="button"
                    @click="sidebarOpen = false"
                    aria-label="মেনু বন্ধ করুন"
                    class="lg:hidden w-9 h-9 rounded-xl bg-slate-800/80 hover:bg-slate-700 text-slate-300 hover:text-white flex items-center justify-center transition shrink-0">
                <i class="fa-solid fa-xmark text-base"></i>
            </button>
        </div>

        <!-- Student Identity & Batch Card -->
        @if($portalUser)
        <div class="px-4 pt-4 pb-2 shrink-0">
            <div class="p-3.5 rounded-2xl bg-slate-900/90 border border-slate-800/90 space-y-2.5">
                <div class="flex items-center gap-3 min-w-0">
                    <div class="w-10 h-10 rounded-xl bg-gradient-to-tr from-emerald-500 to-teal-500 text-white flex items-center justify-center font-extrabold text-sm shadow-sm shrink-0">
                        {{ $portalAvatarInitial }}
                    </div>
                    <div class="min-w-0 flex-1">
                        <div class="text-xs sm:text-sm font-bold text-white truncate">{{ $portalUser->name }}</div>
                        <div class="text-[11px] text-slate-400 font-mono truncate">{{ $portalUser->phone ?? $portalUser->email }}</div>
                    </div>
                </div>
                <div class="flex items-center justify-between gap-2 pt-2 border-t border-slate-800/80 text-[11px]">
                    <span class="inline-flex items-center gap-1.5 px-2.5 py-0.5 rounded-full font-bold bg-emerald-950 text-emerald-300 border border-emerald-700/60 truncate">
                        <i class="fa-solid fa-layer-group text-[10px]"></i>
                        <span class="truncate">{{ $portalBatchName }}</span>
                    </span>
                    <span class="inline-flex items-center gap-1 text-emerald-400 font-semibold shrink-0">
                        <span class="w-1.5 h-1.5 rounded-full bg-emerald-400"></span>
                        <span>সক্রিয়</span>
                    </span>
                </div>
            </div>
        </div>
        @endif

        <!-- Simple Formal Sidebar Navigation -->
        <nav class="flex-1 px-3 py-3 space-y-1 overflow-y-auto" aria-label="শিক্ষার্থী নেভিগেশন মেনু">
            <div class="px-3 pb-1.5 text-[10px] font-bold text-slate-400 uppercase tracking-widest">
                শিক্ষার্থী মেনু
            </div>

            <!-- 1. Overview & Live Class -->
            <a href="{{ $portalIsDashboard ? '#overview' : $portalDashUrl . '#overview' }}"
               @click="navigateTo('overview')"
               :class="activeSection === 'overview' ? 'bg-emerald-600 text-white shadow-md shadow-emerald-600/25' : 'text-slate-300 hover:bg-slate-800/80 hover:text-white'"
               class="flex items-center justify-between px-3.5 py-2.5 rounded-xl text-xs sm:text-[13px] font-bold transition group">
                <div class="flex items-center gap-3 min-w-0">
                    <i class="fa-solid fa-house text-sm w-4 text-center shrink-0"
                       :class="activeSection === 'overview' ? 'text-white' : 'text-emerald-400 group-hover:text-emerald-300'"></i>
                    <span class="truncate">ড্যাশবোর্ড ওভারভিউ</span>
                </div>
                @if($portalIsLive)
                    <span class="inline-flex items-center gap-1 text-[10px] px-2 py-0.5 rounded-full font-black bg-rose-600 text-white animate-pulse shrink-0">
                        <span class="w-1.5 h-1.5 rounded-full bg-white"></span>
                        LIVE
                    </span>
                @endif
            </a>

            <!-- 2. Class Schedule -->
            <a href="{{ $portalIsDashboard ? '#schedule' : $portalDashUrl . '#schedule' }}"
               @click="navigateTo('schedule')"
               :class="activeSection === 'schedule' ? 'bg-emerald-600 text-white shadow-md shadow-emerald-600/25' : 'text-slate-300 hover:bg-slate-800/80 hover:text-white'"
               class="flex items-center justify-between px-3.5 py-2.5 rounded-xl text-xs sm:text-[13px] font-bold transition group">
                <div class="flex items-center gap-3 min-w-0">
                    <i class="fa-solid fa-calendar-days text-sm w-4 text-center shrink-0"
                       :class="activeSection === 'schedule' ? 'text-white' : 'text-amber-400 group-hover:text-amber-300'"></i>
                    <span class="truncate">ক্লাস শিডিউল</span>
                </div>
                @if($portalMeetingsCount > 0)
                    <span class="text-[10px] px-2 py-0.5 rounded-full font-bold shrink-0"
                          :class="activeSection === 'schedule' ? 'bg-emerald-800 text-emerald-100' : 'bg-slate-800 text-slate-300'">
                        {{ $portalMeetingsCount }}
                    </span>
                @endif
            </a>

            <!-- 3. Class Recordings -->
            <a href="{{ $portalIsDashboard ? '#recordings' : $portalDashUrl . '#recordings' }}"
               @click="navigateTo('recordings')"
               :class="activeSection === 'recordings' ? 'bg-emerald-600 text-white shadow-md shadow-emerald-600/25' : 'text-slate-300 hover:bg-slate-800/80 hover:text-white'"
               class="flex items-center justify-between px-3.5 py-2.5 rounded-xl text-xs sm:text-[13px] font-bold transition group">
                <div class="flex items-center gap-3 min-w-0">
                    <i class="fa-solid fa-circle-play text-sm w-4 text-center shrink-0"
                       :class="activeSection === 'recordings' ? 'text-white' : 'text-teal-400 group-hover:text-teal-300'"></i>
                    <span class="truncate">ক্লাস রেকর্ডিং</span>
                </div>
                <span class="text-[10px] px-2 py-0.5 rounded-full font-bold shrink-0"
                      :class="activeSection === 'recordings' ? 'bg-emerald-800 text-emerald-100' : 'bg-slate-800 text-slate-300'">
                    {{ $portalRecordingsCount }}
                </span>
            </a>

            <!-- 4. Attendance & Progress -->
            <a href="{{ $portalIsDashboard ? '#attendance' : $portalDashUrl . '#attendance' }}"
               @click="navigateTo('attendance')"
               :class="activeSection === 'attendance' ? 'bg-emerald-600 text-white shadow-md shadow-emerald-600/25' : 'text-slate-300 hover:bg-slate-800/80 hover:text-white'"
               class="flex items-center justify-between px-3.5 py-2.5 rounded-xl text-xs sm:text-[13px] font-bold transition group">
                <div class="flex items-center gap-3 min-w-0">
                    <i class="fa-solid fa-clipboard-user text-sm w-4 text-center shrink-0"
                       :class="activeSection === 'attendance' ? 'text-white' : 'text-purple-400 group-hover:text-purple-300'"></i>
                    <span class="truncate">হাজিরা ও উপস্থিতি</span>
                </div>
                <span class="text-[10px] px-2 py-0.5 rounded-full font-bold shrink-0"
                      :class="activeSection === 'attendance' ? 'bg-emerald-800 text-emerald-100' : 'bg-slate-800 text-emerald-300'">
                    {{ $portalAttendancePct }}%
                </span>
            </a>

            <!-- 5. Course Curriculum -->
            <a href="{{ $portalIsDashboard ? '#curriculum' : $portalDashUrl . '#curriculum' }}"
               @click="navigateTo('curriculum')"
               :class="activeSection === 'curriculum' ? 'bg-emerald-600 text-white shadow-md shadow-emerald-600/25' : 'text-slate-300 hover:bg-slate-800/80 hover:text-white'"
               class="flex items-center justify-between px-3.5 py-2.5 rounded-xl text-xs sm:text-[13px] font-bold transition group">
                <div class="flex items-center gap-3 min-w-0">
                    <i class="fa-solid fa-book-open text-sm w-4 text-center shrink-0"
                       :class="activeSection === 'curriculum' ? 'text-white' : 'text-blue-400 group-hover:text-blue-300'"></i>
                    <span class="truncate">কোর্স কারিকুলাম</span>
                </div>
                <span class="text-[10px] px-2 py-0.5 rounded-full font-semibold shrink-0"
                      :class="activeSection === 'curriculum' ? 'bg-emerald-800 text-emerald-100' : 'bg-slate-800 text-slate-400'">
                    ১২ মডিউল
                </span>
            </a>

            <!-- 6. Guidelines & Support -->
            <a href="{{ $portalIsDashboard ? '#support' : $portalDashUrl . '#support' }}"
               @click="navigateTo('support')"
               :class="activeSection === 'support' ? 'bg-emerald-600 text-white shadow-md shadow-emerald-600/25' : 'text-slate-300 hover:bg-slate-800/80 hover:text-white'"
               class="flex items-center justify-between px-3.5 py-2.5 rounded-xl text-xs sm:text-[13px] font-bold transition group">
                <div class="flex items-center gap-3 min-w-0">
                    <i class="fa-solid fa-headset text-sm w-4 text-center shrink-0"
                       :class="activeSection === 'support' ? 'text-white' : 'text-emerald-400 group-hover:text-emerald-300'"></i>
                    <span class="truncate">গাইডলাইন ও সহায়তা</span>
                </div>
            </a>

            <!-- Quick Support Links -->
            <div class="pt-4 px-3 pb-1.5 text-[10px] font-bold text-slate-400 uppercase tracking-widest">
                দ্রুত সংযোগ
            </div>

            <a href="https://chat.whatsapp.com/EeM7caaCmE79NR0BFdrwWh?s=sh&p=a&iam=2" target="_blank"
               class="flex items-center justify-between px-3.5 py-2.5 rounded-xl text-xs font-semibold text-slate-300 hover:bg-slate-800/80 hover:text-green-300 transition">
                <div class="flex items-center gap-3 min-w-0">
                    <i class="fa-brands fa-whatsapp text-base text-green-400 w-4 text-center shrink-0"></i>
                    <span class="truncate">WhatsApp ব্যাচ গ্রুপ</span>
                </div>
                <i class="fa-solid fa-arrow-up-right-from-square text-[10px] text-slate-500"></i>
            </a>

            <a href="{{ route('home') }}" target="_blank"
               class="flex items-center justify-between px-3.5 py-2.5 rounded-xl text-xs font-semibold text-slate-300 hover:bg-slate-800/80 hover:text-white transition">
                <div class="flex items-center gap-3 min-w-0">
                    <i class="fa-solid fa-globe text-sm text-emerald-400 w-4 text-center shrink-0"></i>
                    <span class="truncate">মূল ওয়েবসাইট</span>
                </div>
                <i class="fa-solid fa-arrow-up-right-from-square text-[10px] text-slate-500"></i>
            </a>
        </nav>

        <!-- Sidebar Bottom Logout Footer -->
        <div class="p-3.5 border-t border-slate-800/90 bg-slate-950/90 shrink-0">
            <form method="POST" action="{{ route('student.logout') }}">
                @csrf
                <button type="submit"
                        class="w-full flex items-center justify-center gap-2 px-4 py-2.5 rounded-xl bg-rose-950/60 hover:bg-rose-900 text-rose-300 hover:text-white border border-rose-800/60 text-xs font-bold transition">
                    <i class="fa-solid fa-right-from-bracket"></i>
                    <span>পোর্টাল থেকে লগআউট</span>
                </button>
            </form>
        </div>
    </aside>

    <!-- ==================== MAIN CONTENT WRAPPER ==================== -->
    <div class="flex-1 flex flex-col min-w-0 lg:pl-72 overflow-x-clip">

        <!-- Formal Sticky Top Header Bar -->
        <header class="sticky top-0 z-30 bg-white/95 backdrop-blur-md border-b border-slate-200/90 shadow-sm">
            <div class="px-3 sm:px-6 lg:px-8 h-16 lg:h-[72px] flex items-center justify-between gap-2 sm:gap-4">

                <!-- Left: Hamburger Trigger, Brand Icon (Mobile) & Portal Title -->
                <div class="flex items-center gap-2.5 sm:gap-3 min-w-0">
                    <button type="button"
                            @click="sidebarOpen = true"
                            aria-label="নেভিগেশন মেনু খুলুন"
                            class="lg:hidden w-10 h-10 rounded-xl bg-slate-100 hover:bg-slate-200 active:bg-slate-300 text-slate-700 flex items-center justify-center transition shrink-0">
                        <i class="fa-solid fa-bars text-base"></i>
                    </button>

                    <a href="{{ route('student.dashboard') }}" class="lg:hidden w-9 h-9 rounded-xl bg-white border border-slate-200 p-0.5 flex items-center justify-center shadow-sm shrink-0">
                        <img src="{{ asset('images/favicon.png') }}" alt="আমার বাংলা একাডেমি" class="w-full h-full object-contain rounded-lg">
                    </a>

                    <div class="min-w-0">
                        <div class="hidden sm:flex items-center gap-2 text-[11px] font-semibold text-slate-500 leading-none mb-1">
                            <span>আমার বাংলা একাডেমি</span>
                            <span>&bull;</span>
                            <span class="text-emerald-700 font-bold">শিক্ষার্থী পোর্টাল</span>
                        </div>
                        <div class="flex items-center gap-2 min-w-0">
                            <h1 class="text-sm sm:text-base lg:text-lg font-extrabold text-slate-900 leading-tight truncate">
                                স্টুডেন্ট এলএমএস পোর্টাল
                            </h1>
                            <span class="hidden sm:inline-flex items-center gap-1 px-2.5 py-0.5 rounded-full text-[11px] font-bold bg-emerald-50 text-emerald-800 border border-emerald-200 shrink-0">
                                <span class="w-1.5 h-1.5 rounded-full bg-emerald-500"></span>
                                {{ $portalBatchName }}
                            </span>
                        </div>
                    </div>
                </div>

                <!-- Right Actions: Live Status, Student Profile Dropdown, Logout -->
                <div class="flex items-center gap-2 sm:gap-3 shrink-0">
                    @if($portalIsLive)
                        <a href="{{ route('student.live-class') }}" target="_blank"
                           class="inline-flex items-center gap-1.5 bg-rose-600 hover:bg-rose-700 text-white text-xs font-bold px-2.5 sm:px-3.5 py-2 rounded-xl shadow-sm animate-pulse transition">
                            <span class="w-2 h-2 rounded-full bg-white animate-ping"></span>
                            <i class="fa-solid fa-video hidden sm:inline"></i>
                            <span class="hidden sm:inline">লাইভ ক্লাসে যোগ দিন</span>
                            <span class="sm:hidden">লাইভ</span>
                        </a>
                    @else
                        <div class="hidden xl:inline-flex items-center gap-1.5 bg-amber-50 border border-amber-200/80 text-amber-900 text-xs font-medium px-3 py-1.5 rounded-xl select-none"
                             title="ক্লাস শুরুর সময়ে লিংক সক্রিয় হবে">
                            <i class="fa-regular fa-clock text-amber-600 text-xs"></i>
                            <span>পরবর্তী ক্লাস: <strong class="text-amber-950 font-bold">{{ $portalSchedule }}</strong></span>
                        </div>
                    @endif

                    <!-- Student Account Menu Dropdown -->
                    @if($portalUser)
                    <div class="relative">
                        <button type="button"
                                @click="profileOpen = !profileOpen"
                                class="flex items-center gap-2 bg-slate-50 hover:bg-slate-100 border border-slate-200 px-2 sm:px-3 py-1.5 rounded-xl transition cursor-pointer">
                            <div class="w-8 h-8 rounded-lg bg-gradient-to-tr from-emerald-600 to-teal-500 text-white flex items-center justify-center text-xs font-bold shrink-0">
                                {{ $portalAvatarInitial }}
                            </div>
                            <div class="hidden md:flex flex-col text-left leading-tight max-w-[140px]">
                                <span class="text-xs font-bold text-slate-900 truncate">{{ $portalUser->name }}</span>
                                <span class="text-[10px] text-slate-500 font-mono truncate">{{ $portalUser->phone ?? $portalUser->email }}</span>
                            </div>
                            <i class="fa-solid fa-chevron-down text-[10px] text-slate-400 transition-transform"
                               :class="{ 'rotate-180': profileOpen }"></i>
                        </button>

                        <!-- Account Dropdown Panel -->
                        <div x-show="profileOpen"
                             x-cloak
                             @click.away="profileOpen = false"
                             x-transition.opacity.duration.150ms
                             class="absolute right-0 mt-2 w-72 max-w-[calc(100vw-1.5rem)] bg-white rounded-2xl border border-slate-200 shadow-xl py-2 z-50">
                            <div class="px-4 py-3 border-b border-slate-100">
                                <div class="text-sm font-extrabold text-slate-900 truncate">{{ $portalUser->name }}</div>
                                <div class="text-xs text-slate-500 font-mono truncate mt-0.5">{{ $portalUser->phone ?? $portalUser->email }}</div>
                                <div class="flex items-center gap-1.5 mt-2 flex-wrap">
                                    <span class="text-[10px] font-bold px-2 py-0.5 rounded-full bg-emerald-50 text-emerald-700 border border-emerald-200">
                                        {{ $portalBatchName }}
                                    </span>
                                    <span class="text-[10px] font-bold px-2 py-0.5 rounded-full bg-slate-100 text-slate-700">
                                        উপস্থিতি: {{ $portalAttendancePct }}%
                                    </span>
                                </div>
                            </div>

                            <div class="py-1.5 text-xs">
                                <a href="{{ $portalIsDashboard ? '#attendance' : $portalDashUrl . '#attendance' }}"
                                   @click="profileOpen = false; navigateTo('attendance')"
                                   class="flex items-center gap-2.5 px-4 py-2 text-slate-700 hover:bg-slate-50 hover:text-emerald-700 font-semibold transition">
                                    <i class="fa-solid fa-clipboard-user text-purple-600 w-4 text-center"></i>
                                    <span>হাজিরা ও উপস্থিতির হার</span>
                                </a>
                                <a href="{{ $portalIsDashboard ? '#curriculum' : $portalDashUrl . '#curriculum' }}"
                                   @click="profileOpen = false; navigateTo('curriculum')"
                                   class="flex items-center gap-2.5 px-4 py-2 text-slate-700 hover:bg-slate-50 hover:text-emerald-700 font-semibold transition">
                                    <i class="fa-solid fa-book-open text-blue-600 w-4 text-center"></i>
                                    <span>কোর্স সিলেবাস (১২ মডিউল)</span>
                                </a>
                                <a href="https://wa.me/8801332549549" target="_blank"
                                   class="flex items-center gap-2.5 px-4 py-2 text-slate-700 hover:bg-slate-50 hover:text-emerald-700 font-semibold transition">
                                    <i class="fa-brands fa-whatsapp text-green-600 w-4 text-center"></i>
                                    <span>হেল্পলাইন (০১৩৩২-৫৪৯৫৪৯)</span>
                                </a>
                            </div>

                            <div class="pt-1.5 px-3 border-t border-slate-100">
                                <form method="POST" action="{{ route('student.logout') }}">
                                    @csrf
                                    <button type="submit"
                                            class="w-full flex items-center justify-center gap-2 px-3 py-2 rounded-xl bg-rose-50 hover:bg-rose-100 text-rose-700 font-bold text-xs transition">
                                        <i class="fa-solid fa-right-from-bracket"></i>
                                        <span>লগআউট</span>
                                    </button>
                                </form>
                            </div>
                        </div>
                    </div>
                    @endif

                    <!-- Direct Desktop Logout Button -->
                    <form method="POST" action="{{ route('student.logout') }}" class="hidden sm:inline-block">
                        @csrf
                        <button type="submit"
                                title="লগআউট"
                                class="inline-flex items-center gap-1.5 text-xs font-bold text-rose-600 hover:text-rose-700 bg-rose-50 hover:bg-rose-100 border border-rose-200 px-3 py-2 rounded-xl transition">
                            <i class="fa-solid fa-right-from-bracket"></i>
                            <span class="hidden lg:inline">লগআউট</span>
                        </button>
                    </form>
                </div>
            </div>
        </header>

        <!-- Flash Message Container -->
        @if(session('success') || session('info') || session('error'))
        <div class="px-3 sm:px-6 lg:px-8 mt-4 w-full max-w-7xl mx-auto">
            @if(session('success'))
                <div class="p-3.5 sm:p-4 rounded-2xl bg-emerald-50 border border-emerald-200 text-emerald-800 flex items-center justify-between gap-3 shadow-sm mb-3">
                    <div class="flex items-center gap-2.5 min-w-0">
                        <i class="fa-solid fa-circle-check text-emerald-600 text-lg shrink-0"></i>
                        <span class="text-xs sm:text-sm font-semibold">{{ session('success') }}</span>
                    </div>
                    <button type="button" onclick="this.parentElement.remove()" class="text-emerald-500 hover:text-emerald-700 p-1 shrink-0">
                        <i class="fa-solid fa-xmark"></i>
                    </button>
                </div>
            @endif

            @if(session('info'))
                <div class="p-3.5 sm:p-4 rounded-2xl bg-blue-50 border border-blue-200 text-blue-800 flex items-center justify-between gap-3 shadow-sm mb-3">
                    <div class="flex items-center gap-2.5 min-w-0">
                        <i class="fa-solid fa-circle-info text-blue-600 text-lg shrink-0"></i>
                        <span class="text-xs sm:text-sm font-semibold">{{ session('info') }}</span>
                    </div>
                    <button type="button" onclick="this.parentElement.remove()" class="text-blue-500 hover:text-blue-700 p-1 shrink-0">
                        <i class="fa-solid fa-xmark"></i>
                    </button>
                </div>
            @endif

            @if(session('error'))
                <div class="p-3.5 sm:p-4 rounded-2xl bg-rose-50 border border-rose-200 text-rose-800 flex items-center justify-between gap-3 shadow-sm mb-3">
                    <div class="flex items-center gap-2.5 min-w-0">
                        <i class="fa-solid fa-circle-exclamation text-rose-600 text-lg shrink-0"></i>
                        <span class="text-xs sm:text-sm font-semibold">{{ session('error') }}</span>
                    </div>
                    <button type="button" onclick="this.parentElement.remove()" class="text-rose-500 hover:text-rose-700 p-1 shrink-0">
                        <i class="fa-solid fa-xmark"></i>
                    </button>
                </div>
            @endif
        </div>
        @endif

        <!-- Main Body Content -->
        <main class="flex-1 px-3 sm:px-6 lg:px-8 py-4 sm:py-6 w-full max-w-7xl mx-auto">
            @yield('content')
        </main>

        <!-- Footer -->
        <footer class="bg-white border-t border-slate-200 py-4 px-4 sm:px-6 lg:px-8 pb-24 lg:pb-5 text-xs text-slate-500 mt-auto">
            <div class="max-w-7xl mx-auto flex flex-col sm:flex-row items-center justify-between gap-2 text-center sm:text-left">
                <div>
                    আমার বাংলা একাডেমি শিক্ষার্থী পোর্টাল &copy; {{ date('Y') }} । সর্বস্বত্ব সংরক্ষিত।
                </div>
                <div class="flex items-center gap-3 text-slate-400">
                    <a href="{{ route('home') }}" target="_blank" class="hover:text-emerald-700 transition">মূল ওয়েবসাইট</a>
                    <span>&bull;</span>
                    <a href="https://wa.me/8801332549549" target="_blank" class="hover:text-emerald-700 transition">হেল্পলাইন</a>
                </div>
            </div>
        </footer>
    </div>

    <!-- ==================== MOBILE QUICK NAVIGATION DOCK (< lg) ==================== -->
    <nav class="lg:hidden fixed bottom-0 inset-x-0 z-30 bg-white/95 backdrop-blur-md border-t border-slate-200 shadow-[0_-4px_20px_rgba(15,23,42,0.08)] pb-[env(safe-area-inset-bottom)]"
         aria-label="মোবাইল দ্রুত মেনু">
        <div class="grid grid-cols-5 h-16 px-1">
            <a href="{{ $portalIsDashboard ? '#overview' : $portalDashUrl . '#overview' }}"
               @click="navigateTo('overview')"
               :class="activeSection === 'overview' ? 'text-emerald-700' : 'text-slate-500 hover:text-slate-800'"
               class="relative flex flex-col items-center justify-center py-2 gap-0.5 text-[10px] font-bold transition">
                <span x-show="activeSection === 'overview'" x-cloak class="w-5 h-0.5 rounded-full bg-emerald-600 absolute top-0"></span>
                <i class="fa-solid fa-house text-sm"></i>
                <span>ওভারভিউ</span>
            </a>

            <a href="{{ $portalIsDashboard ? '#schedule' : $portalDashUrl . '#schedule' }}"
               @click="navigateTo('schedule')"
               :class="activeSection === 'schedule' ? 'text-emerald-700' : 'text-slate-500 hover:text-slate-800'"
               class="relative flex flex-col items-center justify-center py-2 gap-0.5 text-[10px] font-bold transition">
                <span x-show="activeSection === 'schedule'" x-cloak class="w-5 h-0.5 rounded-full bg-emerald-600 absolute top-0"></span>
                <i class="fa-solid fa-calendar-days text-sm"></i>
                <span>শিডিউল</span>
            </a>

            <a href="{{ $portalIsDashboard ? '#recordings' : $portalDashUrl . '#recordings' }}"
               @click="navigateTo('recordings')"
               :class="activeSection === 'recordings' ? 'text-emerald-700' : 'text-slate-500 hover:text-slate-800'"
               class="relative flex flex-col items-center justify-center py-2 gap-0.5 text-[10px] font-bold transition">
                <span x-show="activeSection === 'recordings'" x-cloak class="w-5 h-0.5 rounded-full bg-emerald-600 absolute top-0"></span>
                <i class="fa-solid fa-circle-play text-sm"></i>
                <span>রেকর্ডিং</span>
            </a>

            <a href="{{ $portalIsDashboard ? '#attendance' : $portalDashUrl . '#attendance' }}"
               @click="navigateTo('attendance')"
               :class="activeSection === 'attendance' ? 'text-emerald-700' : 'text-slate-500 hover:text-slate-800'"
               class="relative flex flex-col items-center justify-center py-2 gap-0.5 text-[10px] font-bold transition">
                <span x-show="activeSection === 'attendance'" x-cloak class="w-5 h-0.5 rounded-full bg-emerald-600 absolute top-0"></span>
                <i class="fa-solid fa-clipboard-user text-sm"></i>
                <span>হাজিরা</span>
            </a>

            <button type="button"
                    @click="sidebarOpen = true"
                    :class="sidebarOpen ? 'text-emerald-700' : 'text-slate-600 hover:text-emerald-700'"
                    class="relative flex flex-col items-center justify-center py-2 gap-0.5 text-[10px] font-bold transition">
                <i class="fa-solid fa-bars text-sm"></i>
                <span>মেনু</span>
            </button>
        </div>
    </nav>

    <script>
        function studentPortalShell() {
            return {
                sidebarOpen: false,
                profileOpen: false,
                activeSection: 'overview',
                openModule: 1,
                init() {
                    const sections = ['overview', 'schedule', 'recordings', 'attendance', 'curriculum', 'support'];
                    const updateFromHash = () => {
                        const hash = window.location.hash.replace('#', '');
                        if (sections.includes(hash)) {
                            this.activeSection = hash;
                        }
                    };
                    updateFromHash();
                    window.addEventListener('hashchange', updateFromHash);

                    const onScroll = () => {
                        if (window.innerHeight + window.scrollY >= document.documentElement.scrollHeight - 40) {
                            this.activeSection = 'support';
                            return;
                        }
                        const scrollY = window.scrollY + 140;
                        let current = 'overview';
                        for (const id of sections) {
                            const el = document.getElementById(id);
                            if (el) {
                                const top = el.getBoundingClientRect().top + window.scrollY;
                                if (top <= scrollY) {
                                    current = id;
                                }
                            }
                        }
                        this.activeSection = current;
                    };
                    window.addEventListener('scroll', onScroll, { passive: true });
                },
                navigateTo(section) {
                    this.activeSection = section;
                    this.sidebarOpen = false;
                }
            };
        }

        document.addEventListener('alpine:init', () => {
            if (window.Alpine) {
                window.Alpine.data('studentPortalShell', studentPortalShell);
            }
        });
    </script>
    @stack('scripts')
</body>
</html>
