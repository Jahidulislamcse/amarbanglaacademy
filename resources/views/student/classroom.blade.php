<!DOCTYPE html>
<html lang="bn" class="h-full bg-slate-950">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>{{ $course?->live_class_topic ?? 'অনলাইন লাইভ ক্লাসরুম' }} - আমার বাংলা একাডেমি</title>
    <meta name="robots" content="noindex, nofollow">

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
               class="flex items-center gap-2 group shrink-0"
               title="ড্যাশবোর্ডে ফিরে যান">
                <div class="w-9 h-9 sm:w-10 sm:h-10 rounded-xl bg-gradient-to-tr from-emerald-600 to-teal-500 text-white font-black flex items-center justify-center text-base sm:text-lg shadow-md shadow-emerald-500/20 group-hover:scale-105 transition-transform">
                    আ
                </div>
                <div class="hidden md:block leading-tight">
                    <span class="text-xs font-black tracking-tight text-white block">আমার বাংলা একাডেমি</span>
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

    <!-- Main Classroom Canvas -->
    <main id="classroom-main" class="flex-1 w-full classroom-frame-container relative bg-slate-950 flex flex-col">

        @if($provider === 'in_system')
            <!-- MODE 1: NATIVE IN-SYSTEM WEBRTC VIDEO CLASSROOM (100% Inside website) -->
            <div class="relative w-full h-full flex flex-col">
                <iframe
                    id="in-system-frame"
                    src="{{ $embedUrl }}"
                    allow="camera; microphone; display-capture; autoplay; clipboard-write; fullscreen"
                    class="w-full flex-1 border-0 bg-slate-950"
                    allowfullscreen>
                </iframe>

                <!-- In-System Classroom Bottom Info Strip -->
                <div class="bg-slate-900/90 border-t border-slate-800/80 px-4 py-2 flex flex-col sm:flex-row items-center justify-between gap-2 text-xs text-slate-400 shrink-0">
                    <div class="flex items-center gap-2">
                        <i class="fa-solid fa-circle-check text-emerald-400"></i>
                        <span>আপনি সরাসরি <strong>ইন-সিস্টেম লাইভ ক্লাসরুমে</strong> যুক্ত আছেন। মাইক্রোফোন ও ক্যামেরা স্ক্রিনের নিচ থেকে চালু/বন্ধ করতে পারবেন।</span>
                    </div>
                    <div class="flex items-center gap-3 text-[11px]">
                        @if($course?->live_meeting_passcode)
                            <span>পাসকোড: <strong class="text-white font-mono">{{ $course->live_meeting_passcode }}</strong></span>
                        @endif
                        <a href="https://wa.me/8801332549549" target="_blank" class="text-emerald-400 hover:underline flex items-center gap-1">
                            <i class="fa-brands fa-whatsapp"></i>
                            <span>হেল্পলাইন: 01332-549549</span>
                        </a>
                    </div>
                </div>
            </div>

        @elseif($provider === 'google_meet')
            <!-- MODE 2: GOOGLE MEET CLASSROOM THEATRE -->
            <div class="flex-1 overflow-y-auto p-4 sm:p-8 flex items-center justify-center">
                <div class="max-w-2xl w-full bg-slate-900 border border-slate-800 rounded-3xl p-6 sm:p-10 shadow-2xl text-center space-y-6 relative overflow-hidden">
                    <!-- Glow effect -->
                    <div class="absolute -right-20 -top-20 w-60 h-60 bg-emerald-500/10 rounded-full blur-3xl pointer-events-none"></div>

                    <!-- Platform Logo & Status -->
                    <div class="w-16 h-16 rounded-2xl bg-emerald-950/80 border border-emerald-500/30 text-emerald-400 flex items-center justify-center text-3xl mx-auto shadow-inner">
                        <i class="fa-solid fa-chalkboard-user"></i>
                    </div>

                    <div class="space-y-2">
                        <div class="inline-flex items-center gap-2 px-3 py-1 rounded-full text-xs font-bold bg-emerald-900/40 text-emerald-300 border border-emerald-700/50">
                            <span class="w-2 h-2 rounded-full bg-emerald-400 animate-pulse"></span>
                            <span>গুগল মিট অনলাইন লাইভ সেশন</span>
                        </div>
                        <h2 class="text-xl sm:text-2xl font-black text-white">
                            {{ $course?->live_class_topic ?: 'আজকের লাইভ ক্লাস শুরু হয়েছে' }}
                        </h2>
                        <p class="text-xs sm:text-sm text-slate-300 max-w-lg mx-auto leading-relaxed">
                            গুগল মিটের সিকিউরিটি ও গোপনীয়তা পলিসির কারণে ক্যামেরা ও মাইক্রোফোন ব্যবহারের জন্য এটি গুগল অনুমোদিত সুরক্ষিত ট্যাবে ওপেন হয়। নিচের বাটনে ক্লিক করে সরাসরি ক্লাসে অংশ নিন।
                        </p>
                    </div>

                    <!-- Big Launch Button -->
                    <div class="pt-2">
                        <a href="{{ $meetingUrl }}" target="_blank"
                           class="inline-flex items-center justify-center gap-3 w-full sm:w-auto px-8 py-4 rounded-2xl bg-gradient-to-r from-emerald-500 via-teal-500 to-emerald-600 hover:from-emerald-400 hover:to-teal-500 text-slate-950 font-black text-sm sm:text-base shadow-xl shadow-emerald-500/20 transform hover:-translate-y-0.5 active:scale-95 transition">
                            <i class="fa-solid fa-video text-lg"></i>
                            <span>গুগল মিটে সরাসরি ক্লাসে যুক্ত হন</span>
                            <i class="fa-solid fa-arrow-up-right-from-square text-xs"></i>
                        </a>
                    </div>

                    <!-- Meeting Details Card -->
                    <div class="grid grid-cols-1 sm:grid-cols-2 gap-3 pt-4 border-t border-slate-800 text-left text-xs">
                        <div class="p-3 bg-slate-950/70 rounded-xl border border-slate-800">
                            <span class="text-slate-400 text-[11px] block">কোর্স ও ব্যাচ</span>
                            <span class="font-bold text-white mt-0.5 block">{{ $course?->bangla_title ?? 'সাংবাদিকতা, আইন ও উপস্থাপনা' }} ({{ $course?->batch_no ?? '১ম ব্যাচ' }})</span>
                        </div>
                        <div class="p-3 bg-slate-950/70 rounded-xl border border-slate-800">
                            <span class="text-slate-400 text-[11px] block">অংশগ্রহণকারী শিক্ষার্থী</span>
                            <span class="font-bold text-emerald-400 mt-0.5 block">{{ $user->name }}</span>
                        </div>
                    </div>

                    <!-- WhatsApp Group Backup -->
                    <div class="pt-2 text-xs text-slate-400">
                        কোনো সমস্যা হলে WhatsApp গ্রুপে যুক্ত থাকুন:
                        <a href="https://chat.whatsapp.com/EeM7caaCmE79NR0BFdrwWh?s=sh&p=a&iam=2" target="_blank" class="text-emerald-400 underline font-bold ml-1">
                            WhatsApp ব্যাচ গ্রুপ
                        </a>
                    </div>
                </div>
            </div>

        @elseif($provider === 'zoom')
            <!-- MODE 3: ZOOM CLASSROOM THEATRE -->
            <div class="flex-1 overflow-y-auto p-4 sm:p-8 flex items-center justify-center">
                <div class="max-w-2xl w-full bg-slate-900 border border-slate-800 rounded-3xl p-6 sm:p-10 shadow-2xl text-center space-y-6 relative overflow-hidden">
                    <div class="w-16 h-16 rounded-2xl bg-blue-950/80 border border-blue-500/30 text-blue-400 flex items-center justify-center text-3xl mx-auto shadow-inner">
                        <i class="fa-solid fa-video"></i>
                    </div>

                    <div class="space-y-2">
                        <div class="inline-flex items-center gap-2 px-3 py-1 rounded-full text-xs font-bold bg-blue-900/40 text-blue-300 border border-blue-700/50">
                            <span class="w-2 h-2 rounded-full bg-blue-400 animate-pulse"></span>
                            <span>জুম (Zoom) অনলাইন লাইভ সেশন</span>
                        </div>
                        <h2 class="text-xl sm:text-2xl font-black text-white">
                            {{ $course?->live_class_topic ?: 'আজকের জুম লাইভ ক্লাস' }}
                        </h2>
                        <p class="text-xs sm:text-sm text-slate-300 max-w-lg mx-auto leading-relaxed">
                            আপনি সরাসরি জুম অ্যাপ অথবা ব্রাউজারের মাধ্যমে আজকের ক্লাসে যুক্ত হতে পারবেন। নিচে মিটিং আইডি ও পাসকোড দেওয়া আছে।
                        </p>
                    </div>

                    <!-- Meeting ID & Passcode Card -->
                    <div class="grid grid-cols-1 sm:grid-cols-2 gap-3 text-xs">
                        <div class="p-3.5 bg-slate-950/80 rounded-2xl border border-slate-800 flex items-center justify-between">
                            <div class="text-left">
                                <span class="text-slate-400 text-[11px] block">Meeting ID</span>
                                <strong class="text-white font-mono text-sm block mt-0.5">{{ $course?->live_meeting_id ?: 'সরাসরি লিংকে যুক্ত' }}</strong>
                            </div>
                            @if($course?->live_meeting_id)
                                <button type="button" onclick="copyClassText('{{ $course->live_meeting_id }}', this)" class="p-2 text-slate-400 hover:text-white">
                                    <i class="fa-regular fa-copy"></i>
                                </button>
                            @endif
                        </div>

                        <div class="p-3.5 bg-slate-950/80 rounded-2xl border border-slate-800 flex items-center justify-between">
                            <div class="text-left">
                                <span class="text-slate-400 text-[11px] block">Passcode</span>
                                <strong class="text-white font-mono text-sm block mt-0.5">{{ $course?->live_meeting_passcode ?: 'পাসকোড নেই' }}</strong>
                            </div>
                            @if($course?->live_meeting_passcode)
                                <button type="button" onclick="copyClassText('{{ $course->live_meeting_passcode }}', this)" class="p-2 text-slate-400 hover:text-white">
                                    <i class="fa-regular fa-copy"></i>
                                </button>
                            @endif
                        </div>
                    </div>

                    <!-- Direct Launch Button -->
                    <div class="pt-2">
                        <a href="{{ $meetingUrl }}" target="_blank"
                           class="inline-flex items-center justify-center gap-3 w-full sm:w-auto px-8 py-4 rounded-2xl bg-gradient-to-r from-blue-600 to-indigo-600 hover:from-blue-500 hover:to-indigo-500 text-white font-black text-sm sm:text-base shadow-xl shadow-blue-500/20 transform hover:-translate-y-0.5 active:scale-95 transition">
                            <i class="fa-solid fa-video text-lg"></i>
                            <span>জুম ক্লাসে প্রবেশ করুন</span>
                            <i class="fa-solid fa-arrow-up-right-from-square text-xs"></i>
                        </a>
                    </div>
                </div>
            </div>

        @elseif($provider === 'youtube')
            <!-- MODE 4: YOUTUBE LIVE EMBED -->
            <div class="relative w-full h-full flex flex-col">
                <iframe
                    src="{{ $embedUrl }}"
                    class="w-full flex-1 border-0"
                    allow="accelerometer; autoplay; clipboard-write; encrypted-media; gyroscope; picture-in-picture"
                    allowfullscreen>
                </iframe>
            </div>
        @endif

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
