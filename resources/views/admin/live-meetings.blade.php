@extends('layouts.admin')

@section('title', 'লাইভ ক্লাস ও মিটিং ম্যানেজমেন্ট - আমার বাংলা একাডেমি এডমিন')

@section('content')
<div class="space-y-6">

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
                <span>লাইভ ক্লাস ও মিটিং রুম কন্ট্রোল</span>
            </h1>
            <p class="text-xs sm:text-sm text-slate-500 mt-1">
                জুম বা গুগল মিট লাইভ ক্লাসের লিংক সেট করুন। শিক্ষার্থীরা তাদের স্টুডেন্ট পোর্টাল থেকে এক ক্লিকে সরাসরি যুক্ত হতে পারবে।
            </p>
        </div>

        <div class="flex items-center gap-2">
            <a href="{{ route('student.dashboard') }}" target="_blank"
               class="inline-flex items-center gap-2 bg-slate-900 hover:bg-slate-800 text-white font-semibold text-xs sm:text-sm px-4 py-2.5 rounded-xl shadow-sm transition">
                <i class="fa-solid fa-graduation-cap text-amber-400"></i>
                <span>স্টুডেন্ট পোর্টাল দেখুন</span>
            </a>
        </div>
    </div>

    <!-- Active Meeting Details Display (বর্তমান সক্রিয় মিটিং বিবরণী) -->
    @if($primaryCourse && $primaryCourse->live_meeting_url)
        <div class="bg-gradient-to-br from-slate-900 via-emerald-950 to-slate-900 rounded-3xl border border-emerald-500/30 p-6 sm:p-7 shadow-xl text-white relative overflow-hidden">
            <!-- Background Glow -->
            <div class="absolute -right-16 -top-16 w-64 h-64 bg-emerald-500/10 rounded-full blur-3xl pointer-events-none"></div>

            <div class="flex flex-col lg:flex-row lg:items-center justify-between gap-4 pb-5 border-b border-emerald-800/60 relative z-10">
                <div class="flex items-start gap-4">
                    <div class="w-14 h-14 rounded-2xl bg-emerald-500/20 text-emerald-400 flex items-center justify-center text-2xl shrink-0 border border-emerald-500/40 shadow-inner">
                        @if(str_contains(strtolower($primaryCourse->live_meeting_url), 'zoom'))
                            <i class="fa-solid fa-video text-blue-400"></i>
                        @elseif(str_contains(strtolower($primaryCourse->live_meeting_url), 'meet.google'))
                            <i class="fa-solid fa-chalkboard-user text-emerald-400"></i>
                        @else
                            <i class="fa-solid fa-video"></i>
                        @endif
                    </div>
                    <div>
                        <div class="flex items-center gap-2.5 flex-wrap">
                            <span class="text-xs font-bold uppercase tracking-wider text-emerald-300">বর্তমান সক্রিয় লাইভ মিটিং</span>
                            @if($primaryCourse->is_live_now)
                                <span class="inline-flex items-center gap-1.5 px-3 py-1 rounded-full text-xs font-black bg-red-600 text-white animate-pulse shadow-md shadow-red-600/30">
                                    <span class="w-2 h-2 rounded-full bg-white"></span>
                                    এখন লাইভ চলছে (LIVE NOW)
                                </span>
                            @else
                                <span class="inline-flex items-center gap-1 px-2.5 py-0.5 rounded-full text-xs font-semibold bg-slate-800 text-slate-300 border border-slate-700">
                                    <i class="fa-solid fa-clock text-[10px]"></i>
                                    শিডিউল করা (অফলাইন)
                                </span>
                            @endif
                        </div>
                        <h2 class="text-xl sm:text-2xl font-black text-white mt-1">
                            {{ $primaryCourse->live_class_topic ?: 'সাংবাদিকতা, আইন ও উপস্থাপনা — অনলাইন লাইভ সেশন' }}
                        </h2>
                        <p class="text-xs text-emerald-200/80 mt-0.5">
                            কোর্স: <strong class="text-white">{{ $primaryCourse->bangla_title ?? 'সাংবাদিকতা, আইন ও উপস্থাপনা' }}</strong> ({{ $primaryCourse->batch_no ?? '১ম ব্যাচ' }})
                        </p>
                    </div>
                </div>

                <!-- Action Buttons: Direct Join & Test -->
                <div class="flex items-center gap-2.5 shrink-0 flex-wrap">
                    <a href="{{ $primaryCourse->live_meeting_url }}" target="_blank"
                       class="inline-flex items-center gap-2 px-5 py-2.5 rounded-xl bg-gradient-to-r from-emerald-500 to-teal-500 hover:from-emerald-400 hover:to-teal-400 text-slate-950 font-black text-xs sm:text-sm shadow-lg shadow-emerald-500/20 transition transform active:scale-95">
                        <i class="fa-solid fa-arrow-up-right-from-square"></i>
                        <span>সরাসরি মিটিংয়ে যুক্ত হন (Zoom / Meet)</span>
                    </a>
                    <button type="button" onclick="copyMeetingLink('{{ $primaryCourse->live_meeting_url }}', this)"
                            class="inline-flex items-center gap-1.5 px-4 py-2.5 rounded-xl bg-slate-800/90 hover:bg-slate-700 text-white text-xs font-bold border border-emerald-700/50 transition">
                        <i class="fa-regular fa-copy"></i>
                        <span>লিংক কপি</span>
                    </button>
                </div>
            </div>

            <!-- Credentials & Access Grid -->
            <div class="grid grid-cols-1 sm:grid-cols-2 lg:grid-cols-4 gap-3.5 mt-5 relative z-10 text-xs">
                <!-- Meeting URL Box -->
                <div class="p-3.5 bg-slate-800/80 rounded-2xl border border-emerald-900/60 lg:col-span-2">
                    <span class="text-[11px] text-emerald-300 block mb-1 font-semibold">লাইভ মিটিং URL (শিক্ষার্থী রিডাইরেক্ট লিঙ্ক)</span>
                    <div class="font-mono text-emerald-100 text-xs break-all select-all bg-slate-900/90 p-2 rounded-xl border border-slate-700">
                        {{ $primaryCourse->live_meeting_url }}
                    </div>
                </div>

                <!-- Meeting ID Box -->
                <div class="p-3.5 bg-slate-800/80 rounded-2xl border border-emerald-900/60">
                    <span class="text-[11px] text-emerald-300 block mb-1 font-semibold">Meeting ID (মিটিং আইডি)</span>
                    <div class="font-mono text-white text-sm font-bold bg-slate-900/90 p-2 rounded-xl border border-slate-700 flex items-center justify-between">
                        <span>{{ $primaryCourse->live_meeting_id ?: 'সরাসরি লিংকে যুক্ত' }}</span>
                        @if($primaryCourse->live_meeting_id)
                            <button type="button" onclick="copyMeetingLink('{{ $primaryCourse->live_meeting_id }}', this)" class="text-slate-400 hover:text-white">
                                <i class="fa-regular fa-copy"></i>
                            </button>
                        @endif
                    </div>
                </div>

                <!-- Passcode Box -->
                <div class="p-3.5 bg-slate-800/80 rounded-2xl border border-emerald-900/60">
                    <span class="text-[11px] text-emerald-300 block mb-1 font-semibold">Passcode (পাসকোড)</span>
                    <div class="font-mono text-white text-sm font-bold bg-slate-900/90 p-2 rounded-xl border border-slate-700 flex items-center justify-between">
                        <span>{{ $primaryCourse->live_meeting_passcode ?: 'পাসকোড প্রয়োজন নেই' }}</span>
                        @if($primaryCourse->live_meeting_passcode)
                            <button type="button" onclick="copyMeetingLink('{{ $primaryCourse->live_meeting_passcode }}', this)" class="text-slate-400 hover:text-white">
                                <i class="fa-regular fa-copy"></i>
                            </button>
                        @endif
                    </div>
                </div>
            </div>

            <!-- WhatsApp Batch Group Broadcast Bar -->
            <div class="mt-5 pt-4 border-t border-emerald-800/50 flex flex-col sm:flex-row sm:items-center justify-between gap-3 text-xs relative z-10">
                <div class="flex items-center gap-2 text-emerald-200">
                    <i class="fa-solid fa-users text-emerald-400"></i>
                    <span>মোট <strong>{{ $totalStudents }}</strong> জন সফল শিক্ষার্থী সরাসরি তাদের পোর্টাল থেকে এই ক্লাসে যোগ দিতে পারবে।</span>
                </div>
                @php
                    $waNotice = urlencode("জরুরি নোটিশ — লাইভ ক্লাস শুরু!\n\nআমার বাংলা একাডেমীর \"{$primaryCourse->bangla_title}\" কোর্সের লাইভ ক্লাস শুরু হয়েছে।\n\nবিষয়: {$primaryCourse->live_class_topic}\n\nআপনার স্টুডেন্ট পোর্টালে লগইন করে লাইভ ক্লাসে অংশ নিন:\n🌐 লগইন: " . url('/login') . "\n\nঅথবা সরাসরি মিটিং লিংক:\n🔗 {$primaryCourse->live_meeting_url}\n" . ($primaryCourse->live_meeting_id ? "Meeting ID: {$primaryCourse->live_meeting_id}\nPasscode: {$primaryCourse->live_meeting_passcode}\n" : "") . "\nসবার উপস্থিতি কাম্য। ধন্যবাদ।");
                    $waGroupUrl = "https://chat.whatsapp.com/EeM7caaCmE79NR0BFdrwWh?s=sh&p=a&iam=2";
                @endphp
                <div class="flex items-center gap-2">
                    <a href="{{ $waGroupUrl }}" target="_blank"
                       class="inline-flex items-center gap-1.5 px-3.5 py-1.5 rounded-xl bg-green-600 hover:bg-green-700 text-white font-bold text-xs shadow transition">
                        <i class="fa-brands fa-whatsapp text-sm"></i>
                        <span>WhatsApp ব্যাচ গ্রুপ খুলুন</span>
                    </a>
                </div>
            </div>
        </div>
    @else
        <!-- No Meeting Created Yet Notice -->
        <div class="bg-amber-50 border border-amber-300 rounded-2xl p-5 text-amber-900 flex items-start gap-3.5">
            <div class="w-10 h-10 rounded-xl bg-amber-200/70 text-amber-800 flex items-center justify-center text-xl shrink-0 mt-0.5">
                <i class="fa-solid fa-triangle-exclamation"></i>
            </div>
            <div>
                <h3 class="font-bold text-base text-amber-950">বর্তমানে কোনো লাইভ মিটিং লিংক সক্রিয় নেই!</h3>
                <p class="text-xs text-amber-800 mt-1">
                    আজকের ক্লাসের জন্য এখনো কোনো জুম বা গুগল মিট লিংক দেওয়া হয়নি। শিক্ষার্থীরা তাদের ড্যাশবোর্ডে গিয়ে কোনো সক্রিয় মিটিং পাবে না। অনুগ্রহ করে নিচের ফর্মটি পূরণ করে আজকের ক্লাসের মিটিং লিংক সেট করুন।
                </p>
            </div>
        </div>
    @endif

    <!-- Create / Update Meeting Form Card -->
    <div class="bg-white rounded-3xl border border-slate-200 shadow-sm p-6 sm:p-7">
        <div class="flex items-center justify-between pb-4 border-b border-slate-100 mb-5">
            <div class="flex items-center gap-3">
                <div class="w-10 h-10 rounded-xl bg-emerald-100 text-emerald-800 flex items-center justify-center text-lg font-bold">
                    <i class="fa-solid fa-gear"></i>
                </div>
                <div>
                    <h2 class="text-base font-bold text-slate-900">মিটিং লিংক তৈরি ও আপডেট ফর্ম</h2>
                    <p class="text-xs text-slate-500">Zoom বা Google Meet লিংক দিন ও 'লাইভ চলছে' স্ট্যাটাস নির্ধারণ করুন</p>
                </div>
            </div>
        </div>

        <form method="POST" action="{{ route('admin.courses.update-meeting', $primaryCourse?->id ?? 1) }}" class="space-y-5">
            @csrf

            <div class="grid grid-cols-1 md:grid-cols-2 gap-4">
                <!-- Live Meeting URL -->
                <div class="md:col-span-2">
                    <div class="flex items-center justify-between mb-1.5 flex-wrap gap-2">
                        <label class="block text-xs font-bold text-slate-700">
                            Zoom বা Google Meet মিটিং লিংক <span class="text-red-500">*</span>
                        </label>
                        <div class="flex items-center gap-1.5 text-[11px] flex-wrap">
                            <span class="text-slate-400 font-medium">ক্লিক করে বসান:</span>
                            <button type="button" onclick="setMeetingPreset('zoom')"
                                    class="px-2.5 py-0.5 rounded-lg bg-blue-100 hover:bg-blue-200 text-blue-800 font-bold transition">
                                <i class="fa-solid fa-video text-[10px]"></i> + Zoom লিংক
                            </button>
                            <button type="button" onclick="setMeetingPreset('meet')"
                                    class="px-2.5 py-0.5 rounded-lg bg-emerald-100 hover:bg-emerald-200 text-emerald-800 font-bold transition">
                                <i class="fa-solid fa-chalkboard-user text-[10px]"></i> + Google Meet লিংক
                            </button>
                        </div>
                    </div>
                    <div class="relative">
                        <div class="absolute inset-y-0 left-0 pl-3.5 flex items-center pointer-events-none text-slate-400">
                            <i class="fa-solid fa-link text-xs"></i>
                        </div>
                        <input type="text" name="live_meeting_url" id="live_meeting_url_input" required
                               value="{{ old('live_meeting_url', $primaryCourse?->live_meeting_url) }}"
                               placeholder="যেমন: https://meet.google.com/abc-defg-hij অথবা https://us05web.zoom.us/j/85212345678"
                               class="w-full pl-9 pr-4 py-2.5 text-xs sm:text-sm bg-slate-50 border @error('live_meeting_url') border-red-400 bg-red-50/40 @else border-slate-300 @enderror rounded-xl focus:ring-2 focus:ring-emerald-500 focus:border-emerald-500 outline-none font-mono transition">
                    </div>
                    @error('live_meeting_url')
                        <p class="text-red-500 text-xs mt-1">{{ $message }}</p>
                    @enderror
                    <p class="text-[11px] text-slate-400 mt-1">শিক্ষার্থীরা তাদের ড্যাশবোর্ডে "সরাসরি ক্লাসে যোগ দিন" বাটনে ক্লিক করলে কোনো লিমিট ছাড়া সরাসরি আপনার দেওয়া এই Zoom বা Google Meet-এ যুক্ত হয়ে যাবে।</p>
                </div>

                <!-- Live Class Topic -->
                <div class="md:col-span-2">
                    <label class="block text-xs font-bold text-slate-700 mb-1.5">
                        আজকের ক্লাসের বিষয় / টপিক
                    </label>
                    <div class="relative">
                        <div class="absolute inset-y-0 left-0 pl-3.5 flex items-center pointer-events-none text-slate-400">
                            <i class="fa-solid fa-heading text-xs"></i>
                        </div>
                        <input type="text" name="live_class_topic"
                               value="{{ old('live_class_topic', $primaryCourse?->live_class_topic) }}"
                               placeholder="যেমন: ওরিয়েন্টেশন ও মডিউল ১: সাংবাদিকতার পরিচিতি ও নিউজ সেন্স"
                               class="w-full pl-9 pr-4 py-2.5 text-xs sm:text-sm bg-slate-50 border border-slate-300 rounded-xl focus:ring-2 focus:ring-emerald-500 focus:border-emerald-500 outline-none transition">
                    </div>
                </div>

                <!-- Meeting ID -->
                <div>
                    <label class="block text-xs font-bold text-slate-700 mb-1.5">
                        মিটিং আইডি (Meeting ID) <span class="text-xs font-normal text-slate-400">(ঐচ্ছিক)</span>
                    </label>
                    <div class="relative">
                        <div class="absolute inset-y-0 left-0 pl-3.5 flex items-center pointer-events-none text-slate-400">
                            <i class="fa-solid fa-id-badge text-xs"></i>
                        </div>
                        <input type="text" name="live_meeting_id"
                               value="{{ old('live_meeting_id', $primaryCourse?->live_meeting_id) }}"
                               placeholder="যেমন: 852 1234 5678"
                               class="w-full pl-9 pr-4 py-2.5 text-xs sm:text-sm bg-slate-50 border border-slate-300 rounded-xl focus:ring-2 focus:ring-emerald-500 focus:border-emerald-500 outline-none font-mono transition">
                    </div>
                </div>

                <!-- Passcode -->
                <div>
                    <label class="block text-xs font-bold text-slate-700 mb-1.5">
                        পাসকোড (Passcode / Password) <span class="text-xs font-normal text-slate-400">(ঐচ্ছিক)</span>
                    </label>
                    <div class="relative">
                        <div class="absolute inset-y-0 left-0 pl-3.5 flex items-center pointer-events-none text-slate-400">
                            <i class="fa-solid fa-lock text-xs"></i>
                        </div>
                        <input type="text" name="live_meeting_passcode"
                               value="{{ old('live_meeting_passcode', $primaryCourse?->live_meeting_passcode) }}"
                               placeholder="যেমন: 123456"
                               class="w-full pl-9 pr-4 py-2.5 text-xs sm:text-sm bg-slate-50 border border-slate-300 rounded-xl focus:ring-2 focus:ring-emerald-500 focus:border-emerald-500 outline-none font-mono transition">
                    </div>
                </div>
            </div>

            <!-- Is Live Now Checkbox -->
            <div class="p-4 bg-emerald-50/60 border border-emerald-200 rounded-2xl flex items-center justify-between">
                <div>
                    <label for="is_live_now" class="font-bold text-xs sm:text-sm text-emerald-950 flex items-center gap-2 cursor-pointer">
                        <input type="checkbox" id="is_live_now" name="is_live_now" value="1"
                               {{ $primaryCourse?->is_live_now ? 'checked' : '' }}
                               class="w-4 h-4 text-emerald-600 rounded border-slate-300 focus:ring-emerald-500">
                        <span>এখন লাইভ ক্লাস চলছে হিসেবে চিহ্নিত করুন</span>
                    </label>
                    <p class="text-[11px] text-emerald-800 mt-0.5 ml-6">
                        এটি টিক দিলে শিক্ষার্থীদের ড্যাশবোর্ডে লাল ব্লিঙ্ক ইফেক্ট সহ <strong>"লাইভ ক্লাস চলছে"</strong> নোটিশ ভেসে উঠবে।
                    </p>
                </div>
                @if($primaryCourse?->is_live_now)
                    <span class="w-3 h-3 rounded-full bg-red-600 animate-ping"></span>
                @endif
            </div>

            <!-- Submit Button -->
            <div class="flex items-center justify-end gap-3 pt-2">
                <a href="{{ route('admin.students') }}" class="px-5 py-2.5 rounded-xl border border-slate-300 hover:bg-slate-100 text-slate-700 font-semibold text-xs transition">
                    বাতিল
                </a>
                <button type="submit"
                        class="inline-flex items-center gap-2 px-6 py-2.5 rounded-xl bg-emerald-600 hover:bg-emerald-700 text-white font-bold text-xs sm:text-sm shadow-md shadow-emerald-600/30 transition">
                    <i class="fa-solid fa-floppy-disk"></i>
                    <span>মিটিং লিংক সংরক্ষণ ও আপডেট করুন</span>
                </button>
            </div>
        </form>
    </div>

    <!-- Student Access Workflow Guide (কীভাবে কাজ করে) -->
    <div class="bg-slate-50 border border-slate-200 rounded-3xl p-6 text-xs text-slate-600 space-y-3">
        <h3 class="font-bold text-sm text-slate-800 flex items-center gap-2">
            <i class="fa-solid fa-circle-question text-emerald-600"></i>
            <span>শিক্ষার্থীদের লাইভ ক্লাসে অংশ নেওয়ার নিয়ম:</span>
        </h3>
        <div class="grid grid-cols-1 md:grid-cols-3 gap-4 pt-1">
            <div class="p-3.5 bg-white rounded-2xl border border-slate-200">
                <div class="font-bold text-slate-900 mb-1">১. এডমিন থেকে আইডি প্রদান</div>
                <p class="text-[11px] text-slate-500">শিক্ষার্থী তালিকায় "লগইন তৈরি" বাটনে ক্লিক করে ইউজার আইডি ও পাসওয়ার্ড দিয়ে হোয়াটসঅ্যাপে পাঠিয়ে দিন।</p>
            </div>
            <div class="p-3.5 bg-white rounded-2xl border border-slate-200">
                <div class="font-bold text-slate-900 mb-1">২. শিক্ষার্থী লগইন করবে</div>
                <p class="text-[11px] text-slate-500">শিক্ষার্থী <strong>{{ url('/login') }}</strong> পেজে গিয়ে তার মোবাইল নম্বর ও পাসওয়ার্ড দিয়ে ড্যাশবোর্ডে ঢুকবে।</p>
            </div>
            <div class="p-3.5 bg-white rounded-2xl border border-slate-200">
                <div class="font-bold text-slate-900 mb-1">৩. অটোমেটিক রিডাইরেক্ট</div>
                <p class="text-[11px] text-slate-500">ড্যাশবোর্ডের "লাইভ ক্লাসে যোগ দিন" বাটনে চাপ দিলেই সরাসরি আপনার দেওয়া জুম বা মিট মিটিংয়ে যুক্ত হয়ে যাবে।</p>
            </div>
        </div>
    </div>

</div>

@push('scripts')
<script>
function setMeetingPreset(type) {
    var urlInput = document.getElementById('live_meeting_url_input');
    if (!urlInput) return;
    if (type === 'zoom') {
        urlInput.value = 'https://us05web.zoom.us/j/';
        urlInput.focus();
    } else if (type === 'meet') {
        urlInput.value = 'https://meet.google.com/';
        urlInput.focus();
    }
}

function copyMeetingLink(text, btn) {
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
        window.prompt('মিটিং লিংক কপি করুন:', text);
    }
}
</script>
@endpush
@endsection
