@extends('layouts.admin')

@section('title', 'হাজিরা ও উপস্থিতি ব্যবস্থাপনা - আমার বাংলা একাডেমি এডমিন')

@section('content')
<div class="space-y-6" x-data="{ showCreateModal: false }">

    <!-- Page Header -->
    <div class="flex flex-col sm:flex-row sm:items-center justify-between gap-4">
        <div>
            <div class="flex items-center gap-2 text-xs text-slate-500 mb-1">
                <a href="{{ route('admin.students') }}" class="hover:text-emerald-700">ড্যাশবোর্ড</a>
                <span>/</span>
                <span class="text-slate-800 font-semibold">হাজিরা ও উপস্থিতি</span>
            </div>
            <h1 class="text-2xl font-bold text-slate-900 flex items-center gap-2.5">
                <i class="fa-solid fa-clipboard-user text-emerald-600"></i>
                <span>লাইভ ক্লাসের হাজিরা ও উপস্থিতি ব্যবস্থাপনা</span>
            </h1>
            <p class="text-xs sm:text-sm text-slate-500 mt-1">
                শিক্ষার্থীরা ক্লাসে যোগ দিলে স্বয়ংক্রিয়ভাবে হাজিরা রেকর্ড হয়। এছাড়া এডমিন ম্যানুয়ালি যেকোনো সেশনের হাজিরা নিতে ও দেখতে পারেন।
            </p>
        </div>

        <div class="flex items-center gap-2.5 flex-wrap">
            <a href="{{ route('admin.attendance.students') }}"
               class="inline-flex items-center gap-2 bg-slate-100 hover:bg-slate-200 text-slate-700 font-semibold text-xs sm:text-sm px-4 py-2.5 rounded-xl border border-slate-300 transition">
                <i class="fa-solid fa-users-viewfinder text-slate-600"></i>
                <span>শিক্ষার্থীভিত্তিক উপস্থিতি তালিকা</span>
            </a>
            <button type="button"
                    @click="showCreateModal = true"
                    class="inline-flex items-center gap-2 bg-emerald-600 hover:bg-emerald-700 text-white font-bold text-xs sm:text-sm px-4 py-2.5 rounded-xl shadow-sm transition">
                <i class="fa-solid fa-plus"></i>
                <span>নতুন ক্লাস সেশন তৈরি</span>
            </button>
        </div>
    </div>

    <!-- 4 Stats Cards -->
    <div class="grid grid-cols-2 lg:grid-cols-4 gap-3 sm:gap-4">
        <!-- Total Sessions -->
        <div class="bg-white p-4 sm:p-5 rounded-2xl border border-slate-200 shadow-xs flex items-center gap-3.5">
            <div class="w-12 h-12 rounded-xl bg-purple-50 text-purple-600 flex items-center justify-center text-xl shrink-0">
                <i class="fa-solid fa-calendar-check"></i>
            </div>
            <div class="min-w-0">
                <div class="text-xs font-semibold text-slate-500">মোট ক্লাস সেশন</div>
                <div class="text-xl sm:text-2xl font-black text-slate-900">{{ $totalSessions }} টি</div>
            </div>
        </div>

        <!-- Active Students -->
        <div class="bg-white p-4 sm:p-5 rounded-2xl border border-slate-200 shadow-xs flex items-center gap-3.5">
            <div class="w-12 h-12 rounded-xl bg-blue-50 text-blue-600 flex items-center justify-center text-xl shrink-0">
                <i class="fa-solid fa-user-graduate"></i>
            </div>
            <div class="min-w-0">
                <div class="text-xs font-semibold text-slate-500">সক্রিয় শিক্ষার্থী</div>
                <div class="text-xl sm:text-2xl font-black text-slate-900">{{ $totalActiveStudents }} জন</div>
            </div>
        </div>

        <!-- Average Attendance Rate -->
        <div class="bg-white p-4 sm:p-5 rounded-2xl border border-slate-200 shadow-xs flex items-center gap-3.5">
            <div class="w-12 h-12 rounded-xl bg-emerald-50 text-emerald-600 flex items-center justify-center text-xl shrink-0">
                <i class="fa-solid fa-chart-pie"></i>
            </div>
            <div class="min-w-0">
                <div class="text-xs font-semibold text-slate-500">গড়ে উপস্থিতি হার</div>
                <div class="text-xl sm:text-2xl font-black {{ $avgPercentage >= 75 ? 'text-emerald-600' : 'text-amber-600' }}">
                    {{ $avgPercentage }}%
                </div>
            </div>
        </div>

        <!-- Today's Attendance -->
        <div class="bg-white p-4 sm:p-5 rounded-2xl border border-slate-200 shadow-xs flex items-center gap-3.5">
            <div class="w-12 h-12 rounded-xl bg-amber-50 text-amber-600 flex items-center justify-center text-xl shrink-0">
                <i class="fa-solid fa-clock"></i>
            </div>
            <div class="min-w-0">
                <div class="text-xs font-semibold text-slate-500">আজকের ক্লাস</div>
                @if($todaySession)
                    <div class="text-xs sm:text-sm font-bold text-slate-900">
                        {{ $todaySession->present_count }} জন উপস্থিত
                    </div>
                    <span class="text-[10px] text-emerald-600 font-semibold block">সেশন সক্রিয়</span>
                @else
                    <div class="text-xs sm:text-sm font-medium text-slate-400">আজ কোনো সেশন নেই</div>
                @endif
            </div>
        </div>
    </div>

    <!-- Sessions List Card -->
    <div class="bg-white rounded-3xl border border-slate-200 shadow-xs overflow-hidden">
        <div class="p-5 sm:p-6 border-b border-slate-100 flex flex-col sm:flex-row sm:items-center justify-between gap-3">
            <div>
                <h2 class="text-lg font-bold text-slate-900 flex items-center gap-2">
                    <i class="fa-solid fa-list-check text-emerald-600"></i>
                    <span>সকল ক্লাস সেশনের হাজিরা বিবরণী</span>
                </h2>
                <p class="text-xs text-slate-500 mt-0.5">
                    প্রতিটি ক্লাস সেশনের তারিখ, বিষয় ও উপস্থিতির বিস্তারিত শিট
                </p>
            </div>
            <span class="text-xs font-semibold text-slate-500 bg-slate-50 px-3 py-1 rounded-full border border-slate-200">
                মোট সেশন: {{ $sessions->total() }}টি
            </span>
        </div>

        @if($sessions->count() > 0)
            <div class="overflow-x-auto">
                <table class="w-full text-left border-collapse text-xs sm:text-sm">
                    <thead>
                        <tr class="bg-slate-50/75 border-b border-slate-200 text-slate-600 font-bold uppercase tracking-wider text-[11px]">
                            <th class="py-3 px-4 sm:px-6">তারিখ</th>
                            <th class="py-3 px-4 sm:px-6">ক্লাসের বিষয় ও শিরোনাম</th>
                            <th class="py-3 px-4 sm:px-6 text-center">উপস্থিতি হার</th>
                            <th class="py-3 px-4 sm:px-6 text-center">স্ট্যাটাস</th>
                            <th class="py-3 px-4 sm:px-6 text-right">কার্যক্রম</th>
                        </tr>
                    </thead>
                    <tbody class="divide-y divide-slate-100">
                        @foreach($sessions as $session)
                            @php
                                $percent = $totalActiveStudents > 0 ? round(($session->present_count / $totalActiveStudents) * 100) : 0;
                            @endphp
                            <tr class="hover:bg-slate-50/70 transition">
                                <td class="py-3.5 px-4 sm:px-6 whitespace-nowrap">
                                    <div class="font-bold text-slate-900">
                                        {{ $session->session_date->format('d M, Y') }}
                                    </div>
                                    <div class="text-[11px] text-slate-400">
                                        {{ $session->session_date->isoFormat('dddd') }}
                                    </div>
                                </td>
                                <td class="py-3.5 px-4 sm:px-6">
                                    <div class="font-bold text-slate-900 flex items-center gap-2">
                                        <span>{{ $session->title }}</span>
                                        @if($session->session_date->isToday())
                                            <span class="px-2 py-0.5 rounded-full text-[10px] font-bold bg-emerald-100 text-emerald-800 border border-emerald-200">
                                                আজকের ক্লাস
                                            </span>
                                        @endif
                                    </div>
                                    @if($session->notes)
                                        <p class="text-xs text-slate-500 mt-0.5 line-clamp-1">{{ $session->notes }}</p>
                                    @endif
                                </td>
                                <td class="py-3.5 px-4 sm:px-6 text-center whitespace-nowrap">
                                    <div class="inline-flex flex-col items-center">
                                        <span class="font-black {{ $percent >= 70 ? 'text-emerald-700' : 'text-amber-700' }} text-sm">
                                            {{ $session->present_count }} / {{ $totalActiveStudents }} জন ({{ $percent }}%)
                                        </span>
                                        <div class="w-24 bg-slate-100 rounded-full h-1.5 mt-1 overflow-hidden">
                                            <div class="h-full {{ $percent >= 70 ? 'bg-emerald-500' : 'bg-amber-500' }} rounded-full" style="width: {{ min(100, $percent) }}%"></div>
                                        </div>
                                    </div>
                                </td>
                                <td class="py-3.5 px-4 sm:px-6 text-center whitespace-nowrap">
                                    @if($session->session_date->isToday())
                                        <span class="inline-flex items-center gap-1.5 px-2.5 py-1 rounded-full text-[11px] font-bold bg-green-50 text-green-700 border border-green-200">
                                            <span class="w-1.5 h-1.5 rounded-full bg-green-500 animate-pulse"></span>
                                            চলমান
                                        </span>
                                    @else
                                        <span class="inline-flex items-center px-2.5 py-1 rounded-full text-[11px] font-semibold bg-slate-100 text-slate-700 border border-slate-200">
                                            সম্পন্ন
                                        </span>
                                    @endif
                                </td>
                                <td class="py-3.5 px-4 sm:px-6 text-right whitespace-nowrap">
                                    <div class="flex items-center justify-end gap-2">
                                        <a href="{{ route('admin.attendance.session', $session->id) }}"
                                           class="inline-flex items-center gap-1.5 bg-emerald-50 hover:bg-emerald-100 text-emerald-800 font-bold px-3 py-1.5 rounded-xl border border-emerald-200 transition">
                                            <i class="fa-solid fa-clipboard-check text-xs"></i>
                                            <span>হাজিরা শিট</span>
                                        </a>

                                        <form method="POST" action="{{ route('admin.attendance.sessions.destroy', $session->id) }}"
                                              onsubmit="return confirm('আপনি কি নিশ্চিত যে এই ক্লাস সেশন ও এর সমস্ত উপস্থিতি রেকর্ড মুছে ফেলতে চান?')">
                                            @csrf
                                            @method('DELETE')
                                            <button type="submit"
                                                    title="মুছে ফেলুন"
                                                    class="p-1.5 text-slate-400 hover:text-red-600 transition">
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

            <!-- Pagination -->
            <div class="p-4 border-t border-slate-100">
                {{ $sessions->links() }}
            </div>
        @else
            <div class="p-12 text-center">
                <div class="w-16 h-16 rounded-full bg-slate-100 text-slate-400 flex items-center justify-center mx-auto text-2xl mb-3">
                    <i class="fa-solid fa-clipboard-user"></i>
                </div>
                <h3 class="font-bold text-base text-slate-800">এখনো কোনো ক্লাস সেশন তৈরি করা হয়নি</h3>
                <p class="text-xs text-slate-500 mt-1 max-w-md mx-auto">
                    লাইভ ক্লাস শুরু হলে অথবা আপনি নিজে নতুন ক্লাস সেশন তৈরি করলে স্বয়ংক্রিয়ভাবে শিক্ষার্থীরা যুক্ত হতে পারবে এবং হাজিরা তালিকা তৈরি হবে।
                </p>
                <button type="button"
                        @click="showCreateModal = true"
                        class="mt-4 inline-flex items-center gap-2 bg-emerald-600 hover:bg-emerald-700 text-white font-bold text-xs px-4 py-2 rounded-xl transition">
                    <i class="fa-solid fa-plus"></i>
                    <span>প্রথম ক্লাস সেশন তৈরি করুন</span>
                </button>
            </div>
        @endif
    </div>

    <!-- Create Session Modal -->
    <div x-show="showCreateModal"
         x-cloak
         class="fixed inset-0 z-50 overflow-y-auto bg-slate-900/60 backdrop-blur-xs flex items-center justify-center p-4">
        <div class="bg-white rounded-3xl max-w-lg w-full p-6 shadow-2xl border border-slate-200 relative"
             @click.away="showCreateModal = false">
            <div class="flex items-center justify-between pb-4 border-b border-slate-100 mb-4">
                <h3 class="font-bold text-lg text-slate-900 flex items-center gap-2">
                    <i class="fa-solid fa-calendar-plus text-emerald-600"></i>
                    <span>নতুন ক্লাস সেশন যোগ করুন</span>
                </h3>
                <button type="button" @click="showCreateModal = false" class="text-slate-400 hover:text-slate-600 p-1">
                    <i class="fa-solid fa-xmark text-lg"></i>
                </button>
            </div>

            <form method="POST" action="{{ route('admin.attendance.sessions.store') }}" class="space-y-4">
                @csrf

                <div>
                    <label class="block text-xs font-bold text-slate-700 mb-1.5">
                        ক্লাসের বিষয় / শিরোনাম <span class="text-rose-500">*</span>
                    </label>
                    <input type="text" name="title" required
                           value="ক্লাস {{ $totalSessions + 1 }}: "
                           placeholder="যেমন: ক্লাস ১: সাংবাদিকতার পরিচিতি ও মৌলিক ধারণা"
                           class="w-full px-3.5 py-2.5 text-xs sm:text-sm bg-slate-50 border border-slate-300 rounded-xl focus:ring-2 focus:ring-emerald-500 focus:border-emerald-500 outline-none transition">
                </div>

                <div>
                    <label class="block text-xs font-bold text-slate-700 mb-1.5">
                        ক্লাসের তারিখ <span class="text-rose-500">*</span>
                    </label>
                    <input type="date" name="session_date" required
                           value="{{ date('Y-m-d') }}"
                           class="w-full px-3.5 py-2.5 text-xs sm:text-sm bg-slate-50 border border-slate-300 rounded-xl focus:ring-2 focus:ring-emerald-500 focus:border-emerald-500 outline-none transition">
                </div>

                <div>
                    <label class="block text-xs font-bold text-slate-700 mb-1.5">
                        বিশেষ নোট বা বিবরণ (ঐচ্ছিক)
                    </label>
                    <textarea name="notes" rows="2"
                              placeholder="ক্লাসের বিশেষ কোনো নির্দেশনা বা প্রাসঙ্গিক তথ্য..."
                              class="w-full px-3.5 py-2 text-xs sm:text-sm bg-slate-50 border border-slate-300 rounded-xl focus:ring-2 focus:ring-emerald-500 focus:border-emerald-500 outline-none transition"></textarea>
                </div>

                <div class="flex items-center justify-end gap-3 pt-3 border-t border-slate-100">
                    <button type="button" @click="showCreateModal = false"
                            class="px-4 py-2.5 text-xs font-semibold text-slate-600 hover:text-slate-800 bg-slate-100 hover:bg-slate-200 rounded-xl transition">
                        বাতিল
                    </button>
                    <button type="submit"
                            class="px-5 py-2.5 text-xs font-bold text-white bg-emerald-600 hover:bg-emerald-700 rounded-xl shadow-sm transition">
                        সেশন তৈরি করুন ও শিট ওপেন করুন
                    </button>
                </div>
            </form>
        </div>
    </div>

</div>
@endsection
