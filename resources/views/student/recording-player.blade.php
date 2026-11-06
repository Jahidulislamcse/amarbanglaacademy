<!DOCTYPE html>
<html lang="bn" class="h-full bg-slate-950">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
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
<body class="min-h-full flex flex-col bg-slate-950 text-slate-100 font-bangla select-none">

    <!-- Top Navigation Bar -->
    <header class="h-16 bg-slate-900/95 border-b border-slate-800 px-4 sm:px-6 flex items-center justify-between gap-4 sticky top-0 z-40 backdrop-blur-md">
        <!-- Left: Logo & Back Button -->
        <div class="flex items-center gap-3 sm:gap-4 overflow-hidden">
            <a href="{{ route('student.dashboard') }}"
               class="inline-flex items-center gap-2 px-3 py-1.5 rounded-xl bg-slate-800 hover:bg-slate-700 text-slate-200 text-xs sm:text-sm font-bold border border-slate-700 transition shrink-0"
               title="ড্যাশবোর্ডে ফিরে যান">
                <i class="fa-solid fa-arrow-left text-xs"></i>
                <span>ড্যাশবোর্ড</span>
            </a>

            <div class="h-5 w-px bg-slate-800 shrink-0"></div>

            <div class="flex items-center gap-2 overflow-hidden">
                <span class="inline-flex items-center gap-1.5 px-2.5 py-0.5 rounded-full text-[11px] font-bold bg-emerald-950 text-emerald-300 border border-emerald-800 shrink-0">
                    <i class="fa-solid fa-circle-play text-[10px]"></i>
                    <span>ভিডিও রেকর্ডিং</span>
                </span>
                <h1 class="text-xs sm:text-sm font-bold text-slate-200 truncate">
                    {{ $meeting->title }}
                </h1>
            </div>
        </div>

        <!-- Right: Student Profile & Badge -->
        <div class="flex items-center gap-2 sm:gap-3 shrink-0">
            <div class="hidden md:flex items-center gap-2 px-3 py-1.5 rounded-xl bg-slate-800/80 border border-slate-700/80 text-xs">
                <div class="w-2 h-2 rounded-full bg-emerald-400"></div>
                <span class="text-slate-300 font-medium">শিক্ষার্থী:</span>
                <strong class="text-white">{{ $user->name }}</strong>
            </div>

            <a href="{{ route('student.dashboard') }}"
               class="p-2 text-slate-400 hover:text-white rounded-lg transition"
               title="বন্ধ করুন">
                <i class="fa-solid fa-xmark text-lg"></i>
            </a>
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
                                {{ $course?->bangla_title ?? 'সাংবাদিকতা, আইন ও উপস্থাপনা' }} ({{ $course?->batch_no ?? '১ম ব্যাচ' }})
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
            <div class="space-y-4">
                <div class="bg-slate-900 rounded-3xl border border-slate-800 p-5 space-y-4">
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

</body>
</html>
