@extends('layouts.admin')

@section('title', 'ভর্তি হওয়া শিক্ষার্থী তালিকা - আমার বাংলা একাডেমি এডমিন')

@section('content')
<div x-data="{
    detailModalOpen: false,
    selectedStudent: null,
    copiedTrxId: null,
    openDetails(student) {
        this.selectedStudent = student;
        this.detailModalOpen = true;
    },
    copyToClipboard(text, id) {
        navigator.clipboard.writeText(text).then(() => {
            this.copiedTrxId = id;
            setTimeout(() => { this.copiedTrxId = null; }, 2000);
        });
    }
}" class="space-y-6">

    <!-- Page Header & Action Bar -->
    <div class="flex flex-col sm:flex-row sm:items-center justify-between gap-4">
        <div>
            <h1 class="text-2xl font-bold text-slate-900 flex items-center gap-2.5">
                <i class="fa-solid fa-graduation-cap text-emerald-600"></i>
                <span>কোর্সে ভর্তি হওয়া শিক্ষার্থী তালিকা</span>
            </h1>
            <p class="text-xs sm:text-sm text-slate-500 mt-1">
                “সাংবাদিকতা, আইন ও উপস্থাপনা” কোর্সে সফল পেমেন্ট সম্পন্নকারী শিক্ষার্থীদের তালিকা ও যোগাযোগের তথ্য
            </p>
        </div>

        <div class="flex items-center gap-2">
            <!-- CSV Export Button -->
            <a href="{{ route('admin.students.export', request()->query()) }}"
               class="inline-flex items-center gap-2 bg-emerald-600 hover:bg-emerald-700 text-white font-semibold text-xs sm:text-sm px-4 py-2.5 rounded-xl shadow-sm transition">
                <i class="fa-solid fa-file-csv text-base"></i>
                <span>CSV ডাউনলোড</span>
            </a>
            <!-- Refresh Page -->
            <a href="{{ route('admin.students') }}"
               title="রিফ্রেশ"
               class="inline-flex items-center justify-center w-10 h-10 bg-white hover:bg-slate-50 text-slate-600 border border-slate-200 rounded-xl shadow-sm transition">
                <i class="fa-solid fa-rotate-right"></i>
            </a>
        </div>
    </div>

    <!-- KPI Metric Cards (Purchased Students Only) -->
    <div class="grid grid-cols-2 lg:grid-cols-4 gap-3 sm:gap-4">
        <!-- Total Enrolled Students -->
        <div class="bg-white p-4 rounded-2xl border border-slate-200 shadow-sm flex items-center gap-3">
            <div class="w-12 h-12 rounded-xl bg-emerald-50 text-emerald-600 flex items-center justify-center text-xl shrink-0">
                <i class="fa-solid fa-user-graduate"></i>
            </div>
            <div>
                <div class="text-[11px] font-semibold text-slate-500 uppercase tracking-wider">মোট ভর্তি শিক্ষার্থী</div>
                <div class="text-xl sm:text-2xl font-bold text-slate-900 mt-0.5">{{ number_format($stats['total_students']) }} <span class="text-xs font-normal text-slate-500">জন</span></div>
            </div>
        </div>

        <!-- Total Revenue -->
        <div class="bg-white p-4 rounded-2xl border border-slate-200 shadow-sm flex items-center gap-3">
            <div class="w-12 h-12 rounded-xl bg-emerald-100 text-emerald-800 flex items-center justify-center text-xl shrink-0 font-bold">
                ৳
            </div>
            <div>
                <div class="text-[11px] font-semibold text-slate-500 uppercase tracking-wider">মোট সংগৃহীত ফি</div>
                <div class="text-xl sm:text-2xl font-bold text-emerald-700 mt-0.5">৳{{ number_format($stats['total_revenue']) }}</div>
            </div>
        </div>

        <!-- EPS Online Gateway -->
        <div class="bg-white p-4 rounded-2xl border border-slate-200 shadow-sm flex items-center gap-3">
            <div class="w-12 h-12 rounded-xl bg-blue-50 text-blue-600 flex items-center justify-center text-xl shrink-0">
                <i class="fa-solid fa-bolt"></i>
            </div>
            <div>
                <div class="text-[11px] font-semibold text-slate-500 uppercase tracking-wider">EPS অনলাইন পেমেন্ট</div>
                <div class="text-xl sm:text-2xl font-bold text-blue-700 mt-0.5">{{ number_format($stats['eps_count']) }} <span class="text-xs font-normal text-slate-500">জন</span></div>
            </div>
        </div>

        <!-- Mobile Banking & Others -->
        <div class="bg-white p-4 rounded-2xl border border-slate-200 shadow-sm flex items-center gap-3">
            <div class="w-12 h-12 rounded-xl bg-pink-50 text-pink-600 flex items-center justify-center text-xl shrink-0">
                <i class="fa-solid fa-mobile-screen"></i>
            </div>
            <div>
                <div class="text-[11px] font-semibold text-slate-500 uppercase tracking-wider">বিকাশ/নগদ/অন্যান্য</div>
                <div class="text-xl sm:text-2xl font-bold text-slate-800 mt-0.5">{{ number_format($stats['mobile_count']) }} <span class="text-xs font-normal text-slate-500">জন</span></div>
            </div>
        </div>
    </div>

        <!-- PENDING PAYMENTS ALERT - Students who paid but status not confirmed -->
    @if($pendingPayments->isNotEmpty())
    <div class="bg-amber-50 border border-amber-300 rounded-2xl p-5 shadow-sm">
        <div class="flex items-center gap-3 mb-4">
            <div class="w-10 h-10 rounded-xl bg-amber-100 text-amber-600 flex items-center justify-center text-lg shrink-0">
                <i class="fa-solid fa-triangle-exclamation"></i>
            </div>
            <div>
                <h2 class="text-base font-bold text-amber-900">
                    &#9888; {{ $pendingPayments->count() }}টি পেমেন্ট অনুমোদনের অপেক্ষায়
                </h2>
                <p class="text-xs text-amber-700 mt-0.5">
                    এই শিক্ষার্থীরা পেমেন্ট শুরু করেছেন কিন্তু স্ট্যাটাস <strong>pending</strong> রয়েছে।
                    পেমেন্ট যাচাই করে নিশ্চিত করুন অথবা বাতিল করুন।
                </p>
            </div>
        </div>
        <div class="space-y-3">
            @foreach($pendingPayments as $pp)
            <div class="bg-white rounded-xl border border-amber-200 p-4 flex flex-col sm:flex-row sm:items-center justify-between gap-3">
                <div class="flex items-start gap-3">
                    <div class="w-9 h-9 rounded-full bg-amber-100 text-amber-700 flex items-center justify-center text-sm font-bold shrink-0">
                        {{ strtoupper(substr($pp->student_name, 0, 1)) }}
                    </div>
                    <div class="min-w-0">
                        <p class="font-bold text-slate-900 text-sm">{{ $pp->student_name }}</p>
                        <p class="text-xs text-slate-500">{{ $pp->student_email }} &bull; {{ $pp->student_phone }}</p>
                        <div class="flex flex-wrap gap-2 mt-1">
                            <span class="text-[11px] bg-slate-100 text-slate-600 rounded px-1.5 py-0.5 font-mono">TrxID: {{ $pp->transaction_id }}</span>
                            <span class="text-[11px] bg-blue-50 text-blue-700 rounded px-1.5 py-0.5 font-semibold uppercase">{{ $pp->payment_method }}</span>
                            <span class="text-[11px] bg-emerald-50 text-emerald-700 rounded px-1.5 py-0.5 font-bold">৳{{ number_format($pp->amount) }}</span>
                            <span class="text-[11px] text-slate-400">{{ $pp->created_at ? $pp->created_at->format('d M Y, h:i A') : '' }}</span>
                        </div>
                    </div>
                </div>
                <div class="flex items-center gap-2 shrink-0">
                    <form method="POST" action="{{ route('admin.payments.update-status', $pp->id) }}" onsubmit="return confirm('পেমেন্ট নিশ্চিত করবেন?')">
                        @csrf @method('PATCH')
                        <input type="hidden" name="status" value="completed">
                        <input type="hidden" name="notes" value="Admin manually approved pending payment.">
                        <button type="submit" class="inline-flex items-center gap-1.5 px-3.5 py-2 rounded-xl bg-emerald-600 hover:bg-emerald-700 text-white text-xs font-bold shadow-sm transition">
                            <i class="fa-solid fa-circle-check"></i> পেমেন্ট নিশ্চিত করুন
                        </button>
                    </form>
                    <form method="POST" action="{{ route('admin.payments.update-status', $pp->id) }}" onsubmit="return confirm('পেমেন্ট বাতিল করবেন?')">
                        @csrf @method('PATCH')
                        <input type="hidden" name="status" value="failed">
                        <input type="hidden" name="notes" value="Admin rejected pending payment.">
                        <button type="submit" class="inline-flex items-center gap-1.5 px-3 py-2 rounded-xl bg-red-100 hover:bg-red-200 text-red-700 text-xs font-bold transition">
                            <i class="fa-solid fa-xmark"></i> বাতিল
                        </button>
                    </form>
                </div>
            </div>
            @endforeach
        </div>
    </div>
    @endif

    <!-- Search & Filter Card -->
    <div class="bg-white rounded-2xl border border-slate-200 shadow-sm p-4 sm:p-5">
        <form method="GET" action="{{ route('admin.students') }}" class="grid grid-cols-1 sm:grid-cols-2 lg:grid-cols-12 gap-3 items-end">
            <!-- Search Input -->
            <div class="lg:col-span-7">
                <label for="search" class="block text-xs font-semibold text-slate-700 mb-1.5">
                    শিক্ষার্থী অনুসন্ধান
                </label>
                <div class="relative">
                    <div class="absolute inset-y-0 left-0 pl-3.5 flex items-center pointer-events-none text-slate-400">
                        <i class="fa-solid fa-magnifying-glass text-xs"></i>
                    </div>
                    <input type="text" id="search" name="search" value="{{ request('search') }}"
                        placeholder="নাম, মোবাইল নম্বর, ইমেইল বা ট্রানজেকশন আইডি..."
                        class="w-full pl-9 pr-4 py-2 text-xs sm:text-sm bg-slate-50 border border-slate-300 rounded-xl focus:ring-2 focus:ring-emerald-500 focus:border-emerald-500 outline-none transition">
                </div>
            </div>

            <!-- Payment Method Filter -->
            <div class="lg:col-span-3">
                <label for="method" class="block text-xs font-semibold text-slate-700 mb-1.5">
                    পেমেন্ট মাধ্যম
                </label>
                <select id="method" name="method"
                    class="w-full px-3 py-2 text-xs sm:text-sm bg-slate-50 border border-slate-300 rounded-xl focus:ring-2 focus:ring-emerald-500 focus:border-emerald-500 outline-none transition">
                    <option value="">সকল মাধ্যম</option>
                    <option value="eps" {{ request('method') === 'eps' ? 'selected' : '' }}>EPS অনলাইন গেটওয়ে</option>
                    <option value="bkash" {{ request('method') === 'bkash' ? 'selected' : '' }}>বিকাশ (bKash)</option>
                    <option value="nagad" {{ request('method') === 'nagad' ? 'selected' : '' }}>নগদ (Nagad)</option>
                    <option value="rocket" {{ request('method') === 'rocket' ? 'selected' : '' }}>রকেট (Rocket)</option>
                    <option value="card" {{ request('method') === 'card' ? 'selected' : '' }}>কার্ড (Card)</option>
                </select>
            </div>

            <!-- Action Buttons -->
            <div class="lg:col-span-2 flex items-center gap-2">
                <button type="submit"
                    class="flex-1 bg-slate-900 hover:bg-slate-800 text-white font-semibold py-2 px-3 rounded-xl text-xs sm:text-sm transition flex items-center justify-center gap-1.5 shadow-sm">
                    <i class="fa-solid fa-filter text-xs"></i>
                    <span>ফিল্টার</span>
                </button>
                @if(request()->hasAny(['search', 'method', 'course_id']))
                    <a href="{{ route('admin.students') }}"
                        title="রিসেট"
                        class="bg-slate-100 hover:bg-slate-200 text-slate-600 py-2 px-3 rounded-xl text-xs sm:text-sm transition flex items-center justify-center border border-slate-200">
                        <i class="fa-solid fa-xmark"></i>
                    </a>
                @endif
            </div>
        </form>
    </div>

    <!-- Students Table Card -->
    <div class="bg-white rounded-2xl border border-slate-200 shadow-sm overflow-hidden">
        <div class="p-4 sm:p-5 border-b border-slate-100 flex flex-col sm:flex-row items-start sm:items-center justify-between gap-2">
            <div>
                <h2 class="font-bold text-slate-800 text-base flex items-center gap-2">
                    <i class="fa-solid fa-circle-check text-green-600"></i>
                    <span>সফল কোর্স ক্রয়ের শিক্ষার্থী তালিকা (মোট: {{ $payments->total() }})</span>
                </h2>
                <p class="text-xs text-slate-500 mt-0.5">পেমেন্ট সম্পন্নকারী সকল সক্রিয় শিক্ষার্থীর সম্পূর্ণ বিবরণ</p>
            </div>
            <div class="text-xs text-slate-500 bg-slate-50 px-3 py-1 rounded-lg border border-slate-200">
                পেজ: {{ $payments->currentPage() }} / {{ $payments->lastPage() > 0 ? $payments->lastPage() : 1 }}
            </div>
        </div>

        @if($payments->count() > 0)
            <!-- Responsive Table Container -->
            <div class="overflow-x-auto">
                <table class="w-full text-left border-collapse">
                    <thead>
                        <tr class="bg-slate-50/80 border-b border-slate-200 text-[11px] font-bold text-slate-600 uppercase tracking-wider">
                            <th class="py-3 px-4">#</th>
                            <th class="py-3 px-4">শিক্ষার্থী বিবরণ</th>
                            <th class="py-3 px-4">যোগাযোগ & WhatsApp</th>
                            <th class="py-3 px-4">কোর্স</th>
                            <th class="py-3 px-4 text-right">ফি (টাকা)</th>
                            <th class="py-3 px-4">পেমেন্ট মাধ্যম ও TrxID</th>
                            <th class="py-3 px-4 text-center">স্ট্যাটাস</th>
                            <th class="py-3 px-4 text-center">অ্যাকশন</th>
                        </tr>
                    </thead>
                    <tbody class="divide-y divide-slate-100 text-xs text-slate-700">
                        @foreach($payments as $index => $payment)
                            @php
                                // Clean phone number for WhatsApp URL
                                $rawPhone = preg_replace('/[^0-9]/', '', $payment->student_phone);
                                if (str_starts_with($rawPhone, '0')) {
                                    $waPhone = '880' . substr($rawPhone, 1);
                                } elseif (str_starts_with($rawPhone, '880')) {
                                    $waPhone = $rawPhone;
                                } else {
                                    $waPhone = '880' . $rawPhone;
                                }
                                $waText = urlencode("আসসালামু আলাইকুম " . $payment->student_name . ", আমার বাংলা একাডেমীর কোর্সে ভর্তির বিষয়ে আপনার সাথে যোগাযোগ করা হচ্ছে।");
                                $waUrl = "https://wa.me/{$waPhone}?text={$waText}";

                                $studentJson = htmlspecialchars(json_encode([
                                    'id' => $payment->id,
                                    'student_name' => $payment->student_name,
                                    'student_phone' => $payment->student_phone,
                                    'student_email' => $payment->student_email,
                                    'course_title' => $payment->course?->bangla_title ?? $payment->course?->title ?? 'N/A',
                                    'batch_no' => $payment->course?->batch_no ?? '১ম ব্যাচ',
                                    'amount' => $payment->amount,
                                    'payment_method' => $payment->payment_method,
                                    'transaction_id' => $payment->transaction_id,
                                    'sender_phone' => $payment->sender_phone,
                                    'status' => $payment->status,
                                    'enrollment_status' => $payment->enrollment?->status ?? 'active',
                                    'created_at' => $payment->created_at ? $payment->created_at->format('d M Y, h:i A') : 'N/A',
                                    'notes' => $payment->notes,
                                    'wa_url' => $waUrl,
                                    'update_url' => route('admin.payments.update-status', $payment->id),
                                ]), ENT_QUOTES, 'UTF-8');
                            @endphp
                            <tr class="hover:bg-slate-50/60 transition">
                                <!-- Index -->
                                <td class="py-3.5 px-4 text-slate-400 font-mono">
                                    {{ $payments->firstItem() + $index }}
                                </td>

                                <!-- Student Info -->
                                <td class="py-3.5 px-4">
                                    <div class="font-bold text-slate-900 text-sm">{{ $payment->student_name }}</div>
                                    <div class="text-slate-500 text-[11px] mt-0.5 flex items-center gap-1.5">
                                        <i class="fa-regular fa-envelope text-slate-400"></i>
                                        <span>{{ $payment->student_email }}</span>
                                    </div>
                                    <div class="text-[10px] text-slate-400 mt-1 flex items-center gap-1">
                                        <i class="fa-regular fa-calendar-check text-slate-400"></i>
                                        <span>{{ $payment->created_at ? $payment->created_at->format('d M Y, h:i A') : 'N/A' }}</span>
                                    </div>
                                </td>

                                <!-- Contact & WhatsApp -->
                                <td class="py-3.5 px-4">
                                    <div class="font-mono text-slate-900 font-semibold">{{ $payment->student_phone }}</div>
                                    <div class="flex items-center gap-1.5 mt-1.5">
                                        <!-- WhatsApp Direct Message -->
                                        <a href="{{ $waUrl }}" target="_blank"
                                           class="inline-flex items-center gap-1 bg-green-500 hover:bg-green-600 text-white text-[11px] font-semibold px-2 py-0.5 rounded-lg shadow-sm transition">
                                            <i class="fa-brands fa-whatsapp text-xs"></i>
                                            <span>WhatsApp</span>
                                        </a>
                                        <!-- Call -->
                                        <a href="tel:{{ $payment->student_phone }}"
                                           class="inline-flex items-center gap-1 bg-slate-100 hover:bg-slate-200 text-slate-700 text-[11px] font-medium px-2 py-0.5 rounded-lg border border-slate-200 transition">
                                            <i class="fa-solid fa-phone text-[10px]"></i>
                                            <span>কল</span>
                                        </a>
                                    </div>
                                </td>

                                <!-- Course -->
                                <td class="py-3.5 px-4 max-w-[200px]">
                                    <div class="font-semibold text-slate-800 truncate" title="{{ $payment->course?->bangla_title }}">
                                        {{ $payment->course?->bangla_title ?? 'সাংবাদিকতা, আইন ও উপস্থাপনা' }}
                                    </div>
                                    <span class="inline-block text-[10px] bg-slate-100 text-slate-600 px-2 py-0.5 rounded border border-slate-200 mt-1">
                                        {{ $payment->course?->batch_no ?? '১ম ব্যাচ' }}
                                    </span>
                                </td>

                                <!-- Amount -->
                                <td class="py-3.5 px-4 text-right">
                                    <span class="font-bold text-sm text-emerald-700 font-mono">
                                        ৳{{ number_format($payment->amount) }}
                                    </span>
                                </td>

                                <!-- Payment Method & Trx ID -->
                                <td class="py-3.5 px-4">
                                    <!-- Method Badge -->
                                    <div class="flex items-center gap-1.5">
                                        @if(strtolower($payment->payment_method) === 'eps')
                                            <span class="inline-flex items-center gap-1 px-2 py-0.5 rounded text-[11px] font-semibold bg-blue-50 text-blue-700 border border-blue-200">
                                                <i class="fa-solid fa-bolt text-[10px]"></i>
                                                <span>EPS অনলাইন</span>
                                            </span>
                                        @elseif(strtolower($payment->payment_method) === 'bkash')
                                            <span class="inline-flex items-center gap-1 px-2 py-0.5 rounded text-[11px] font-semibold bg-pink-50 text-pink-700 border border-pink-200">
                                                <span>বিকাশ</span>
                                            </span>
                                        @elseif(strtolower($payment->payment_method) === 'nagad')
                                            <span class="inline-flex items-center gap-1 px-2 py-0.5 rounded text-[11px] font-semibold bg-orange-50 text-orange-700 border border-orange-200">
                                                <span>নগদ</span>
                                            </span>
                                        @elseif(strtolower($payment->payment_method) === 'rocket')
                                            <span class="inline-flex items-center gap-1 px-2 py-0.5 rounded text-[11px] font-semibold bg-purple-50 text-purple-700 border border-purple-200">
                                                <span>রকেট</span>
                                            </span>
                                        @else
                                            <span class="inline-flex items-center gap-1 px-2 py-0.5 rounded text-[11px] font-semibold bg-slate-100 text-slate-700 border border-slate-200">
                                                <span>{{ strtoupper($payment->payment_method) }}</span>
                                            </span>
                                        @endif
                                    </div>

                                    <!-- Transaction ID with Copy -->
                                    <div class="mt-1 flex items-center gap-1">
                                        <span class="font-mono text-[11px] text-slate-700 select-all font-semibold">
                                            {{ $payment->transaction_id }}
                                        </span>
                                        <button type="button"
                                            @click="copyToClipboard('{{ $payment->transaction_id }}', {{ $payment->id }})"
                                            title="TrxID কপি করুন"
                                            class="text-slate-400 hover:text-emerald-600 p-0.5 transition">
                                            <i :class="copiedTrxId === {{ $payment->id }} ? 'fa-solid fa-check text-emerald-600' : 'fa-regular fa-copy'" class="text-xs"></i>
                                        </button>
                                    </div>
                                    @if($payment->sender_phone && $payment->sender_phone !== $payment->student_phone)
                                        <div class="text-[10px] text-slate-400 mt-0.5">
                                            প্রেরক: {{ $payment->sender_phone }}
                                        </div>
                                    @endif
                                </td>

                                <!-- Payment Status Badge -->
                                <td class="py-3.5 px-4 text-center">
                                    <span class="inline-flex items-center gap-1 px-2.5 py-1 rounded-full text-[11px] font-bold bg-green-50 text-green-700 border border-green-200">
                                        <i class="fa-solid fa-circle-check text-[10px]"></i>
                                        <span>পেমেন্ট সফল</span>
                                    </span>
                                </td>

                                <!-- Actions -->
                                <td class="py-3.5 px-4 text-center">
                                    <!-- View Details Modal Trigger -->
                                    <button type="button"
                                        @click="openDetails({{ $studentJson }})"
                                        title="বিস্তারিত দেখুন"
                                        class="inline-flex items-center gap-1 bg-slate-100 hover:bg-emerald-50 text-slate-700 hover:text-emerald-700 text-xs font-semibold px-2.5 py-1 rounded-lg border border-slate-200 hover:border-emerald-200 transition">
                                        <i class="fa-solid fa-eye text-xs"></i>
                                        <span>বিস্তারিত</span>
                                    </button>
                                </td>
                            </tr>
                        @endforeach
                    </tbody>
                </table>
            </div>

            <!-- Pagination Bar -->
            <div class="p-4 border-t border-slate-100 bg-slate-50/50 flex flex-col sm:flex-row items-center justify-between gap-3 text-xs text-slate-600">
                <div>
                    মোট <span class="font-bold text-slate-800">{{ $payments->total() }}</span> জন সফল শিক্ষার্থীর মধ্যে
                    <span class="font-bold text-slate-800">{{ $payments->firstItem() }}</span> থেকে
                    <span class="font-bold text-slate-800">{{ $payments->lastItem() }}</span> দেখানো হচ্ছে
                </div>
                <div>
                    {{ $payments->links() }}
                </div>
            </div>
        @else
            <!-- Empty State -->
            <div class="p-12 text-center">
                <div class="w-16 h-16 rounded-full bg-slate-100 text-slate-400 flex items-center justify-center mx-auto text-2xl mb-3">
                    <i class="fa-solid fa-user-check"></i>
                </div>
                <h3 class="text-base font-bold text-slate-700">কোনো সফল শিক্ষার্থী রেকর্ড পাওয়া যায়নি</h3>
                <p class="text-xs text-slate-500 mt-1 max-w-sm mx-auto">
                    @if(request()->hasAny(['search', 'method']))
                        আপনার সার্চ বা ফিল্টার শর্ত অনুযায়ী কোনো ফলাফল নেই। অনুগ্রহ করে অন্য কি-ওয়ার্ড বা ফিল্টার ট্রাই করুন।
                    @else
                        এখনো কোনো শিক্ষার্থী কোর্সটির ফি সফলভাবে পরিশোধ করে ভর্তি সম্পন্ন করেননি। নতুন পেমেন্ট সফল হওয়ার সাথে সাথে স্বয়ংক্রিয়ভাবে এখানে প্রদর্শিত হবে।
                    @endif
                </p>
                @if(request()->hasAny(['search', 'method']))
                    <div class="mt-4">
                        <a href="{{ route('admin.students') }}" class="inline-flex items-center gap-1.5 text-xs font-semibold text-emerald-700 hover:underline">
                            <i class="fa-solid fa-rotate-left"></i>
                            <span>ফিল্টার রিসেট করুন</span>
                        </a>
                    </div>
                @endif
            </div>
        @endif
    </div>

    <!-- Student Details Modal -->
    <div x-show="detailModalOpen"
         x-transition:enter="transition ease-out duration-200"
         x-transition:enter-start="opacity-0"
         x-transition:enter-end="opacity-100"
         x-transition:leave="transition ease-in duration-150"
         x-transition:leave-start="opacity-100"
         x-transition:leave-end="opacity-0"
         class="fixed inset-0 z-50 overflow-y-auto bg-slate-900/60 backdrop-blur-sm flex items-center justify-center p-4"
         style="display: none;">

        <div @click.away="detailModalOpen = false"
             class="bg-white rounded-3xl shadow-2xl max-w-lg w-full overflow-hidden border border-slate-200"
             x-show="detailModalOpen"
             x-transition:enter="transition ease-out duration-200 transform"
             x-transition:enter-start="opacity-0 scale-95"
             x-transition:enter-end="opacity-100 scale-100"
             x-transition:leave="transition ease-in duration-150 transform"
             x-transition:leave-start="opacity-100 scale-100"
             x-transition:leave-end="opacity-0 scale-95">

            <!-- Modal Header -->
            <div class="p-5 bg-gradient-to-r from-emerald-800 to-emerald-700 text-white flex items-center justify-between">
                <div>
                    <h3 class="font-bold text-lg" x-text="selectedStudent?.student_name"></h3>
                    <p class="text-xs text-emerald-200 mt-0.5">ভর্তি ও পেমেন্ট বিস্তারিত তথ্য</p>
                </div>
                <button type="button" @click="detailModalOpen = false" class="text-emerald-200 hover:text-white p-1 rounded-lg">
                    <i class="fa-solid fa-xmark text-lg"></i>
                </button>
            </div>

            <!-- Modal Body -->
            <div class="p-6 space-y-4 max-h-[75vh] overflow-y-auto text-xs sm:text-sm">
                <!-- Contact Action Bar -->
                <div class="flex items-center gap-2 p-3 bg-emerald-50 rounded-2xl border border-emerald-100">
                    <div class="flex-1">
                        <div class="text-[11px] text-slate-500">মোবাইল নম্বর</div>
                        <div class="font-bold text-slate-900 font-mono text-sm" x-text="selectedStudent?.student_phone"></div>
                    </div>
                    <a :href="selectedStudent?.wa_url" target="_blank"
                       class="inline-flex items-center gap-1.5 bg-green-500 hover:bg-green-600 text-white text-xs font-bold px-3 py-2 rounded-xl shadow-sm transition">
                        <i class="fa-brands fa-whatsapp text-sm"></i>
                        <span>WhatsApp এ মেসেজ</span>
                    </a>
                </div>

                <!-- Info Grid -->
                <div class="grid grid-cols-2 gap-3">
                    <div class="p-3 bg-slate-50 rounded-xl border border-slate-100">
                        <div class="text-[11px] text-slate-500">ইমেইল ঠিকানা</div>
                        <div class="font-semibold text-slate-800 break-all mt-0.5" x-text="selectedStudent?.student_email"></div>
                    </div>
                    <div class="p-3 bg-slate-50 rounded-xl border border-slate-100">
                        <div class="text-[11px] text-slate-500">ভর্তিকৃত কোর্স</div>
                        <div class="font-semibold text-slate-800 mt-0.5" x-text="selectedStudent?.course_title"></div>
                    </div>
                    <div class="p-3 bg-slate-50 rounded-xl border border-slate-100">
                        <div class="text-[11px] text-slate-500">পরিশোধিত ফি</div>
                        <div class="font-bold text-emerald-700 text-base mt-0.5">৳<span x-text="selectedStudent?.amount"></span></div>
                    </div>
                    <div class="p-3 bg-slate-50 rounded-xl border border-slate-100">
                        <div class="text-[11px] text-slate-500">পেমেন্ট মাধ্যম</div>
                        <div class="font-semibold text-slate-800 uppercase mt-0.5" x-text="selectedStudent?.payment_method"></div>
                    </div>
                    <div class="p-3 bg-slate-50 rounded-xl border border-slate-100 col-span-2">
                        <div class="text-[11px] text-slate-500">ট্রানজেকশন আইডি (Transaction ID)</div>
                        <div class="font-mono font-bold text-slate-900 text-sm mt-0.5 select-all" x-text="selectedStudent?.transaction_id"></div>
                    </div>
                    <div class="p-3 bg-slate-50 rounded-xl border border-slate-100">
                        <div class="text-[11px] text-slate-500">পেমেন্ট স্ট্যাটাস</div>
                        <div class="font-bold text-green-600 mt-0.5 flex items-center gap-1">
                            <i class="fa-solid fa-circle-check text-xs"></i>
                            <span>সফল (COMPLETED)</span>
                        </div>
                    </div>
                    <div class="p-3 bg-slate-50 rounded-xl border border-slate-100">
                        <div class="text-[11px] text-slate-500">ভর্তির সময়</div>
                        <div class="font-semibold text-slate-700 mt-0.5" x-text="selectedStudent?.created_at"></div>
                    </div>
                </div>

                <!-- Notes / Remarks if any -->
                <div class="p-3 bg-slate-50 rounded-xl border border-slate-100" x-show="selectedStudent?.notes">
                    <div class="text-[11px] text-slate-500">পেমেন্ট নোট / ভেরিফিকেশন তথ্য</div>
                    <div class="text-xs text-slate-700 mt-0.5" x-text="selectedStudent?.notes"></div>
                </div>

                <!-- Admin Remarks / Status Note Form -->
                <div class="pt-4 border-t border-slate-200">
                    <h4 class="font-bold text-slate-800 text-xs uppercase tracking-wider mb-2">এডমিন নোট বা আপডেট</h4>
                    <form :action="selectedStudent?.update_url" method="POST" class="space-y-3">
                        @csrf
                        @method('PATCH')
                        <input type="hidden" name="status" value="completed">
                        <div>
                            <label class="block text-[11px] font-semibold text-slate-600 mb-1">অ্যাডমিন নোট (যেমন: ব্যাচ বা স্টুডেন্ট আইডি নোট)</label>
                            <input type="text" name="notes" placeholder="নোট লিখুন..." class="w-full px-3 py-2 text-xs bg-slate-50 border border-slate-300 rounded-xl focus:ring-2 focus:ring-emerald-500 outline-none">
                        </div>
                        <button type="submit" class="w-full py-2.5 bg-emerald-600 hover:bg-emerald-700 text-white font-bold rounded-xl text-xs shadow-sm transition">
                            সংরক্ষণ করুন
                        </button>
                    </form>
                </div>
            </div>
        </div>
    </div>

</div>
@endsection
