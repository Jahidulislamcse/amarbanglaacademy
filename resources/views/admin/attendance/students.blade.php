@extends('layouts.admin')

@section('title', 'শিক্ষার্থীভিত্তিক উপস্থিতি তালিকা - আমার বাংলা একাডেমি এডমিন')

@section('content')
<div class="space-y-6">

    <!-- Header -->
    <div class="flex flex-col sm:flex-row sm:items-center justify-between gap-4">
        <div>
            <div class="flex items-center gap-2 text-xs text-slate-500 mb-1">
                <a href="{{ route('admin.attendance.index') }}" class="hover:text-emerald-700">হাজিরা ব্যবস্থাপনা</a>
                <span>/</span>
                <span class="text-slate-800 font-semibold">শিক্ষার্থী উপস্থিতি</span>
            </div>
            <h1 class="text-2xl font-bold text-slate-900 flex items-center gap-2.5">
                <i class="fa-solid fa-users-viewfinder text-emerald-600"></i>
                <span>শিক্ষার্থীভিত্তিক উপস্থিতির হার ও রিপোর্ট</span>
            </h1>
            <p class="text-xs sm:text-sm text-slate-500 mt-1">
                প্রত্যেক শিক্ষার্থীর মোট উপস্থিতির সংখ্যা ও শতকরা হার (Percentage)। সার্টিফিকেট প্রদানের ক্ষেত্রে এটি সহায়ক।
            </p>
        </div>

        <div class="flex items-center gap-2.5">
            <a href="{{ route('admin.attendance.index') }}"
               class="inline-flex items-center gap-2 bg-slate-100 hover:bg-slate-200 text-slate-700 font-semibold text-xs sm:text-sm px-4 py-2.5 rounded-xl border border-slate-300 transition">
                <i class="fa-solid fa-arrow-left"></i>
                <span>ক্লাস সেশন তালিকা</span>
            </a>
        </div>
    </div>

    <!-- Filter & Search Bar -->
    <div class="bg-white p-4 rounded-2xl border border-slate-200 shadow-xs flex flex-col lg:flex-row items-center justify-between gap-3">
        <form method="GET" action="{{ route('admin.attendance.students') }}" class="w-full lg:w-auto flex flex-wrap sm:flex-nowrap items-center gap-2 flex-1 max-w-2xl">
            <div class="relative flex-1 min-w-[200px]">
                <i class="fa-solid fa-magnifying-glass absolute left-3.5 top-1/2 -translate-y-1/2 text-slate-400 text-xs"></i>
                <input type="text" name="search" value="{{ $search }}"
                       placeholder="শিক্ষার্থীর নাম, ফোন বা ইমেইল খুঁজুন..."
                       class="w-full pl-9 pr-3 py-2 text-xs bg-slate-50 border border-slate-300 rounded-xl focus:ring-2 focus:ring-emerald-500 outline-none">
            </div>
            @if(isset($batches) && $batches->count() > 0)
                <select name="batch_id"
                        class="px-3 py-2 text-xs bg-slate-50 border border-slate-300 rounded-xl focus:ring-2 focus:ring-emerald-500 outline-none font-semibold text-slate-700">
                    <option value="">সকল ব্যাচ</option>
                    @foreach($batches as $b)
                        <option value="{{ $b->id }}" {{ ($selectedBatchId ?? null) == $b->id ? 'selected' : '' }}>
                            {{ $b->name }}
                        </option>
                    @endforeach
                </select>
            @endif
            <button type="submit" class="px-3.5 py-2 text-xs font-bold bg-slate-900 text-white rounded-xl shrink-0">
                খুঁজুন
            </button>
            @if($search || ($selectedBatchId ?? null))
                <a href="{{ route('admin.attendance.students') }}" class="text-xs text-rose-600 hover:underline shrink-0">
                    রিসেট
                </a>
            @endif
        </form>

        <div class="flex items-center gap-2 text-xs font-semibold text-slate-600">
            <span class="w-3 h-3 rounded-full bg-emerald-500 inline-block"></span> ৮০%+ (চমৎকার)
            <span class="w-3 h-3 rounded-full bg-amber-500 inline-block ml-2"></span> ৬০-৭৯% (মোটামুটি)
            <span class="w-3 h-3 rounded-full bg-rose-500 inline-block ml-2"></span> ৬০% এর কম (সতর্কতা)
        </div>
    </div>

    <!-- Students Table -->
    <div class="bg-white rounded-3xl border border-slate-200 shadow-xs overflow-hidden">
        <div class="overflow-x-auto">
            <table class="w-full text-left border-collapse text-xs sm:text-sm">
                <thead>
                    <tr class="bg-slate-50/75 border-b border-slate-200 text-slate-600 font-bold uppercase tracking-wider text-[11px]">
                        <th class="py-3 px-4 sm:px-6 w-12 text-center">নং</th>
                        <th class="py-3 px-4 sm:px-6">শিক্ষার্থীর বিবরণ</th>
                        <th class="py-3 px-4 sm:px-6">যোগাযোগ</th>
                        <th class="py-3 px-4 sm:px-6 text-center">মোট ক্লাস</th>
                        <th class="py-3 px-4 sm:px-6 text-center">উপস্থিত</th>
                        <th class="py-3 px-4 sm:px-6 text-center">উপস্থিতির শতকরা হার</th>
                        <th class="py-3 px-4 sm:px-6 text-center">স্ট্যাটাস</th>
                    </tr>
                </thead>
                <tbody class="divide-y divide-slate-100">
                    @forelse($students as $idx => $student)
                        @php
                            $user = $student->user;
                            $studentBatchId = $student->batch_id;
                            $applicableSessions = isset($allCourseSessions)
                                ? $allCourseSessions->filter(fn($s) => $studentBatchId ? ($s->batch_id === $studentBatchId || is_null($s->batch_id)) : true)
                                : collect();
                            $applicableIds = $applicableSessions->pluck('id');
                            $studentTotalSessions = isset($allCourseSessions) ? $applicableSessions->count() : $totalSessions;
                            $attendedCount = $user
                                ? $user->attendances->filter(fn($a) => $a->isPresent() && (!isset($allCourseSessions) || $applicableIds->contains($a->attendance_session_id)))->count()
                                : 0;
                            $pct = $studentTotalSessions > 0 ? (int) round(($attendedCount / $studentTotalSessions) * 100) : 100;
                        @endphp
                        <tr class="hover:bg-slate-50/70 transition">
                            <td class="py-3 px-4 sm:px-6 text-center font-bold text-slate-400">
                                {{ $students->firstItem() + $idx }}
                            </td>
                            <td class="py-3 px-4 sm:px-6">
                                <div class="flex items-center gap-3">
                                    <div class="w-8 h-8 rounded-full bg-emerald-600 text-white flex items-center justify-center text-xs font-bold shrink-0">
                                        {{ mb_substr($student->student_name, 0, 1) }}
                                    </div>
                                    <div>
                                        <div class="font-bold text-slate-900">{{ $student->student_name }}</div>
                                        <div class="flex items-center gap-1.5 flex-wrap mt-0.5">
                                            <span class="text-[10px] text-slate-400 font-mono">
                                                ID: {{ $user?->username ?? ('STU-' . $student->id) }}
                                            </span>
                                            @if($student->batch)
                                                <span class="text-[10px] font-bold bg-emerald-50 text-emerald-700 px-1.5 py-0.2 rounded border border-emerald-200">
                                                    {{ $student->batch->name }}
                                                </span>
                                            @endif
                                        </div>
                                    </div>
                                </div>
                            </td>
                            <td class="py-3 px-4 sm:px-6">
                                <div class="font-mono font-semibold text-slate-800">{{ $student->student_phone }}</div>
                                <div class="text-[11px] text-slate-400">{{ $student->student_email }}</div>
                            </td>
                            <td class="py-3 px-4 sm:px-6 text-center font-bold text-slate-700">
                                {{ $studentTotalSessions }} টি
                            </td>
                            <td class="py-3 px-4 sm:px-6 text-center font-black text-emerald-700">
                                {{ $attendedCount }} টি
                            </td>
                            <td class="py-3 px-4 sm:px-6 text-center">
                                <div class="inline-flex flex-col items-center">
                                    <span class="font-black text-sm {{ $pct >= 80 ? 'text-emerald-700' : ($pct >= 60 ? 'text-amber-700' : 'text-rose-700') }}">
                                        {{ $pct }}%
                                    </span>
                                    <div class="w-20 bg-slate-100 rounded-full h-1.5 mt-1 overflow-hidden">
                                        <div class="h-full {{ $pct >= 80 ? 'bg-emerald-500' : ($pct >= 60 ? 'bg-amber-500' : 'bg-rose-500') }} rounded-full" style="width: {{ min(100, $pct) }}%"></div>
                                    </div>
                                </div>
                            </td>
                            <td class="py-3 px-4 sm:px-6 text-center whitespace-nowrap">
                                @if($pct >= 80)
                                    <span class="inline-flex items-center gap-1 px-2.5 py-0.5 rounded-full text-[11px] font-bold bg-emerald-100 text-emerald-800">
                                        <i class="fa-solid fa-check text-[10px]"></i> সার্টিফিকেট যোগ্য
                                    </span>
                                @elseif($pct >= 60)
                                    <span class="inline-flex items-center gap-1 px-2.5 py-0.5 rounded-full text-[11px] font-semibold bg-amber-100 text-amber-800">
                                        সন্তোষজনক
                                    </span>
                                @else
                                    <span class="inline-flex items-center gap-1 px-2.5 py-0.5 rounded-full text-[11px] font-semibold bg-rose-100 text-rose-800">
                                        সতর্কতা
                                    </span>
                                @endif
                            </td>
                        </tr>
                    @empty
                        <tr>
                            <td colspan="7" class="p-8 text-center text-slate-400">
                                কোনো শিক্ষার্থী পাওয়া যায়নি।
                            </td>
                        </tr>
                    @endforelse
                </tbody>
            </table>
        </div>

        <div class="p-4 border-t border-slate-100">
            {{ $students->links() }}
        </div>
    </div>

</div>
@endsection
