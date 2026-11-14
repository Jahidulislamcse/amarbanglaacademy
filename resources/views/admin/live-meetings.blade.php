@extends('layouts.admin')

@section('title', 'তারিখ ও সময়ভিত্তিক লাইভ মিটিং শিডিউল - আমার বাংলা একাডেমি এডমিন')

@section('content')
@php
    $displayMeeting = $activeMeeting;
    $isLive = $displayMeeting ? (bool) $displayMeeting->is_live_now : (empty($selectedBatchId) && $primaryCourse && $primaryCourse->is_live_now);
    $meetingTopic = $displayMeeting?->title ?: ($primaryCourse?->live_class_topic ?: 'সাংবাদিকতা, আইন ও উপস্থাপনা — অনলাইন লাইভ সেশন');
    $meetingUrl = $displayMeeting?->meeting_url ?: (empty($selectedBatchId) ? ($primaryCourse?->live_meeting_url ?: '') : '');
    $meetingId = $displayMeeting?->meeting_id ?: (empty($selectedBatchId) ? ($primaryCourse?->live_meeting_id ?: '') : '');
    $meetingPasscode = $displayMeeting?->meeting_passcode ?: (empty($selectedBatchId) ? ($primaryCourse?->live_meeting_passcode ?: '') : '');
    $meetingBatchLabel = $displayMeeting?->batch?->name ?? ($selectedBatchId ? (($batches ?? collect())->firstWhere('id', $selectedBatchId)?->name ?? 'নির্বাচিত ব্যাচ') : ($primaryCourse?->batch_no ?? 'সকল ব্যাচ'));
    $heroScheduleText = $displayMeeting?->formatted_schedule ?: ($primaryCourse?->live_schedule_text ?: 'শিডিউল করা');

    $meetingsJsonList = $meetings->map(function($m) {
        $startsAt = $m->starts_at ? \Carbon\Carbon::parse($m->starts_at) : null;
        return [
            'id' => $m->id,
            'batch_id' => $m->batch_id ? (string) $m->batch_id : '',
            'batch_name' => $m->batch?->name ?? 'সকল ব্যাচ',
            'title' => $m->title,
            'meeting_url' => $m->meeting_url,
            'meeting_id' => $m->meeting_id ?? '',
            'meeting_passcode' => $m->meeting_passcode ?? '',
            'platform' => $m->platform ?? 'other',
            'starts_at' => $startsAt ? $startsAt->toIso8601String() : null,
            'month_short' => $startsAt ? $startsAt->format('M') : 'N/A',
            'day_num' => $startsAt ? $startsAt->format('d') : '--',
            'date_formatted' => $startsAt ? $startsAt->format('d M, Y') : 'N/A',
            'day_name' => $startsAt ? $startsAt->isoFormat('dddd') : '',
            'time_formatted' => $startsAt ? $startsAt->format('h:i A') : 'N/A',
            'date_str' => $startsAt ? $startsAt->format('Y-m-d') : date('Y-m-d'),
            'time_str' => $startsAt ? $startsAt->format('H:i') : '21:00',
            'is_past' => $startsAt ? $startsAt->isPast() : false,
            'schedule_text' => $m->schedule_text ?? '',
            'formatted_schedule' => $m->formatted_schedule,
            'is_live_now' => (bool) $m->is_live_now,
            'has_recording' => (bool) $m->hasRecording(),
            'recording_url' => $m->recording_url ?? '',
            'recording_duration' => $m->recording_duration ?? '',
            'recording_notes' => $m->recording_notes ?? '',
            'notes' => $m->notes ?? '',
            'status' => $m->status ?? 'scheduled',
            'watch_url' => route('student.recordings.watch', $m->id),
        ];
    })->values();
@endphp

<div class="space-y-6" x-data="liveMeetingsManager({
    initialMeetings: {{ Js::from($meetingsJsonList) }},
    heroData: {
        activeMeetingId: {{ $displayMeeting ? $displayMeeting->id : 'null' }},
        isLive: {{ $isLive ? 'true' : 'false' }},
        topic: {{ Js::from($meetingTopic) }},
        schedule: {{ Js::from($heroScheduleText) }},
        batchLabel: {{ Js::from($meetingBatchLabel) }},
        url: {{ Js::from($meetingUrl) }},
        meetingId: {{ Js::from($meetingId) }},
        passcode: {{ Js::from($meetingPasscode) }}
    }
})">

    <!-- Floating Sleek Toast Notification -->
    <div x-show="toast.show"
         x-transition:enter="transform ease-out duration-300 transition"
         x-transition:enter-start="translate-y-2 opacity-0 sm:translate-y-0 sm:translate-x-2"
         x-transition:enter-end="translate-y-0 opacity-100 sm:translate-x-0"
         x-transition:leave="transition ease-in duration-200"
         x-transition:leave-start="opacity-100"
         x-transition:leave-end="opacity-0"
         class="fixed top-5 right-5 z-[9999] max-w-sm w-full shadow-2xl rounded-2xl pointer-events-auto border overflow-hidden p-4 flex items-start gap-3"
         :class="toast.type === 'error' ? 'bg-rose-900 text-white border-rose-700' : 'bg-slate-900 text-white border-emerald-500/50'"
         style="display: none;">
        <div class="shrink-0 mt-0.5">
            <template x-if="toast.type === 'error'">
                <i class="fa-solid fa-circle-exclamation text-rose-400 text-lg"></i>
            </template>
            <template x-if="toast.type !== 'error'">
                <i class="fa-solid fa-circle-check text-emerald-400 text-lg"></i>
            </template>
        </div>
        <div class="flex-1">
            <p class="text-xs sm:text-sm font-semibold leading-relaxed" x-text="toast.message"></p>
        </div>
        <button type="button" @click="toast.show = false" class="text-white/60 hover:text-white p-1">
            <i class="fa-solid fa-xmark text-sm"></i>
        </button>
    </div>

    <!-- Page Header -->
    <div class="flex flex-col sm:flex-row sm:items-center justify-between gap-4">
        <div>
            <div class="flex items-center gap-2 text-xs text-slate-500 mb-1">
                <a href="{{ route('admin.students') }}" class="hover:text-emerald-700">ড্যাশবোর্ড</a>
                <span>/</span>
                <span class="text-slate-800 font-semibold">লাইভ ক্লাস ও মিটিং</span>
            </div>
            <h1 class="text-2xl font-bold text-slate-900 flex items-center gap-2.5">
                <i class="fa-solid fa-video text-emerald-600"></i>
                <span>লাইভ ক্লাস ও মিটিং রুম কন্ট্রোল (তারিখ ও সময়ভিত্তিক শিডিউল)</span>
            </h1>
            <p class="text-xs sm:text-sm text-slate-500 mt-1">
                নির্দিষ্ট তারিখ ও সময় নির্ধারণ করে লাইভ ক্লাসের লিংক তৈরি করুন। শিক্ষার্থীরা তাদের পোর্টালে সেই তারিখ ও সময় অনুযায়ী ক্লাস দেখতে পাবে।
            </p>
        </div>

        <div class="flex items-center gap-2.5 flex-wrap">
            <button type="button"
                    @click="openQuickRecording()"
                    class="inline-flex items-center gap-2 bg-gradient-to-r from-emerald-600 to-teal-600 hover:from-emerald-700 hover:to-teal-700 text-white font-bold text-xs sm:text-sm px-4 py-2.5 rounded-xl shadow-md transition transform hover:-translate-y-0.5">
                <i class="fa-solid fa-circle-play text-white"></i>
                <span>+ ক্লাস রেকর্ডিং পোস্ট করুন</span>
            </button>

            <button type="button"
                    @click="showScheduleModal = true"
                    class="inline-flex items-center gap-2 bg-white hover:bg-slate-50 text-slate-800 font-bold text-xs sm:text-sm px-4 py-2.5 rounded-xl border border-slate-300 shadow-xs transition">
                <i class="fa-solid fa-calendar-plus text-emerald-600"></i>
                <span>+ নতুন মিটিং শিডিউল করুন</span>
            </button>

            <a href="{{ route('student.dashboard') }}" target="_blank"
               class="inline-flex items-center gap-2 bg-slate-900 hover:bg-slate-800 text-white font-semibold text-xs sm:text-sm px-4 py-2.5 rounded-xl shadow-xs transition">
                <i class="fa-solid fa-graduation-cap text-amber-400"></i>
                <span>স্টুডেন্ট পোর্টাল দেখুন</span>
            </a>
        </div>
    </div>

    <!-- Active / Current Meeting Hero Card -->
    @if($meetingUrl)
        <div class="bg-gradient-to-br from-slate-900 via-emerald-950 to-slate-900 rounded-3xl border border-emerald-500/30 p-6 sm:p-7 shadow-xl text-white relative overflow-hidden">
            <div class="absolute -right-16 -top-16 w-64 h-64 bg-emerald-500/10 rounded-full blur-3xl pointer-events-none"></div>

            <div class="flex flex-col lg:flex-row lg:items-center justify-between gap-4 pb-5 border-b border-emerald-800/60 relative z-10">
                <div class="flex items-start gap-4">
                    <div class="w-14 h-14 rounded-2xl bg-emerald-500/20 text-emerald-400 flex items-center justify-center text-2xl shrink-0 border border-emerald-500/40 shadow-inner">
                        <template x-if="(heroData.url || '').toLowerCase().includes('zoom')">
                            <i class="fa-solid fa-video text-blue-400"></i>
                        </template>
                        <template x-if="(heroData.url || '').toLowerCase().includes('meet.google')">
                            <i class="fa-solid fa-chalkboard-user text-emerald-400"></i>
                        </template>
                        <template x-if="!(heroData.url || '').toLowerCase().includes('zoom') && !(heroData.url || '').toLowerCase().includes('meet.google')">
                            <i class="fa-solid fa-video"></i>
                        </template>
                    </div>
                    <div>
                        <div class="flex items-center gap-2.5 flex-wrap">
                            <span class="text-xs font-bold uppercase tracking-wider text-emerald-300">
                                <span x-show="heroData.isLive">বর্তমান সক্রিয় লাইভ ক্লাস</span>
                                <span x-show="!heroData.isLive">পরবর্তী শিডিউলকৃত ক্লাস</span>
                            </span>
                            <span x-show="heroData.isLive"
                                  class="inline-flex items-center gap-1.5 px-3 py-1 rounded-full text-xs font-black bg-red-600 text-white animate-pulse shadow-md shadow-red-600/30">
                                <span class="w-2 h-2 rounded-full bg-white"></span>
                                এখন লাইভ চলছে (LIVE NOW)
                            </span>
                            <span x-show="!heroData.isLive"
                                  class="inline-flex items-center gap-1 px-2.5 py-0.5 rounded-full text-xs font-semibold bg-amber-500/20 text-amber-300 border border-amber-500/30">
                                <i class="fa-solid fa-clock text-[10px]"></i>
                                <span x-text="heroData.schedule"></span>
                            </span>
                        </div>
                        <h2 class="text-xl sm:text-2xl font-black text-white mt-1" x-text="heroData.topic">
                            {{ $meetingTopic }}
                        </h2>
                        <p class="text-xs text-emerald-200/80 mt-0.5">
                            কোর্স: <strong class="text-white">{{ $primaryCourse?->bangla_title ?? 'সাংবাদিকতা, আইন ও উপস্থাপনা' }}</strong> &bull; ব্যাচ: <strong class="text-amber-300" x-text="heroData.batchLabel">{{ $meetingBatchLabel }}</strong>
                        </p>
                    </div>
                </div>

                <!-- Action Buttons: Direct Join & Test -->
                <div class="flex items-center gap-2.5 shrink-0 flex-wrap">
                    <template x-if="heroData.activeMeetingId">
                        <button type="button"
                                @click="toggleLive(heroData.activeMeetingId)"
                                :class="heroData.isLive ? 'bg-slate-800 hover:bg-slate-700 text-rose-300 border border-rose-500/30' : 'bg-red-600 hover:bg-red-700 text-white shadow-lg shadow-red-600/30 animate-pulse'"
                                class="inline-flex items-center gap-2 px-4 py-2.5 rounded-xl font-bold text-xs sm:text-sm transition">
                            <span x-show="heroData.isLive" class="inline-flex items-center gap-1.5">
                                <i class="fa-solid fa-stop text-xs"></i>
                                <span>লাইভ বন্ধ করুন</span>
                            </span>
                            <span x-show="!heroData.isLive" class="inline-flex items-center gap-2">
                                <span class="w-2 h-2 rounded-full bg-white"></span>
                                <span>🔴 এখনই লাইভ শুরু করুন</span>
                            </span>
                        </button>
                    </template>

                    <a :href="heroData.url" href="{{ $meetingUrl }}" target="_blank"
                       class="inline-flex items-center gap-2 px-4 py-2.5 rounded-xl bg-gradient-to-r from-emerald-500 to-teal-500 hover:from-emerald-400 hover:to-teal-400 text-slate-950 font-black text-xs sm:text-sm shadow-lg shadow-emerald-500/20 transition">
                        <i class="fa-solid fa-arrow-up-right-from-square"></i>
                        <span>মিটিংয়ে যুক্ত হন</span>
                    </a>

                    <template x-if="heroData.activeMeetingId">
                        <button type="button"
                                @click="openRecordingById(heroData.activeMeetingId)"
                                class="inline-flex items-center gap-2 px-4 py-2.5 rounded-xl bg-slate-800 hover:bg-slate-700 text-emerald-300 hover:text-white font-bold text-xs sm:text-sm border border-emerald-500/40 shadow-xs transition">
                            <i class="fa-solid fa-circle-play text-emerald-400"></i>
                            <span>রেকর্ডিং পোস্ট/পরিবর্তন</span>
                        </button>
                    </template>
                </div>
            </div>

            <!-- Credentials & Access Grid -->
            <div class="grid grid-cols-1 sm:grid-cols-2 lg:grid-cols-4 gap-3.5 mt-5 relative z-10 text-xs">
                <div class="p-3.5 bg-slate-800/80 rounded-2xl border border-emerald-900/60 lg:col-span-2">
                    <span class="text-[11px] text-emerald-300 block mb-1 font-semibold">লাইভ মিটিং URL (রিডাইরেক্ট লিঙ্ক)</span>
                    <div class="font-mono text-emerald-100 text-xs break-all select-all bg-slate-900/90 p-2 rounded-xl border border-slate-700" x-text="heroData.url">
                        {{ $meetingUrl }}
                    </div>
                </div>

                <div class="p-3.5 bg-slate-800/80 rounded-2xl border border-emerald-900/60">
                    <span class="text-[11px] text-emerald-300 block mb-1 font-semibold">Meeting ID (মিটিং আইডি)</span>
                    <div class="font-mono text-white text-sm font-bold bg-slate-900/90 p-2 rounded-xl border border-slate-700 flex items-center justify-between">
                        <span x-text="heroData.meetingId || 'সরাসরি লিংকে যুক্ত'">{{ $meetingId ?: 'সরাসরি লিংকে যুক্ত' }}</span>
                        <template x-if="heroData.meetingId">
                            <button type="button" @click="copyMeetingLink(heroData.meetingId, $el)" class="text-slate-400 hover:text-white">
                                <i class="fa-regular fa-copy"></i>
                            </button>
                        </template>
                    </div>
                </div>

                <div class="p-3.5 bg-slate-800/80 rounded-2xl border border-emerald-900/60">
                    <span class="text-[11px] text-emerald-300 block mb-1 font-semibold">Passcode (পাসকোড)</span>
                    <div class="font-mono text-white text-sm font-bold bg-slate-900/90 p-2 rounded-xl border border-slate-700 flex items-center justify-between">
                        <span x-text="heroData.passcode || 'পাসকোড প্রয়োজন নেই'">{{ $meetingPasscode ?: 'পাসকোড প্রয়োজন নেই' }}</span>
                        <template x-if="heroData.passcode">
                            <button type="button" @click="copyMeetingLink(heroData.passcode, $el)" class="text-slate-400 hover:text-white">
                                <i class="fa-regular fa-copy"></i>
                            </button>
                        </template>
                    </div>
                </div>
            </div>
        </div>
    @endif

    <!-- Scheduled Meetings List Table (Space Smart & Compact Layout) -->
    <div class="bg-white rounded-3xl border border-slate-200 shadow-xs overflow-hidden">
        <div class="p-4 sm:p-5 border-b border-slate-100 flex flex-col sm:flex-row sm:items-center justify-between gap-3">
            <div>
                <h2 class="text-base sm:text-lg font-bold text-slate-900 flex items-center gap-2">
                    <i class="fa-solid fa-calendar-days text-emerald-600"></i>
                    <span>তারিখ ও সময়ভিত্তিক ক্লাস শিডিউল তালিকা</span>
                </h2>
                <p class="text-xs text-slate-500 mt-0.5">
                    প্রতিটি ক্লাসের তারিখ, সময়, রেকর্ডিং ও লাইভ স্ট্যাটাস পরিচালনা করুন (পেজ রিলোড ছাড়াই তাৎক্ষণিক পরিবর্তন)
                </p>
            </div>
            <div class="flex items-center gap-2 flex-wrap">
                @if(isset($batches) && $batches->count() > 0)
                    <form method="GET" action="{{ route('admin.live-meetings') }}" class="flex items-center gap-1.5">
                        <select name="batch_id" onchange="this.form.submit()"
                                class="px-3 py-1.5 text-xs bg-slate-50 border border-slate-300 rounded-xl focus:ring-2 focus:ring-emerald-500 outline-none font-semibold text-slate-700">
                            <option value="">সকল ব্যাচ ({{ count($meetings) }})</option>
                            @foreach($batches as $b)
                                <option value="{{ $b->id }}" {{ ($selectedBatchId ?? null) == $b->id ? 'selected' : '' }}>
                                    {{ $b->name }}
                                </option>
                            @endforeach
                        </select>
                    </form>
                @endif
                <button type="button"
                        @click="showScheduleModal = true"
                        class="inline-flex items-center gap-1.5 px-3 py-1.5 rounded-xl bg-emerald-50 hover:bg-emerald-100 text-emerald-800 font-bold text-xs border border-emerald-200 transition">
                    <i class="fa-solid fa-plus text-xs"></i>
                    <span>নতুন ক্লাস যোগ করুন</span>
                </button>
            </div>
        </div>

        <div class="overflow-x-auto">
            <table class="w-full text-left border-collapse text-xs sm:text-sm">
                <thead>
                    <tr class="bg-slate-50/80 border-b border-slate-200 text-slate-600 font-bold uppercase tracking-wider text-[11px]">
                        <th class="py-2.5 px-3 sm:px-4">তারিখ ও সময়</th>
                        <th class="py-2.5 px-3 sm:px-4">ক্লাসের বিষয় ও ব্যাচ</th>
                        <th class="py-2.5 px-3 sm:px-4">প্ল্যাটফর্ম</th>
                        <th class="py-2.5 px-3 sm:px-4">রেকর্ডিং</th>
                        <th class="py-2.5 px-3 sm:px-4 text-center">স্ট্যাটাস</th>
                        <th class="py-2.5 px-3 sm:px-4 text-right">লাইভ ও অ্যাকশন</th>
                    </tr>
                </thead>
                <tbody id="meetingsTableBody" class="divide-y divide-slate-100">
                    @forelse($meetings as $meeting)
                        @php
                            $mStarts = $meeting->starts_at ? \Carbon\Carbon::parse($meeting->starts_at) : null;
                        @endphp
                        <tr id="meeting-row-{{ $meeting->id }}"
                            class="hover:bg-slate-50/70 transition {{ $meeting->is_live_now ? 'bg-red-50/20' : '' }}"
                            :class="getMeeting({{ $meeting->id }})?.is_live_now ? 'bg-red-50/25' : ''">

                            <!-- Date & Time (Smart combined column) -->
                            <td class="py-2.5 px-3 sm:px-4 whitespace-nowrap">
                                <div class="flex items-center gap-2">
                                    <div class="w-8 h-8 rounded-lg bg-emerald-50 text-emerald-700 flex flex-col items-center justify-center font-bold shrink-0 border border-emerald-100">
                                        <span class="text-[9px] uppercase leading-none font-semibold">{{ $mStarts ? $mStarts->format('M') : 'N/A' }}</span>
                                        <span class="text-xs leading-none font-black mt-0.5">{{ $mStarts ? $mStarts->format('d') : '--' }}</span>
                                    </div>
                                    <div>
                                        <div class="font-bold text-slate-900 text-xs flex items-center gap-1.5">
                                            <span>{{ $mStarts ? $mStarts->format('d M, Y') : 'N/A' }}</span>
                                            @if($mStarts)
                                                <span class="text-[10px] font-normal text-slate-500">({{ $mStarts->isoFormat('dddd') }})</span>
                                            @endif
                                        </div>
                                        <div class="text-[11px] text-slate-500 flex items-center gap-1 mt-0.5">
                                            <i class="fa-regular fa-clock text-emerald-600 text-[10px]"></i>
                                            <span class="font-semibold text-slate-700">{{ $mStarts ? $mStarts->format('h:i A') : 'N/A' }}</span>
                                            @if($meeting->schedule_text)
                                                <span class="text-[10px] text-amber-700 bg-amber-50 px-1 rounded border border-amber-200/60 font-medium ml-1">{{ $meeting->schedule_text }}</span>
                                            @endif
                                        </div>
                                    </div>
                                </div>
                            </td>

                            <!-- Topic & Batch -->
                            <td class="py-2.5 px-3 sm:px-4">
                                <div class="font-bold text-slate-900 text-xs sm:text-sm line-clamp-1">
                                    {{ $meeting->title }}
                                </div>
                                <div class="flex items-center gap-1.5 flex-wrap mt-0.5">
                                    <span class="inline-flex items-center px-1.5 py-0.2 rounded text-[10px] font-bold {{ $meeting->batch ? 'bg-emerald-50 text-emerald-800 border border-emerald-200' : 'bg-slate-100 text-slate-600 border border-slate-200' }}">
                                        {{ $meeting->batch?->name ?? 'সকল ব্যাচ' }}
                                    </span>
                                    @if($meeting->meeting_id || $meeting->meeting_passcode)
                                        <span class="text-[10px] text-slate-400 font-mono inline-flex items-center gap-1">
                                            @if($meeting->meeting_id) ID: {{ $meeting->meeting_id }} @endif
                                            @if($meeting->meeting_passcode) • PW: {{ $meeting->meeting_passcode }} @endif
                                        </span>
                                    @endif
                                </div>
                            </td>

                            <!-- Platform & Link -->
                            <td class="py-2.5 px-3 sm:px-4 whitespace-nowrap">
                                <a href="{{ $meeting->meeting_url }}" target="_blank"
                                   class="inline-flex items-center gap-1.5 px-2.5 py-1 rounded-lg text-xs font-semibold transition border {{ str_contains(strtolower($meeting->meeting_url ?? ''), 'zoom') ? 'bg-blue-50 text-blue-700 border-blue-200 hover:bg-blue-100' : (str_contains(strtolower($meeting->meeting_url ?? ''), 'meet.google') ? 'bg-emerald-50 text-emerald-700 border-emerald-200 hover:bg-emerald-100' : 'bg-slate-50 text-slate-700 border-slate-200 hover:bg-slate-100') }}">
                                    @if(str_contains(strtolower($meeting->meeting_url ?? ''), 'zoom'))
                                        <i class="fa-solid fa-video text-blue-500 text-[11px]"></i>
                                        <span>Zoom</span>
                                    @elseif(str_contains(strtolower($meeting->meeting_url ?? ''), 'meet.google'))
                                        <i class="fa-solid fa-chalkboard-user text-emerald-600 text-[11px]"></i>
                                        <span>Meet</span>
                                    @else
                                        <i class="fa-solid fa-link text-slate-500 text-[11px]"></i>
                                        <span>লিংক</span>
                                    @endif
                                    <i class="fa-solid fa-arrow-up-right-from-square text-[9px] opacity-70"></i>
                                </a>
                            </td>

                            <!-- Recording -->
                            <td class="py-2.5 px-3 sm:px-4 whitespace-nowrap" id="recording-cell-{{ $meeting->id }}">
                                @if($meeting->hasRecording())
                                    <div class="flex items-center gap-1.5">
                                        <span class="inline-flex items-center gap-1 px-2 py-0.5 rounded-full text-[10px] font-bold bg-emerald-100 text-emerald-800 border border-emerald-200">
                                            <i class="fa-solid fa-circle-play text-emerald-600 text-[9px]"></i>
                                            <span>রেকর্ড সক্রিয়</span>
                                        </span>
                                        <a href="{{ route('student.recordings.watch', $meeting->id) }}" target="_blank"
                                           class="p-1 text-emerald-700 hover:text-emerald-900 transition"
                                           title="সুরক্ষিত প্লেয়ারে প্রিভিউ দেখুন">
                                            <i class="fa-solid fa-arrow-up-right-from-square text-[11px]"></i>
                                        </a>
                                        <button type="button"
                                                @click="openRecordingById({{ $meeting->id }})"
                                                class="p-1 text-slate-400 hover:text-emerald-700 transition"
                                                title="রেকর্ডিং পরিবর্তন করুন">
                                            <i class="fa-solid fa-pen text-[11px]"></i>
                                        </button>
                                    </div>
                                    @if($meeting->recording_duration)
                                        <div class="text-[9px] text-slate-400 font-mono mt-0.5">
                                            {{ $meeting->recording_duration }}
                                        </div>
                                    @endif
                                @else
                                    <button type="button"
                                            @click="openRecordingById({{ $meeting->id }})"
                                            class="inline-flex items-center gap-1 px-2.5 py-1 rounded-lg text-xs font-semibold text-emerald-700 bg-emerald-50 hover:bg-emerald-100 border border-emerald-200 transition"
                                            title="ভিডিও রেকর্ডিং পোস্ট করুন">
                                        <i class="fa-solid fa-video text-[10px] text-emerald-600"></i>
                                        <span>+ রেকর্ডিং</span>
                                    </button>
                                @endif
                            </td>

                            <!-- Status -->
                            <td class="py-2.5 px-3 sm:px-4 text-center whitespace-nowrap">
                                <span x-show="getMeeting({{ $meeting->id }})?.is_live_now" style="{{ $meeting->is_live_now ? '' : 'display:none;' }}"
                                      class="inline-flex items-center gap-1.5 px-2.5 py-0.5 rounded-full text-[10px] font-black bg-red-100 text-red-700 border border-red-200 animate-pulse">
                                    <span class="w-1.5 h-1.5 rounded-full bg-red-600"></span>
                                    LIVE NOW
                                </span>
                                <span x-show="!getMeeting({{ $meeting->id }})?.is_live_now && {{ $meeting->starts_at && $meeting->starts_at->isPast() ? 'true' : 'false' }}"
                                      style="{{ !$meeting->is_live_now && $meeting->starts_at && $meeting->starts_at->isPast() ? '' : 'display:none;' }}"
                                      class="inline-flex items-center px-2 py-0.5 rounded-full text-[10px] font-semibold bg-slate-100 text-slate-600">
                                    সম্পন্ন
                                </span>
                                <span x-show="!getMeeting({{ $meeting->id }})?.is_live_now && {{ $meeting->starts_at && !$meeting->starts_at->isPast() ? 'true' : 'false' }}"
                                      style="{{ !$meeting->is_live_now && $meeting->starts_at && !$meeting->starts_at->isPast() ? '' : 'display:none;' }}"
                                      class="inline-flex items-center px-2 py-0.5 rounded-full text-[10px] font-semibold bg-blue-50 text-blue-700 border border-blue-200">
                                    শিডিউল করা
                                </span>
                            </td>

                            <!-- Actions -->
                            <td class="py-2.5 px-3 sm:px-4 text-right whitespace-nowrap">
                                <div class="flex items-center justify-end gap-1.5">
                                    <button type="button"
                                            @click="toggleLive({{ $meeting->id }})"
                                            :class="getMeeting({{ $meeting->id }})?.is_live_now ? 'bg-rose-50 hover:bg-rose-100 text-rose-700 border-rose-200' : 'bg-red-600 hover:bg-red-700 text-white shadow-xs'"
                                            class="inline-flex items-center gap-1 px-2.5 py-1 rounded-lg text-xs font-bold border transition {{ $meeting->is_live_now ? 'bg-rose-50 hover:bg-rose-100 text-rose-700 border-rose-200' : 'bg-red-600 hover:bg-red-700 text-white shadow-xs' }}">
                                        <span x-show="getMeeting({{ $meeting->id }})?.is_live_now" style="{{ $meeting->is_live_now ? '' : 'display:none;' }}" class="inline-flex items-center gap-1">
                                            <i class="fa-solid fa-stop text-[10px]"></i>
                                            <span>লাইভ বন্ধ</span>
                                        </span>
                                        <span x-show="!getMeeting({{ $meeting->id }})?.is_live_now" style="{{ $meeting->is_live_now ? 'display:none;' : '' }}" class="inline-flex items-center gap-1">
                                            <span class="w-1.5 h-1.5 rounded-full bg-white animate-ping"></span>
                                            <span>🔴 লাইভ শুরু</span>
                                        </span>
                                    </button>

                                    <button type="button"
                                            @click="openEditById({{ $meeting->id }})"
                                            class="p-1.5 text-slate-400 hover:text-emerald-700 hover:bg-emerald-50 rounded-lg transition"
                                            title="সম্পাদনা করুন">
                                        <i class="fa-regular fa-pen-to-square text-xs"></i>
                                    </button>

                                    <button type="button"
                                            @click="deleteMeeting({{ $meeting->id }}, '{{ addslashes($meeting->title) }}')"
                                            class="p-1.5 text-slate-400 hover:text-red-600 hover:bg-red-50 rounded-lg transition"
                                            title="মুছে ফেলুন">
                                        <i class="fa-regular fa-trash-can text-xs"></i>
                                    </button>
                                </div>
                            </td>
                        </tr>
                    @empty
                        <tr id="emptyMeetingsRow">
                            <td colspan="6" class="p-8 text-center text-slate-500 text-xs">
                                এখনো নির্দিষ্ট তারিখ-সময়ভিত্তিক কোনো মিটিং শিডিউল করা হয়নি। উপরে <strong>"+ নতুন মিটিং শিডিউল করুন"</strong> বাটনে ক্লিক করে তারিখ ও সময় দিয়ে ক্লাস যুক্ত করুন।
                            </td>
                        </tr>
                    @endforelse
                </tbody>
            </table>
        </div>
    </div>

    <!-- Published Class Recordings Archive -->
    @php
        $recordedMeetings = collect($meetings)->filter(fn($m) => $m->hasRecording());
    @endphp
    @if(count($recordedMeetings) > 0)
        <div class="bg-white rounded-3xl border border-slate-200 shadow-xs p-6 space-y-4">
            <div class="flex flex-col sm:flex-row sm:items-center justify-between gap-2 pb-3 border-b border-slate-100">
                <div class="flex items-center gap-2.5">
                    <div class="w-9 h-9 rounded-2xl bg-emerald-50 text-emerald-600 flex items-center justify-center text-base font-bold shadow-xs">
                        <i class="fa-solid fa-film"></i>
                    </div>
                    <div>
                        <h3 class="font-bold text-base text-slate-900">
                            প্রকাশিত ক্লাস রেকর্ডিং আর্কাইভ ({{ count($recordedMeetings) }}টি ক্লাস)
                        </h3>
                        <p class="text-xs text-slate-500">
                            শিক্ষার্থীরা এই ক্লাসগুলোর রেকর্ডিং সুরক্ষিত আনব্র্যান্ডেড প্লেয়ারে দেখতে পাচ্ছে
                        </p>
                    </div>
                </div>
            </div>

            <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-3 gap-4" id="recordingsArchiveGrid">
                @foreach($recordedMeetings as $rec)
                    <div id="recording-card-{{ $rec->id }}" class="p-4 bg-slate-50 rounded-2xl border border-slate-200 hover:border-emerald-300 transition flex flex-col justify-between space-y-3">
                        <div>
                            <div class="flex items-center justify-between text-[11px] text-slate-500 mb-1">
                                <span>{{ $rec->starts_at ? $rec->starts_at->format('d M, Y') : 'ক্লাস' }}</span>
                                @if($rec->recording_duration)
                                    <span class="font-mono text-emerald-700 bg-emerald-100 px-1.5 py-0.5 rounded font-semibold">{{ $rec->recording_duration }}</span>
                                @endif
                            </div>
                            <h4 class="font-bold text-sm text-slate-900 line-clamp-1">
                                {{ $rec->title }}
                            </h4>
                        </div>

                        <div class="flex items-center justify-between pt-2 border-t border-slate-200/80">
                            <a href="{{ route('student.recordings.watch', $rec->id) }}" target="_blank"
                               class="inline-flex items-center gap-1.5 text-xs font-bold text-emerald-700 hover:text-emerald-900">
                                <i class="fa-solid fa-circle-play text-emerald-600"></i>
                                <span>সুরক্ষিত প্লেয়ার প্রিভিউ</span>
                                <i class="fa-solid fa-arrow-up-right-from-square text-[10px]"></i>
                            </a>
                            <button type="button"
                                    @click="openRecordingById({{ $rec->id }})"
                                    class="p-1 text-slate-400 hover:text-emerald-700 transition"
                                    title="রেকর্ডিং সম্পাদনা">
                                <i class="fa-regular fa-pen-to-square text-xs"></i>
                            </button>
                        </div>
                    </div>
                @endforeach
            </div>
        </div>
    @endif

    <!-- Create Scheduled Meeting Modal (AJAX - Zero Reload) -->
    <div x-show="showScheduleModal"
         x-cloak
         class="fixed inset-0 z-50 overflow-y-auto bg-slate-900/60 backdrop-blur-xs flex items-center justify-center p-4">
        <div class="bg-white rounded-3xl max-w-xl w-full p-6 shadow-2xl border border-slate-200 relative"
             @click.away="showScheduleModal = false">
            <div class="flex items-center justify-between pb-4 border-b border-slate-100 mb-4">
                <h3 class="font-bold text-lg text-slate-900 flex items-center gap-2">
                    <i class="fa-solid fa-calendar-plus text-emerald-600"></i>
                    <span>তারিখ ও সময়ভিত্তিক নতুন মিটিং শিডিউল করুন</span>
                </h3>
                <button type="button" @click="showScheduleModal = false" class="text-slate-400 hover:text-slate-600 p-1">
                    <i class="fa-solid fa-xmark text-lg"></i>
                </button>
            </div>

            <form @submit.prevent="saveCreate()" class="space-y-4">
                @if(isset($batches) && $batches->count() > 0)
                    <div>
                        <label class="block text-xs font-bold text-slate-700 mb-1.5">
                            কোন ব্যাচের ক্লাস?
                        </label>
                        <select x-model="createForm.batch_id"
                                class="w-full px-3.5 py-2.5 text-xs sm:text-sm bg-slate-50 border border-slate-300 rounded-xl focus:ring-2 focus:ring-emerald-500 outline-none font-semibold text-slate-800">
                            <option value="">সকল ব্যাচ (কমন মিটিং)</option>
                            @foreach($batches as $b)
                                <option value="{{ $b->id }}">
                                    {{ $b->name }} ({{ $b->enrollment_open ? 'ভর্তি চালু' : 'ভর্তি বন্ধ' }})
                                </option>
                            @endforeach
                        </select>
                    </div>
                @endif

                <div>
                    <label class="block text-xs font-bold text-slate-700 mb-1.5">
                        ক্লাসের বিষয় / শিরোনাম <span class="text-rose-500">*</span>
                    </label>
                    <input type="text" x-model="createForm.title" required
                           placeholder="যেমন: ক্লাস ১: সাংবাদিকতার পরিচিতি ও মৌলিক ধারণা"
                           class="w-full px-3.5 py-2.5 text-xs sm:text-sm bg-slate-50 border border-slate-300 rounded-xl focus:ring-2 focus:ring-emerald-500 outline-none">
                </div>

                <div class="grid grid-cols-1 sm:grid-cols-2 gap-3">
                    <div>
                        <label class="block text-xs font-bold text-slate-700 mb-1.5">
                            ক্লাসের তারিখ <span class="text-rose-500">*</span>
                        </label>
                        <input type="date" x-model="createForm.scheduled_date" required
                               class="w-full px-3.5 py-2.5 text-xs sm:text-sm bg-slate-50 border border-slate-300 rounded-xl focus:ring-2 focus:ring-emerald-500 outline-none">
                    </div>

                    <div>
                        <label class="block text-xs font-bold text-slate-700 mb-1.5">
                            ক্লাস শুরুর সময় <span class="text-rose-500">*</span>
                        </label>
                        <input type="time" x-model="createForm.scheduled_time" required
                               class="w-full px-3.5 py-2.5 text-xs sm:text-sm bg-slate-50 border border-slate-300 rounded-xl focus:ring-2 focus:ring-emerald-500 outline-none font-mono">
                    </div>
                </div>

                <div>
                    <label class="block text-xs font-bold text-slate-700 mb-1.5">
                        মিটিং URL (Zoom বা Google Meet লিংক) <span class="text-rose-500">*</span>
                    </label>
                    <input type="text" x-model="createForm.meeting_url" required
                           placeholder="https://us05web.zoom.us/j/... অথবা https://meet.google.com/..."
                           class="w-full px-3.5 py-2.5 text-xs sm:text-sm bg-slate-50 border border-slate-300 rounded-xl focus:ring-2 focus:ring-emerald-500 outline-none font-mono">
                </div>

                <div class="grid grid-cols-1 sm:grid-cols-2 gap-3">
                    <div>
                        <label class="block text-xs font-bold text-slate-700 mb-1.5">মিটিং আইডি (ঐচ্ছিক)</label>
                        <input type="text" x-model="createForm.meeting_id"
                               placeholder="যেমন: 852 1234 5678"
                               class="w-full px-3.5 py-2 text-xs sm:text-sm bg-slate-50 border border-slate-300 rounded-xl focus:ring-2 focus:ring-emerald-500 outline-none font-mono">
                    </div>

                    <div>
                        <label class="block text-xs font-bold text-slate-700 mb-1.5">পাসকোড (ঐচ্ছিক)</label>
                        <input type="text" x-model="createForm.meeting_passcode"
                               placeholder="যেমন: 123456"
                               class="w-full px-3.5 py-2 text-xs sm:text-sm bg-slate-50 border border-slate-300 rounded-xl focus:ring-2 focus:ring-emerald-500 outline-none font-mono">
                    </div>
                </div>

                <div>
                    <label class="block text-xs font-bold text-slate-700 mb-1.5">
                        কাস্টম শিডিউল টেক্সট <span class="text-slate-400 font-normal">(ঐচ্ছিক, শিক্ষার্থীদের বাটনের নিচে দেখাবে)</span>
                    </label>
                    <input type="text" x-model="createForm.schedule_text"
                           placeholder="যেমন: আজ রাত ৯:০০ টা - ১১:০০ টা"
                           class="w-full px-3.5 py-2 text-xs sm:text-sm bg-slate-50 border border-slate-300 rounded-xl focus:ring-2 focus:ring-emerald-500 outline-none">
                </div>

                <div class="p-3 bg-red-50/70 border border-red-200 rounded-xl flex items-center gap-2">
                    <input type="checkbox" id="modal_is_live_now" x-model="createForm.is_live_now"
                           class="w-4 h-4 text-red-600 rounded border-slate-300 focus:ring-red-500 cursor-pointer">
                    <label for="modal_is_live_now" class="text-xs font-bold text-red-900 cursor-pointer">
                        🔴 এখনই লাইভ মিটিং শুরু করুন (শিক্ষার্থীদের জয়েন বাটন সক্রিয় হয়ে যাবে)
                    </label>
                </div>

                <div class="flex items-center justify-end gap-3 pt-3 border-t border-slate-100">
                    <button type="button" @click="showScheduleModal = false"
                            class="px-4 py-2 text-xs font-semibold text-slate-600 hover:text-slate-800 bg-slate-100 hover:bg-slate-200 rounded-xl transition">
                        বাতিল
                    </button>
                    <button type="submit"
                            :disabled="createForm.loading"
                            class="inline-flex items-center gap-1.5 px-5 py-2 text-xs font-bold text-white bg-emerald-600 hover:bg-emerald-700 rounded-xl shadow-xs transition disabled:opacity-50">
                        <i class="fa-solid fa-spinner fa-spin" x-show="createForm.loading" style="display: none;"></i>
                        <span>শিডিউল মিটিং সংরক্ষণ করুন</span>
                    </button>
                </div>
            </form>
        </div>
    </div>

    <!-- Edit Scheduled Meeting Modal (AJAX - Zero Reload) -->
    <div x-show="showEditModal"
         x-cloak
         class="fixed inset-0 z-50 overflow-y-auto bg-slate-900/60 backdrop-blur-xs flex items-center justify-center p-4">
        <div class="bg-white rounded-3xl max-w-xl w-full p-6 shadow-2xl border border-slate-200 relative"
             @click.away="showEditModal = false">
            <div class="flex items-center justify-between pb-4 border-b border-slate-100 mb-4">
                <h3 class="font-bold text-lg text-slate-900 flex items-center gap-2">
                    <i class="fa-solid fa-pen-to-square text-emerald-600"></i>
                    <span>শিডিউল মিটিং সম্পাদনা করুন</span>
                </h3>
                <button type="button" @click="showEditModal = false" class="text-slate-400 hover:text-slate-600 p-1">
                    <i class="fa-solid fa-xmark text-lg"></i>
                </button>
            </div>

            <form @submit.prevent="saveEdit()" class="space-y-4">
                @if(isset($batches) && $batches->count() > 0)
                    <div>
                        <label class="block text-xs font-bold text-slate-700 mb-1.5">
                            কোন ব্যাচের ক্লাস?
                        </label>
                        <select x-model="editForm.data.batch_id"
                                class="w-full px-3.5 py-2.5 text-xs sm:text-sm bg-slate-50 border border-slate-300 rounded-xl focus:ring-2 focus:ring-emerald-500 outline-none font-semibold text-slate-800">
                            <option value="">সকল ব্যাচ (কমন মিটিং)</option>
                            @foreach($batches as $b)
                                <option value="{{ $b->id }}">{{ $b->name }}</option>
                            @endforeach
                        </select>
                    </div>
                @endif

                <div>
                    <label class="block text-xs font-bold text-slate-700 mb-1.5">
                        ক্লাসের বিষয় / শিরোনাম <span class="text-rose-500">*</span>
                    </label>
                    <input type="text" required x-model="editForm.data.title"
                           placeholder="যেমন: ক্লাস ১: সাংবাদিকতার পরিচিতি ও মৌলিক ধারণা"
                           class="w-full px-3.5 py-2.5 text-xs sm:text-sm bg-slate-50 border border-slate-300 rounded-xl focus:ring-2 focus:ring-emerald-500 outline-none">
                </div>

                <div class="grid grid-cols-1 sm:grid-cols-2 gap-3">
                    <div>
                        <label class="block text-xs font-bold text-slate-700 mb-1.5">
                            ক্লাসের তারিখ <span class="text-rose-500">*</span>
                        </label>
                        <input type="date" required x-model="editForm.data.scheduled_date"
                               class="w-full px-3.5 py-2.5 text-xs sm:text-sm bg-slate-50 border border-slate-300 rounded-xl focus:ring-2 focus:ring-emerald-500 outline-none">
                    </div>

                    <div>
                        <label class="block text-xs font-bold text-slate-700 mb-1.5">
                            ক্লাস শুরুর সময় <span class="text-rose-500">*</span>
                        </label>
                        <input type="time" required x-model="editForm.data.scheduled_time"
                               class="w-full px-3.5 py-2.5 text-xs sm:text-sm bg-slate-50 border border-slate-300 rounded-xl focus:ring-2 focus:ring-emerald-500 outline-none font-mono">
                    </div>
                </div>

                <div>
                    <label class="block text-xs font-bold text-slate-700 mb-1.5">
                        মিটিং URL (Zoom বা Google Meet লিংক) <span class="text-rose-500">*</span>
                    </label>
                    <input type="text" required x-model="editForm.data.meeting_url"
                           placeholder="https://us05web.zoom.us/j/... অথবা https://meet.google.com/..."
                           class="w-full px-3.5 py-2.5 text-xs sm:text-sm bg-slate-50 border border-slate-300 rounded-xl focus:ring-2 focus:ring-emerald-500 outline-none font-mono">
                </div>

                <div class="grid grid-cols-1 sm:grid-cols-2 gap-3">
                    <div>
                        <label class="block text-xs font-bold text-slate-700 mb-1.5">মিটিং আইডি (ঐচ্ছিক)</label>
                        <input type="text" x-model="editForm.data.meeting_id"
                               placeholder="যেমন: 852 1234 5678"
                               class="w-full px-3.5 py-2 text-xs sm:text-sm bg-slate-50 border border-slate-300 rounded-xl focus:ring-2 focus:ring-emerald-500 outline-none font-mono">
                    </div>

                    <div>
                        <label class="block text-xs font-bold text-slate-700 mb-1.5">পাসকোড (ঐচ্ছিক)</label>
                        <input type="text" x-model="editForm.data.meeting_passcode"
                               placeholder="যেমন: 123456"
                               class="w-full px-3.5 py-2 text-xs sm:text-sm bg-slate-50 border border-slate-300 rounded-xl focus:ring-2 focus:ring-emerald-500 outline-none font-mono">
                    </div>
                </div>

                <div>
                    <label class="block text-xs font-bold text-slate-700 mb-1.5">
                        কাস্টম শিডিউল টেক্সট <span class="text-slate-400 font-normal">(ঐচ্ছিক, শিক্ষার্থীদের বাটনের নিচে দেখাবে)</span>
                    </label>
                    <input type="text" x-model="editForm.data.schedule_text"
                           placeholder="যেমন: আজ রাত ৯:০০ টা - ১১:০০ টা"
                           class="w-full px-3.5 py-2 text-xs sm:text-sm bg-slate-50 border border-slate-300 rounded-xl focus:ring-2 focus:ring-emerald-500 outline-none">
                </div>

                <div class="p-3 bg-red-50/70 border border-red-200 rounded-xl flex items-center gap-2">
                    <input type="checkbox" id="edit_modal_is_live_now" x-model="editForm.data.is_live_now"
                           class="w-4 h-4 text-red-600 rounded border-slate-300 focus:ring-red-500 cursor-pointer">
                    <label for="edit_modal_is_live_now" class="text-xs font-bold text-red-900 cursor-pointer">
                        🔴 এই মিটিংটি সরাসরি লাইভ হিসেবে সক্রিয় করুন
                    </label>
                </div>

                <div class="flex items-center justify-end gap-3 pt-3 border-t border-slate-100">
                    <button type="button" @click="showEditModal = false"
                            class="px-4 py-2 text-xs font-semibold text-slate-600 hover:text-slate-800 bg-slate-100 hover:bg-slate-200 rounded-xl transition">
                        বাতিল
                    </button>
                    <button type="submit"
                            :disabled="editForm.loading"
                            class="inline-flex items-center gap-1.5 px-5 py-2 text-xs font-bold text-white bg-emerald-600 hover:bg-emerald-700 rounded-xl shadow-xs transition disabled:opacity-50">
                        <i class="fa-solid fa-spinner fa-spin" x-show="editForm.loading" style="display: none;"></i>
                        <span>আপডেট সংরক্ষণ করুন</span>
                    </button>
                </div>
            </form>
        </div>
    </div>

    <!-- Share Class Recording Modal (AJAX - Zero Reload) -->
    <div x-show="showRecordingModal"
         x-cloak
         class="fixed inset-0 z-50 overflow-y-auto bg-slate-900/60 backdrop-blur-xs flex items-center justify-center p-4">
        <div class="bg-white rounded-3xl max-w-lg w-full p-6 shadow-2xl border border-slate-200 relative"
             @click.away="showRecordingModal = false">
            <div class="flex items-center justify-between pb-4 border-b border-slate-100 mb-4">
                <div class="flex items-center gap-2.5">
                    <div class="w-10 h-10 rounded-2xl bg-emerald-50 text-emerald-600 flex items-center justify-center text-lg font-bold">
                        <i class="fa-solid fa-circle-play"></i>
                    </div>
                    <div>
                        <h3 class="font-bold text-base sm:text-lg text-slate-900">
                            ক্লাস রেকর্ডিং শেয়ার ও প্রকাশ করুন
                        </h3>
                        <p class="text-xs text-slate-500 line-clamp-1" x-text="recordingForm.title"></p>
                    </div>
                </div>
                <button type="button" @click="showRecordingModal = false" class="text-slate-400 hover:text-slate-600 p-1">
                    <i class="fa-solid fa-xmark text-lg"></i>
                </button>
            </div>

            <form @submit.prevent="saveRecording()" class="space-y-4">
                <div class="p-3.5 bg-emerald-50/70 border border-emerald-200 rounded-2xl text-xs text-emerald-900 space-y-1">
                    <div class="font-bold flex items-center gap-1.5 text-emerald-800">
                        <i class="fa-solid fa-shield-halved text-emerald-600"></i>
                        <span>সুরক্ষিত আনব্র্যান্ডেড প্লেয়ার সুবিধা:</span>
                    </div>
                    <p class="text-[11px] text-emerald-700 leading-relaxed">
                        ইউটিউবের আনলিস্টেড (Unlisted) বা পাবলিক যেকোনো ভিডিও লিংক দিন। শিক্ষার্থী পোর্টালে ভিডিও ফ্রেমের ভেতরে <strong>কোনো ইউটিউব লোগো, বাটন বা লিংক দেখানো হবে না</strong>। এটি সম্পূর্ণ নিজস্ব ক্লাউড স্টোরেজ ভিডিওর মতো প্রিমিয়াম লুকে চলবে।
                    </p>
                </div>

                <template x-if="meetings.length > 0">
                    <div>
                        <label class="block text-xs font-bold text-slate-700 mb-1.5">
                            কোন ক্লাসের রেকর্ডিং পোস্ট করছেন? <span class="text-rose-500">*</span>
                        </label>
                        <select class="w-full px-3.5 py-2.5 text-xs sm:text-sm bg-slate-50 border border-slate-300 rounded-xl focus:ring-2 focus:ring-emerald-500 outline-none font-semibold text-slate-800"
                                x-model="recordingForm.id"
                                @change="onSelectRecordingMeeting($event.target.value)">
                            <template x-for="m in meetings" :key="m.id">
                                <option :value="m.id" :selected="m.id === recordingForm.id" x-text="`${m.title} (${m.date_formatted})`"></option>
                            </template>
                        </select>
                    </div>
                </template>

                <div>
                    <label class="block text-xs font-bold text-slate-700 mb-1.5">
                        ভিডিও রেকর্ডিং লিংক (YouTube লিংক বা ভিডিও URL) <span class="text-rose-500">*</span>
                    </label>
                    <input type="text" required x-model="recordingForm.recording_url"
                           placeholder="যেমন: https://www.youtube.com/watch?v=... অথবা https://youtu.be/..."
                           class="w-full px-3.5 py-2.5 text-xs sm:text-sm bg-slate-50 border border-slate-300 rounded-xl focus:ring-2 focus:ring-emerald-500 outline-none font-mono">
                </div>

                <div class="grid grid-cols-1 sm:grid-cols-2 gap-3">
                    <div>
                        <label class="block text-xs font-bold text-slate-700 mb-1.5">
                            স্থিতিকাল / সময় (ঐচ্ছিক)
                        </label>
                        <input type="text" x-model="recordingForm.recording_duration"
                               placeholder="যেমন: ১ ঘণ্টা ৪৫ মিনিট"
                               class="w-full px-3.5 py-2 text-xs sm:text-sm bg-slate-50 border border-slate-300 rounded-xl focus:ring-2 focus:ring-emerald-500 outline-none">
                    </div>
                    <div>
                        <label class="block text-xs font-bold text-slate-700 mb-1.5">
                            স্ট্যাটাস
                        </label>
                        <div class="px-3.5 py-2 text-xs bg-slate-100 text-slate-700 rounded-xl border border-slate-200 font-semibold flex items-center gap-1.5">
                            <span class="w-2 h-2 rounded-full bg-emerald-500"></span>
                            <span>শিক্ষার্থীদের জন্য প্রকাশিত হবে</span>
                        </div>
                    </div>
                </div>

                <div>
                    <label class="block text-xs font-bold text-slate-700 mb-1.5">
                        লেকচার নোট বা নির্দেশনা (ঐচ্ছিক)
                    </label>
                    <textarea rows="3" x-model="recordingForm.recording_notes"
                              placeholder="ক্লাসের মূল বিষয়বস্তু বা শিক্ষার্থীদের জন্য প্রয়োজনীয় নির্দেশনা..."
                              class="w-full px-3.5 py-2 text-xs sm:text-sm bg-slate-50 border border-slate-300 rounded-xl focus:ring-2 focus:ring-emerald-500 outline-none"></textarea>
                </div>

                <div class="flex items-center justify-between pt-3 border-t border-slate-100">
                    <template x-if="recordingForm.has_recording">
                        <button type="button"
                                @click="deleteRecording()"
                                class="px-3 py-2 text-xs font-bold text-rose-600 hover:text-rose-800 hover:bg-rose-50 rounded-xl transition">
                            <i class="fa-regular fa-trash-can mr-1"></i> রেকর্ডিং মুছুন
                        </button>
                    </template>
                    <template x-if="!recordingForm.has_recording">
                        <div></div>
                    </template>

                    <div class="flex items-center gap-2">
                        <button type="button" @click="showRecordingModal = false"
                                class="px-4 py-2 text-xs font-semibold text-slate-600 hover:text-slate-800 bg-slate-100 hover:bg-slate-200 rounded-xl transition">
                            বাতিল
                        </button>
                        <button type="submit"
                                :disabled="recordingForm.loading"
                                class="inline-flex items-center gap-1.5 px-5 py-2 text-xs font-bold text-white bg-emerald-600 hover:bg-emerald-700 rounded-xl shadow-xs transition disabled:opacity-50">
                            <i class="fa-solid fa-spinner fa-spin" x-show="recordingForm.loading" style="display: none;"></i>
                            <span>রেকর্ডিং প্রকাশ করুন</span>
                        </button>
                    </div>
                </div>
            </form>
        </div>
    </div>

</div>

<script>
function copyMeetingLink(text, btn) {
    if (!text) return;
    navigator.clipboard.writeText(text).then(function() {
        const origHtml = btn.innerHTML;
        btn.innerHTML = '<i class="fa-solid fa-check text-emerald-400"></i>';
        setTimeout(function() {
            btn.innerHTML = origHtml;
        }, 2000);
    });
}

function liveMeetingsManager(config) {
    return {
        meetings: config.initialMeetings || [],
        heroData: config.heroData || {},
        showScheduleModal: false,
        showEditModal: false,
        showRecordingModal: false,
        toast: {
            show: false,
            message: '',
            type: 'success',
            timeout: null
        },
        createForm: {
            loading: false,
            batch_id: '{{ $selectedBatchId ?? '' }}',
            title: '',
            scheduled_date: '{{ date('Y-m-d') }}',
            scheduled_time: '21:00',
            meeting_url: '{{ old('meeting_url', $primaryCourse?->live_meeting_url) }}',
            meeting_id: '{{ old('meeting_id', $primaryCourse?->live_meeting_id) }}',
            meeting_passcode: '{{ old('meeting_passcode', $primaryCourse?->live_meeting_passcode) }}',
            schedule_text: '',
            is_live_now: false
        },
        editForm: {
            loading: false,
            actionUrl: '',
            data: {
                id: null,
                batch_id: '',
                title: '',
                meeting_url: '',
                meeting_id: '',
                meeting_passcode: '',
                scheduled_date: '',
                scheduled_time: '',
                schedule_text: '',
                is_live_now: false
            }
        },
        recordingForm: {
            loading: false,
            id: null,
            title: '',
            recording_url: '',
            recording_duration: '',
            recording_notes: '',
            has_recording: false,
            actionUrl: '',
            deleteUrl: ''
        },

        getMeeting(id) {
            return this.meetings.find(m => m.id === Number(id));
        },

        showToast(message, type = 'success') {
            this.toast.message = message;
            this.toast.type = type;
            this.toast.show = true;
            if (this.toast.timeout) clearTimeout(this.toast.timeout);
            this.toast.timeout = setTimeout(() => {
                this.toast.show = false;
            }, 4000);
        },

        escapeHtml(str) {
            if (!str) return '';
            return String(str)
                .replace(/&/g, '&amp;')
                .replace(/</g, '&lt;')
                .replace(/>/g, '&gt;')
                .replace(/"/g, '&quot;')
                .replace(/'/g, '&#039;');
        },

        escapeJs(str) {
            if (!str) return '';
            return String(str).replace(/'/g, "\\'").replace(/"/g, '\\"');
        },

        formatMeeting(m) {
            const startsAt = m.starts_at ? new Date(m.starts_at) : null;
            const months = ['Jan', 'Feb', 'Mar', 'Apr', 'May', 'Jun', 'Jul', 'Aug', 'Sep', 'Oct', 'Nov', 'Dec'];
            const days = ['Sunday', 'Monday', 'Tuesday', 'Wednesday', 'Thursday', 'Friday', 'Saturday'];

            let timeFormatted = 'N/A';
            let dateStr = '';
            let timeStr = '21:00';
            let monthShort = 'N/A';
            let dayNum = '--';
            let dateFormatted = 'N/A';
            let dayName = '';
            let isPast = false;

            if (startsAt && !isNaN(startsAt.getTime())) {
                const hours = startsAt.getHours();
                const mins = startsAt.getMinutes();
                const ampm = hours >= 12 ? 'PM' : 'AM';
                const h12 = hours % 12 || 12;
                timeFormatted = `${String(h12).padStart(2, '0')}:${String(mins).padStart(2, '0')} ${ampm}`;
                monthShort = months[startsAt.getMonth()];
                dayNum = String(startsAt.getDate()).padStart(2, '0');
                dateFormatted = `${dayNum} ${monthShort}, ${startsAt.getFullYear()}`;
                dayName = days[startsAt.getDay()];
                dateStr = `${startsAt.getFullYear()}-${String(startsAt.getMonth()+1).padStart(2, '0')}-${dayNum}`;
                timeStr = `${String(hours).padStart(2, '0')}:${String(mins).padStart(2, '0')}`;
                isPast = startsAt.getTime() < Date.now();
            }

            return {
                id: m.id,
                batch_id: m.batch_id ? String(m.batch_id) : '',
                batch_name: m.batch ? m.batch.name : 'সকল ব্যাচ',
                title: m.title || '',
                meeting_url: m.meeting_url || '',
                meeting_id: m.meeting_id || '',
                meeting_passcode: m.meeting_passcode || '',
                platform: m.platform || 'other',
                starts_at: m.starts_at,
                month_short: monthShort,
                day_num: dayNum,
                date_formatted: dateFormatted,
                day_name: dayName,
                time_formatted: timeFormatted,
                date_str: dateStr,
                time_str: timeStr,
                is_past: isPast,
                schedule_text: m.schedule_text || '',
                formatted_schedule: m.formatted_schedule || timeFormatted,
                is_live_now: Boolean(m.is_live_now),
                has_recording: Boolean(m.has_recording && m.recording_url),
                recording_url: m.recording_url || '',
                recording_duration: m.recording_duration || '',
                recording_notes: m.recording_notes || '',
                notes: m.notes || '',
                status: m.status || (m.is_live_now ? 'live' : 'scheduled'),
                watch_url: `{{ url('/student/recordings') }}/${m.id}`
            };
        },

        buildRowInnerHtml(m) {
            const isZoom = (m.meeting_url || '').toLowerCase().includes('zoom');
            const isMeet = (m.meeting_url || '').toLowerCase().includes('meet.google');

            let platformHtml = '';
            if (isZoom) {
                platformHtml = `<a href="${this.escapeHtml(m.meeting_url)}" target="_blank"
                    class="inline-flex items-center gap-1.5 px-2.5 py-1 rounded-lg text-xs font-semibold bg-blue-50 text-blue-700 border border-blue-200 hover:bg-blue-100 transition">
                    <i class="fa-solid fa-video text-blue-500 text-[11px]"></i>
                    <span>Zoom</span>
                    <i class="fa-solid fa-arrow-up-right-from-square text-[9px] opacity-70"></i>
                </a>`;
            } else if (isMeet) {
                platformHtml = `<a href="${this.escapeHtml(m.meeting_url)}" target="_blank"
                    class="inline-flex items-center gap-1.5 px-2.5 py-1 rounded-lg text-xs font-semibold bg-emerald-50 text-emerald-700 border border-emerald-200 hover:bg-emerald-100 transition">
                    <i class="fa-solid fa-chalkboard-user text-emerald-600 text-[11px]"></i>
                    <span>Meet</span>
                    <i class="fa-solid fa-arrow-up-right-from-square text-[9px] opacity-70"></i>
                </a>`;
            } else {
                platformHtml = `<a href="${this.escapeHtml(m.meeting_url)}" target="_blank"
                    class="inline-flex items-center gap-1.5 px-2.5 py-1 rounded-lg text-xs font-semibold bg-slate-50 text-slate-700 border border-slate-200 hover:bg-slate-100 transition">
                    <i class="fa-solid fa-link text-slate-500 text-[11px]"></i>
                    <span>লিংক</span>
                    <i class="fa-solid fa-arrow-up-right-from-square text-[9px] opacity-70"></i>
                </a>`;
            }

            let recHtml = '';
            if (m.has_recording) {
                recHtml = `
                    <div class="flex items-center gap-1.5">
                        <span class="inline-flex items-center gap-1 px-2 py-0.5 rounded-full text-[10px] font-bold bg-emerald-100 text-emerald-800 border border-emerald-200">
                            <i class="fa-solid fa-circle-play text-emerald-600 text-[9px]"></i>
                            <span>রেকর্ড সক্রিয়</span>
                        </span>
                        <a href="${m.watch_url}" target="_blank"
                           class="p-1 text-emerald-700 hover:text-emerald-900 transition"
                           title="সুরক্ষিত প্লেয়ারে প্রিভিউ দেখুন">
                            <i class="fa-solid fa-arrow-up-right-from-square text-[11px]"></i>
                        </a>
                        <button type="button"
                                @click="openRecordingById(${m.id})"
                                class="p-1 text-slate-400 hover:text-emerald-700 transition"
                                title="রেকর্ডিং পরিবর্তন করুন">
                            <i class="fa-solid fa-pen text-[11px]"></i>
                        </button>
                    </div>
                    ${m.recording_duration ? `<div class="text-[9px] text-slate-400 font-mono mt-0.5">${this.escapeHtml(m.recording_duration)}</div>` : ''}
                `;
            } else {
                recHtml = `
                    <button type="button"
                            @click="openRecordingById(${m.id})"
                            class="inline-flex items-center gap-1 px-2.5 py-1 rounded-lg text-xs font-semibold text-emerald-700 bg-emerald-50 hover:bg-emerald-100 border border-emerald-200 transition"
                            title="ভিডিও রেকর্ডিং পোস্ট করুন">
                        <i class="fa-solid fa-video text-[10px] text-emerald-600"></i>
                        <span>+ রেকর্ডিং</span>
                    </button>
                `;
            }

            let statusHtml = '';
            if (m.is_live_now) {
                statusHtml = `
                    <span class="inline-flex items-center gap-1.5 px-2.5 py-0.5 rounded-full text-[10px] font-black bg-red-100 text-red-700 border border-red-200 animate-pulse">
                        <span class="w-1.5 h-1.5 rounded-full bg-red-600"></span>
                        LIVE NOW
                    </span>
                `;
            } else if (m.is_past) {
                statusHtml = `
                    <span class="inline-flex items-center px-2 py-0.5 rounded-full text-[10px] font-semibold bg-slate-100 text-slate-600">
                        সম্পন্ন
                    </span>
                `;
            } else {
                statusHtml = `
                    <span class="inline-flex items-center px-2 py-0.5 rounded-full text-[10px] font-semibold bg-blue-50 text-blue-700 border border-blue-200">
                        শিডিউল করা
                    </span>
                `;
            }

            let toggleBtnHtml = '';
            if (m.is_live_now) {
                toggleBtnHtml = `
                    <button type="button"
                            @click="toggleLive(${m.id})"
                            class="inline-flex items-center gap-1 px-2.5 py-1 rounded-lg text-xs font-bold text-rose-700 bg-rose-50 hover:bg-rose-100 border border-rose-200 transition">
                        <i class="fa-solid fa-stop text-[10px]"></i>
                        <span>লাইভ বন্ধ</span>
                    </button>
                `;
            } else {
                toggleBtnHtml = `
                    <button type="button"
                            @click="toggleLive(${m.id})"
                            class="inline-flex items-center gap-1 px-2.5 py-1 rounded-lg text-xs font-bold text-white bg-red-600 hover:bg-red-700 shadow-xs transition">
                        <span class="w-1.5 h-1.5 rounded-full bg-white animate-ping"></span>
                        <span>🔴 লাইভ শুরু</span>
                    </button>
                `;
            }

            let credsChip = '';
            if (m.meeting_id || m.meeting_passcode) {
                credsChip = `<span class="text-[10px] text-slate-400 font-mono inline-flex items-center gap-1">`;
                if (m.meeting_id) credsChip += `ID: ${this.escapeHtml(m.meeting_id)}`;
                if (m.meeting_passcode) credsChip += ` • PW: ${this.escapeHtml(m.meeting_passcode)}`;
                credsChip += `</span>`;
            }

            return `
                <td class="py-2.5 px-3 sm:px-4 whitespace-nowrap">
                    <div class="flex items-center gap-2">
                        <div class="w-8 h-8 rounded-lg bg-emerald-50 text-emerald-700 flex flex-col items-center justify-center font-bold shrink-0 border border-emerald-100">
                            <span class="text-[9px] uppercase leading-none font-semibold">${this.escapeHtml(m.month_short)}</span>
                            <span class="text-xs leading-none font-black mt-0.5">${this.escapeHtml(m.day_num)}</span>
                        </div>
                        <div>
                            <div class="font-bold text-slate-900 text-xs flex items-center gap-1.5">
                                <span>${this.escapeHtml(m.date_formatted)}</span>
                                ${m.day_name ? `<span class="text-[10px] font-normal text-slate-500">(${this.escapeHtml(m.day_name)})</span>` : ''}
                            </div>
                            <div class="text-[11px] text-slate-500 flex items-center gap-1 mt-0.5">
                                <i class="fa-regular fa-clock text-emerald-600 text-[10px]"></i>
                                <span class="font-semibold text-slate-700">${this.escapeHtml(m.time_formatted)}</span>
                                ${m.schedule_text ? `<span class="text-[10px] text-amber-700 bg-amber-50 px-1 rounded border border-amber-200/60 font-medium ml-1">${this.escapeHtml(m.schedule_text)}</span>` : ''}
                            </div>
                        </div>
                    </div>
                </td>
                <td class="py-2.5 px-3 sm:px-4">
                    <div class="font-bold text-slate-900 text-xs sm:text-sm line-clamp-1">
                        ${this.escapeHtml(m.title)}
                    </div>
                    <div class="flex items-center gap-1.5 flex-wrap mt-0.5">
                        <span class="inline-flex items-center px-1.5 py-0.2 rounded text-[10px] font-bold ${m.batch_id ? 'bg-emerald-50 text-emerald-800 border border-emerald-200' : 'bg-slate-100 text-slate-600 border border-slate-200'}">
                            ${this.escapeHtml(m.batch_name)}
                        </span>
                        ${credsChip}
                    </div>
                </td>
                <td class="py-2.5 px-3 sm:px-4 whitespace-nowrap">
                    ${platformHtml}
                </td>
                <td class="py-2.5 px-3 sm:px-4 whitespace-nowrap" id="recording-cell-${m.id}">
                    ${recHtml}
                </td>
                <td class="py-2.5 px-3 sm:px-4 text-center whitespace-nowrap">
                    ${statusHtml}
                </td>
                <td class="py-2.5 px-3 sm:px-4 text-right whitespace-nowrap">
                    <div class="flex items-center justify-end gap-1.5">
                        ${toggleBtnHtml}
                        <button type="button"
                                @click="openEditById(${m.id})"
                                class="p-1.5 text-slate-400 hover:text-emerald-700 hover:bg-emerald-50 rounded-lg transition"
                                title="সম্পাদনা করুন">
                            <i class="fa-regular fa-pen-to-square text-xs"></i>
                        </button>
                        <button type="button"
                                @click="deleteMeeting(${m.id}, '${this.escapeJs(m.title)}')"
                                class="p-1.5 text-slate-400 hover:text-red-600 hover:bg-red-50 rounded-lg transition"
                                title="মুছে ফেলুন">
                            <i class="fa-regular fa-trash-can text-xs"></i>
                        </button>
                    </div>
                </td>
            `;
        },

        openEditById(id) {
            const meeting = this.getMeeting(id);
            if (!meeting) return;
            this.editForm.actionUrl = '{{ url('/admin/meetings') }}/' + id;
            this.editForm.data = {
                id: meeting.id,
                batch_id: meeting.batch_id ? String(meeting.batch_id) : '',
                title: meeting.title || '',
                meeting_url: meeting.meeting_url || '',
                meeting_id: meeting.meeting_id || '',
                meeting_passcode: meeting.meeting_passcode || '',
                scheduled_date: meeting.date_str || '',
                scheduled_time: meeting.time_str || '21:00',
                schedule_text: meeting.schedule_text || '',
                is_live_now: Boolean(meeting.is_live_now)
            };
            this.showEditModal = true;
        },

        openRecordingById(id) {
            const meeting = this.getMeeting(id);
            if (!meeting) return;
            this.recordingForm = {
                loading: false,
                id: meeting.id,
                title: meeting.title || '',
                recording_url: meeting.recording_url || '',
                recording_duration: meeting.recording_duration || '',
                recording_notes: meeting.recording_notes || '',
                has_recording: Boolean(meeting.has_recording),
                actionUrl: '{{ url('/admin/meetings') }}/' + id + '/recording',
                deleteUrl: '{{ url('/admin/meetings') }}/' + id + '/recording'
            };
            this.showRecordingModal = true;
        },

        openQuickRecording() {
            if (this.meetings.length > 0) {
                this.openRecordingById(this.meetings[0].id);
            } else {
                this.showScheduleModal = true;
            }
        },

        onSelectRecordingMeeting(id) {
            const m = this.getMeeting(id);
            if (m) {
                this.recordingForm.id = m.id;
                this.recordingForm.title = m.title;
                this.recordingForm.recording_url = m.recording_url || '';
                this.recordingForm.recording_duration = m.recording_duration || '';
                this.recordingForm.recording_notes = m.recording_notes || '';
                this.recordingForm.has_recording = Boolean(m.has_recording);
                this.recordingForm.actionUrl = '{{ url('/admin/meetings') }}/' + m.id + '/recording';
                this.recordingForm.deleteUrl = '{{ url('/admin/meetings') }}/' + m.id + '/recording';
            }
        },

        async toggleLive(id) {
            if (!id) return;
            try {
                const res = await fetch(`{{ url('/admin/meetings') }}/${id}/toggle-live`, {
                    method: 'POST',
                    headers: {
                        'Content-Type': 'application/json',
                        'Accept': 'application/json',
                        'X-CSRF-TOKEN': '{{ csrf_token() }}',
                        'X-Requested-With': 'XMLHttpRequest'
                    }
                });
                const data = await res.json();
                if (data.success) {
                    const target = this.getMeeting(id);
                    if (target) {
                        if (data.is_live_now) {
                            this.meetings.forEach(m => {
                                if (m.id !== id && (!target.batch_id || m.batch_id === target.batch_id)) {
                                    m.is_live_now = false;
                                }
                            });
                        }
                        target.is_live_now = data.is_live_now;
                        target.status = data.is_live_now ? 'live' : 'completed';

                        const tr = document.getElementById(`meeting-row-${id}`);
                        if (tr) {
                            tr.innerHTML = this.buildRowInnerHtml(target);
                            if (window.Alpine && window.Alpine.initTree) {
                                window.Alpine.initTree(tr);
                            }
                        }
                    }

                    if (this.heroData) {
                        this.heroData.isLive = data.is_live_now;
                        if (target) {
                            this.heroData.activeMeetingId = target.id;
                            this.heroData.topic = target.title;
                            this.heroData.schedule = target.formatted_schedule;
                            this.heroData.batchLabel = target.batch_name;
                            this.heroData.url = target.meeting_url;
                            this.heroData.meetingId = target.meeting_id;
                            this.heroData.passcode = target.meeting_passcode;
                        }
                    }

                    this.showToast(data.message, 'success');
                } else {
                    this.showToast(data.message || 'লাইভ স্ট্যাটাস পরিবর্তন করতে সমস্যা হয়েছে।', 'error');
                }
            } catch (err) {
                this.showToast('নেটওয়ার্ক ত্রুটি হয়েছে, অনুগ্রহ করে আবার চেষ্টা করুন।', 'error');
            }
        },

        async deleteMeeting(id, title) {
            if (!confirm(`'${title}' মিটিংটি মুছে ফেলতে চান?`)) return;
            try {
                const res = await fetch(`{{ url('/admin/meetings') }}/${id}`, {
                    method: 'DELETE',
                    headers: {
                        'Accept': 'application/json',
                        'X-CSRF-TOKEN': '{{ csrf_token() }}',
                        'X-Requested-With': 'XMLHttpRequest'
                    }
                });
                const data = await res.json();
                if (data.success) {
                    this.meetings = this.meetings.filter(m => m.id !== id);
                    const row = document.getElementById(`meeting-row-${id}`);
                    if (row) {
                        row.style.transition = 'all 0.25s ease-out';
                        row.style.opacity = '0';
                        row.style.transform = 'scale(0.96)';
                        setTimeout(() => {
                            row.remove();
                            if (this.meetings.length === 0) {
                                const emptyRow = document.getElementById('emptyMeetingsRow');
                                if (emptyRow) emptyRow.style.display = '';
                            }
                        }, 250);
                    }
                    this.showToast(data.message, 'success');
                } else {
                    this.showToast(data.message || 'মিটিং মুছতে সমস্যা হয়েছে।', 'error');
                }
            } catch (err) {
                this.showToast('নেটওয়ার্ক ত্রুটি হয়েছে, অনুগ্রহ করে আবার চেষ্টা করুন।', 'error');
            }
        },

        async saveCreate() {
            this.createForm.loading = true;
            try {
                const res = await fetch('{{ route('admin.meetings.store') }}', {
                    method: 'POST',
                    headers: {
                        'Content-Type': 'application/json',
                        'Accept': 'application/json',
                        'X-CSRF-TOKEN': '{{ csrf_token() }}',
                        'X-Requested-With': 'XMLHttpRequest'
                    },
                    body: JSON.stringify(this.createForm)
                });
                const data = await res.json();
                if (data.success && data.meeting) {
                    const formatted = this.formatMeeting(data.meeting);
                    this.meetings.unshift(formatted);

                    if (formatted.is_live_now) {
                        this.meetings.forEach(m => {
                            if (m.id !== formatted.id && (!formatted.batch_id || m.batch_id === formatted.batch_id)) {
                                m.is_live_now = false;
                            }
                        });
                        if (this.heroData) {
                            this.heroData.activeMeetingId = formatted.id;
                            this.heroData.isLive = true;
                            this.heroData.topic = formatted.title;
                            this.heroData.schedule = formatted.formatted_schedule;
                            this.heroData.batchLabel = formatted.batch_name;
                            this.heroData.url = formatted.meeting_url;
                            this.heroData.meetingId = formatted.meeting_id;
                            this.heroData.passcode = formatted.meeting_passcode;
                        }
                    }

                    // Dynamically insert row at top of table
                    const tbody = document.getElementById('meetingsTableBody');
                    if (tbody) {
                        const emptyRow = document.getElementById('emptyMeetingsRow');
                        if (emptyRow) emptyRow.style.display = 'none';

                        const tr = document.createElement('tr');
                        tr.id = `meeting-row-${formatted.id}`;
                        tr.className = 'hover:bg-slate-50/70 transition border-b border-slate-100 ' + (formatted.is_live_now ? 'bg-red-50/20' : '');
                        tr.setAttribute(':class', `getMeeting(${formatted.id})?.is_live_now ? 'bg-red-50/25' : ''`);
                        tr.innerHTML = this.buildRowInnerHtml(formatted);
                        tbody.insertBefore(tr, tbody.firstChild);

                        if (window.Alpine && window.Alpine.initTree) {
                            window.Alpine.initTree(tr);
                        }
                    }

                    this.showScheduleModal = false;
                    this.showToast(data.message, 'success');
                    this.createForm.title = '';
                    this.createForm.schedule_text = '';
                    this.createForm.is_live_now = false;
                } else {
                    this.showToast(data.message || 'মিটিং শিডিউল করতে সমস্যা হয়েছে।', 'error');
                }
            } catch (err) {
                this.showToast('নেটওয়ার্ক ত্রুটি হয়েছে, অনুগ্রহ করে আবার চেষ্টা করুন।', 'error');
            } finally {
                this.createForm.loading = false;
            }
        },

        async saveEdit() {
            this.editForm.loading = true;
            try {
                const res = await fetch(this.editForm.actionUrl, {
                    method: 'PUT',
                    headers: {
                        'Content-Type': 'application/json',
                        'Accept': 'application/json',
                        'X-CSRF-TOKEN': '{{ csrf_token() }}',
                        'X-Requested-With': 'XMLHttpRequest'
                    },
                    body: JSON.stringify(this.editForm.data)
                });
                const data = await res.json();
                if (data.success && data.meeting) {
                    const formatted = this.formatMeeting(data.meeting);
                    const idx = this.meetings.findIndex(x => x.id === formatted.id);
                    if (idx !== -1) {
                        this.meetings[idx] = formatted;
                    }
                    if (formatted.is_live_now) {
                        this.meetings.forEach(m => {
                            if (m.id !== formatted.id && (!formatted.batch_id || m.batch_id === formatted.batch_id)) {
                                m.is_live_now = false;
                            }
                        });
                        if (this.heroData) {
                            this.heroData.activeMeetingId = formatted.id;
                            this.heroData.isLive = true;
                            this.heroData.topic = formatted.title;
                            this.heroData.schedule = formatted.formatted_schedule;
                            this.heroData.batchLabel = formatted.batch_name;
                            this.heroData.url = formatted.meeting_url;
                            this.heroData.meetingId = formatted.meeting_id;
                            this.heroData.passcode = formatted.meeting_passcode;
                        }
                    }

                    const tr = document.getElementById(`meeting-row-${formatted.id}`);
                    if (tr) {
                        tr.innerHTML = this.buildRowInnerHtml(formatted);
                        if (window.Alpine && window.Alpine.initTree) {
                            window.Alpine.initTree(tr);
                        }
                    }

                    this.showEditModal = false;
                    this.showToast(data.message, 'success');
                } else {
                    this.showToast(data.message || 'হালনাগাদ করতে সমস্যা হয়েছে।', 'error');
                }
            } catch (err) {
                this.showToast('নেটওয়ার্ক ত্রুটি হয়েছে, অনুগ্রহ করে আবার চেষ্টা করুন।', 'error');
            } finally {
                this.editForm.loading = false;
            }
        },

        async saveRecording() {
            this.recordingForm.loading = true;
            try {
                const res = await fetch(this.recordingForm.actionUrl, {
                    method: 'POST',
                    headers: {
                        'Content-Type': 'application/json',
                        'Accept': 'application/json',
                        'X-CSRF-TOKEN': '{{ csrf_token() }}',
                        'X-Requested-With': 'XMLHttpRequest'
                    },
                    body: JSON.stringify({
                        recording_url: this.recordingForm.recording_url,
                        recording_duration: this.recordingForm.recording_duration,
                        recording_notes: this.recordingForm.recording_notes
                    })
                });
                const data = await res.json();
                if (data.success && data.meeting) {
                    const formatted = this.formatMeeting(data.meeting);
                    const idx = this.meetings.findIndex(x => x.id === formatted.id);
                    if (idx !== -1) {
                        this.meetings[idx] = formatted;
                    }

                    const tr = document.getElementById(`meeting-row-${formatted.id}`);
                    if (tr) {
                        tr.innerHTML = this.buildRowInnerHtml(formatted);
                        if (window.Alpine && window.Alpine.initTree) {
                            window.Alpine.initTree(tr);
                        }
                    }

                    this.showRecordingModal = false;
                    this.showToast(data.message, 'success');
                } else {
                    this.showToast(data.message || 'রেকর্ডিং প্রকাশ করতে সমস্যা হয়েছে।', 'error');
                }
            } catch (err) {
                this.showToast('নেটওয়ার্ক ত্রুটি হয়েছে, অনুগ্রহ করে আবার চেষ্টা করুন।', 'error');
            } finally {
                this.recordingForm.loading = false;
            }
        },

        async deleteRecording() {
            if (!confirm('এই ক্লাসের রেকর্ডিং সরিয়ে ফেলতে চান?')) return;
            this.recordingForm.loading = true;
            try {
                const res = await fetch(this.recordingForm.deleteUrl, {
                    method: 'DELETE',
                    headers: {
                        'Accept': 'application/json',
                        'X-CSRF-TOKEN': '{{ csrf_token() }}',
                        'X-Requested-With': 'XMLHttpRequest'
                    }
                });
                const data = await res.json();
                if (data.success && data.meeting) {
                    const formatted = this.formatMeeting(data.meeting);
                    const idx = this.meetings.findIndex(x => x.id === formatted.id);
                    if (idx !== -1) {
                        this.meetings[idx] = formatted;
                    }

                    const tr = document.getElementById(`meeting-row-${formatted.id}`);
                    if (tr) {
                        tr.innerHTML = this.buildRowInnerHtml(formatted);
                        if (window.Alpine && window.Alpine.initTree) {
                            window.Alpine.initTree(tr);
                        }
                    }

                    this.showRecordingModal = false;
                    this.showToast(data.message, 'success');
                } else {
                    this.showToast(data.message || 'রেকর্ডিং মুছে ফেলতে সমস্যা হয়েছে।', 'error');
                }
            } catch (err) {
                this.showToast('নেটওয়ার্ক ত্রুটি হয়েছে, অনুগ্রহ করে আবার চেষ্টা করুন।', 'error');
            } finally {
                this.recordingForm.loading = false;
            }
        }
    };
}

document.addEventListener('alpine:init', () => {
    if (window.Alpine) {
        window.Alpine.data('liveMeetingsManager', liveMeetingsManager);
    }
});
</script>
@endsection
