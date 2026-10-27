@extends('layouts.app')

@section('title', 'অনলাইন এক্সাম ও সার্টিফিকেশন - Amar Bangla Academy')

@section('content')
<div class="bg-slate-50 py-12" x-data="{ sampleAnswer: null, submitted: false }">
    <div class="max-w-5xl mx-auto px-4 sm:px-6 lg:px-8 space-y-12">
        
        <!-- Header -->
        <div class="text-center max-w-2xl mx-auto space-y-3">
            <span class="inline-flex items-center gap-1.5 px-3 py-1 rounded-full text-xs font-bold bg-amber-100 text-amber-800">
                <i class="fa-solid fa-clipboard-check"></i>
                <span>স্ট্যান্ডার্ড LMS এক্সামিনেশন সিস্টেম</span>
            </span>
            <h1 class="text-3xl font-extrabold text-slate-900">
                লাইভ অনলাইন এক্সাম ও ভেরিফায়েড সার্টিফিকেট
            </h1>
            <p class="text-slate-600 text-sm">
                মডিউল ভিত্তিক কুইজ, প্র্যাক্টিক্যাল কোডিং চ্যালেঞ্জ এবং কিউআর কোড যুক্ত আন্তর্জাতিক সার্টিফিকেট
            </p>
        </div>

        <!-- 3 Feature Columns -->
        <div class="grid grid-cols-1 md:grid-cols-3 gap-6">
            <div class="bg-white p-6 rounded-2xl border border-slate-200 space-y-2">
                <div class="w-10 h-10 rounded-xl bg-emerald-100 text-emerald-700 flex items-center justify-center text-lg">
                    <i class="fa-solid fa-stopwatch"></i>
                </div>
                <h3 class="font-bold text-base text-slate-900">টাইমার যুক্ত অবজেক্টিভ কুইজ</h3>
                <p class="text-xs text-slate-600 leading-relaxed">
                    প্রতিটি মডিউল শেষে ২০-৩০ নম্বরের টাইমার সহ অটোমেটেড কুইজ। তাৎক্ষণিক ফলাফল ও বিস্তারিত ব্যাখ্যা।
                </p>
            </div>

            <div class="bg-white p-6 rounded-2xl border border-slate-200 space-y-2">
                <div class="w-10 h-10 rounded-xl bg-blue-100 text-blue-700 flex items-center justify-center text-lg">
                    <i class="fa-solid fa-code"></i>
                </div>
                <h3 class="font-bold text-base text-slate-900">হ্যান্ডস-অন প্রজেক্ট অ্যাসেসমেন্ট</h3>
                <p class="text-xs text-slate-600 leading-relaxed">
                    বাস্তব প্রজেক্ট গিটহাবে পুশ করে সাবমিট করতে হয়। ফ্যাকাল্টি মেম্বাররা সরাসরি কোড অডিট করে গ্রেডিং দেন।
                </p>
            </div>

            <div class="bg-white p-6 rounded-2xl border border-slate-200 space-y-2">
                <div class="w-10 h-10 rounded-xl bg-amber-100 text-amber-700 flex items-center justify-center text-lg">
                    <i class="fa-solid fa-qrcode"></i>
                </div>
                <h3 class="font-bold text-base text-slate-900">QR ভেরিফায়েবল সার্টিফিকেট</h3>
                <p class="text-xs text-slate-600 leading-relaxed">
                    উত্তীর্ণ শিক্ষার্থীদের দেওয়া হয় ইউনিক আইডি সম্বলিত সার্টিফিকেট, যা যেকোনো এমপ্লয়ার সরাসরি ভেরিফাই করতে পারে।
                </p>
            </div>
        </div>

        <!-- Interactive Sample Quiz Simulator -->
        <div class="bg-white rounded-3xl border border-slate-200 p-6 sm:p-8 space-y-6 shadow-sm">
            <div class="flex items-center justify-between pb-4 border-b border-slate-100">
                <div>
                    <span class="text-xs font-bold text-emerald-700">লাইভ কুইজ ডেমো প্রিভিউ</span>
                    <h3 class="text-lg font-bold text-slate-900">Laravel 12 & Modern Web - নমুনা প্রশ্ন</h3>
                </div>
                <span class="px-3 py-1 rounded-full text-xs font-mono font-bold bg-amber-100 text-amber-900 flex items-center gap-1.5">
                    <i class="fa-solid fa-clock"></i> সময়: ০২:০০ মিনিট
                </span>
            </div>

            <div class="space-y-4">
                <p class="text-sm font-semibold text-slate-800">
                    প্রশ্ন ১: লারাবেলে Eloquent রিলেশনে `hasManyThrough` মেথডটি সাধারণত কোন ক্ষেত্রে ব্যবহৃত হয়?
                </p>

                <div class="space-y-2 text-xs">
                    <label class="block p-3 rounded-xl border cursor-pointer transition"
                           :class="sampleAnswer === 'a' ? 'border-emerald-600 bg-emerald-50 text-emerald-900 font-semibold' : 'border-slate-200 hover:border-slate-300'">
                        <input type="radio" name="sample_quiz" value="a" x-model="sampleAnswer" class="mr-2">
                        ক) শুধুমাত্র দুটি টেবিলের মধ্যে সরাসরি ওয়ান-টু-ওয়ান সম্পর্ক স্থাপনে
                    </label>

                    <label class="block p-3 rounded-xl border cursor-pointer transition"
                           :class="sampleAnswer === 'b' ? 'border-emerald-600 bg-emerald-50 text-emerald-900 font-semibold' : 'border-slate-200 hover:border-slate-300'">
                        <input type="radio" name="sample_quiz" value="b" x-model="sampleAnswer" class="mr-2">
                        খ) একটি মধ্যবর্তী (Intermediate) মডেল বা টেবিলের মাধ্যমে দূরবর্তী মডেলের সাথে ওয়ান-টু-মেনি রিলেশন অ্যাক্সেস করতে
                    </label>

                    <label class="block p-3 rounded-xl border cursor-pointer transition"
                           :class="sampleAnswer === 'c' ? 'border-emerald-600 bg-emerald-50 text-emerald-900 font-semibold' : 'border-slate-200 hover:border-slate-300'">
                        <input type="radio" name="sample_quiz" value="c" x-model="sampleAnswer" class="mr-2">
                        গ) শুধুমাত্র ডাটাবেজ ব্যাকআপ ও মাইগ্রেশন রোলব্যাক করতে
                    </label>
                </div>

                <div class="pt-2">
                    <button type="button" @click="submitted = true" class="px-6 py-2.5 rounded-xl bg-emerald-600 hover:bg-emerald-700 text-white font-bold text-xs transition">
                        উত্তর সাবমিট করুন
                    </button>
                </div>

                <div x-show="submitted" class="p-4 rounded-xl text-xs space-y-1" :class="sampleAnswer === 'b' ? 'bg-emerald-50 text-emerald-900 border border-emerald-200' : 'bg-rose-50 text-rose-900 border border-rose-200'">
                    <template x-if="sampleAnswer === 'b'">
                        <div>
                            <strong class="font-bold flex items-center gap-1.5"><i class="fa-solid fa-circle-check text-emerald-600"></i> সঠিক উত্তর!</strong>
                            <p class="text-slate-600 mt-1">লারাবেলে `hasManyThrough` কোনো মধ্যবর্তী মডেল (যেমন: Course -> Module -> Lesson) এর মাধ্যমে সরাসরি চাইল্ড অবজেক্ট অ্যাক্সেস করার সুবিধা দেয়।</p>
                        </div>
                    </template>
                    <template x-if="sampleAnswer !== 'b'">
                        <div>
                            <strong class="font-bold flex items-center gap-1.5"><i class="fa-solid fa-circle-xmark text-rose-600"></i> ভুল উত্তর!</strong>
                            <p class="text-slate-600 mt-1">সঠিক উত্তর হলো (খ)। `hasManyThrough` মধ্যবর্তী মডেলের মাধ্যমে দূরবর্তী টেবিল বা মডেলের ডেটা ফেচ করে।</p>
                        </div>
                    </template>
                </div>
            </div>
        </div>

        <!-- Sample Certificate Preview Card -->
        <div class="bg-gradient-to-r from-slate-900 to-emerald-950 text-white rounded-3xl p-8 border border-emerald-800 flex flex-col md:flex-row items-center justify-between gap-6">
            <div class="space-y-3">
                <span class="px-3 py-1 rounded-full text-xs font-bold bg-amber-400 text-slate-950 uppercase">
                    অফিসিয়াল সার্টিফিকেট
                </span>
                <h3 class="text-2xl font-bold">ইন্ডাস্ট্রি রিকগনাইজড ডিজিটাল ক্রেডেনশিয়াল</h3>
                <p class="text-xs text-slate-300 max-w-lg leading-relaxed">
                    সফলভাবে সবগুলো উইকলি এক্সাম, মিড-টার্ম এবং ফাইনাল ক্যাপস্টোন প্রজেক্ট জমা দিলে পাবেন আপনার নামের ডিজিটাল সার্টিফিকেট, যা সরাসরি লিংকডইন প্রোফাইলে এবং সিভিতে ভেরিফিকেশন লিঙ্ক সহ প্রদর্শন করতে পারবেন।
                </p>
            </div>
            <div class="flex-shrink-0">
                <a href="{{ route('home') }}#featured-course" class="px-6 py-3.5 rounded-xl bg-emerald-500 hover:bg-emerald-600 text-white font-bold text-xs shadow-lg transition">
                    কোর্সে এনরোল করুন
                </a>
            </div>
        </div>

    </div>
</div>
@endsection
