<!DOCTYPE html>
<html lang="bn" class="scroll-smooth">
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <meta name="csrf-token" content="{{ csrf_token() }}">

    <title>@yield('title', 'এডমিন পোর্টাল - আমার বাংলা একাডেমি')</title>

    <!-- Google Fonts: Hind Siliguri (Bangla) & Inter -->
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

    <!-- Alpine.js CDN -->
    <script defer src="https://cdn.jsdelivr.net/npm/alpinejs@3.14.3/dist/cdn.min.js"></script>

    <style>
        body { font-family: 'Hind Siliguri', 'Inter', sans-serif; }
    </style>
    @stack('styles')
</head>
<body class="bg-slate-100 text-slate-800 antialiased font-bangla min-h-screen flex" x-data="{ sidebarOpen: false }">

    <!-- Backdrop for Mobile Sidebar -->
    <div x-show="sidebarOpen"
         x-transition:enter="transition-opacity ease-linear duration-200"
         x-transition:enter-start="opacity-0"
         x-transition:enter-end="opacity-100"
         x-transition:leave="transition-opacity ease-linear duration-200"
         x-transition:leave-start="opacity-100"
         x-transition:leave-end="opacity-0"
         @click="sidebarOpen = false"
         class="fixed inset-0 bg-slate-950/70 backdrop-blur-xs z-40 lg:hidden"
         style="display: none;"></div>

    <!-- ==================== SIDEBAR (DESKTOP & MOBILE DRAWER) ==================== -->
    <aside :class="sidebarOpen ? 'translate-x-0' : '-translate-x-full lg:translate-x-0'"
           class="fixed inset-y-0 left-0 z-50 w-64 lg:w-72 bg-gradient-to-b from-slate-950 via-slate-900 to-slate-950 text-slate-300 border-r border-slate-800 flex flex-col transition-transform duration-300 ease-in-out shrink-0">

        <!-- Sidebar Brand Header -->
        <div class="h-16 lg:h-20 px-5 flex items-center justify-between border-b border-slate-800/80">
            <a href="{{ route('admin.students') }}" class="flex items-center gap-3 group">
                <div class="w-10 h-10 rounded-xl bg-gradient-to-tr from-emerald-600 to-teal-400 text-white flex items-center justify-center font-bold text-xl shadow-md shadow-emerald-600/30">
                    আ
                </div>
                <div class="leading-tight">
                    <span class="text-base font-extrabold text-white tracking-tight group-hover:text-emerald-400 transition block">
                        আমার বাংলা একাডেমি
                    </span>
                    <span class="text-[11px] font-semibold text-emerald-400 uppercase tracking-wider flex items-center gap-1.5 mt-0.5">
                        <span class="w-1.5 h-1.5 rounded-full bg-emerald-400"></span>
                        Executive LMS Admin
                    </span>
                </div>
            </a>
            <button @click="sidebarOpen = false" class="lg:hidden text-slate-400 hover:text-white p-1 rounded-lg">
                <i class="fa-solid fa-xmark text-lg"></i>
            </button>
        </div>

        <!-- Sidebar Navigation Menu -->
        <div class="flex-1 px-3 py-5 space-y-1.5 overflow-y-auto">

            <div class="px-3 pb-2 text-[10px] font-bold text-slate-400 uppercase tracking-widest">
                মূল মেনু ও কার্যক্রম
            </div>

            <!-- Students & Roster Link -->
            @php $isStudents = request()->routeIs('admin.students') || request()->routeIs('admin.dashboard'); @endphp
            <a href="{{ route('admin.students') }}"
               class="flex items-center justify-between px-3.5 py-3 rounded-xl text-xs font-bold transition group {{ $isStudents ? 'bg-emerald-600 text-white shadow-md shadow-emerald-600/20' : 'hover:bg-slate-800/80 hover:text-white text-slate-300' }}">
                <div class="flex items-center gap-3">
                    <i class="fa-solid fa-graduation-cap text-base {{ $isStudents ? 'text-white' : 'text-emerald-400 group-hover:text-emerald-300' }}"></i>
                    <span>শিক্ষার্থী ও ড্যাশবোর্ড</span>
                </div>
                <span class="text-[10px] px-2 py-0.5 rounded-full font-bold {{ $isStudents ? 'bg-emerald-800 text-emerald-100' : 'bg-slate-800 text-slate-400' }}">
                    তালিকা
                </span>
            </a>

            <!-- Live Meeting Manager Link -->
            @php
                $isMeeting = request()->routeIs('admin.live-meetings');
                $courseMeta = \App\Models\Course::first();
                $isLiveNow = $courseMeta?->is_live_now;
            @endphp
            <a href="{{ route('admin.live-meetings') }}"
               class="flex items-center justify-between px-3.5 py-3 rounded-xl text-xs font-bold transition group {{ $isMeeting ? 'bg-emerald-600 text-white shadow-md shadow-emerald-600/20' : 'hover:bg-slate-800/80 hover:text-white text-slate-300' }}">
                <div class="flex items-center gap-3">
                    <i class="fa-solid fa-video text-base {{ $isMeeting ? 'text-white' : 'text-blue-400 group-hover:text-blue-300' }}"></i>
                    <span>লাইভ ক্লাস ও মিটিং</span>
                </div>
                @if($isLiveNow)
                    <span class="inline-flex items-center gap-1 text-[10px] px-2 py-0.5 rounded-full font-bold bg-red-600 text-white animate-pulse">
                        <span class="w-1.5 h-1.5 rounded-full bg-white"></span>
                        LIVE
                    </span>
                @else
                    <span class="text-[10px] px-2 py-0.5 rounded-full font-semibold {{ $isMeeting ? 'bg-emerald-800 text-emerald-100' : 'bg-slate-800 text-slate-400' }}">
                        রুম
                    </span>
                @endif
            </a>

            <!-- Duplicate Clean Link (Fast Action) -->
            <form method="POST" action="{{ route('admin.students.clean-duplicates') }}"
                  onsubmit="return confirm('অতিরিক্ত ডুপ্লিকেট পেন্ডিং রেজিস্ট্রেশন মুছে পরিষ্কার করতে চান?')">
                @csrf
                <button type="submit"
                        class="w-full flex items-center justify-between px-3.5 py-2.5 rounded-xl text-xs font-semibold text-slate-300 hover:bg-slate-800/80 hover:text-amber-300 transition text-left group">
                    <div class="flex items-center gap-3">
                        <i class="fa-solid fa-broom text-base text-amber-400 group-hover:rotate-12 transition-transform"></i>
                        <span>ডুপ্লিকেট ক্লিন করুন</span>
                    </div>
                    <i class="fa-solid fa-angle-right text-[10px] text-slate-600 group-hover:text-slate-400"></i>
                </button>
            </form>

            <div class="pt-4 px-3 pb-2 text-[10px] font-bold text-slate-400 uppercase tracking-widest">
                পোর্টাল ও এক্সটার্নাল লিংক
            </div>

            <!-- Student Login Portal Link -->
            <a href="{{ route('student.dashboard') }}" target="_blank"
               class="flex items-center justify-between px-3.5 py-2.5 rounded-xl text-xs font-semibold text-slate-300 hover:bg-slate-800/80 hover:text-white transition group">
                <div class="flex items-center gap-3">
                    <i class="fa-solid fa-user-graduate text-base text-teal-400"></i>
                    <span>স্টুডেন্ট LMS পোর্টাল</span>
                </div>
                <i class="fa-solid fa-arrow-up-right-from-square text-[10px] text-slate-500"></i>
            </a>

            <!-- Live Site Link -->
            <a href="{{ route('home') }}" target="_blank"
               class="flex items-center justify-between px-3.5 py-2.5 rounded-xl text-xs font-semibold text-slate-300 hover:bg-slate-800/80 hover:text-white transition group">
                <div class="flex items-center gap-3">
                    <i class="fa-solid fa-globe text-base text-emerald-400"></i>
                    <span>মূল ওয়েবসাইট দেখুন</span>
                </div>
                <i class="fa-solid fa-arrow-up-right-from-square text-[10px] text-slate-500"></i>
            </a>

            <!-- WhatsApp Batch Group Link -->
            <a href="https://chat.whatsapp.com/EeM7caaCmE79NR0BFdrwWh?s=sh&p=a&iam=2" target="_blank"
               class="flex items-center justify-between px-3.5 py-2.5 rounded-xl text-xs font-semibold text-slate-300 hover:bg-slate-800/80 hover:text-green-300 transition group">
                <div class="flex items-center gap-3">
                    <i class="fa-brands fa-whatsapp text-base text-green-400"></i>
                    <span>WhatsApp ব্যাচ গ্রুপ</span>
                </div>
                <i class="fa-solid fa-arrow-up-right-from-square text-[10px] text-slate-500"></i>
            </a>

        </div>

        <!-- Sidebar Bottom Admin Profile -->
        <div class="p-3.5 border-t border-slate-800 bg-slate-950/90">
            <div class="flex items-center justify-between p-2 rounded-xl bg-slate-900 border border-slate-800">
                <div class="flex items-center gap-2.5 min-w-0">
                    <div class="w-9 h-9 rounded-xl bg-gradient-to-tr from-emerald-600 to-teal-500 text-white flex items-center justify-center font-bold text-sm shrink-0">
                        {{ strtoupper(substr(Auth::user()->name ?? 'A', 0, 1)) }}
                    </div>
                    <div class="min-w-0">
                        <div class="font-bold text-white text-xs truncate">{{ Auth::user()->name ?? 'এডমিন' }}</div>
                        <div class="text-[10px] text-slate-400 truncate">{{ Auth::user()->email ?? '' }}</div>
                    </div>
                </div>

                <form method="POST" action="{{ route('admin.logout') }}">
                    @csrf
                    <button type="submit" title="লগআউট" class="w-8 h-8 rounded-lg bg-red-950/60 hover:bg-red-900 text-red-400 hover:text-white flex items-center justify-center transition border border-red-800/50">
                        <i class="fa-solid fa-right-from-bracket text-xs"></i>
                    </button>
                </form>
            </div>
        </div>

    </aside>

    <!-- ==================== MAIN CONTENT WRAPPER ==================== -->
    <div class="flex-1 flex flex-col min-w-0 lg:pl-72">

        <!-- Top Header Bar -->
        <header class="sticky top-0 z-30 bg-white/95 backdrop-blur-md border-b border-slate-200/90 shadow-xs">
            <div class="px-4 sm:px-6 lg:px-8 h-16 lg:h-20 flex items-center justify-between gap-4">

                <!-- Left: Hamburger & Breadcrumb -->
                <div class="flex items-center gap-3">
                    <button @click="sidebarOpen = true"
                            class="lg:hidden w-10 h-10 rounded-xl bg-slate-100 hover:bg-slate-200 text-slate-700 flex items-center justify-center transition">
                        <i class="fa-solid fa-bars text-lg"></i>
                    </button>
                    <div>
                        <div class="text-[11px] font-semibold text-slate-400 uppercase tracking-wider hidden sm:block">
                            আমার বাংলা একাডেমি এডমিন
                        </div>
                        <h1 class="text-base sm:text-lg font-bold text-slate-900 leading-tight">
                            @yield('title', 'ড্যাশবোর্ড')
                        </h1>
                    </div>
                </div>

                <!-- Right: Header Actions -->
                <div class="flex items-center gap-2 sm:gap-3">
                    <!-- Live Meeting Status Pill -->
                    @if($courseMeta && $courseMeta->live_meeting_url)
                        @if($courseMeta->is_live_now)
                            <a href="{{ route('admin.live-meetings') }}"
                               class="inline-flex items-center gap-1.5 px-3 py-1.5 rounded-full text-xs font-bold bg-red-50 text-red-700 border border-red-200 hover:bg-red-100 transition shadow-xs">
                                <span class="w-2 h-2 rounded-full bg-red-600 animate-ping"></span>
                                <span>লাইভ ক্লাস চলছে</span>
                            </a>
                        @else
                            <a href="{{ route('admin.live-meetings') }}"
                               class="hidden sm:inline-flex items-center gap-1.5 px-3 py-1.5 rounded-full text-xs font-semibold bg-emerald-50 text-emerald-800 border border-emerald-200 hover:bg-emerald-100 transition">
                                <i class="fa-solid fa-video text-emerald-600 text-[11px]"></i>
                                <span>মিটিং লিংক সক্রিয়</span>
                            </a>
                        @endif
                    @else
                        <a href="{{ route('admin.live-meetings') }}"
                           class="hidden sm:inline-flex items-center gap-1.5 px-3 py-1.5 rounded-full text-xs font-semibold bg-slate-100 text-slate-700 border border-slate-300 hover:bg-slate-200 transition">
                            <i class="fa-solid fa-plus text-xs"></i>
                            <span>মিটিং লিংক সেট করুন</span>
                        </a>
                    @endif

                    <!-- Website View Button -->
                    <a href="{{ route('home') }}" target="_blank"
                       class="inline-flex items-center gap-1.5 text-xs font-bold px-3 py-2 rounded-xl bg-slate-100 hover:bg-slate-200 text-slate-700 border border-slate-200 transition">
                        <i class="fa-solid fa-globe text-slate-500"></i>
                        <span class="hidden md:inline">ওয়েবসাইট</span>
                    </a>

                    <!-- Admin User Avatar Menu -->
                    <div class="flex items-center gap-2 pl-2 border-l border-slate-200">
                        <div class="w-9 h-9 rounded-xl bg-gradient-to-tr from-emerald-600 to-teal-500 text-white flex items-center justify-center font-bold text-sm shadow-xs">
                            {{ strtoupper(substr(Auth::user()->name ?? 'A', 0, 1)) }}
                        </div>
                        <div class="hidden md:flex flex-col text-left leading-tight">
                            <span class="text-xs font-bold text-slate-900">{{ Auth::user()->name ?? 'এডমিন' }}</span>
                            <span class="text-[10px] text-emerald-700 font-semibold">সুপার এডমিন</span>
                        </div>
                    </div>
                </div>

            </div>
        </header>

        <!-- Flash Alert Messages -->
        <div class="px-4 sm:px-6 lg:px-8 mt-4 w-full">
            @if(session('success'))
                <div x-data="{ show: true }" x-show="show" class="mb-4 bg-emerald-50 border border-emerald-200 text-emerald-800 px-4 py-3 rounded-2xl flex items-center justify-between shadow-xs">
                    <div class="flex items-center gap-2.5 text-xs sm:text-sm font-semibold">
                        <i class="fa-solid fa-circle-check text-emerald-600 text-base shrink-0"></i>
                        <span>{{ session('success') }}</span>
                    </div>
                    <button @click="show = false" class="text-emerald-500 hover:text-emerald-800 p-1">
                        <i class="fa-solid fa-xmark"></i>
                    </button>
                </div>
            @endif

            @if(session('error'))
                <div x-data="{ show: true }" x-show="show" class="mb-4 bg-red-50 border border-red-200 text-red-800 px-4 py-3 rounded-2xl flex items-center justify-between shadow-xs">
                    <div class="flex items-center gap-2.5 text-xs sm:text-sm font-semibold">
                        <i class="fa-solid fa-circle-exclamation text-red-600 text-base shrink-0"></i>
                        <span>{{ session('error') }}</span>
                    </div>
                    <button @click="show = false" class="text-red-500 hover:text-red-800 p-1">
                        <i class="fa-solid fa-xmark"></i>
                    </button>
                </div>
            @endif

            @if(session('info'))
                <div x-data="{ show: true }" x-show="show" class="mb-4 bg-blue-50 border border-blue-200 text-blue-800 px-4 py-3 rounded-2xl flex items-center justify-between shadow-xs">
                    <div class="flex items-center gap-2.5 text-xs sm:text-sm font-semibold">
                        <i class="fa-solid fa-circle-info text-blue-600 text-base shrink-0"></i>
                        <span>{{ session('info') }}</span>
                    </div>
                    <button @click="show = false" class="text-blue-500 hover:text-blue-800 p-1">
                        <i class="fa-solid fa-xmark"></i>
                    </button>
                </div>
            @endif
        </div>

        <!-- Main Body Content -->
        <main class="flex-1 px-4 sm:px-6 lg:px-8 py-5 sm:py-6 w-full max-w-7xl">
            @yield('content')
        </main>

        <!-- Admin Footer -->
        <footer class="bg-white border-t border-slate-200 py-4 px-4 sm:px-6 lg:px-8 text-xs text-slate-500 mt-auto flex flex-col sm:flex-row items-center justify-between gap-2">
            <div>
                আমার বাংলা একাডেমি এডমিন পোর্টাল &copy; {{ date('Y') }} । সর্বস্বত্ব সংরক্ষিত।
            </div>
            <div class="flex items-center gap-3 text-slate-400">
                <a href="{{ route('home') }}" target="_blank" class="hover:text-emerald-700">ওয়েবসাইট</a>
                <span>&bull;</span>
                <a href="{{ route('student.dashboard') }}" target="_blank" class="hover:text-emerald-700">স্টুডেন্ট পোর্টাল</a>
                <span>&bull;</span>
                <a href="https://wa.me/8801332549549" target="_blank" class="hover:text-emerald-700">হেল্পলাইন</a>
            </div>
        </footer>

    </div>

    @stack('scripts')
</body>
</html>
