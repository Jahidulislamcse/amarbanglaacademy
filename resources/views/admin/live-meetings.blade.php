@extends('layouts.admin')

@section('title', 'তারিখ ও সময়ভিত্তিক লাইভ মিটিং শিডিউল - আমার বাংলা একাডেমি এডমিন')

@section('content')
<div class="space-y-6" x-data="{
    showScheduleModal: false,
    showEditModal: false,
    showRecordingModal: false,
    editForm: {
        id: null,
        title: '',
        meeting_url: '',
        recording_url: '',
        recording_duration: '',
        recording_notes: '',
        meeting_id: '',
        meeting_passcode: '',
        scheduled_date: '',
        scheduled_time: '',
        schedule_text: '',
        notes: '',
        is_live_now: false,
        actionUrl: ''
    },
    recordingForm: {
        id: null,
        title: '',
        recording_url: '',
        recording_duration: '',
        recording_notes: '',
        has_recording: false,
        actionUrl: '',
        deleteUrl: ''
    },
    openEdit(meeting) {
        this.editForm = {
            id: meeting.id,
            title: meeting.title,
            meeting_url: meeting.meeting_url || '',
            recording_url: meeting.recording_url || '',
            recording_duration: meeting.recording_duration || '',
            recording_notes: meeting.recording_notes || '',
            meeting_id: meeting.meeting_id || '',
            meeting_passcode: meeting.meeting_passcode || '',
            scheduled_date: meeting.date_str,
            scheduled_time: meeting.time_str,
            schedule_text: meeting.schedule_text || '',
            notes: meeting.notes || '',
            is_live_now: Boolean(meeting.is_live_now),
            actionUrl: '{{ url('/admin/meetings') }}/' + meeting.id
        };
        this.showEditModal = true;
    },
    openRecording(meeting) {
        this.recordingForm = {
            id: meeting.id,
            title: meeting.title,
            recording_url: meeting.recording_url || '',
            recording_duration: meeting.recording_duration || '',
            recording_notes: meeting.recording_notes || '',
            has_recording: Boolean(meeting.has_recording),
            actionUrl: '{{ url('/admin/meetings') }}/' + meeting.id + '/recording',
            deleteUrl: '{{ url('/admin/meetings') }}/' + meeting.id + '/recording'
        };
        this.showRecordingModal = true;
    },
    openQuickRecording() {
        @if(count($meetings) > 0)
            @php $firstMeeting = $meetings->first(); @endphp
            this.openRecording({
                id: {{ $firstMeeting->id }},
                title: @json($firstMeeting->title),
                recording_url: @json($firstMeeting->recording_url ?? ''),
                recording_duration: @json($firstMeeting->recording_duration ?? ''),
                recording_notes: @json($firstMeeting->recording_notes ?? ''),
                has_recording: {{ $firstMeeting->hasRecording() ? 'true' : 'false' }}
            });
        @else
            this.showScheduleModal = true;
        @endif
    },
    onSelectRecordingMeeting(id) {
        this.recordingForm.actionUrl = '{{ url('/admin/meetings') }}/' + id + '/recording';
        this.recordingForm.deleteUrl = '{{ url('/admin/meetings') }}/' + id + '/recording';
    }
}">

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
                    class="inline-flex items-center gap-2 bg-white hover:bg-slate-50 text-slate-800 font-bold text-xs sm:text-sm px-4 py-2.5 rounded-xl border border-slate-300 shadow-sm transition">
                <i class="fa-solid fa-calendar-plus text-emerald-600"></i>
                <span>+ নতুন মিটিং শিডিউল করুন</span>
            </button>

            <a href="{{ route('student.dashboard') }}" target="_blank"
               class="inline-flex items-center gap-2 bg-slate-900 hover:bg-slate-800 text-white font-semibold text-xs sm:text-sm px-4 py-2.5 rounded-xl shadow-sm transition">
                <i class="fa-solid fa-graduation-cap text-amber-400"></i>
                <span>স্টুডেন্ট পোর্টাল দেখুন</span>
            </a>
        </div>
    </div>

    <!-- Active / Current Meeting Hero Card -->
    @php
        $displayMeeting = $activeMeeting;
        $isLive = ($displayMeeting && $displayMeeting->is_live_now) || ($primaryCourse && $primaryCourse->is_live_now);
        $meetingTopic = $displayMeeting?->title ?: ($primaryCourse?->live_class_topic ?: 'সাংবাদিকতা, আইন ও উপস্থাপনা — অনলাইন লাইভ সেশন');
        $meetingUrl = $displayMeeting?->meeting_url ?: ($primaryCourse?->live_meeting_url ?: '');
        $meetingId = $displayMeeting?->meeting_id ?: ($primaryCourse?->live_meeting_id ?: '');
        $meetingPasscode = $displayMeeting?->meeting_passcode ?: ($primaryCourse?->live_meeting_passcode ?: '');
    @endphp

    @if($meetingUrl)
        <div class="bg-gradient-to-br from-slate-900 via-emerald-950 to-slate-900 rounded-3xl border border-emerald-500/30 p-6 sm:p-7 shadow-xl text-white relative overflow-hidden">
            <div class="absolute -right-16 -top-16 w-64 h-64 bg-emerald-500/10 rounded-full blur-3xl pointer-events-none"></div>

            <div class="flex flex-col lg:flex-row lg:items-center justify-between gap-4 pb-5 border-b border-emerald-800/60 relative z-10">
                <div class="flex items-start gap-4">
                    <div class="w-14 h-14 rounded-2xl bg-emerald-500/20 text-emerald-400 flex items-center justify-center text-2xl shrink-0 border border-emerald-500/40 shadow-inner">
                        @if(str_contains(strtolower($meetingUrl), 'zoom'))
                            <i class="fa-solid fa-video text-blue-400"></i>
                        @elseif(str_contains(strtolower($meetingUrl), 'meet.google'))
                            <i class="fa-solid fa-chalkboard-user text-emerald-400"></i>
                        @else
                            <i class="fa-solid fa-video"></i>
                        @endif
                    </div>
                    <div>
                        <div class="flex items-center gap-2.5 flex-wrap">
                            <span class="text-xs font-bold uppercase tracking-wider text-emerald-300">
                                @if($isLive) বর্তমান সক্রিয় লাইভ ক্লাস @else পরবর্তী শিডিউলকৃত ক্লাস @endif
                            </span>
                            @if($isLive)
                                <span class="inline-flex items-center gap-1.5 px-3 py-1 rounded-full text-xs font-black bg-red-600 text-white animate-pulse shadow-md shadow-red-600/30">
                                    <span class="w-2 h-2 rounded-full bg-white"></span>
                                    এখন লাইভ চলছে (LIVE NOW)
                                </span>
                            @else
                                <span class="inline-flex items-center gap-1 px-2.5 py-0.5 rounded-full text-xs font-semibold bg-amber-500/20 text-amber-300 border border-amber-500/30">
                                    <i class="fa-solid fa-clock text-[10px]"></i>
                                    {{ $displayMeeting?->formatted_schedule ?: ($primaryCourse?->live_schedule_text ?: 'শিডিউল করা') }}
                                </span>
                            @endif
                        </div>
                        <h2 class="text-xl sm:text-2xl font-black text-white mt-1">
                            {{ $meetingTopic }}
                        </h2>
                        <p class="text-xs text-emerald-200/80 mt-0.5">
                            কোর্স: <strong class="text-white">{{ $primaryCourse?->bangla_title ?? 'সাংবাদিকতা, আইন ও উপস্থাপনা' }}</strong> ({{ $primaryCourse?->batch_no ?? '১ম ব্যাচ' }})
                        </p>
                    </div>
                </div>

                <!-- Action Buttons: Direct Join & Test -->
                <div class="flex items-center gap-2.5 shrink-0 flex-wrap">
                    @if($displayMeeting)
                        <form method="POST" action="{{ route('admin.meetings.toggle-live', $displayMeeting->id) }}">
                            @csrf
                            @if($displayMeeting->is_live_now)
                                <button type="submit"
                                        class="inline-flex items-center gap-1.5 px-4 py-2.5 rounded-xl bg-slate-800 hover:bg-slate-700 text-rose-300 text-xs font-bold border border-rose-500/30 transition">
                                    <i class="fa-solid fa-stop text-xs"></i>
                                    <span>লাইভ বন্ধ করুন</span>
                                </button>
                            @else
                                <button type="submit"
                                        class="inline-flex items-center gap-2 px-5 py-2.5 rounded-xl bg-red-600 hover:bg-red-700 text-white font-bold text-xs sm:text-sm shadow-lg shadow-red-600/30 animate-pulse transition">
                                    <span class="w-2 h-2 rounded-full bg-white"></span>
                                    <span>🔴 এখনই লাইভ শুরু করুন</span>
                                </button>
                            @endif
                        </form>
                    @endif

                    <a href="{{ $meetingUrl }}" target="_blank"
                       class="inline-flex items-center gap-2 px-4 py-2.5 rounded-xl bg-gradient-to-r from-emerald-500 to-teal-500 hover:from-emerald-400 hover:to-teal-400 text-slate-950 font-black text-xs sm:text-sm shadow-lg shadow-emerald-500/20 transition">
                        <i class="fa-solid fa-arrow-up-right-from-square"></i>
                        <span>মিটিংয়ে যুক্ত হন</span>
                    </a>

                    @if($displayMeeting)
                        @php
                            $heroRecJson = [
                                'id' => $displayMeeting->id,
                                'title' => $displayMeeting->title,
                                'recording_url' => $displayMeeting->recording_url,
                                'recording_duration' => $displayMeeting->recording_duration,
                                'recording_notes' => $displayMeeting->recording_notes,
                                'has_recording' => (bool) $displayMeeting->hasRecording(),
                            ];
                        @endphp
                        <button type="button"
                                @click='openRecording(@json($heroRecJson))'
                                class="inline-flex items-center gap-2 px-4 py-2.5 rounded-xl bg-slate-800 hover:bg-slate-700 text-emerald-300 hover:text-white font-bold text-xs sm:text-sm border border-emerald-500/40 shadow transition">
                            <i class="fa-solid fa-circle-play text-emerald-400"></i>
                            <span>{{ $displayMeeting->hasRecording() ? 'রেকর্ডিং পরিবর্তন' : 'রেকর্ডিং পোস্ট করুন' }}</span>
                        </button>
                    @endif
                </div>
            </div>

            <!-- Credentials & Access Grid -->
            <div class="grid grid-cols-1 sm:grid-cols-2 lg:grid-cols-4 gap-3.5 mt-5 relative z-10 text-xs">
                <div class="p-3.5 bg-slate-800/80 rounded-2xl border border-emerald-900/60 lg:col-span-2">
                    <span class="text-[11px] text-emerald-300 block mb-1 font-semibold">লাইভ মিটিং URL (রিডাইরেক্ট লিঙ্ক)</span>
                    <div class="font-mono text-emerald-100 text-xs break-all select-all bg-slate-900/90 p-2 rounded-xl border border-slate-700">
                        {{ $meetingUrl }}
                    </div>
                </div>

                <div class="p-3.5 bg-slate-800/80 rounded-2xl border border-emerald-900/60">
                    <span class="text-[11px] text-emerald-300 block mb-1 font-semibold">Meeting ID (মিটিং আইডি)</span>
                    <div class="font-mono text-white text-sm font-bold bg-slate-900/90 p-2 rounded-xl border border-slate-700 flex items-center justify-between">
                        <span>{{ $meetingId ?: 'সরাসরি লিংকে যুক্ত' }}</span>
                        @if($meetingId)
                            <button type="button" onclick="copyMeetingLink('{{ $meetingId }}', this)" class="text-slate-400 hover:text-white">
                                <i class="fa-regular fa-copy"></i>
                            </button>
                        @endif
                    </div>
                </div>

                <div class="p-3.5 bg-slate-800/80 rounded-2xl border border-emerald-900/60">
                    <span class="text-[11px] text-emerald-300 block mb-1 font-semibold">Passcode (পাসকোড)</span>
                    <div class="font-mono text-white text-sm font-bold bg-slate-900/90 p-2 rounded-xl border border-slate-700 flex items-center justify-between">
                        <span>{{ $meetingPasscode ?: 'পাসকোড প্রয়োজন নেই' }}</span>
                        @if($meetingPasscode)
                            <button type="button" onclick="copyMeetingLink('{{ $meetingPasscode }}', this)" class="text-slate-400 hover:text-white">
                                <i class="fa-regular fa-copy"></i>
                            </button>
                        @endif
                    </div>
                </div>
            </div>
        </div>
    @endif

    <!-- Scheduled Meetings List Table -->
    <div class="bg-white rounded-3xl border border-slate-200 shadow-xs overflow-hidden">
        <div class="p-5 sm:p-6 border-b border-slate-100 flex flex-col sm:flex-row sm:items-center justify-between gap-3">
            <div>
                <h2 class="text-lg font-bold text-slate-900 flex items-center gap-2">
                    <i class="fa-solid fa-calendar-days text-emerald-600"></i>
                    <span>তারিখ ও সময়ভিত্তিক ক্লাস শিডিউল তালিকা</span>
                </h2>
                <p class="text-xs text-slate-500 mt-0.5">
                    প্রতিটি ক্লাসের নির্দিষ্ট তারিখ, শুরুর সময় এবং লাইভ স্ট্যাটাস পরিচালনা করুন
                </p>
            </div>
            <div class="flex items-center gap-2">
                <button type="button"
                        @click="showScheduleModal = true"
                        class="inline-flex items-center gap-1.5 px-3.5 py-1.5 rounded-xl bg-emerald-50 hover:bg-emerald-100 text-emerald-800 font-bold text-xs border border-emerald-200 transition">
                    <i class="fa-solid fa-plus text-xs"></i>
                    <span>নতুন ক্লাস যোগ করুন</span>
                </button>
            </div>
        </div>

        @if(count($meetings) > 0)
            <div class="overflow-x-auto">
                <table class="w-full text-left border-collapse text-xs sm:text-sm">
                    <thead>
                        <tr class="bg-slate-50/75 border-b border-slate-200 text-slate-600 font-bold uppercase tracking-wider text-[11px]">
                            <th class="py-3 px-4 sm:px-6">তারিখ ও বার</th>
                            <th class="py-3 px-4 sm:px-6">ক্লাস শুরুর সময়</th>
                            <th class="py-3 px-4 sm:px-6">ক্লাসের বিষয় / টপিক</th>
                            <th class="py-3 px-4 sm:px-6">প্ল্যাটফর্ম ও লিংক</th>
                            <th class="py-3 px-4 sm:px-6">ক্লাস রেকর্ডিং</th>
                            <th class="py-3 px-4 sm:px-6 text-center">স্ট্যাটাস</th>
                            <th class="py-3 px-4 sm:px-6 text-right">লাইভ অ্যাকশন</th>
                        </tr>
                    </thead>
                    <tbody class="divide-y divide-slate-100">
                        @foreach($meetings as $meeting)
                            <tr class="hover:bg-slate-50/70 transition {{ $meeting->is_live_now ? 'bg-red-50/20' : '' }}">
                                <td class="py-3.5 px-4 sm:px-6 whitespace-nowrap">
                                    <div class="font-bold text-slate-900">
                                        {{ $meeting->starts_at ? $meeting->starts_at->format('d M, Y') : 'N/A' }}
                                    </div>
                                    <div class="text-[11px] text-slate-400">
                                        {{ $meeting->starts_at ? $meeting->starts_at->isoFormat('dddd') : '' }}
                                    </div>
                                </td>
                                <td class="py-3.5 px-4 sm:px-6 whitespace-nowrap">
                                    <div class="font-bold text-slate-900 flex items-center gap-1.5">
                                        <i class="fa-regular fa-clock text-emerald-600 text-xs"></i>
                                        <span>{{ $meeting->starts_at ? $meeting->starts_at->format('h:i A') : 'N/A' }}</span>
                                    </div>
                                    @if($meeting->schedule_text)
                                        <div class="text-[10px] text-amber-700 font-semibold mt-0.5">
                                            {{ $meeting->schedule_text }}
                                        </div>
                                    @endif
                                </td>
                                <td class="py-3.5 px-4 sm:px-6">
                                    <div class="font-bold text-slate-900">
                                        {{ $meeting->title }}
                                    </div>
                                    @if($meeting->meeting_id || $meeting->meeting_passcode)
                                        <div class="text-[10px] text-slate-400 font-mono mt-0.5">
                                            @if($meeting->meeting_id) ID: {{ $meeting->meeting_id }} @endif
                                            @if($meeting->meeting_passcode) • পাসকোড: {{ $meeting->meeting_passcode }} @endif
                                        </div>
                                    @endif
                                </td>
                                <td class="py-3.5 px-4 sm:px-6 whitespace-nowrap">
                                    <a href="{{ $meeting->meeting_url }}" target="_blank"
                                       class="inline-flex items-center gap-1.5 text-xs font-semibold text-emerald-700 hover:text-emerald-900 hover:underline">
                                        @if(str_contains(strtolower($meeting->meeting_url ?? ''), 'zoom'))
                                            <i class="fa-solid fa-video text-blue-500"></i>
                                            <span>Zoom লিংক</span>
                                        @elseif(str_contains(strtolower($meeting->meeting_url ?? ''), 'meet.google'))
                                            <i class="fa-solid fa-chalkboard-user text-emerald-600"></i>
                                            <span>Google Meet</span>
                                        @else
                                            <i class="fa-solid fa-link text-slate-500"></i>
                                            <span>মিটিং লিংক</span>
                                        @endif
                                        <i class="fa-solid fa-arrow-up-right-from-square text-[10px]"></i>
                                    </a>
                                </td>
                                <td class="py-3.5 px-4 sm:px-6 whitespace-nowrap">
                                    @php
                                        $meetingJson = [
                                            'id' => $meeting->id,
                                            'title' => $meeting->title,
                                            'meeting_url' => $meeting->meeting_url,
                                            'recording_url' => $meeting->recording_url,
                                            'recording_duration' => $meeting->recording_duration,
                                            'recording_notes' => $meeting->recording_notes,
                                            'has_recording' => (bool) $meeting->hasRecording(),
                                            'meeting_id' => $meeting->meeting_id,
                                            'meeting_passcode' => $meeting->meeting_passcode,
                                            'date_str' => $meeting->starts_at ? $meeting->starts_at->format('Y-m-d') : date('Y-m-d'),
                                            'time_str' => $meeting->starts_at ? $meeting->starts_at->format('H:i') : '21:00',
                                            'schedule_text' => $meeting->schedule_text,
                                            'notes' => $meeting->notes,
                                            'is_live_now' => (bool) $meeting->is_live_now,
                                        ];
                                    @endphp

                                    @if($meeting->hasRecording())
                                        <div class="flex items-center gap-1.5">
                                            <span class="inline-flex items-center gap-1 px-2.5 py-0.5 rounded-full text-[11px] font-bold bg-emerald-100 text-emerald-800 border border-emerald-200">
                                                <i class="fa-solid fa-circle-play text-emerald-600 text-[10px]"></i>
                                                <span>রেকর্ডিং সক্রিয়</span>
                                            </span>
                                            <a href="{{ route('student.recordings.watch', $meeting->id) }}" target="_blank"
                                               class="p-1 text-emerald-700 hover:text-emerald-900 transition"
                                               title="সুরক্ষিত প্লেয়ারে প্রিভিউ দেখুন">
                                                <i class="fa-solid fa-arrow-up-right-from-square text-xs"></i>
                                            </a>
                                            <button type="button"
                                                    @click='openRecording(@json($meetingJson))'
                                                    class="p-1 text-slate-400 hover:text-emerald-700 transition"
                                                    title="রেকর্ডিং পরিবর্তন করুন">
                                                <i class="fa-solid fa-pen text-xs"></i>
                                            </button>
                                        </div>
                                        @if($meeting->recording_duration)
                                            <div class="text-[10px] text-slate-400 font-mono mt-0.5">
                                                {{ $meeting->recording_duration }}
                                            </div>
                                        @endif
                                    @else
                                        <button type="button"
                                                @click='openRecording(@json($meetingJson))'
                                                class="inline-flex items-center gap-1.5 px-3 py-1.5 rounded-xl text-xs font-bold bg-emerald-600 hover:bg-emerald-700 text-white shadow-xs transition transform hover:scale-105"
                                                title="এই ক্লাসের ভিডিও রেকর্ডিং পোস্ট করুন">
                                            <i class="fa-solid fa-video text-xs"></i>
                                            <span>রেকর্ডিং পোস্ট করুন</span>
                                        </button>
                                    @endif
                                </td>
                                <td class="py-3.5 px-4 sm:px-6 text-center whitespace-nowrap">
                                    @if($meeting->is_live_now)
                                        <span class="inline-flex items-center gap-1.5 px-2.5 py-1 rounded-full text-xs font-bold bg-red-100 text-red-700 border border-red-200 animate-pulse">
                                            <span class="w-1.5 h-1.5 rounded-full bg-red-600"></span>
                                            LIVE NOW
                                        </span>
                                    @elseif($meeting->starts_at && $meeting->starts_at->isPast())
                                        <span class="inline-flex items-center px-2.5 py-1 rounded-full text-[11px] font-semibold bg-slate-100 text-slate-600">
                                            সম্পন্ন
                                        </span>
                                    @else
                                        <span class="inline-flex items-center px-2.5 py-1 rounded-full text-[11px] font-semibold bg-blue-50 text-blue-700 border border-blue-200">
                                            শিডিউল করা
                                        </span>
                                    @endif
                                </td>
                                <td class="py-3.5 px-4 sm:px-6 text-right whitespace-nowrap">
                                    <div class="flex items-center justify-end gap-2">
                                        <form method="POST" action="{{ route('admin.meetings.toggle-live', $meeting->id) }}" class="inline">
                                            @csrf
                                            @if($meeting->is_live_now)
                                                <button type="submit"
                                                        class="inline-flex items-center gap-1 px-3 py-1.5 rounded-xl text-xs font-bold text-slate-700 bg-slate-100 hover:bg-slate-200 border border-slate-300 transition">
                                                    <i class="fa-solid fa-stop text-[10px]"></i>
                                                    <span>লাইভ বন্ধ</span>
                                                </button>
                                            @else
                                                <button type="submit"
                                                        class="inline-flex items-center gap-1 px-3 py-1.5 rounded-xl text-xs font-bold text-white bg-red-600 hover:bg-red-700 shadow-xs transition">
                                                    <span class="w-1.5 h-1.5 rounded-full bg-white animate-ping"></span>
                                                    <span>🔴 লাইভ শুরু</span>
                                                </button>
                                            @endif
                                        </form>

                                        <button type="button"
                                                @click='openEdit(@json($meetingJson))'
                                                class="p-1.5 text-slate-500 hover:text-emerald-700 hover:bg-emerald-50 rounded-lg transition"
                                                title="সম্পাদনা করুন">
                                            <i class="fa-regular fa-pen-to-square"></i>
                                        </button>

                                        <form method="POST" action="{{ route('admin.meetings.destroy', $meeting->id) }}"
                                              onsubmit="return confirm('এই শিডিউল মিটিংটি মুছে ফেলতে চান?')" class="inline">
                                            @csrf
                                            @method('DELETE')
                                            <button type="submit" title="মুছে ফেলুন" class="p-1.5 text-slate-400 hover:text-red-600 transition">
                                                <i class="fa-regular fa-trash-can"></i>
                                            </button>
                                            @csrf
                                            @method('DELETE')
                                            <button type="submit" title="মুছে ফেলুন" class="p-1.5 text-slate-400 hover:text-red-600 transition">
                                                <i class="fa-regular fa-trash-can"></i>
                                            </button>
                                        </form>
                                    </div>
                                </td>
                            </tr>
                        @endforeach
                    </tbody>
                </table>
            </div>
        @else
            <div class="p-8 text-center text-slate-500 text-xs">
                এখনো নির্দিষ্ট তারিখ-সময়ভিত্তিক কোনো মিটিং শিডিউল করা হয়নি। উপরে <strong>"+ নতুন মিটিং শিডিউল করুন"</strong> বাটনে ক্লিক করে তারিখ ও সময় দিয়ে ক্লাস যুক্ত করুন।
            </div>
        @endif
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

            <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-3 gap-4">
                @foreach($recordedMeetings as $rec)
                    <div class="p-4 bg-slate-50 rounded-2xl border border-slate-200 hover:border-emerald-300 transition flex flex-col justify-between space-y-3">
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
                            @php
                                $recJson = [
                                    'id' => $rec->id,
                                    'title' => $rec->title,
                                    'recording_url' => $rec->recording_url,
                                    'recording_duration' => $rec->recording_duration,
                                    'recording_notes' => $rec->recording_notes,
                                    'has_recording' => true,
                                ];
                            @endphp
                            <button type="button"
                                    @click='openRecording(@json($recJson))'
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



    <!-- Create Scheduled Meeting Modal -->
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

            <form method="POST" action="{{ route('admin.meetings.store') }}" class="space-y-4">
                @csrf

                <div>
                    <label class="block text-xs font-bold text-slate-700 mb-1.5">
                        ক্লাসের বিষয় / শিরোনাম <span class="text-rose-500">*</span>
                    </label>
                    <input type="text" name="title" required
                           placeholder="যেমন: ক্লাস ১: সাংবাদিকতার পরিচিতি ও মৌলিক ধারণা"
                           class="w-full px-3.5 py-2.5 text-xs sm:text-sm bg-slate-50 border border-slate-300 rounded-xl focus:ring-2 focus:ring-emerald-500 outline-none">
                </div>

                <div class="grid grid-cols-1 sm:grid-cols-2 gap-3">
                    <div>
                        <label class="block text-xs font-bold text-slate-700 mb-1.5">
                            ক্লাসের তারিখ <span class="text-rose-500">*</span>
                        </label>
                        <input type="date" name="scheduled_date" required
                               value="{{ date('Y-m-d') }}"
                               class="w-full px-3.5 py-2.5 text-xs sm:text-sm bg-slate-50 border border-slate-300 rounded-xl focus:ring-2 focus:ring-emerald-500 outline-none">
                    </div>

                    <div>
                        <label class="block text-xs font-bold text-slate-700 mb-1.5">
                            ক্লাস শুরুর সময় <span class="text-rose-500">*</span>
                        </label>
                        <input type="time" name="scheduled_time" required
                               value="21:00"
                               class="w-full px-3.5 py-2.5 text-xs sm:text-sm bg-slate-50 border border-slate-300 rounded-xl focus:ring-2 focus:ring-emerald-500 outline-none font-mono">
                    </div>
                </div>

                <div>
                    <label class="block text-xs font-bold text-slate-700 mb-1.5">
                        মিটিং URL (Zoom বা Google Meet লিংক) <span class="text-rose-500">*</span>
                    </label>
                    <input type="text" name="meeting_url" required
                           value="{{ old('meeting_url', $primaryCourse?->live_meeting_url) }}"
                           placeholder="https://us05web.zoom.us/j/... অথবা https://meet.google.com/..."
                           class="w-full px-3.5 py-2.5 text-xs sm:text-sm bg-slate-50 border border-slate-300 rounded-xl focus:ring-2 focus:ring-emerald-500 outline-none font-mono">
                </div>

                <div class="grid grid-cols-1 sm:grid-cols-2 gap-3">
                    <div>
                        <label class="block text-xs font-bold text-slate-700 mb-1.5">মিটিং আইডি (ঐচ্ছিক)</label>
                        <input type="text" name="meeting_id"
                               value="{{ old('meeting_id', $primaryCourse?->live_meeting_id) }}"
                               placeholder="যেমন: 852 1234 5678"
                               class="w-full px-3.5 py-2 text-xs sm:text-sm bg-slate-50 border border-slate-300 rounded-xl focus:ring-2 focus:ring-emerald-500 outline-none font-mono">
                    </div>

                    <div>
                        <label class="block text-xs font-bold text-slate-700 mb-1.5">পাসকোড (ঐচ্ছিক)</label>
                        <input type="text" name="meeting_passcode"
                               value="{{ old('meeting_passcode', $primaryCourse?->live_meeting_passcode) }}"
                               placeholder="যেমন: 123456"
                               class="w-full px-3.5 py-2 text-xs sm:text-sm bg-slate-50 border border-slate-300 rounded-xl focus:ring-2 focus:ring-emerald-500 outline-none font-mono">
                    </div>
                </div>

                <div>
                    <label class="block text-xs font-bold text-slate-700 mb-1.5">
                        কাস্টম শিডিউল টেক্সট <span class="text-slate-400 font-normal">(ঐচ্ছিক, শিক্ষার্থীদের বাটনের নিচে দেখাবে)</span>
                    </label>
                    <input type="text" name="schedule_text"
                           placeholder="যেমন: আজ রাত ৯:০০ টা - ১১:০০ টা"
                           class="w-full px-3.5 py-2 text-xs sm:text-sm bg-slate-50 border border-slate-300 rounded-xl focus:ring-2 focus:ring-emerald-500 outline-none">
                </div>

                <div class="p-3 bg-red-50/70 border border-red-200 rounded-xl flex items-center gap-2">
                    <input type="checkbox" id="modal_is_live_now" name="is_live_now" value="1"
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
                            class="px-5 py-2 text-xs font-bold text-white bg-emerald-600 hover:bg-emerald-700 rounded-xl shadow-sm transition">
                        শিডিউল মিটিং সংরক্ষণ করুন
                    </button>
                </div>
            </form>
        </div>
    </div>

    <!-- Edit Scheduled Meeting Modal -->
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

            <form method="POST" :action="editForm.actionUrl" class="space-y-4">
                @csrf
                @method('PUT')

                <div>
                    <label class="block text-xs font-bold text-slate-700 mb-1.5">
                        ক্লাসের বিষয় / শিরোনাম <span class="text-rose-500">*</span>
                    </label>
                    <input type="text" name="title" required
                           x-model="editForm.title"
                           placeholder="যেমন: ক্লাস ১: সাংবাদিকতার পরিচিতি ও মৌলিক ধারণা"
                           class="w-full px-3.5 py-2.5 text-xs sm:text-sm bg-slate-50 border border-slate-300 rounded-xl focus:ring-2 focus:ring-emerald-500 outline-none">
                </div>

                <div class="grid grid-cols-1 sm:grid-cols-2 gap-3">
                    <div>
                        <label class="block text-xs font-bold text-slate-700 mb-1.5">
                            ক্লাসের তারিখ <span class="text-rose-500">*</span>
                        </label>
                        <input type="date" name="scheduled_date" required
                               x-model="editForm.scheduled_date"
                               class="w-full px-3.5 py-2.5 text-xs sm:text-sm bg-slate-50 border border-slate-300 rounded-xl focus:ring-2 focus:ring-emerald-500 outline-none">
                    </div>

                    <div>
                        <label class="block text-xs font-bold text-slate-700 mb-1.5">
                            ক্লাস শুরুর সময় <span class="text-rose-500">*</span>
                        </label>
                        <input type="time" name="scheduled_time" required
                               x-model="editForm.scheduled_time"
                               class="w-full px-3.5 py-2.5 text-xs sm:text-sm bg-slate-50 border border-slate-300 rounded-xl focus:ring-2 focus:ring-emerald-500 outline-none font-mono">
                    </div>
                </div>

                <div>
                    <label class="block text-xs font-bold text-slate-700 mb-1.5">
                        মিটিং URL (Zoom বা Google Meet লিংক) <span class="text-rose-500">*</span>
                    </label>
                    <input type="text" name="meeting_url" required
                           x-model="editForm.meeting_url"
                           placeholder="https://us05web.zoom.us/j/... অথবা https://meet.google.com/..."
                           class="w-full px-3.5 py-2.5 text-xs sm:text-sm bg-slate-50 border border-slate-300 rounded-xl focus:ring-2 focus:ring-emerald-500 outline-none font-mono">
                </div>

                <div class="grid grid-cols-1 sm:grid-cols-2 gap-3">
                    <div>
                        <label class="block text-xs font-bold text-slate-700 mb-1.5">মিটিং আইডি (ঐচ্ছিক)</label>
                        <input type="text" name="meeting_id"
                               x-model="editForm.meeting_id"
                               placeholder="যেমন: 852 1234 5678"
                               class="w-full px-3.5 py-2 text-xs sm:text-sm bg-slate-50 border border-slate-300 rounded-xl focus:ring-2 focus:ring-emerald-500 outline-none font-mono">
                    </div>

                    <div>
                        <label class="block text-xs font-bold text-slate-700 mb-1.5">পাসকোড (ঐচ্ছিক)</label>
                        <input type="text" name="meeting_passcode"
                               x-model="editForm.meeting_passcode"
                               placeholder="যেমন: 123456"
                               class="w-full px-3.5 py-2 text-xs sm:text-sm bg-slate-50 border border-slate-300 rounded-xl focus:ring-2 focus:ring-emerald-500 outline-none font-mono">
                    </div>
                </div>

                <div>
                    <label class="block text-xs font-bold text-slate-700 mb-1.5">
                        কাস্টম শিডিউল টেক্সট <span class="text-slate-400 font-normal">(ঐচ্ছিক, শিক্ষার্থীদের বাটনের নিচে দেখাবে)</span>
                    </label>
                    <input type="text" name="schedule_text"
                           x-model="editForm.schedule_text"
                           placeholder="যেমন: আজ রাত ৯:০০ টা - ১১:০০ টা"
                           class="w-full px-3.5 py-2 text-xs sm:text-sm bg-slate-50 border border-slate-300 rounded-xl focus:ring-2 focus:ring-emerald-500 outline-none">
                </div>

                <div class="p-3 bg-red-50/70 border border-red-200 rounded-xl flex items-center gap-2">
                    <input type="checkbox" id="edit_modal_is_live_now" name="is_live_now" value="1"
                           x-model="editForm.is_live_now"
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
                            class="px-5 py-2 text-xs font-bold text-white bg-emerald-600 hover:bg-emerald-700 rounded-xl shadow-sm transition">
                        আপডেট সংরক্ষণ করুন
                    </button>
                </div>
            </form>
        </div>
    </div>

    <!-- Share Class Recording Modal (YouTube / Video URL) -->
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

            <form method="POST" :action="recordingForm.actionUrl" class="space-y-4">
                @csrf

                <!-- Notice about Unbranded Protection -->
                <div class="p-3.5 bg-emerald-50/70 border border-emerald-200 rounded-2xl text-xs text-emerald-900 space-y-1">
                    <div class="font-bold flex items-center gap-1.5 text-emerald-800">
                        <i class="fa-solid fa-shield-halved text-emerald-600"></i>
                        <span>সুরক্ষিত আনব্র্যান্ডেড প্লেয়ার সুবিধা:</span>
                    </div>
                    <p class="text-[11px] text-emerald-700 leading-relaxed">
                        ইউটিউবের আনলিস্টেড (Unlisted) বা পাবলিক যেকোনো ভিডিও লিংক দিন। শিক্ষার্থী পোর্টালে ভিডিও ফ্রেমের ভেতরে <strong>কোনো ইউটিউব লোগো, বাটন বা লিংক দেখানো হবে না</strong>। এটি সম্পূর্ণ নিজস্ব ক্লাউড স্টোরেজ ভিডিওর মতো প্রিমিয়াম লুকে চলবে।
                    </p>
                </div>

                @if(count($meetings) > 0)
                    <div>
                        <label class="block text-xs font-bold text-slate-700 mb-1.5">
                            কোন ক্লাসের রেকর্ডিং পোস্ট করছেন? <span class="text-rose-500">*</span>
                        </label>
                        <select class="w-full px-3.5 py-2.5 text-xs sm:text-sm bg-slate-50 border border-slate-300 rounded-xl focus:ring-2 focus:ring-emerald-500 outline-none font-semibold text-slate-800"
                                x-model="recordingForm.id"
                                @change="onSelectRecordingMeeting($event.target.value)">
                            @foreach($meetings as $m)
                                <option value="{{ $m->id }}">{{ $m->title }} ({{ $m->starts_at ? $m->starts_at->format('d M, Y') : '' }})</option>
                            @endforeach
                        </select>
                    </div>
                @endif

                <div>
                    <label class="block text-xs font-bold text-slate-700 mb-1.5">
                        ভিডিও রেকর্ডিং লিংক (YouTube লিংক বা ভিডিও URL) <span class="text-rose-500">*</span>
                    </label>
                    <input type="text" name="recording_url" required
                           x-model="recordingForm.recording_url"
                           placeholder="যেমন: https://www.youtube.com/watch?v=... অথবা https://youtu.be/..."
                           class="w-full px-3.5 py-2.5 text-xs sm:text-sm bg-slate-50 border border-slate-300 rounded-xl focus:ring-2 focus:ring-emerald-500 outline-none font-mono">
                </div>

                <div class="grid grid-cols-1 sm:grid-cols-2 gap-3">
                    <div>
                        <label class="block text-xs font-bold text-slate-700 mb-1.5">
                            স্থিতিকাল / সময় (ঐচ্ছিক)
                        </label>
                        <input type="text" name="recording_duration"
                               x-model="recordingForm.recording_duration"
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
                    <textarea name="recording_notes" rows="3"
                              x-model="recordingForm.recording_notes"
                              placeholder="ক্লাসের মূল বিষয়বস্তু বা শিক্ষার্থীদের জন্য প্রয়োজনীয় নির্দেশনা..."
                              class="w-full px-3.5 py-2 text-xs sm:text-sm bg-slate-50 border border-slate-300 rounded-xl focus:ring-2 focus:ring-emerald-500 outline-none"></textarea>
                </div>

                <div class="flex items-center justify-between pt-3 border-t border-slate-100">
                    <template x-if="recordingForm.has_recording">
                        <button type="button"
                                @click="if(confirm('এই ক্লাসের রেকর্ডিং সরিয়ে ফেলতে চান?')) { $refs.deleteRecordingForm.submit(); }"
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
                                class="px-5 py-2 text-xs font-bold text-white bg-emerald-600 hover:bg-emerald-700 rounded-xl shadow-sm transition">
                            রেকর্ডিং প্রকাশ করুন
                        </button>
                    </div>
                </div>
            </form>

            <!-- Hidden Delete Recording Form -->
            <form x-ref="deleteRecordingForm" method="POST" :action="recordingForm.deleteUrl" class="hidden">
                @csrf
                @method('DELETE')
            </form>
        </div>
    </div>

</div>

<script>
function copyMeetingLink(text, btn) {
    if (!text) return;
    navigator.clipboard.writeText(text).then(function() {
        const origHtml = btn.innerHTML;
        btn.innerHTML = '<i class="fa-solid fa-check text-emerald-400"></i> কপির সম্পন্ন!';
        setTimeout(function() {
            btn.innerHTML = origHtml;
        }, 2000);
    });
}
</script>
@endsection
