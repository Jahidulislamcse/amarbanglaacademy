<!DOCTYPE html>
<html lang="bn" class="h-full bg-slate-950">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>{{ $course?->live_class_topic ?? 'অনলাইন লাইভ ক্লাসরুম' }} - আমার বাংলা একাডেমি</title>
    <meta name="robots" content="noindex, nofollow">

    <!-- Favicon -->
    <link rel="icon" type="image/png" href="{{ asset('images/favicon.png') }}">
    <link rel="shortcut icon" href="{{ asset('favicon.ico') }}">
    <link rel="apple-touch-icon" href="{{ asset('images/favicon.png') }}">

    <!-- Tailwind CSS CDN -->
    <script src="https://cdn.tailwindcss.com"></script>
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.5.1/css/all.min.css">
    <link rel="preconnect" href="https://fonts.googleapis.com">
    <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
    <link href="https://fonts.googleapis.com/css2?family=Hind+Siliguri:wght@400;500;600;700;800&family=Plus+Jakarta+Sans:wght@500;700;800&display=swap" rel="stylesheet">

    <style>
        body {
            font-family: 'Hind Siliguri', 'Plus Jakarta Sans', sans-serif;
        }
        .classroom-frame-container {
            height: calc(100vh - 65px);
        }
        @media (max-width: 640px) {
            .classroom-frame-container {
                height: calc(100vh - 105px);
            }
        }
    </style>
</head>
<body class="h-full flex flex-col bg-slate-950 text-white overflow-hidden select-none">

    <!-- Top Navigation & Classroom Control Bar -->
    <header class="h-16 sm:h-[65px] bg-slate-900/95 border-b border-slate-800 px-3 sm:px-6 flex items-center justify-between gap-3 shrink-0 backdrop-blur-md z-30">
        <!-- Left: Logo, Live Status & Topic -->
        <div class="flex items-center gap-3 sm:gap-4 overflow-hidden">
            <a href="{{ route('student.dashboard') }}"
               class="flex items-center gap-2.5 group shrink-0"
               title="ড্যাশবোর্ডে ফিরে যান">
                <div class="bg-white rounded-xl px-2 py-1 shadow-sm group-hover:scale-105 transition-transform">
                    <img src="{{ asset('images/AmarBanglaAcademy.png') }}"
                         alt="আমার বাংলা একাডেমি"
                         class="h-7 sm:h-8 w-auto object-contain">
                </div>
                <div class="hidden md:block leading-tight">
                    <span class="sr-only">আমার বাংলা একাডেমি</span>
                    <span class="text-[10px] text-emerald-400 font-bold uppercase tracking-wider block">লাইভ ক্লাসরুম</span>
                </div>
            </a>

            <div class="h-6 w-px bg-slate-800 hidden md:block"></div>

            <!-- Topic & Live Indicator -->
            <div class="flex items-center gap-2 overflow-hidden">
                @if($course?->is_live_now)
                    <span class="inline-flex items-center gap-1.5 px-2.5 py-1 rounded-full text-[11px] font-black bg-rose-600 text-white animate-pulse shrink-0">
                        <span class="w-1.5 h-1.5 rounded-full bg-white"></span>
                        <span class="hidden sm:inline">LIVE</span>
                    </span>
                @else
                    <span class="inline-flex items-center gap-1 px-2.5 py-1 rounded-full text-[11px] font-bold bg-emerald-950 text-emerald-300 border border-emerald-800 shrink-0">
                        <i class="fa-solid fa-video text-[10px]"></i>
                        <span class="hidden sm:inline">ক্লাসরুম</span>
                    </span>
                @endif

                <h1 class="text-xs sm:text-sm font-bold text-slate-200 truncate max-w-xs sm:max-w-md lg:max-w-xl">
                    {{ $course?->live_class_topic ?: ($course?->bangla_title ?? 'সাংবাদিকতা, আইন ও উপস্থাপনা — অনলাইন লাইভ ক্লাস') }}
                </h1>
            </div>
        </div>

        <!-- Right: Actions & Return Button -->
        <div class="flex items-center gap-2 sm:gap-3 shrink-0">
            <!-- Student Profile Chip -->
            <div class="hidden lg:flex items-center gap-2 px-3 py-1.5 rounded-xl bg-slate-800/80 border border-slate-700/80 text-xs">
                <div class="w-2 h-2 rounded-full bg-emerald-400"></div>
                <span class="text-slate-300 font-medium">শিক্ষার্থী:</span>
                <strong class="text-white font-bold">{{ $user->name }}</strong>
            </div>

            <!-- Fullscreen Button -->
            <button type="button" onclick="toggleClassroomFullscreen()"
                    id="fullscreen-btn"
                    title="ফুলস্ক্রিন মোড"
                    class="w-9 h-9 sm:w-10 sm:h-10 rounded-xl bg-slate-800 hover:bg-slate-700 text-slate-300 hover:text-white flex items-center justify-center transition border border-slate-700">
                <i class="fa-solid fa-expand text-xs sm:text-sm"></i>
            </button>

            <!-- Back to Dashboard -->
            <a href="{{ route('student.dashboard') }}"
               class="inline-flex items-center gap-1.5 sm:gap-2 px-3 sm:px-4 py-2 rounded-xl bg-slate-800 hover:bg-rose-950/60 hover:text-rose-300 hover:border-rose-800 text-slate-300 text-xs sm:text-sm font-bold border border-slate-700 transition">
                <i class="fa-solid fa-arrow-left text-xs"></i>
                <span class="hidden sm:inline">ড্যাশবোর্ড</span>
            </a>
        </div>
    </header>

    <!-- Main Classroom Canvas: 100% In-System Video Meeting Panel -->
    <main id="classroom-main" class="flex-1 w-full classroom-frame-container relative bg-slate-950 flex flex-col">

        <!-- Embedded Interactive Video Meeting -->
        <iframe
            id="in-system-frame"
            src="{{ $embedUrl }}"
            allow="camera *; microphone *; display-capture *; autoplay *; clipboard-write *; fullscreen *"
            class="w-full flex-1 border-0 bg-slate-950"
            allowfullscreen>
        </iframe>

        <!-- In-System Classroom Bottom Info Strip -->
        <div class="bg-slate-900/95 border-t border-slate-800 px-4 py-2.5 flex flex-col sm:flex-row items-center justify-between gap-2 text-xs text-slate-300 shrink-0">
            <div class="flex items-center gap-2">
                <span class="w-2.5 h-2.5 rounded-full bg-emerald-400 animate-pulse"></span>
                <span>আপনি সরাসরি <strong>আমার বাংলা একাডেমি লাইভ ক্লাসরুমে</strong> যুক্ত আছেন। ক্যামেরা, মাইক ও স্ক্রিন শেয়ারের সকল কন্ট্রোল নিচে রয়েছে।</span>
            </div>

            <div class="flex items-center gap-3 text-xs flex-wrap">
                @if($course?->live_meeting_passcode)
                    <span class="bg-slate-800 px-2.5 py-1 rounded-lg border border-slate-700 font-mono text-white">
                        পাসকোড: <strong>{{ $course->live_meeting_passcode }}</strong>
                    </span>
                @endif

                @if(!empty($meetingUrl) && !str_contains(strtolower($meetingUrl), 'meet.jit.si'))
                    <a href="{{ $meetingUrl }}" target="_blank"
                       class="inline-flex items-center gap-1.5 px-3 py-1 rounded-lg bg-slate-800 hover:bg-slate-700 text-slate-300 hover:text-white border border-slate-700 transition"
                       title="যদি এই পেজে ব্রাউজারে কোনো সমস্যা হয়, তবে বিকল্প হিসেবে এক্সটার্নাল অ্যাপে খুলতে ক্লিক করুন">
                        <i class="fa-solid fa-arrow-up-right-from-square text-[10px]"></i>
                        <span>বিকল্প অ্যাপ লিংক</span>
                    </a>
                @endif

                <a href="https://wa.me/8801332549549" target="_blank" class="text-emerald-400 hover:underline flex items-center gap-1 font-bold">
                    <i class="fa-brands fa-whatsapp text-sm"></i>
                    <span>হেল্পলাইন: 01332-549549</span>
                </a>
            </div>
        </div>

    </main>

    <!-- Fullscreen & Copy Helper Scripts -->
    <script>
        function toggleClassroomFullscreen() {
            var elem = document.documentElement;
            var btn = document.getElementById('fullscreen-btn');
            var icon = btn.querySelector('i');

            if (!document.fullscreenElement) {
                if (elem.requestFullscreen) {
                    elem.requestFullscreen();
                } else if (elem.webkitRequestFullscreen) {
                    elem.webkitRequestFullscreen();
                } else if (elem.msRequestFullscreen) {
                    elem.msRequestFullscreen();
                }
                if (icon) icon.className = 'fa-solid fa-compress text-xs sm:text-sm';
            } else {
                if (document.exitFullscreen) {
                    document.exitFullscreen();
                } else if (document.webkitExitFullscreen) {
                    document.webkitExitFullscreen();
                } else if (document.msExitFullscreen) {
                    document.msExitFullscreen();
                }
                if (icon) icon.className = 'fa-solid fa-expand text-xs sm:text-sm';
            }
        }

        function copyClassText(text, btn) {
            if (navigator.clipboard && navigator.clipboard.writeText) {
                navigator.clipboard.writeText(text).then(function() {
                    var icon = btn.querySelector('i');
                    if (icon) {
                        var orig = icon.className;
                        icon.className = 'fa-solid fa-check text-emerald-400 text-xs';
                        setTimeout(function() { icon.className = orig; }, 2000);
                    }
                });
            } else {
                window.prompt('তথ্য কপি করুন:', text);
            }
        }
    </script>
</body>
</html>
