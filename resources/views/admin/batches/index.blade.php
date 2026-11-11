@extends('layouts.admin')

@section('title', 'ব্যাচ ম্যানেজমেন্ট ও ভর্তি নিয়ন্ত্রণ - আমার বাংলা একাডেমি এডমিন')

@section('content')
<div class="space-y-6" x-data="batchManager()">

    <!-- Page Header -->
    <div class="flex flex-col sm:flex-row sm:items-center justify-between gap-4">
        <div>
            <div class="flex items-center gap-2 text-xs text-slate-500 mb-1">
                <a href="{{ route('admin.students') }}" class="hover:text-emerald-700">ড্যাশবোর্ড</a>
                <span>/</span>
                <span class="text-slate-800 font-semibold">ব্যাচ ম্যানেজমেন্ট</span>
            </div>
            <h1 class="text-2xl font-bold text-slate-900 flex items-center gap-2.5">
                <i class="fa-solid fa-layer-group text-emerald-600"></i>
                <span>ব্যাচ ম্যানেজমেন্ট ও ভর্তি নিয়ন্ত্রণ</span>
            </h1>
            <p class="text-xs sm:text-sm text-slate-500 mt-1">
                নতুন ব্যাচ তৈরি করুন, আসন সংখ্যা ও ফি নির্ধারণ করুন এবং যেকোনো সময় অনলাইন ভর্তি চালু বা বন্ধ করুন। কোর্সের মূল মডিউল ও সিলেবাস সকল ব্যাচে একই থাকবে।
            </p>
        </div>

        <div class="flex items-center gap-2.5 flex-wrap">
            <button type="button"
                    @click="showCreateModal = true"
                    class="inline-flex items-center gap-2 bg-gradient-to-r from-emerald-600 to-teal-600 hover:from-emerald-700 hover:to-teal-700 text-white font-bold text-xs sm:text-sm px-4 py-2.5 rounded-xl shadow-md shadow-emerald-600/20 transition cursor-pointer">
                <i class="fa-solid fa-plus"></i>
                <span>+ নতুন ব্যাচ তৈরি করুন</span>
            </button>

            <a href="{{ route('home') }}" target="_blank"
               class="inline-flex items-center gap-2 bg-slate-900 hover:bg-slate-800 text-white font-semibold text-xs sm:text-sm px-4 py-2.5 rounded-xl shadow-sm transition">
                <i class="fa-solid fa-globe text-emerald-400"></i>
                <span>ওয়েবসাইট দেখুন</span>
            </a>
        </div>
    </div>

    @if($errors->any())
        <div class="bg-red-50 border border-red-200 text-red-800 px-4 py-3 rounded-2xl text-xs space-y-1">
            <div class="font-bold flex items-center gap-1.5">
                <i class="fa-solid fa-triangle-exclamation"></i>
                <span>ফর্মের তথ্য যাচাই করুন:</span>
            </div>
            <ul class="list-disc pl-5">
                @foreach($errors->all() as $error)
                    <li>{{ $error }}</li>
                @endforeach
            </ul>
        </div>
    @endif

    <!-- Public Enrollment Status Banner -->
    @if($openBatch)
        <div class="bg-gradient-to-r from-emerald-950 via-slate-900 to-emerald-950 rounded-3xl border border-emerald-500/40 p-5 sm:p-6 text-white shadow-lg flex flex-col lg:flex-row lg:items-center justify-between gap-4">
            <div class="flex items-start gap-4">
                <div class="w-12 h-12 rounded-2xl bg-emerald-500/20 text-emerald-300 border border-emerald-500/40 flex items-center justify-center text-2xl shrink-0">
                    <i class="fa-solid fa-door-open"></i>
                </div>
                <div>
                    <div class="flex items-center gap-2 flex-wrap">
                        <span class="inline-flex items-center gap-1.5 px-2.5 py-0.5 rounded-full text-[11px] font-bold bg-emerald-500 text-slate-950">
                            <span class="w-1.5 h-1.5 rounded-full bg-slate-950 animate-ping"></span>
                            অনলাইন ভর্তি চালু রয়েছে (OPEN)
                        </span>
                        <span class="text-xs text-emerald-200">
                            শুরু: <strong>{{ $openBatch->formatted_start_date }}</strong>
                        </span>
                    </div>
                    <h2 class="text-lg sm:text-xl font-black text-white mt-1">
                        বর্তমানে পাবলিক ওয়েবসাইটে <span class="text-amber-300">“{{ $openBatch->name }}”</span>-এ ভর্তি চলছে
                    </h2>
                    <p class="text-xs text-slate-300 mt-1">
                        কোর্স ফি: <strong class="text-emerald-300">৳{{ number_format($openBatch->effective_price) }}</strong>
                        &bull; বাকি আসন: <strong class="text-amber-300">{{ $openBatch->seats_remaining }} / {{ $openBatch->seat_limit }}</strong>
                        &bull; <span class="text-emerald-200">কোর্সের ১২টি মডিউল ও সিলেবাস অপরিবর্তিত রয়েছে।</span>
                    </p>
                </div>
            </div>

            @php
                $checkoutSlug = $openBatch->course?->slug ?? $primaryCourse?->slug ?? 'journalism-law-presentation';
            @endphp
            <div class="flex items-center gap-2.5 shrink-0 flex-wrap">
                <a href="{{ route('courses.checkout', ['slug' => $checkoutSlug, 'batch' => $openBatch->id]) }}" target="_blank"
                   class="inline-flex items-center gap-1.5 px-4 py-2 rounded-xl bg-white/10 hover:bg-white/20 text-white text-xs font-bold border border-white/20 transition">
                    <i class="fa-solid fa-arrow-up-right-from-square"></i>
                    <span>চেকআউট দেখুন</span>
                </a>
                <form method="POST" action="{{ route('admin.batches.toggle-enrollment', $openBatch->id) }}">
                    @csrf
                    <button type="submit"
                            class="inline-flex items-center gap-1.5 px-4 py-2 rounded-xl bg-rose-600 hover:bg-rose-700 text-white text-xs font-bold shadow-sm transition">
                        <i class="fa-solid fa-lock"></i>
                        <span>{{ $openBatch->name }}-এর ভর্তি বন্ধ করুন</span>
                    </button>
                </form>
            </div>
        </div>
    @else
        <div class="bg-gradient-to-r from-slate-900 via-amber-950/90 to-slate-900 rounded-3xl border border-amber-500/40 p-5 sm:p-6 text-white shadow-lg flex flex-col lg:flex-row lg:items-center justify-between gap-4">
            <div class="flex items-start gap-4">
                <div class="w-12 h-12 rounded-2xl bg-amber-500/20 text-amber-300 border border-amber-500/40 flex items-center justify-center text-2xl shrink-0">
                    <i class="fa-solid fa-door-closed"></i>
                </div>
                <div>
                    <span class="inline-flex items-center gap-1.5 px-2.5 py-0.5 rounded-full text-[11px] font-bold bg-rose-600 text-white">
                        <i class="fa-solid fa-lock text-[10px]"></i>
                        বর্তমানে অনলাইন ভর্তি বন্ধ রয়েছে (CLOSED)
                    </span>
                    <h2 class="text-lg sm:text-xl font-black text-white mt-1.5">
                        এই মুহূর্তে কোনো ব্যাচে পাবলিক অনলাইন ভর্তি চালু নেই
                    </h2>
                    <p class="text-xs text-slate-300 mt-1">
                        ওয়েবসাইটের ভিজিটররা এখন ভর্তি বন্ধ নোটিশ দেখতে পাচ্ছেন। নতুন ব্যাচ তৈরি করে অথবা নিচের কোনো ব্যাচের “ভর্তি চালু করুন” বাটনে ক্লিক করে ভর্তি শুরু করতে পারবেন।
                    </p>
                </div>
            </div>

            <div class="shrink-0">
                <button type="button"
                        @click="showCreateModal = true"
                        class="inline-flex items-center gap-2 px-4 py-2.5 rounded-xl bg-amber-400 hover:bg-amber-300 text-slate-950 font-black text-xs shadow-md transition">
                    <i class="fa-solid fa-plus"></i>
                    <span>নতুন ব্যাচ চালু করুন</span>
                </button>
            </div>
        </div>
    @endif

    <!-- KPI Cards -->
    @php
        $totalBatchesCount = $batches->count();
        $openBatchesCount = $batches->where('enrollment_open', true)->where('is_active', true)->count();
        $totalActiveStudents = $batches->sum('active_enrollments_count');
        $totalSeatsCapacity = $batches->sum('seat_limit');
    @endphp
    <div class="grid grid-cols-2 lg:grid-cols-4 gap-3 sm:gap-4">
        <div class="bg-white p-4 rounded-2xl border border-slate-200 shadow-xs flex items-center gap-3.5">
            <div class="w-11 h-11 rounded-xl bg-emerald-50 text-emerald-600 flex items-center justify-center text-lg shrink-0">
                <i class="fa-solid fa-layer-group"></i>
            </div>
            <div>
                <div class="text-[11px] font-semibold text-slate-500">মোট ব্যাচ</div>
                <div class="text-xl font-black text-slate-900">{{ $totalBatchesCount }} টি</div>
            </div>
        </div>

        <div class="bg-white p-4 rounded-2xl border border-slate-200 shadow-xs flex items-center gap-3.5">
            <div class="w-11 h-11 rounded-xl {{ $openBatchesCount > 0 ? 'bg-green-50 text-green-600' : 'bg-rose-50 text-rose-600' }} flex items-center justify-center text-lg shrink-0">
                <i class="fa-solid {{ $openBatchesCount > 0 ? 'fa-lock-open' : 'fa-lock' }}"></i>
            </div>
            <div>
                <div class="text-[11px] font-semibold text-slate-500">ভর্তি চালু ব্যাচ</div>
                <div class="text-xl font-black {{ $openBatchesCount > 0 ? 'text-green-700' : 'text-rose-700' }}">
                    {{ $openBatchesCount > 0 ? $openBatchesCount . ' টি চালু' : 'সব বন্ধ' }}
                </div>
            </div>
        </div>

        <div class="bg-white p-4 rounded-2xl border border-slate-200 shadow-xs flex items-center gap-3.5">
            <div class="w-11 h-11 rounded-xl bg-blue-50 text-blue-600 flex items-center justify-center text-lg shrink-0">
                <i class="fa-solid fa-users"></i>
            </div>
            <div>
                <div class="text-[11px] font-semibold text-slate-500">সর্বমোট শিক্ষার্থী</div>
                <div class="text-xl font-black text-slate-900">{{ $totalActiveStudents }} জন</div>
            </div>
        </div>

        <div class="bg-white p-4 rounded-2xl border border-slate-200 shadow-xs flex items-center gap-3.5">
            <div class="w-11 h-11 rounded-xl bg-amber-50 text-amber-600 flex items-center justify-center text-lg shrink-0">
                <i class="fa-solid fa-book-open-reader"></i>
            </div>
            <div>
                <div class="text-[11px] font-semibold text-slate-500">মোট আসন সক্ষমতা</div>
                <div class="text-xl font-black text-slate-900">{{ $totalSeatsCapacity }} টি</div>
            </div>
        </div>
    </div>

    <!-- Batches Table -->
    <div class="bg-white rounded-3xl border border-slate-200 shadow-xs overflow-hidden">
        <div class="p-5 sm:p-6 border-b border-slate-100 flex flex-col sm:flex-row sm:items-center justify-between gap-3">
            <div>
                <h2 class="text-lg font-bold text-slate-900 flex items-center gap-2">
                    <i class="fa-solid fa-list-ul text-emerald-600"></i>
                    <span>সকল ব্যাচের তালিকা ও নিয়ন্ত্রণ</span>
                </h2>
                <p class="text-xs text-slate-500 mt-0.5">
                    এক ক্লিকে যেকোনো ব্যাচের ভর্তি খোলা/বন্ধ করুন, ফি বা শুরুর তারিখ পরিবর্তন করুন অথবা ব্যাচভিত্তিক শিক্ষার্থী/মিটিং/হাজিরা দেখুন
                </p>
            </div>
            <div class="text-xs font-semibold text-emerald-800 bg-emerald-50 px-3 py-1.5 rounded-xl border border-emerald-200">
                <i class="fa-solid fa-circle-info mr-1"></i>
                কোর্স: {{ $primaryCourse?->bangla_title ?? 'সাংবাদিকতা, আইন ও উপস্থাপনা' }} (১২টি মডিউল শেয়ার্ড)
            </div>
        </div>

        @if($batches->count() > 0)
            <div class="overflow-x-auto">
                <table class="w-full text-left border-collapse text-xs sm:text-sm">
                    <thead>
                        <tr class="bg-slate-50/80 border-b border-slate-200 text-slate-600 font-bold uppercase tracking-wider text-[11px]">
                            <th class="py-3 px-4 sm:px-6">ব্যাচ ও সময়সূচি</th>
                            <th class="py-3 px-4 sm:px-6">আসন ও শিক্ষার্থী</th>
                            <th class="py-3 px-4 sm:px-6">ব্যাচ ফি</th>
                            <th class="py-3 px-4 sm:px-6">ব্যাচ স্ট্যাটাস ও ভর্তি</th>
                            <th class="py-3 px-4 sm:px-6">দ্রুত লিংক</th>
                            <th class="py-3 px-4 sm:px-6 text-right">কার্যক্রম</th>
                        </tr>
                    </thead>
                    <tbody class="divide-y divide-slate-100">
                        @foreach($batches as $batch)
                            @php
                                $booked = $batch->seats_booked;
                                $limit = max(1, (int) $batch->seat_limit);
                                $fillPct = min(100, (int) round(($booked / $limit) * 100));
                                $hasLinkedData = ($batch->active_enrollments_count > 0 || $batch->completed_payments_count > 0);
                                $batchJson = [
                                    'id' => $batch->id,
                                    'name' => $batch->name,
                                    'batch_number' => $batch->batch_number,
                                    'start_date' => $batch->start_date ? $batch->start_date->format('Y-m-d') : '',
                                    'start_date_text' => $batch->start_date_text ?? '',
                                    'schedule_text' => $batch->schedule_text ?? '',
                                    'seat_limit' => $batch->seat_limit,
                                    'seats_remaining_override' => $batch->seats_remaining_override,
                                    'regular_price' => $batch->regular_price,
                                    'discount_price' => $batch->discount_price,
                                    'discount_badge_text' => $batch->discount_badge_text ?? '',
                                    'offer_label' => $batch->offer_label ?? '',
                                    'enrollment_open' => (bool) $batch->enrollment_open,
                                    'is_active' => (bool) $batch->is_active,
                                    'status' => $batch->status ?? 'upcoming',
                                    'notes' => $batch->notes ?? '',
                                ];
                            @endphp
                            <tr class="hover:bg-slate-50/70 transition {{ $batch->enrollment_open ? 'bg-emerald-50/20' : '' }}">
                                <!-- Batch Name & Dates -->
                                <td class="py-4 px-4 sm:px-6">
                                    <div class="flex items-center gap-2 flex-wrap">
                                        <span class="px-2 py-0.5 rounded-lg text-[10px] font-black bg-slate-900 text-white font-mono">
                                            #{{ $batch->batch_number }}
                                        </span>
                                        <span class="font-extrabold text-slate-900 text-sm">{{ $batch->name }}</span>
                                    </div>
                                    <div class="text-xs text-slate-600 mt-1 flex items-center gap-1.5">
                                        <i class="fa-regular fa-calendar text-emerald-600 text-[11px]"></i>
                                        <span>শুরু: <strong>{{ $batch->formatted_start_date }}</strong></span>
                                    </div>
                                    <div class="text-[11px] text-slate-400 mt-0.5 line-clamp-1">
                                        <i class="fa-regular fa-clock text-[10px] mr-1"></i>
                                        {{ $batch->formatted_schedule }}
                                    </div>
                                </td>

                                <!-- Seats & Enrollments -->
                                <td class="py-4 px-4 sm:px-6 whitespace-nowrap">
                                    <div class="font-bold text-slate-800 text-xs">
                                        বুকড: {{ $booked }} / {{ $batch->seat_limit }} আসন
                                        @if(!is_null($batch->seats_remaining_override))
                                            <span class="text-[10px] text-amber-700 font-semibold">(কাস্টম)</span>
                                        @endif
                                    </div>
                                    <div class="w-28 h-1.5 bg-slate-100 rounded-full overflow-hidden mt-1.5">
                                        <div class="h-full bg-emerald-500 rounded-full" style="width: {{ $fillPct }}%"></div>
                                    </div>
                                    <div class="text-[11px] text-slate-500 mt-1">
                                        বাকি আসন: <strong class="text-emerald-700">{{ $batch->seats_remaining }}</strong>
                                        &bull; প্রকৃত ভর্তি: <strong>{{ $batch->active_enrollments_count }} জন</strong>
                                    </div>
                                </td>

                                <!-- Fee -->
                                <td class="py-4 px-4 sm:px-6 whitespace-nowrap">
                                    <div class="font-black text-emerald-700 text-sm">
                                        ৳{{ number_format($batch->effective_price) }}
                                    </div>
                                    @if($batch->effective_regular_price > $batch->effective_price)
                                        <div class="text-[11px] text-slate-400 line-through">
                                            ৳{{ number_format($batch->effective_regular_price) }}
                                        </div>
                                    @endif
                                    @if($batch->offer_label)
                                        <div class="text-[10px] text-amber-700 font-semibold mt-0.5 max-w-[160px] truncate" title="{{ $batch->offer_label }}">
                                            {{ $batch->offer_label }}
                                        </div>
                                    @endif
                                </td>

                                <!-- Status & Enrollment Toggle -->
                                <td class="py-4 px-4 sm:px-6 whitespace-nowrap space-y-1.5">
                                    <div>
                                        @if($batch->status === 'upcoming')
                                            <span class="inline-flex items-center px-2.5 py-0.5 rounded-full text-[11px] font-bold bg-blue-50 text-blue-700 border border-blue-200">
                                                আসন্ন / ভর্তি চলছে
                                            </span>
                                        @elseif($batch->status === 'running')
                                            <span class="inline-flex items-center px-2.5 py-0.5 rounded-full text-[11px] font-bold bg-purple-50 text-purple-700 border border-purple-200">
                                                ক্লাস চলমান
                                            </span>
                                        @else
                                            <span class="inline-flex items-center px-2.5 py-0.5 rounded-full text-[11px] font-bold bg-slate-100 text-slate-700 border border-slate-200">
                                                সম্পন্ন
                                            </span>
                                        @endif
                                    </div>

                                    <div>
                                        @if($batch->enrollment_open && $batch->is_active)
                                            <span class="inline-flex items-center gap-1 px-2.5 py-0.5 rounded-full text-[11px] font-bold bg-emerald-100 text-emerald-800 border border-emerald-300">
                                                <span class="w-1.5 h-1.5 rounded-full bg-emerald-600 animate-pulse"></span>
                                                ভর্তি চালু (OPEN)
                                            </span>
                                        @else
                                            <span class="inline-flex items-center gap-1 px-2.5 py-0.5 rounded-full text-[11px] font-bold bg-rose-50 text-rose-700 border border-rose-200">
                                                <i class="fa-solid fa-lock text-[9px]"></i>
                                                ভর্তি বন্ধ (CLOSED)
                                            </span>
                                        @endif
                                    </div>
                                </td>

                                <!-- Quick Links -->
                                <td class="py-4 px-4 sm:px-6">
                                    <div class="flex flex-wrap gap-1.5">
                                        <a href="{{ route('admin.students', ['batch_id' => $batch->id]) }}"
                                           class="inline-flex items-center gap-1 px-2 py-1 rounded-lg bg-slate-100 hover:bg-emerald-50 text-slate-700 hover:text-emerald-800 text-[11px] font-semibold border border-slate-200 transition">
                                            <i class="fa-solid fa-user-graduate text-[10px]"></i>
                                            <span>শিক্ষার্থী ({{ $batch->active_enrollments_count }})</span>
                                        </a>
                                        <a href="{{ route('admin.live-meetings', ['batch_id' => $batch->id]) }}"
                                           class="inline-flex items-center gap-1 px-2 py-1 rounded-lg bg-slate-100 hover:bg-blue-50 text-slate-700 hover:text-blue-800 text-[11px] font-semibold border border-slate-200 transition">
                                            <i class="fa-solid fa-video text-[10px]"></i>
                                            <span>মিটিং ({{ $batch->meetings_count }})</span>
                                        </a>
                                        <a href="{{ route('admin.attendance.index', ['batch_id' => $batch->id]) }}"
                                           class="inline-flex items-center gap-1 px-2 py-1 rounded-lg bg-slate-100 hover:bg-purple-50 text-slate-700 hover:text-purple-800 text-[11px] font-semibold border border-slate-200 transition">
                                            <i class="fa-solid fa-clipboard-user text-[10px]"></i>
                                            <span>হাজিরা ({{ $batch->sessions_count }})</span>
                                        </a>
                                    </div>
                                </td>

                                <!-- Actions -->
                                <td class="py-4 px-4 sm:px-6 text-right whitespace-nowrap">
                                    <div class="flex items-center justify-end gap-2">
                                        <form method="POST" action="{{ route('admin.batches.toggle-enrollment', $batch->id) }}" class="inline">
                                            @csrf
                                            @if($batch->enrollment_open)
                                                <button type="submit"
                                                        class="inline-flex items-center gap-1.5 px-3 py-1.5 rounded-xl text-xs font-bold bg-rose-50 hover:bg-rose-100 text-rose-700 border border-rose-200 transition">
                                                    <i class="fa-solid fa-lock text-[10px]"></i>
                                                    <span>ভর্তি বন্ধ করুন</span>
                                                </button>
                                            @else
                                                <button type="submit"
                                                        class="inline-flex items-center gap-1.5 px-3 py-1.5 rounded-xl text-xs font-bold bg-emerald-600 hover:bg-emerald-700 text-white shadow-xs transition">
                                                    <i class="fa-solid fa-lock-open text-[10px]"></i>
                                                    <span>ভর্তি চালু করুন</span>
                                                </button>
                                            @endif
                                        </form>

                                        <button type="button"
                                                @click='openEdit(@json($batchJson))'
                                                class="p-2 text-slate-500 hover:text-emerald-700 hover:bg-emerald-50 rounded-xl border border-transparent hover:border-emerald-200 transition"
                                                title="ব্যাচ সম্পাদনা করুন">
                                            <i class="fa-regular fa-pen-to-square"></i>
                                        </button>

                                        @if(!$hasLinkedData)
                                            <form method="POST" action="{{ route('admin.batches.destroy', $batch->id) }}"
                                                  onsubmit="return confirm('আপনি কি নিশ্চিত যে ব্যাচ {{ $batch->name }} মুছে ফেলতে চান?')" class="inline">
                                                @csrf
                                                @method('DELETE')
                                                <button type="submit"
                                                        title="ব্যাচ মুছে ফেলুন"
                                                        class="p-2 text-slate-400 hover:text-rose-600 hover:bg-rose-50 rounded-xl transition">
                                                    <i class="fa-regular fa-trash-can"></i>
                                                </button>
                                            </form>
                                        @else
                                            <span title="ভর্তিকৃত শিক্ষার্থী/পেমেন্ট থাকায় মুছে ফেলা যাবে না"
                                                  class="p-2 text-slate-300 cursor-not-allowed">
                                                <i class="fa-solid fa-shield-halved text-xs"></i>
                                            </span>
                                        @endif
                                    </div>
                                </td>
                            </tr>
                        @endforeach
                    </tbody>
                </table>
            </div>
        @else
            <div class="p-12 text-center">
                <div class="w-16 h-16 rounded-full bg-slate-100 text-slate-400 flex items-center justify-center mx-auto text-2xl mb-3">
                    <i class="fa-solid fa-layer-group"></i>
                </div>
                <h3 class="font-bold text-base text-slate-800">এখনো কোনো ব্যাচ তৈরি করা হয়নি</h3>
                <p class="text-xs text-slate-500 mt-1">
                    নতুন ব্যাচ তৈরি করে আসন, সময়সূচি ও ফি নির্ধারণ করুন।
                </p>
            </div>
        @endif
    </div>

    <!-- Create Batch Modal -->
    <div x-show="showCreateModal"
         x-cloak
         style="display: none;"
         class="fixed inset-0 z-50 overflow-y-auto bg-slate-900/60 backdrop-blur-xs flex items-center justify-center p-4">
        <div class="bg-white rounded-3xl max-w-2xl w-full p-6 shadow-2xl border border-slate-200 relative max-h-[90vh] overflow-y-auto"
             @click.away="showCreateModal = false">
            <div class="flex items-center justify-between pb-4 border-b border-slate-100 mb-4">
                <div>
                    <h3 class="font-bold text-lg text-slate-900 flex items-center gap-2">
                        <i class="fa-solid fa-layer-group text-emerald-600"></i>
                        <span>নতুন ব্যাচ তৈরি করুন</span>
                    </h3>
                    <p class="text-xs text-slate-500 mt-0.5">নতুন ব্যাচের জন্যও কোর্সের মূল মডিউল, লেকচার ও প্রশিক্ষকবৃন্দ একই থাকবে</p>
                </div>
                <button type="button" @click="showCreateModal = false" class="text-slate-400 hover:text-slate-600 p-1">
                    <i class="fa-solid fa-xmark text-lg"></i>
                </button>
            </div>

            <form method="POST" action="{{ route('admin.batches.store') }}" class="space-y-4 text-xs sm:text-sm">
                @csrf
                <input type="hidden" name="course_id" value="{{ $primaryCourse?->id }}">

                <div class="grid grid-cols-1 sm:grid-cols-3 gap-3">
                    <div class="sm:col-span-2">
                        <label class="block text-xs font-bold text-slate-700 mb-1.5">
                            ব্যাচের নাম <span class="text-rose-500">*</span>
                        </label>
                        <input type="text" name="name" required
                               placeholder="যেমন: ২য় ব্যাচ"
                               class="w-full px-3.5 py-2.5 bg-slate-50 border border-slate-300 rounded-xl focus:ring-2 focus:ring-emerald-500 outline-none">
                    </div>
                    <div>
                        <label class="block text-xs font-bold text-slate-700 mb-1.5">
                            ব্যাচ নম্বর
                        </label>
                        <input type="number" name="batch_number" min="1"
                               value="{{ ($batches->max('batch_number') ?? 0) + 1 }}"
                               class="w-full px-3.5 py-2.5 bg-slate-50 border border-slate-300 rounded-xl focus:ring-2 focus:ring-emerald-500 outline-none font-mono">
                    </div>
                </div>

                <div class="grid grid-cols-1 sm:grid-cols-2 gap-3">
                    <div>
                        <label class="block text-xs font-bold text-slate-700 mb-1.5">ক্লাস শুরুর তারিখ (ক্যালেন্ডার)</label>
                        <input type="date" name="start_date"
                               class="w-full px-3.5 py-2.5 bg-slate-50 border border-slate-300 rounded-xl focus:ring-2 focus:ring-emerald-500 outline-none">
                    </div>
                    <div>
                        <label class="block text-xs font-bold text-slate-700 mb-1.5">
                            তারিখ বাংলা টেক্সট <span class="text-slate-400 font-normal">(ঐচ্ছিক)</span>
                        </label>
                        <input type="text" name="start_date_text"
                               placeholder="যেমন: ১৫ ডিসেম্বর ২০২৬"
                               class="w-full px-3.5 py-2.5 bg-slate-50 border border-slate-300 rounded-xl focus:ring-2 focus:ring-emerald-500 outline-none">
                    </div>
                </div>

                <div>
                    <label class="block text-xs font-bold text-slate-700 mb-1.5">ক্লাসের সময়সূচি (Schedule)</label>
                    <input type="text" name="schedule_text"
                           value="{{ $primaryCourse?->live_schedule_text ?: 'প্রতি বৃহস্পতি, শুক্র ও শনিবার রাত ৯:০০ টা' }}"
                           class="w-full px-3.5 py-2.5 bg-slate-50 border border-slate-300 rounded-xl focus:ring-2 focus:ring-emerald-500 outline-none">
                </div>

                <div class="grid grid-cols-1 sm:grid-cols-3 gap-3">
                    <div>
                        <label class="block text-xs font-bold text-slate-700 mb-1.5">মোট আসন সংখ্যা</label>
                        <input type="number" name="seat_limit" min="1" value="50"
                               class="w-full px-3.5 py-2.5 bg-slate-50 border border-slate-300 rounded-xl focus:ring-2 focus:ring-emerald-500 outline-none font-mono">
                    </div>
                    <div>
                        <label class="block text-xs font-bold text-slate-700 mb-1.5">
                            বাকি আসন (ম্যানুয়াল ওভাররাইড)
                        </label>
                        <input type="number" name="seats_remaining_override" min="0" placeholder="স্বয়ংক্রিয়"
                               class="w-full px-3.5 py-2.5 bg-slate-50 border border-slate-300 rounded-xl focus:ring-2 focus:ring-emerald-500 outline-none font-mono">
                    </div>
                    <div>
                        <label class="block text-xs font-bold text-slate-700 mb-1.5">ব্যাচ অবস্থা</label>
                        <select name="status" class="w-full px-3.5 py-2.5 bg-slate-50 border border-slate-300 rounded-xl focus:ring-2 focus:ring-emerald-500 outline-none">
                            <option value="upcoming">আসন্ন / ভর্তি চলছে</option>
                            <option value="running">ক্লাস চলমান</option>
                            <option value="completed">সম্পন্ন</option>
                        </select>
                    </div>
                </div>

                <div class="grid grid-cols-1 sm:grid-cols-2 gap-3">
                    <div>
                        <label class="block text-xs font-bold text-slate-700 mb-1.5">রেগুলার ফি (টাকা)</label>
                        <input type="number" name="regular_price" min="0" step="1"
                               value="{{ (int) ($primaryCourse?->regular_price ?? 3000) }}"
                               class="w-full px-3.5 py-2.5 bg-slate-50 border border-slate-300 rounded-xl focus:ring-2 focus:ring-emerald-500 outline-none font-mono">
                    </div>
                    <div>
                        <label class="block text-xs font-bold text-slate-700 mb-1.5">ডিসকাউন্ট ফি (ভর্তি ফি)</label>
                        <input type="number" name="discount_price" min="0" step="1"
                               value="{{ (int) ($primaryCourse?->discount_price ?? 999) }}"
                               class="w-full px-3.5 py-2.5 bg-slate-50 border border-slate-300 rounded-xl focus:ring-2 focus:ring-emerald-500 outline-none font-mono font-bold text-emerald-700">
                    </div>
                </div>

                <div class="grid grid-cols-1 sm:grid-cols-2 gap-3">
                    <div>
                        <label class="block text-xs font-bold text-slate-700 mb-1.5">অফার/ডিসকাউন্ট ব্যাজ টেক্সট (ঐচ্ছিক)</label>
                        <input type="text" name="discount_badge_text" placeholder="যেমন: ২য় ব্যাচে বিশেষ ছাড়!"
                               class="w-full px-3.5 py-2.5 bg-slate-50 border border-slate-300 rounded-xl focus:ring-2 focus:ring-emerald-500 outline-none">
                    </div>
                    <div>
                        <label class="block text-xs font-bold text-slate-700 mb-1.5">অফার লেবেল (ঐচ্ছিক)</label>
                        <input type="text" name="offer_label" placeholder="যেমন: ২য় ব্যাচের প্রথম ৫০ জনের জন্য"
                               class="w-full px-3.5 py-2.5 bg-slate-50 border border-slate-300 rounded-xl focus:ring-2 focus:ring-emerald-500 outline-none">
                    </div>
                </div>

                <div class="p-3.5 bg-emerald-50 border border-emerald-200 rounded-2xl space-y-2">
                    <label class="flex items-center gap-2 text-xs font-bold text-emerald-900 cursor-pointer">
                        <input type="checkbox" name="enrollment_open" value="1" checked
                               class="w-4 h-4 text-emerald-600 rounded border-slate-300 focus:ring-emerald-500">
                        <span>নতুন ব্যাচে অনলাইন ভর্তি এখনই চালু (OPEN) করুন</span>
                    </label>
                    <label class="flex items-center gap-2 text-xs font-medium text-emerald-800 cursor-pointer pl-6">
                        <input type="checkbox" name="close_other_batches" value="1" checked
                               class="w-4 h-4 text-emerald-600 rounded border-slate-300 focus:ring-emerald-500">
                        <span>পূর্ববর্তী অন্যান্য ব্যাচের ভর্তি স্বয়ংক্রিয়ভাবে বন্ধ করুন</span>
                    </label>
                </div>

                <div class="flex items-center justify-end gap-3 pt-3 border-t border-slate-100">
                    <button type="button" @click="showCreateModal = false"
                            class="px-4 py-2 text-xs font-semibold text-slate-600 hover:text-slate-800 bg-slate-100 hover:bg-slate-200 rounded-xl transition">
                        বাতিল
                    </button>
                    <button type="submit"
                            class="px-5 py-2.5 text-xs font-bold text-white bg-emerald-600 hover:bg-emerald-700 rounded-xl shadow-sm transition">
                        ব্যাচ তৈরি ও সংরক্ষণ করুন
                    </button>
                </div>
            </form>
        </div>
    </div>

    <!-- Edit Batch Modal -->
    <div x-show="showEditModal"
         x-cloak
         style="display: none;"
         class="fixed inset-0 z-50 overflow-y-auto bg-slate-900/60 backdrop-blur-xs flex items-center justify-center p-4">
        <div class="bg-white rounded-3xl max-w-2xl w-full p-6 shadow-2xl border border-slate-200 relative max-h-[90vh] overflow-y-auto"
             @click.away="showEditModal = false">
            <div class="flex items-center justify-between pb-4 border-b border-slate-100 mb-4">
                <h3 class="font-bold text-lg text-slate-900 flex items-center gap-2">
                    <i class="fa-solid fa-pen-to-square text-emerald-600"></i>
                    <span>ব্যাচের তথ্য হালনাগাদ করুন (<span x-text="editForm.name"></span>)</span>
                </h3>
                <button type="button" @click="showEditModal = false" class="text-slate-400 hover:text-slate-600 p-1">
                    <i class="fa-solid fa-xmark text-lg"></i>
                </button>
            </div>

            <form method="POST" :action="editForm.actionUrl" class="space-y-4 text-xs sm:text-sm">
                @csrf
                @method('PUT')

                <div class="grid grid-cols-1 sm:grid-cols-3 gap-3">
                    <div class="sm:col-span-2">
                        <label class="block text-xs font-bold text-slate-700 mb-1.5">
                            ব্যাচের নাম <span class="text-rose-500">*</span>
                        </label>
                        <input type="text" name="name" required x-model="editForm.name"
                               class="w-full px-3.5 py-2.5 bg-slate-50 border border-slate-300 rounded-xl focus:ring-2 focus:ring-emerald-500 outline-none">
                    </div>
                    <div>
                        <label class="block text-xs font-bold text-slate-700 mb-1.5">ব্যাচ নম্বর</label>
                        <input type="number" name="batch_number" min="1" x-model="editForm.batch_number"
                               class="w-full px-3.5 py-2.5 bg-slate-50 border border-slate-300 rounded-xl focus:ring-2 focus:ring-emerald-500 outline-none font-mono">
                    </div>
                </div>

                <div class="grid grid-cols-1 sm:grid-cols-2 gap-3">
                    <div>
                        <label class="block text-xs font-bold text-slate-700 mb-1.5">ক্লাস শুরুর তারিখ</label>
                        <input type="date" name="start_date" x-model="editForm.start_date"
                               class="w-full px-3.5 py-2.5 bg-slate-50 border border-slate-300 rounded-xl focus:ring-2 focus:ring-emerald-500 outline-none">
                    </div>
                    <div>
                        <label class="block text-xs font-bold text-slate-700 mb-1.5">তারিখ বাংলা টেক্সট</label>
                        <input type="text" name="start_date_text" x-model="editForm.start_date_text"
                               placeholder="যেমন: ৫ নভেম্বর ২০২৬"
                               class="w-full px-3.5 py-2.5 bg-slate-50 border border-slate-300 rounded-xl focus:ring-2 focus:ring-emerald-500 outline-none">
                    </div>
                </div>

                <div>
                    <label class="block text-xs font-bold text-slate-700 mb-1.5">ক্লাসের সময়সূচি (Schedule)</label>
                    <input type="text" name="schedule_text" x-model="editForm.schedule_text"
                           class="w-full px-3.5 py-2.5 bg-slate-50 border border-slate-300 rounded-xl focus:ring-2 focus:ring-emerald-500 outline-none">
                </div>

                <div class="grid grid-cols-1 sm:grid-cols-3 gap-3">
                    <div>
                        <label class="block text-xs font-bold text-slate-700 mb-1.5">মোট আসন সংখ্যা</label>
                        <input type="number" name="seat_limit" min="1" x-model="editForm.seat_limit"
                               class="w-full px-3.5 py-2.5 bg-slate-50 border border-slate-300 rounded-xl focus:ring-2 focus:ring-emerald-500 outline-none font-mono">
                    </div>
                    <div>
                        <label class="block text-xs font-bold text-slate-700 mb-1.5">বাকি আসন ওভাররাইড</label>
                        <input type="number" name="seats_remaining_override" min="0" x-model="editForm.seats_remaining_override" placeholder="খালি রাখলে অটো"
                               class="w-full px-3.5 py-2.5 bg-slate-50 border border-slate-300 rounded-xl focus:ring-2 focus:ring-emerald-500 outline-none font-mono">
                    </div>
                    <div>
                        <label class="block text-xs font-bold text-slate-700 mb-1.5">ব্যাচ অবস্থা</label>
                        <select name="status" x-model="editForm.status"
                                class="w-full px-3.5 py-2.5 bg-slate-50 border border-slate-300 rounded-xl focus:ring-2 focus:ring-emerald-500 outline-none">
                            <option value="upcoming">আসন্ন / ভর্তি চলছে</option>
                            <option value="running">ক্লাস চলমান</option>
                            <option value="completed">সম্পন্ন</option>
                        </select>
                    </div>
                </div>

                <div class="grid grid-cols-1 sm:grid-cols-2 gap-3">
                    <div>
                        <label class="block text-xs font-bold text-slate-700 mb-1.5">রেগুলার ফি (টাকা)</label>
                        <input type="number" name="regular_price" min="0" step="1" x-model="editForm.regular_price"
                               class="w-full px-3.5 py-2.5 bg-slate-50 border border-slate-300 rounded-xl focus:ring-2 focus:ring-emerald-500 outline-none font-mono">
                    </div>
                    <div>
                        <label class="block text-xs font-bold text-slate-700 mb-1.5">ডিসকাউন্ট ফি (ভর্তি ফি)</label>
                        <input type="number" name="discount_price" min="0" step="1" x-model="editForm.discount_price"
                               class="w-full px-3.5 py-2.5 bg-slate-50 border border-slate-300 rounded-xl focus:ring-2 focus:ring-emerald-500 outline-none font-mono font-bold text-emerald-700">
                    </div>
                </div>

                <div class="grid grid-cols-1 sm:grid-cols-2 gap-3">
                    <div>
                        <label class="block text-xs font-bold text-slate-700 mb-1.5">অফার/ডিসকাউন্ট ব্যাজ টেক্সট</label>
                        <input type="text" name="discount_badge_text" x-model="editForm.discount_badge_text"
                               class="w-full px-3.5 py-2.5 bg-slate-50 border border-slate-300 rounded-xl focus:ring-2 focus:ring-emerald-500 outline-none">
                    </div>
                    <div>
                        <label class="block text-xs font-bold text-slate-700 mb-1.5">অফার লেবেল</label>
                        <input type="text" name="offer_label" x-model="editForm.offer_label"
                               class="w-full px-3.5 py-2.5 bg-slate-50 border border-slate-300 rounded-xl focus:ring-2 focus:ring-emerald-500 outline-none">
                    </div>
                </div>

                <div class="p-3.5 bg-slate-50 border border-slate-200 rounded-2xl grid grid-cols-1 sm:grid-cols-2 gap-3">
                    <label class="flex items-center gap-2 text-xs font-bold text-slate-800 cursor-pointer">
                        <input type="checkbox" name="enrollment_open" value="1" x-model="editForm.enrollment_open"
                               class="w-4 h-4 text-emerald-600 rounded border-slate-300 focus:ring-emerald-500">
                        <span>অনলাইন ভর্তি চালু (OPEN) রাখুন</span>
                    </label>
                    <label class="flex items-center gap-2 text-xs font-bold text-slate-800 cursor-pointer">
                        <input type="checkbox" name="is_active" value="1" x-model="editForm.is_active"
                               class="w-4 h-4 text-emerald-600 rounded border-slate-300 focus:ring-emerald-500">
                        <span>ব্যাচ সক্রিয় (Active)</span>
                    </label>
                </div>

                <div class="flex items-center justify-end gap-3 pt-3 border-t border-slate-100">
                    <button type="button" @click="showEditModal = false"
                            class="px-4 py-2 text-xs font-semibold text-slate-600 hover:text-slate-800 bg-slate-100 hover:bg-slate-200 rounded-xl transition">
                        বাতিল
                    </button>
                    <button type="submit"
                            class="px-5 py-2.5 text-xs font-bold text-white bg-emerald-600 hover:bg-emerald-700 rounded-xl shadow-sm transition">
                        আপডেট সংরক্ষণ করুন
                    </button>
                </div>
            </form>
        </div>
    </div>

</div>

<script>
function batchManager() {
    return {
        showCreateModal: false,
        showEditModal: false,
        editForm: {
            id: null,
            name: '',
            batch_number: 1,
            start_date: '',
            start_date_text: '',
            schedule_text: '',
            seat_limit: 50,
            seats_remaining_override: '',
            regular_price: 3000,
            discount_price: 999,
            discount_badge_text: '',
            offer_label: '',
            enrollment_open: false,
            is_active: true,
            status: 'upcoming',
            notes: '',
            actionUrl: ''
        },
        openEdit(batch) {
            this.editForm = {
                id: batch.id,
                name: batch.name || '',
                batch_number: batch.batch_number || 1,
                start_date: batch.start_date || '',
                start_date_text: batch.start_date_text || '',
                schedule_text: batch.schedule_text || '',
                seat_limit: batch.seat_limit || 50,
                seats_remaining_override: batch.seats_remaining_override ?? '',
                regular_price: batch.regular_price ?? 3000,
                discount_price: batch.discount_price ?? 999,
                discount_badge_text: batch.discount_badge_text || '',
                offer_label: batch.offer_label || '',
                enrollment_open: Boolean(batch.enrollment_open),
                is_active: Boolean(batch.is_active),
                status: batch.status || 'upcoming',
                notes: batch.notes || '',
                actionUrl: '{{ url('/admin/batches') }}/' + batch.id
            };
            this.showEditModal = true;
        }
    };
}

document.addEventListener('alpine:init', () => {
    if (window.Alpine) {
        window.Alpine.data('batchManager', batchManager);
    }
});
</script>
@endsection
