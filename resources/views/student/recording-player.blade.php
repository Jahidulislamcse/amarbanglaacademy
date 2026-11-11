<!DOCTYPE html>
<html lang="bn" class="h-full bg-slate-950 scroll-smooth">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0, viewport-fit=cover">
    <title>{{ $meeting->title }} — ক্লাস রেকর্ডিং | আমার বাংলা একাডেমি</title>
    <meta name="robots" content="noindex, nofollow">

    <!-- Fonts & Icons -->
    <link rel="preconnect" href="https://fonts.googleapis.com">
    <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
    <link href="https://fonts.googleapis.com/css2?family=Hind+Siliguri:wght@400;500;600;700;800&family=Inter:wght@400;500;600;700&display=swap" rel="stylesheet">
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
</head>
<body class="min-h-full flex flex-col bg-slate-950 text-slate-100 font-bangla select-none overflow-x-clip"
      x-data="{ sidebarOpen: false, profileOpen: false }"
      @keydown.escape.window="sidebarOpen = false; profileOpen = false"
      :class="{ 'overflow-hidden': sidebarOpen }">

    @php
        $playerBatchName = $meeting->batch?->name ?? $course?->batch_no ?? '১ম ব্যাচ';
        $playerAvatarInitial = !empty($user->name)
            ? mb_strtoupper(mb_substr($user->name, 0, 1, 'UTF-8'), 'UTF-8')
            : 'শ';
        $dashUrl = route('student.dashboard');
    @endphp

    <!-- Backdrop for Slide-Over Portal Menu -->
    <div x-show="sidebarOpen"
         x-cloak
         x-transition:enter="transition-opacity ease-out duration-200"
         x-transition:enter-start="opacity-0"
         x-transition:enter-end="opacity-100"
         x-transition:leave="transition-opacity ease-in duration-150"
         x-transition:leave-start="opacity-100"
         x-transition:leave-end="opacity-0"
         @click="sidebarOpen = false"
         class="fixed inset-0 bg-slate-950/75 backdrop-blur-sm z-40"></div>

    <!-- ==================== STUDENT PORTAL SLIDE-OVER SIDEBAR ==================== -->
    <aside x-show="sidebarOpen"
           x-cloak
           x-transition:enter="transition-transform ease-out duration-300"
           x-transition:enter-start="-translate-x-full"
           x-transition:enter-end="translate-x-0"
           x-transition:leave="transition-transform ease-in duration-200"
           x-transition:leave-start="translate-x-0"
           x-transition:leave-end="-translate-x-full"
           class="fixed inset-y-0 left-0 z-50 w-72 max-w-[85vw] bg-gradient-to-b from-slate-950 via-slate-900 to-emerald-950/95 text-slate-300 border-r border-slate-800/90 flex flex-col shadow-2xl">

        <!-- Drawer Brand Header -->
        <div class="h-16 px-5 flex items-center justify-between border-b border-slate-800/80 shrink-0">
            <a href="{{ $dashUrl }}" class="flex items-center gap-3 group min-w-0">
                <div class="w-9 h-9 rounded-xl bg-gradient-to-tr from-emerald-600 to-teal-400 text-white flex items-center justify-center font-bold text-lg shadow-md shadow-emerald-600/30 shrink-0">
                    আ
                </div>
                <div class="leading-tight min-w-0">
                    <span class="text-sm font-extrabold text-white tracking-tight group-hover:text-emerald-300 transition block truncate">
                        আমার বাংলা একাডেমি
                    </span>
                    <span class="text-[10px] font-bold text-emerald-400 uppercase tracking-wider block mt-0.5">
                        Student LMS Portal
                    </span>
                </div>
            </a>
            <button type="button"
                    @click="sidebarOpen = false"
                    aria-label="মেনু বন্ধ করুন"
                    class="w-9 h-9 rounded-xl bg-slate-800/80 hover:bg-slate-700 text-slate-300 hover:text-white flex items-center justify-center transition shrink-0">
                <i class="fa-solid fa-xmark text-base"></i>
            </button>
        </div>

        <!-- Student Identity & Batch Card -->
        <div class="px-4 pt-4 pb-2 shrink-0">
            <div class="p-3.5 rounded-2xl bg-slate-900/90 border border-slate-800/90 space-y-2">
                <div class="flex items-center gap-3 min-w-0">
                    <div class="w-9 h-9 rounded-xl bg-gradient-to-tr from-emerald-500 to-teal-500 text-white flex items-center justify-center font-extrabold text-sm shrink-0">
                        {{ $playerAvatarInitial }}
                    </div>
                    <div class="min-w-0 flex-1">
                        <div class="text-xs sm:text-sm font-bold text-white truncate">{{ $user->name }}</div>
                        <div class="text-[11px] text-slate-400 font-mono truncate">{{ $user->phone ?? $user->email }}</div>
                    </div>
                </div>
                <div class="pt-2 border-t border-slate-800/80 flex items-center justify-between text-[11px]">
                    <span class="inline-flex items-center gap-1.5 px-2.5 py-0.5 rounded-full font-bold bg-emerald-950 text-emerald-300 border border-emerald-700/60 truncate">
                        <i class="fa-solid fa-layer-group text-[10px]"></i>
                        <span class="truncate">{{ $playerBatchName }}</span>
                    </span>
                    <span class="text-emerald-400 font-semibold">সক্রিয়</span>
                </div>
            </div>
        </div>

        <!-- Simple 6-Item Student Menu -->
        <nav class="flex-1 px-3 py-3 space-y-1 overflow-y-auto" aria-label="শিক্ষার্থী পোর্টাল মেনু">
            <div class="px-3 pb-1.5 text-[10px] font-bold text-slate-400 uppercase tracking-widest">
                শিক্ষার্থী মেনু
            </div>

            <a href="{{ $dashUrl }}#overview" class="flex items-center gap-3 px-3.5 py-2.5 rounded-xl text-xs font-bold text-slate-300 hover:bg-slate-800/80 hover:text-white transition">
                <i class="fa-solid fa-house text-sm w-4 text-center text-emerald-400"></i>
                <span>ড্যাশবোর্ড ওভারভিউ</span>
            </a>

            <a href="{{ $dashUrl }}#schedule" class="flex items-center gap-3 px-3.5 py-2.5 rounded-xl text-xs font-bold text-slate-300 hover:bg-slate-800/80 hover:text-white transition">
                <i class="fa-solid fa-calendar-days text-sm w-4 text-center text-amber-400"></i>
                <span>ক্লাস শিডিউল</span>
            </a>

            <a href="#recording-playlist" @click="sidebarOpen = false" class="flex items-center justify-between px-3.5 py-2.5 rounded-xl text-xs font-bold bg-emerald-600 text-white shadow-md shadow-emerald-600/25 transition">
                <div class="flex items-center gap-3">
                    <i class="fa-solid fa-circle-play text-sm w-4 text-center text-white"></i>
                    <span>ক্লাস রেকর্ডিং</span>
                </div>
                <span class="text-[10px] px-2 py-0.5 rounded-full bg-emerald-800 text-emerald-100 font-bold">
                    {{ count($otherRecordings) }}
                </span>
            </a>

            <a href="{{ $dashUrl }}#attendance" class="flex items-center gap-3 px-3.5 py-2.5 rounded-xl text-xs font-bold text-slate-300 hover:bg-slate-800/80 hover:text-white transition">
                <i class="fa-solid fa-clipboard-user text-sm w-4 text-center text-purple-400"></i>
                <span>হাজিরা ও উপস্থিতি</span>
            </a>

            <a href="{{ $dashUrl }}#curriculum" class="flex items-center gap-3 px-3.5 py-2.5 rounded-xl text-xs font-bold text-slate-300 hover:bg-slate-800/80 hover:text-white transition">
                <i class="fa-solid fa-book-open text-sm w-4 text-center text-blue-400"></i>
                <span>কোর্স কারিকুলাম</span>
            </a>

            <a href="{{ $dashUrl }}#support" class="flex items-center gap-3 px-3.5 py-2.5 rounded-xl text-xs font-bold text-slate-300 hover:bg-slate-800/80 hover:text-white transition">
                <i class="fa-solid fa-headset text-sm w-4 text-center text-emerald-400"></i>
                <span>গাইডলাইন ও সহায়তা</span>
            </a>

            <div class="pt-4 px-3 pb-1.5 text-[10px] font-bold text-slate-400 uppercase tracking-widest">
                দ্রুত সংযোগ
            </div>

            <a href="https://chat.whatsapp.com/EeM7caaCmE79NR0BFdrwWh?s=sh&p=a&iam=2" target="_blank"
               class="flex items-center justify-between px-3.5 py-2.5 rounded-xl text-xs font-semibold text-slate-300 hover:bg-slate-800/80 hover:text-green-300 transition">
                <div class="flex items-center gap-3">
                    <i class="fa-brands fa-whatsapp text-base text-green-400 w-4 text-center"></i>
                    <span>WhatsApp ব্যাচ গ্রুপ</span>
                </div>
                <i class="fa-solid fa-arrow-up-right-from-square text-[10px] text-slate-500"></i>
            </a>

            <a href="{{ route('home') }}" target="_blank"
               class="flex items-center justify-between px-3.5 py-2.5 rounded-xl text-xs font-semibold text-slate-300 hover:bg-slate-800/80 hover:text-white transition">
                <div class="flex items-center gap-3">
                    <i class="fa-solid fa-globe text-sm text-emerald-400 w-4 text-center"></i>
                    <span>মূল ওয়েবসাইট</span>
                </div>
                <i class="fa-solid fa-arrow-up-right-from-square text-[10px] text-slate-500"></i>
            </a>
        </nav>

        <!-- Logout Footer -->
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

    <!-- Formal Top Navigation Header -->
    <header class="h-16 bg-slate-900/95 border-b border-slate-800 px-3 sm:px-6 flex items-center justify-between gap-2 sm:gap-4 sticky top-0 z-30 backdrop-blur-md">
        <!-- Left: Sidebar Drawer Button, Dashboard Back Link & Video Title -->
        <div class="flex items-center gap-2 sm:gap-3 min-w-0 overflow-hidden">
            <button type="button"
                    @click="sidebarOpen = true"
                    aria-label="পোর্টাল মেনু খুলুন"
                    class="w-9 h-9 sm:w-10 sm:h-10 rounded-xl bg-slate-800 hover:bg-slate-700 text-slate-200 flex items-center justify-center border border-slate-700 transition shrink-0">
                <i class="fa-solid fa-bars text-sm"></i>
            </button>

            <a href="{{ $dashUrl }}"
               class="inline-flex items-center gap-1.5 px-2.5 sm:px-3 py-1.5 rounded-xl bg-slate-800 hover:bg-slate-700 text-slate-200 text-xs sm:text-sm font-bold border border-slate-700 transition shrink-0"
               title="ড্যাশবোর্ডে ফিরে যান">
                <i class="fa-solid fa-arrow-left text-xs"></i>
                <span class="hidden min-[380px]:inline">ড্যাশবোর্ড</span>
            </a>

            <div class="hidden sm:block h-5 w-px bg-slate-800 shrink-0"></div>

            <div class="flex items-center gap-2 min-w-0 overflow-hidden">
                <span class="hidden sm:inline-flex items-center gap-1.5 px-2.5 py-0.5 rounded-full text-[11px] font-bold bg-emerald-950 text-emerald-300 border border-emerald-800 shrink-0">
                    <i class="fa-solid fa-circle-play text-[10px]"></i>
                    <span>ভিডিও রেকর্ডিং</span>
                </span>
                <h1 class="text-xs sm:text-sm font-bold text-slate-200 truncate">
                    {{ $meeting->title }}
                </h1>
            </div>
        </div>

        <!-- Right: Mobile Playlist Jump, Batch Pill & Student Account Menu -->
        <div class="flex items-center gap-2 sm:gap-2.5 shrink-0">
            <a href="#recording-playlist"
               class="lg:hidden inline-flex items-center gap-1.5 px-2.5 py-1.5 rounded-xl bg-emerald-950/90 hover:bg-emerald-900 text-emerald-300 border border-emerald-800 text-xs font-bold transition">
                <i class="fa-solid fa-list-ul text-[11px]"></i>
                <span>প্লেলিস্ট</span>
                <span class="px-1.5 py-0.2 rounded bg-emerald-800 text-white text-[10px]">{{ count($otherRecordings) }}</span>
            </a>

            <span class="hidden xl:inline-flex items-center gap-1.5 px-2.5 py-1 rounded-full text-[11px] font-bold bg-emerald-950/80 text-emerald-300 border border-emerald-800/80">
                <span class="w-1.5 h-1.5 rounded-full bg-emerald-400"></span>
                {{ $playerBatchName }}
            </span>

            <!-- Student Profile Dropdown -->
            <div class="relative">
                <button type="button"
                        @click="profileOpen = !profileOpen"
                        class="flex items-center gap-2 px-2 sm:px-3 py-1.5 rounded-xl bg-slate-800/90 hover:bg-slate-800 border border-slate-700/80 text-xs transition cursor-pointer">
                    <div class="w-7 h-7 rounded-lg bg-gradient-to-tr from-emerald-600 to-teal-500 text-white flex items-center justify-center font-extrabold text-[11px] shrink-0">
                        {{ $playerAvatarInitial }}
                    </div>
                    <strong class="hidden md:inline text-white max-w-[130px] truncate">{{ $user->name }}</strong>
                    <i class="fa-solid fa-chevron-down text-[10px] text-slate-400 transition-transform"
                       :class="{ 'rotate-180': profileOpen }"></i>
                </button>

                <div x-show="profileOpen"
                     x-cloak
                     @click.away="profileOpen = false"
                     x-transition.opacity.duration.150ms
                     class="absolute right-0 mt-2 w-64 max-w-[calc(100vw-1.5rem)] bg-slate-900 border border-slate-700 rounded-2xl shadow-2xl py-2 z-50">
                    <div class="px-4 py-2.5 border-b border-slate-800">
                        <div class="text-xs sm:text-sm font-bold text-white truncate">{{ $user->name }}</div>
                        <div class="text-[11px] text-slate-400 font-mono truncate">{{ $user->phone ?? $user->email }}</div>
                        <span class="inline-block mt-1.5 text-[10px] font-bold px-2 py-0.5 rounded-full bg-emerald-950 text-emerald-300 border border-emerald-800">
                            {{ $playerBatchName }}
                        </span>
                    </div>
                    <div class="py-1 text-xs">
                        <a href="{{ $dashUrl }}#overview" class="flex items-center gap-2.5 px-4 py-2 text-slate-300 hover:bg-slate-800 hover:text-white transition">
                            <i class="fa-solid fa-house text-emerald-400 w-4 text-center"></i>
                            <span>ড্যাশবোর্ডে ফিরুন</span>
                        </a>
                        <a href="{{ $dashUrl }}#attendance" class="flex items-center gap-2.5 px-4 py-2 text-slate-300 hover:bg-slate-800 hover:text-white transition">
                            <i class="fa-solid fa-clipboard-user text-purple-400 w-4 text-center"></i>
                            <span>আমার উপস্থিতি</span>
                        </a>
                        <a href="https://wa.me/8801332549549" target="_blank" class="flex items-center gap-2.5 px-4 py-2 text-slate-300 hover:bg-slate-800 hover:text-white transition">
                            <i class="fa-brands fa-whatsapp text-green-400 w-4 text-center"></i>
                            <span>সাপোর্ট হেল্পলাইন</span>
                        </a>
                    </div>
                    <div class="pt-1.5 px-3 border-t border-slate-800">
                        <form method="POST" action="{{ route('student.logout') }}">
                            @csrf
                            <button type="submit"
                                    class="w-full flex items-center justify-center gap-2 px-3 py-2 rounded-xl bg-rose-950/70 hover:bg-rose-900 text-rose-300 hover:text-white text-xs font-bold transition">
                                <i class="fa-solid fa-right-from-bracket"></i>
                                <span>লগআউট</span>
                            </button>
                        </form>
                    </div>
                </div>
            </div>
        </div>
    </header>

    <!-- Main Content Area: Video Player & Playlist -->
    <main class="flex-1 max-w-7xl w-full mx-auto p-3 sm:p-6 lg:p-8">
        <div class="grid grid-cols-1 lg:grid-cols-3 gap-6">

            <!-- Left / Center: Unbranded Storage Video Player (2 cols on desktop) -->
            <div class="lg:col-span-2 space-y-4">
                <!-- Custom Unbranded Storage Player -->
                <x-storage-video-player :meeting="$meeting" :user="$user" :autoplay="true" playerId="main_recording_player" />

                <!-- Video Metadata & Lecture Details -->
                <div class="bg-slate-900 rounded-3xl border border-slate-800 p-5 sm:p-6 space-y-4">
                    <div class="flex flex-col sm:flex-row sm:items-center justify-between gap-3 pb-4 border-b border-slate-800">
                        <div>
                            <span class="text-[11px] font-bold text-emerald-400 uppercase tracking-wider">
                                {{ $course?->bangla_title ?? 'সাংবাদিকতা, আইন ও উপস্থাপনা' }} ({{ $meeting->batch?->name ?? $course?->batch_no ?? '১ম ব্যাচ' }})
                            </span>
                            <h2 class="text-lg sm:text-xl font-bold text-white mt-1">
                                {{ $meeting->title }}
                            </h2>
                        </div>
                        <div class="flex items-center gap-2 self-start sm:self-auto flex-wrap">
                            @if($meeting->recording_duration)
                                <span class="inline-flex items-center gap-1.5 px-3 py-1 rounded-xl bg-slate-800 text-slate-300 text-xs font-mono font-medium border border-slate-700">
                                    <i class="fa-regular fa-clock text-emerald-400"></i>
                                    {{ $meeting->recording_duration }}
                                </span>
                            @endif
                            <span class="inline-flex items-center gap-1.5 px-3 py-1 rounded-xl bg-emerald-950/80 text-emerald-300 text-xs font-semibold border border-emerald-800/80">
                                <i class="fa-solid fa-shield-halved text-[10px]"></i>
                                সুরক্ষিত প্লেব্যাক
                            </span>
                        </div>
                    </div>

                    <!-- Date & Session Info -->
                    <div class="grid grid-cols-2 sm:grid-cols-4 gap-3 text-xs">
                        <div class="p-3 bg-slate-950/60 rounded-2xl border border-slate-800">
                            <span class="text-slate-400 block text-[11px]">ক্লাসের তারিখ</span>
                            <strong class="text-white mt-0.5 block font-semibold">
                                {{ $meeting->starts_at ? $meeting->starts_at->format('d M, Y') : 'N/A' }}
                            </strong>
                        </div>

                        <div class="p-3 bg-slate-950/60 rounded-2xl border border-slate-800">
                            <span class="text-slate-400 block text-[11px]">সময়সূচি</span>
                            <strong class="text-white mt-0.5 block font-semibold">
                                {{ $meeting->starts_at ? $meeting->starts_at->format('h:i A') : 'N/A' }}
                            </strong>
                        </div>

                        <div class="p-3 bg-slate-950/60 rounded-2xl border border-slate-800">
                            <span class="text-slate-400 block text-[11px]">রেজোলিউশন</span>
                            <strong class="text-emerald-400 mt-0.5 block font-semibold font-mono">
                                Full HD 1080p
                            </strong>
                        </div>

                        <div class="p-3 bg-slate-950/60 rounded-2xl border border-slate-800">
                            <span class="text-slate-400 block text-[11px]">ভিডিও ফরম্যাট</span>
                            <strong class="text-white mt-0.5 block font-semibold">
                                ক্লাউড স্টোরেজ স্ট্রিম
                            </strong>
                        </div>
                    </div>

                    <!-- Lecture Notes / Recording Description -->
                    @if($meeting->recording_notes || $meeting->notes)
                        <div class="pt-2">
                            <h4 class="text-xs font-bold text-slate-300 uppercase tracking-wider mb-2 flex items-center gap-1.5">
                                <i class="fa-solid fa-file-lines text-emerald-400"></i>
                                <span>লেকচার নোট ও ক্লাসের মূল বিষয়বস্তু</span>
                            </h4>
                            <div class="p-4 bg-slate-950/80 rounded-2xl border border-slate-800 text-xs sm:text-sm text-slate-300 leading-relaxed whitespace-pre-line">
                                {{ $meeting->recording_notes ?: $meeting->notes }}
                            </div>
                        </div>
                    @endif
                </div>
            </div>

            <!-- Right Column: All Class Recordings Playlist (1 col on desktop) -->
            <div id="recording-playlist" class="space-y-4 scroll-mt-20">
                <div class="bg-slate-900 rounded-2xl sm:rounded-3xl border border-slate-800 p-4 sm:p-5 space-y-4">
                    <div class="flex items-center justify-between pb-3 border-b border-slate-800">
                        <div>
                            <h3 class="font-bold text-base text-white flex items-center gap-2">
                                <i class="fa-solid fa-list-ul text-emerald-400"></i>
                                <span>কোর্স রেকর্ডিং প্লেলিস্ট</span>
                            </h3>
                            <p class="text-xs text-slate-400 mt-0.5">
                                মোট {{ count($otherRecordings) }}টি ক্লাস রেকর্ডিং উপলব্ধ
                            </p>
                        </div>
                    </div>

                    <!-- Playlist Items List -->
                    <div class="space-y-2.5 max-h-[620px] overflow-y-auto pr-1">
                        @forelse($otherRecordings as $index => $rec)
                            @php
                                $isCurrent = ($rec->id === $meeting->id);
                            @endphp
                            <a href="{{ route('student.recordings.watch', $rec->id) }}"
                               class="block p-3.5 rounded-2xl border transition {{ $isCurrent ? 'bg-emerald-950/50 border-emerald-500/60 shadow-lg' : 'bg-slate-950/70 border-slate-800/80 hover:bg-slate-800/80 hover:border-slate-700' }}">
                                <div class="flex items-start gap-3">
                                    <div class="w-8 h-8 rounded-xl flex items-center justify-center font-bold text-xs shrink-0 {{ $isCurrent ? 'bg-emerald-500 text-slate-950' : 'bg-slate-800 text-slate-400' }}">
                                        @if($isCurrent)
                                            <i class="fa-solid fa-play text-[10px]"></i>
                                        @else
                                            {{ $index + 1 }}
                                        @endif
                                    </div>
                                    <div class="flex-1 min-w-0">
                                        <div class="flex items-center justify-between gap-2">
                                            <span class="text-[10px] text-slate-400">
                                                {{ $rec->starts_at ? $rec->starts_at->format('d M, Y') : 'ক্লাস' }}
                                            </span>
                                            @if($isCurrent)
                                                <span class="text-[9px] font-bold text-emerald-400 bg-emerald-950 px-1.5 py-0.5 rounded border border-emerald-800">
                                                    চলছে
                                                </span>
                                            @endif
                                        </div>
                                        <h4 class="text-xs font-bold {{ $isCurrent ? 'text-emerald-300' : 'text-slate-200' }} truncate mt-0.5">
                                            {{ $rec->title }}
                                        </h4>
                                        <div class="flex items-center gap-2 mt-1 text-[11px] text-slate-400">
                                            @if($rec->recording_duration)
                                                <span><i class="fa-regular fa-clock text-[9px] mr-1"></i>{{ $rec->recording_duration }}</span>
                                            @else
                                                <span><i class="fa-solid fa-video text-[9px] mr-1"></i>ভিডিও</span>
                                            @endif
                                        </div>
                                    </div>
                                </div>
                            </a>
                        @empty
                            <div class="p-6 text-center text-xs text-slate-500">
                                কোনো অতিরিক্ত ক্লাস রেকর্ডিং নেই।
                            </div>
                        @endforelse
                    </div>
                </div>

                <!-- Guidance & Help Box -->
                <div class="p-4 rounded-2xl bg-emerald-950/40 border border-emerald-800/60 text-xs text-emerald-200 space-y-2">
                    <div class="flex items-center gap-2 font-bold text-white">
                        <i class="fa-solid fa-circle-info text-emerald-400"></i>
                        <span>ভিডিও প্লেয়ার নির্দেশিকা:</span>
                    </div>
                    <ul class="space-y-1 text-slate-300 text-[11px] list-disc list-inside">
                        <li>স্পেসবার (Space) টিপে ভিডিও প্লে বা পজ করতে পারবেন।</li>
                        <li>কিবোর্ডের <strong>←</strong> ও <strong>→</strong> তীরচিহ্ন চেপে ৫ সেকেন্ড আগে/পরে যেতে পারবেন।</li>
                        <li><strong>F</strong> চেপে ফুলস্ক্রিন করতে পারবেন।</li>
                        <li>প্লেয়ারের নিচে থেকে ভিডিওর গতি (Speed) পরিবর্তন করা যাবে।</li>
                    </ul>
                </div>
            </div>

        </div>
    </main>

    <!-- Minimal Theater Footer -->
    <footer class="border-t border-slate-900 py-4 px-4 sm:px-6 text-[11px] text-slate-500 text-center">
        আমার বাংলা একাডেমি শিক্ষার্থী পোর্টাল &copy; {{ date('Y') }} । সুরক্ষিত ভিডিও প্লেয়ার
    </footer>

</body>
</html>
