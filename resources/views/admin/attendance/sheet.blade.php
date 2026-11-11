@extends('layouts.admin')

@section('title', 'হাজিরা শিট: ' . $session->title . ' - আমার বাংলা একাডেমি এডমিন')

@section('content')
<div class="space-y-6">

    <!-- Header & Navigation -->
    <div class="flex flex-col sm:flex-row sm:items-center justify-between gap-4">
        <div>
            <div class="flex items-center gap-2 text-xs text-slate-500 mb-1">
                <a href="{{ route('admin.attendance.index') }}" class="hover:text-emerald-700">হাজিরা ব্যবস্থাপনা</a>
                <span>/</span>
                <span class="text-slate-800 font-semibold">হাজিরা শিট</span>
            </div>
            <h1 class="text-2xl font-bold text-slate-900 flex items-center gap-2.5 flex-wrap">
                <i class="fa-solid fa-clipboard-check text-emerald-600"></i>
                <span>{{ $session->title }}</span>
                <span class="px-2.5 py-0.5 rounded-full text-xs font-bold {{ $session->batch ? 'bg-blue-50 text-blue-700 border border-blue-200' : 'bg-slate-100 text-slate-600 border border-slate-200' }}">
                    {{ $session->batch?->name ?? 'সকল ব্যাচ' }}
                </span>
            </h1>
            <p class="text-xs sm:text-sm text-slate-500 mt-1">
                তারিখ: <strong>{{ $session->session_date->format('d M, Y') }}</strong> ({{ $session->session_date->isoFormat('dddd') }})
                @if($session->notes) • নোট: {{ $session->notes }} @endif
            </p>
        </div>

        <div class="flex items-center gap-2.5 flex-wrap">
            <a href="{{ route('admin.attendance.index') }}"
               class="inline-flex items-center gap-2 bg-slate-100 hover:bg-slate-200 text-slate-700 font-semibold text-xs sm:text-sm px-3.5 py-2 rounded-xl border border-slate-300 transition">
                <i class="fa-solid fa-arrow-left"></i>
                <span>সকল সেশন</span>
            </a>

            <form method="POST" action="{{ route('admin.attendance.mark-all-present', $session->id) }}"
                  onsubmit="return confirm('আপনি কি এই সেশনে সকল সক্রিয় শিক্ষার্থীকে উপস্থিত হিসেবে মার্ক করতে চান?')">
                @csrf
                <button type="submit"
                        class="inline-flex items-center gap-2 bg-emerald-600 hover:bg-emerald-700 text-white font-bold text-xs sm:text-sm px-4 py-2 rounded-xl shadow-sm transition">
                    <i class="fa-solid fa-check-double"></i>
                    <span>সকলকে উপস্থিত মার্ক করুন</span>
                </button>
            </form>
        </div>
    </div>

    <!-- Attendance Summary Mini Cards -->
    <div class="grid grid-cols-2 lg:grid-cols-4 gap-3 sm:gap-4">
        <div class="bg-white p-4 rounded-2xl border border-slate-200 shadow-xs flex items-center gap-3">
            <div class="w-10 h-10 rounded-xl bg-blue-50 text-blue-600 flex items-center justify-center text-lg shrink-0">
                <i class="fa-solid fa-users"></i>
            </div>
            <div>
                <div class="text-[11px] font-semibold text-slate-500">মোট তালিকাভুক্ত শিক্ষার্থী</div>
                <div class="text-lg font-black text-slate-900">{{ $totalEnrolled }} জন</div>
            </div>
        </div>

        <div class="bg-white p-4 rounded-2xl border border-slate-200 shadow-xs flex items-center gap-3">
            <div class="w-10 h-10 rounded-xl bg-emerald-50 text-emerald-600 flex items-center justify-center text-lg shrink-0">
                <i class="fa-solid fa-user-check"></i>
            </div>
            <div>
                <div class="text-[11px] font-semibold text-slate-500">মোট উপস্থিত</div>
                <div class="text-lg font-black text-emerald-600">{{ $presentCount }} জন</div>
            </div>
        </div>

        <div class="bg-white p-4 rounded-2xl border border-slate-200 shadow-xs flex items-center gap-3">
            <div class="w-10 h-10 rounded-xl bg-rose-50 text-rose-600 flex items-center justify-center text-lg shrink-0">
                <i class="fa-solid fa-user-xmark"></i>
            </div>
            <div>
                <div class="text-[11px] font-semibold text-slate-500">অনুপস্থিত</div>
                <div class="text-lg font-black text-rose-600">{{ max(0, $totalEnrolled - $presentCount) }} জন</div>
            </div>
        </div>

        <div class="bg-white p-4 rounded-2xl border border-slate-200 shadow-xs flex items-center gap-3">
            <div class="w-10 h-10 rounded-xl bg-purple-50 text-purple-600 flex items-center justify-center text-lg shrink-0">
                <i class="fa-solid fa-chart-pie"></i>
            </div>
            <div>
                <div class="text-[11px] font-semibold text-slate-500">উপস্থিতি হার</div>
                <div class="text-lg font-black {{ $percentage >= 70 ? 'text-emerald-600' : 'text-amber-600' }}">{{ $percentage }}%</div>
            </div>
        </div>
    </div>

    <!-- Student Attendance Sheet Table -->
    <div class="bg-white rounded-3xl border border-slate-200 shadow-xs overflow-hidden">
        <div class="p-5 border-b border-slate-100 flex items-center justify-between">
            <h2 class="font-bold text-base text-slate-900 flex items-center gap-2">
                <i class="fa-solid fa-table-list text-emerald-600"></i>
                <span>শিক্ষার্থী হাজিরা তালিকা</span>
            </h2>
            <span class="text-xs text-slate-500">
                বাটনে ক্লিক করে উপস্থিতি স্ট্যাটাস পরিবর্তন করতে পারবেন
            </span>
        </div>

        <div class="overflow-x-auto">
            <table class="w-full text-left border-collapse text-xs sm:text-sm">
                <thead>
                    <tr class="bg-slate-50/75 border-b border-slate-200 text-slate-600 font-bold uppercase tracking-wider text-[11px]">
                        <th class="py-3 px-4 sm:px-6 w-12 text-center">নং</th>
                        <th class="py-3 px-4 sm:px-6">শিক্ষার্থীর নাম ও বিবরণ</th>
                        <th class="py-3 px-4 sm:px-6">ফোন নম্বর ও ইমেইল</th>
                        <th class="py-3 px-4 sm:px-6 text-center">উপস্থিতি স্ট্যাটাস</th>
                        <th class="py-3 px-4 sm:px-6 text-center">যোগদানের মাধ্যম ও সময়</th>
                        <th class="py-3 px-4 sm:px-6 text-right">হাজিরা অ্যাকশন</th>
                    </tr>
                </thead>
                <tbody class="divide-y divide-slate-100">
                    @forelse($enrollments as $idx => $enrollment)
                        @php
                            $user = $enrollment->user;
                            $att = $user ? ($attendances[$user->id] ?? null) : null;
                            $isPresent = $att && $att->isPresent();
                        @endphp
                        <tr class="hover:bg-slate-50/70 transition {{ $isPresent ? 'bg-emerald-50/20' : '' }}">
                            <td class="py-3 px-4 sm:px-6 text-center font-bold text-slate-400">
                                {{ $idx + 1 }}
                            </td>
                            <td class="py-3 px-4 sm:px-6">
                                <div class="flex items-center gap-3">
                                    <div class="w-8 h-8 rounded-full {{ $isPresent ? 'bg-emerald-600 text-white' : 'bg-slate-200 text-slate-700' }} flex items-center justify-center text-xs font-bold shrink-0">
                                        {{ mb_substr($enrollment->student_name, 0, 1) }}
                                    </div>
                                    <div>
                                        <div class="font-bold text-slate-900">{{ $enrollment->student_name }}</div>
                                        <div class="flex items-center gap-1.5 flex-wrap mt-0.5">
                                            <span class="text-[11px] text-slate-400 font-mono">
                                                ID: {{ $user?->username ?? ('STU-' . $enrollment->id) }}
                                            </span>
                                            @if($enrollment->batch)
                                                <span class="text-[10px] font-bold bg-emerald-50 text-emerald-700 px-1.5 py-0.2 rounded border border-emerald-200">
                                                    {{ $enrollment->batch->name }}
                                                </span>
                                            @endif
                                        </div>
                                    </div>
                                </div>
                            </td>
                            <td class="py-3 px-4 sm:px-6">
                                <div class="font-mono text-slate-800 font-semibold">{{ $enrollment->student_phone }}</div>
                                <div class="text-[11px] text-slate-400">{{ $enrollment->student_email }}</div>
                            </td>
                            <td class="py-3 px-4 sm:px-6 text-center whitespace-nowrap">
                                @if($isPresent)
                                    <span class="inline-flex items-center gap-1.5 px-3 py-1 rounded-full text-xs font-bold bg-emerald-100 text-emerald-800 border border-emerald-200">
                                        <i class="fa-solid fa-circle-check text-emerald-600"></i>
                                        <span>উপস্থিত (Present)</span>
                                    </span>
                                @else
                                    <span class="inline-flex items-center gap-1.5 px-3 py-1 rounded-full text-xs font-semibold bg-rose-50 text-rose-700 border border-rose-200">
                                        <i class="fa-solid fa-circle-xmark text-rose-500"></i>
                                        <span>অনুপস্থিত (Absent)</span>
                                    </span>
                                @endif
                            </td>
                            <td class="py-3 px-4 sm:px-6 text-center whitespace-nowrap">
                                @if($isPresent)
                                    <div class="text-xs font-medium text-slate-700">
                                        @if($att->join_method === 'online_join')
                                            <span class="text-emerald-700 font-semibold">
                                                <i class="fa-solid fa-globe text-[11px] mr-1"></i>অনলাইনে জয়েন
                                            </span>
                                        @else
                                            <span class="text-blue-700 font-semibold">
                                                <i class="fa-solid fa-user-shield text-[11px] mr-1"></i>এডমিন ম্যানুয়াল
                                            </span>
                                        @endif
                                    </div>
                                    <div class="text-[10px] text-slate-400 mt-0.5">
                                        {{ $att->attended_at ? $att->attended_at->format('h:i A') : 'ক্লাস সময়' }}
                                    </div>
                                @else
                                    <span class="text-slate-400 text-xs">—</span>
                                @endif
                            </td>
                            <td class="py-3 px-4 sm:px-6 text-right whitespace-nowrap">
                                <form method="POST" action="{{ route('admin.attendance.toggle', $session->id) }}" class="inline">
                                    @csrf
                                    <input type="hidden" name="user_id" value="{{ $user?->id ?? '' }}">
                                    <input type="hidden" name="enrollment_id" value="{{ $enrollment->id }}">
                                    @if($isPresent)
                                        <button type="submit"
                                                title="অনুপস্থিত হিসেবে পরিবর্তন করতে ক্লিক করুন"
                                                class="inline-flex items-center gap-1 px-3 py-1.5 rounded-xl text-xs font-semibold text-rose-700 bg-rose-50 hover:bg-rose-100 border border-rose-200 transition">
                                            <i class="fa-solid fa-xmark"></i>
                                            <span>অনুপস্থিত</span>
                                        </button>
                                    @else
                                        <button type="submit"
                                                title="উপস্থিত হিসেবে মার্ক করতে ক্লিক করুন"
                                                class="inline-flex items-center gap-1 px-3 py-1.5 rounded-xl text-xs font-bold text-emerald-800 bg-emerald-50 hover:bg-emerald-100 border border-emerald-300 transition">
                                            <i class="fa-solid fa-check"></i>
                                            <span>উপস্থিত</span>
                                        </button>
                                    @endif
                                </form>
                            </td>
                        </tr>
                    @empty
                        <tr>
                            <td colspan="6" class="p-8 text-center text-slate-400">
                                কোনো সক্রিয় শিক্ষার্থী পাওয়া যায়নি।
                            </td>
                        </tr>
                    @endforelse
                </tbody>
            </table>
        </div>
    </div>

</div>
@endsection
