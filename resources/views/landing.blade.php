@extends('layouts.app')

@section('title', 'সাংবাদিকতা, আইন ও উপস্থাপনা - আমার বাংলা একাডেমি ও ন্যাশনাল জার্নালিস্ট ক্লাব')

@section('meta_description', 'আমার বাংলা একাডেমী নিয়ে এসেছে ১ মাসের ব্যবহারিক অনলাইন প্রশিক্ষণ কোর্স: সাংবাদিকতা, আইন ও উপস্থাপনা। প্রশিক্ষক: সাংবাদিক নাজীর আহম্মেদ খান, ব্যারিস্টার মেহেদী হাসান ও আরিফ সরকার।')

@section('content')

<!-- Facebook SDK Script for Embedded Reel -->
<div id="fb-root"></div>
<script async defer crossorigin="anonymous" src="https://connect.facebook.net/bn_IN/sdk.js#xfbml=1&version=v20.0"></script>

<!-- ==================== 1. HERO SECTION (MOBILE-FIRST OPTIMIZED) ==================== -->
<section class="relative overflow-hidden bg-gradient-to-b from-slate-950 via-emerald-950 to-slate-950 text-white pt-6 pb-16 sm:pt-14 sm:pb-24">
    <!-- Ambient Glow / Mesh background -->
    <div class="absolute inset-0 opacity-20 pointer-events-none">
        <div class="absolute -top-20 -left-20 w-72 h-72 sm:w-96 sm:h-96 bg-emerald-500 rounded-full blur-3xl"></div>
        <div class="absolute top-1/2 -right-20 w-72 h-72 sm:w-96 sm:h-96 bg-amber-500 rounded-full blur-3xl"></div>
        <div class="absolute -bottom-10 left-1/3 w-64 h-64 bg-teal-400 rounded-full blur-3xl"></div>
    </div>

    <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8 relative z-10">
        
        <!-- Patronage & Management Top Badge (Clean & Responsive) -->
        <div class="text-center mb-5 sm:mb-8">
            <div class="inline-flex flex-col sm:flex-row items-center justify-center gap-1.5 sm:gap-3 px-3.5 sm:px-5 py-2 rounded-2xl sm:rounded-full bg-slate-900/90 border border-emerald-500/40 text-emerald-300 text-xs sm:text-sm font-semibold shadow-md">
                <span class="flex items-center gap-1.5 text-amber-400 font-bold">
                    <i class="fa-solid fa-award"></i> পৃষ্ঠপোষকতায়: ন্যাশনাল জার্নালিস্ট ক্লাব
                </span>
                <span class="text-slate-600 hidden sm:inline">•</span>
                <span class="flex items-center gap-1.5 text-emerald-300 text-[11px] sm:text-xs">
                    <i class="fa-solid fa-user-tie text-emerald-400"></i> ব্যবস্থাপনায়: সাংবাদিক নাজীর আহম্মেদ খান
                </span>
            </div>
        </div>

        <!-- Hero Content Grid: Balanced for Desktop, Streamlined for Mobile -->
        <div class="grid grid-cols-1 lg:grid-cols-12 gap-8 lg:gap-12 items-start">
            
            <!-- Left Info Column -->
            <div class="lg:col-span-7 space-y-4 sm:space-y-6 text-center lg:text-left">
                
                <!-- Pill Badge -->
                <div class="inline-flex items-center gap-2 px-3 py-1 rounded-full bg-rose-950/90 border border-rose-500/40 text-rose-300 text-xs sm:text-sm font-bold">
                    <span class="w-2 h-2 rounded-full bg-rose-500 animate-pulse"></span>
                    <span>১ মাসের ব্যবহারিক অনলাইন প্রশিক্ষণ কোর্স</span>
                </div>

                <!-- Main Course Heading -->
                <h1 class="text-3xl sm:text-5xl lg:text-6xl font-black tracking-tight leading-tight sm:leading-[1.15] text-white">
                    <span class="text-transparent bg-clip-text bg-gradient-to-r from-amber-400 via-emerald-300 to-teal-300">সাংবাদিকতা, আইন</span><br>
                    ও উপস্থাপনা
                </h1>

                <!-- Subtitle / Value proposition -->
                <p class="text-sm sm:text-base lg:text-lg text-slate-200 max-w-2xl mx-auto lg:mx-0 leading-relaxed font-normal">
                    একটি কোর্সেই সাংবাদিকতার তিনটি গুরুত্বপূর্ণ ক্ষেত্র শেখার সুযোগ: <strong class="text-amber-300 font-bold">সাংবাদিকতা</strong>, <strong class="text-emerald-300 font-bold">আইন</strong> এবং ক্যামেরার সামনে <strong class="text-teal-300 font-bold">উপস্থাপনা</strong>। সরাসরি গণমাধ্যম ও সুপ্রিম কোর্টের শীর্ষ বিশেষজ্ঞদের সাথে ১২টি ইন্টারঅ্যাক্টিভ লাইভ ক্লাস।
                </p>

                <!-- MOBILE ONLY: Video appears right here on mobile with EXACT 16:9 width and zero black bars -->
                <div class="block lg:hidden my-5">
                    <div class="w-full max-w-lg mx-auto rounded-2xl bg-slate-900 border border-emerald-500/50 shadow-2xl p-2.5 sm:p-3 ring-1 ring-emerald-500/20">
                        <div class="flex items-center justify-between px-2 pb-2 text-xs">
                            <span class="flex items-center gap-1.5 text-white font-bold text-xs">
                                <span class="w-2 h-2 rounded-full bg-rose-500 animate-ping"></span>
                                <span>কোর্সের ভিডিও বার্তা</span>
                            </span>
                            <span class="px-2 py-0.5 rounded text-[10px] font-bold bg-blue-600/30 text-blue-300 border border-blue-500/40 flex items-center gap-1">
                                <i class="fa-brands fa-facebook"></i> Facebook Reel
                            </span>
                        </div>
                        
                        <!-- Exact Width 16:9 Video Frame (Matches Facebook native 56.2% padding) -->
                        <div class="relative w-full aspect-video rounded-xl overflow-hidden bg-black shadow-inner border border-slate-800">
                            <iframe 
                                src="https://www.facebook.com/plugins/video.php?href=https%3A%2F%2Fwww.facebook.com%2Freel%2F1401194435417527%2F&show_text=false" 
                                class="w-full h-full border-0 absolute inset-0" 
                                scrolling="no" 
                                frameborder="0" 
                                allowfullscreen="true" 
                                allow="autoplay; clipboard-write; encrypted-media; picture-in-picture; web-share">
                            </iframe>
                        </div>

                        <!-- Direct Facebook Reel Button -->
                        <div class="mt-2.5">
                            <a href="https://www.facebook.com/reel/1401194435417527" target="_blank" rel="noopener noreferrer" 
                               class="w-full py-2.5 px-3 rounded-xl bg-blue-600 hover:bg-blue-500 text-white text-xs font-bold flex items-center justify-center gap-2 shadow transition">
                                <i class="fa-brands fa-facebook text-sm"></i>
                                <span>ফেসবুকে রিল ভিডিওটি দেখুন</span>
                            </a>
                        </div>
                    </div>
                </div>

                <!-- Key Specs Grid -->
                <div class="grid grid-cols-2 sm:grid-cols-3 gap-2 sm:gap-3 text-left pt-1">
                    <div class="p-3 rounded-xl bg-slate-900/80 border border-slate-800">
                        <span class="text-[11px] text-slate-400 block"><i class="fa-solid fa-calendar-day text-amber-400 mr-1"></i> ১ম ব্যাচ শুরু:</span>
                        <strong class="text-xs sm:text-sm text-white">৫ নভেম্বর ২০২৬</strong>
                    </div>
                    <div class="p-3 rounded-xl bg-slate-900/80 border border-slate-800">
                        <span class="text-[11px] text-slate-400 block"><i class="fa-solid fa-clock text-emerald-400 mr-1"></i> ক্লাসের সময়:</span>
                        <strong class="text-xs sm:text-sm text-white">বৃহস্পতি, শুক্র ও শনি রাত ৯টায়</strong>
                    </div>
                    <div class="p-3 rounded-xl bg-slate-900/80 border border-slate-800 col-span-2 sm:col-span-1">
                        <span class="text-[11px] text-slate-400 block"><i class="fa-solid fa-layer-group text-teal-400 mr-1"></i> মোট ক্লাস:</span>
                        <strong class="text-xs sm:text-sm text-white">১২টি মডিউল (১ মাস)</strong>
                    </div>
                </div>

                <!-- Special Discount Banner -->
                <div class="p-4 rounded-2xl bg-gradient-to-r from-amber-950/90 via-slate-900 to-emerald-950/90 border-2 border-amber-500/60 shadow-xl flex flex-col sm:flex-row sm:items-center justify-between gap-3 text-center sm:text-left">
                    <div>
                        <div class="flex items-center justify-center sm:justify-start gap-2">
                            <span class="px-2.5 py-0.5 rounded text-[11px] font-black bg-rose-600 text-white uppercase tracking-wider">
                                বিশেষ ছাড়
                            </span>
                            <span class="text-xs text-slate-300">প্রথম ব্যাচের প্রথম ৫০ জনের জন্য:</span>
                        </div>
                        <div class="flex items-baseline justify-center sm:justify-start gap-3 mt-1">
                            <span class="text-3xl sm:text-4xl font-black text-amber-400">৳৯৯৯</span>
                            <span class="text-sm text-slate-400 line-through">নিয়মিত মূল্য: ৳৩,০০০</span>
                            <span class="text-xs font-bold text-emerald-400 bg-emerald-900/80 px-2 py-0.5 rounded">৬৭% ছাড়</span>
                        </div>
                    </div>
                    <div class="flex-shrink-0">
                        <span class="text-[11px] text-amber-300 font-semibold block sm:text-right">
                            <i class="fa-solid fa-bolt mr-1"></i> আসন সংখ্যা সীমিত!
                        </span>
                    </div>
                </div>

                <!-- Primary Action CTA Buttons -->
                <div class="flex flex-col sm:flex-row items-center justify-center lg:justify-start gap-3 pt-2">
                    <a href="{{ route('courses.checkout', $flagshipCourse->slug) }}" class="w-full sm:w-auto inline-flex items-center justify-center gap-2.5 px-7 py-3.5 sm:py-4 rounded-xl bg-gradient-to-r from-emerald-500 via-teal-500 to-emerald-600 hover:from-emerald-600 hover:to-teal-700 text-white font-extrabold text-sm sm:text-base shadow-xl shadow-emerald-600/30 hover:scale-[1.02] transition-all duration-200">
                        <i class="fa-solid fa-credit-card"></i>
                        <span>ভর্তি নিশ্চিত করুন (মাত্র ৯৯৯ টাকা)</span>
                    </a>
                    <a href="#course-curriculum" class="w-full sm:w-auto inline-flex items-center justify-center gap-2 px-6 py-3.5 sm:py-4 rounded-xl bg-slate-900 hover:bg-slate-800 border border-slate-700 text-slate-200 font-bold text-sm transition duration-200">
                        <i class="fa-solid fa-list-check text-amber-400"></i>
                        <span>১২টি মডিউল কারিকুলাম</span>
                    </a>
                </div>

                <!-- 3 Trainers Mini Preview -->
                <div class="pt-3 border-t border-slate-800/80 flex flex-wrap items-center justify-center lg:justify-start gap-3 text-xs text-slate-300">
                    <span class="font-bold text-amber-400 uppercase text-[11px]">প্রশিক্ষকবৃন্দ:</span>
                    <span class="flex items-center gap-1.5">
                        <img src="{{ asset('images/instructors/nazir-ahmed-khan.jpg') }}" alt="নাজীর আহম্মেদ খান" class="w-5 h-5 rounded-full object-cover border border-emerald-400">
                        <span>সাংবাদিক নাজীর আহম্মেদ খান</span>
                    </span>
                    <span class="flex items-center gap-1.5">
                        <img src="{{ asset('images/instructors/mehedi-hasan.jpg') }}" alt="ব্যারিস্টার মেহেদী হাসান" class="w-5 h-5 rounded-full object-cover border border-teal-400">
                        <span>ব্যারিস্টার মেহেদী হাসান</span>
                    </span>
                    <span class="flex items-center gap-1.5">
                        <img src="{{ asset('images/instructors/arif-sarkar.jpg') }}" alt="আরিফ সরকার" class="w-5 h-5 rounded-full object-cover border border-rose-400">
                        <span>আরিফ সরকার</span>
                    </span>
                </div>

            </div>

            <!-- Right Column: DESKTOP VIDEO SHOWCASE & COMPLEMENTARY HIGHLIGHTS (Perfect Vertical Alignment) -->
            <div class="hidden lg:block lg:col-span-5 space-y-3">
                
                <!-- Upper Portion: Batch Seat Counter & Live Urgency -->
                <div class="p-3.5 rounded-2xl bg-slate-900/90 border border-amber-500/50 shadow-lg flex items-center justify-between gap-3 text-xs">
                    <div class="flex items-center gap-2">
                        <span class="relative flex h-2.5 w-2.5">
                            <span class="animate-ping absolute inline-flex h-full w-full rounded-full bg-rose-400 opacity-75"></span>
                            <span class="relative inline-flex rounded-full h-2.5 w-2.5 bg-rose-500"></span>
                        </span>
                        <span class="text-amber-300 font-bold">১ম ব্যাচে আর মাত্র ১৮টি আসন অবশিষ্ট!</span>
                    </div>
                    <span class="px-2 py-0.5 rounded-md bg-emerald-900/90 text-emerald-300 font-mono text-[11px] font-bold border border-emerald-700">
                        ৩২/৫০ সিট বুকড
                    </span>
                </div>

                <!-- Middle Portion: Video Player Card (Exact 16:9 Aspect Ratio) -->
                <div class="relative w-full rounded-2xl bg-slate-900 border-2 border-emerald-500/50 p-3 shadow-2xl ring-1 ring-emerald-500/30 space-y-2.5">
                    
                    <!-- Header Bar inside Video Frame -->
                    <div class="flex items-center justify-between px-1 text-xs">
                        <div class="flex items-center gap-2">
                            <span class="w-2 h-2 rounded-full bg-rose-500"></span>
                            <span class="text-xs font-bold text-white tracking-wide">কোর্সের অফিশিয়াল ভিডিও বার্তা</span>
                        </div>
                        <span class="text-[10px] text-amber-400 font-mono bg-amber-950/80 px-2 py-0.5 rounded border border-amber-800 flex items-center gap-1">
                            <i class="fa-brands fa-facebook"></i> Reel
                        </span>
                    </div>

                    <!-- Embedded Facebook Reel / Video Player (Exact 16:9 Aspect Ratio) -->
                    <div class="relative w-full aspect-video rounded-xl overflow-hidden bg-black shadow-inner border border-slate-800">
                        <iframe 
                            src="https://www.facebook.com/plugins/video.php?href=https%3A%2F%2Fwww.facebook.com%2Freel%2F1401194435417527%2F&show_text=false" 
                            class="w-full h-full border-0 absolute inset-0" 
                            scrolling="no" 
                            frameborder="0" 
                            allowfullscreen="true" 
                            allow="autoplay; clipboard-write; encrypted-media; picture-in-picture; web-share">
                        </iframe>
                    </div>

                    <!-- Action Bar below video -->
                    <div>
                        <a href="https://www.facebook.com/reel/1401194435417527" target="_blank" rel="noopener noreferrer" 
                           class="w-full py-2.5 px-3 rounded-xl bg-blue-600 hover:bg-blue-500 text-white text-xs font-bold flex items-center justify-center gap-2 shadow transition">
                            <i class="fa-brands fa-facebook text-base"></i>
                            <span>ফেসবুকে রিল ভিডিওটি দেখুন</span>
                        </a>
                    </div>

                </div>

                <!-- Lower Portion: Key Course Highlights & Quick Enroll -->
                <div class="p-4 rounded-2xl bg-slate-900/90 border border-slate-800 shadow-xl space-y-3 text-xs">
                    <div class="grid grid-cols-2 gap-2 text-slate-300 text-[11px]">
                        <div class="flex items-center gap-2 p-2 rounded-xl bg-slate-800/80 border border-slate-700/60">
                            <i class="fa-solid fa-video text-rose-400 text-xs"></i>
                            <span class="font-medium">১২টি লাইভ ক্লাস</span>
                        </div>
                        <div class="flex items-center gap-2 p-2 rounded-xl bg-slate-800/80 border border-slate-700/60">
                            <i class="fa-solid fa-award text-amber-400 text-xs"></i>
                            <span class="font-medium">অফিশিয়াল সার্টিফিকেট</span>
                        </div>
                        <div class="flex items-center gap-2 p-2 rounded-xl bg-slate-800/80 border border-slate-700/60">
                            <i class="fa-solid fa-headset text-emerald-400 text-xs"></i>
                            <span class="font-medium">২৪/৭ সাপোর্ট গ্রুপ</span>
                        </div>
                        <div class="flex items-center gap-2 p-2 rounded-xl bg-slate-800/80 border border-slate-700/60">
                            <i class="fa-solid fa-play-circle text-teal-400 text-xs"></i>
                            <span class="font-medium">ক্লাস রেকর্ডিং প্রদান</span>
                        </div>
                    </div>

                    <div class="pt-2 flex items-center justify-between border-t border-slate-800 text-[11px]">
                        <span class="text-slate-400">পেমেন্ট মেথড:</span>
                        <span class="font-bold text-amber-300 flex items-center gap-1.5">
                            <span class="text-pink-400">বিকাশ</span> • <span class="text-orange-400">নগদ</span> • <span class="text-purple-400">রকেট</span>
                        </span>
                    </div>

                    <a href="{{ route('courses.checkout', $flagshipCourse->slug) }}" 
                       class="w-full py-3 rounded-xl bg-gradient-to-r from-emerald-500 to-teal-600 hover:from-emerald-600 hover:to-teal-700 text-white font-extrabold text-xs sm:text-sm flex items-center justify-center gap-2 shadow-lg shadow-emerald-600/30 transition hover:scale-[1.01]">
                        <i class="fa-solid fa-lock text-xs"></i>
                        <span>অনলাইনে ভর্তি নিশ্চিত করুন (৳৯৯৯)</span>
                        <i class="fa-solid fa-arrow-right text-xs"></i>
                    </a>
                </div>

            </div>

        </div>
    </div>
</section>


<!-- ==================== 2. TARGET AUDIENCE (কাদের জন্য এই কোর্স) ==================== -->
<section class="py-12 sm:py-20 bg-white border-y border-slate-200">
    <div class="max-w-5xl mx-auto px-4 sm:px-6 lg:px-8">
        
        <div class="text-center max-w-2xl mx-auto mb-8 sm:mb-12">
            <span class="text-xs font-bold text-emerald-800 uppercase tracking-widest bg-emerald-100 px-3 py-1 rounded-full">
                সেলফ-অ্যাসেসমেন্ট
            </span>
            <h2 class="text-2xl sm:text-3xl font-extrabold text-slate-900 mt-2">
                এই কোর্সটি কি আপনার জন্য?
            </h2>
            <p class="text-slate-500 text-xs sm:text-sm mt-1">
                নিজের সাথে মিলিয়ে নিন নিচের প্রশ্নগুলোর উত্তর:
            </p>
        </div>

        <div class="grid grid-cols-1 md:grid-cols-2 gap-3 sm:gap-4">
            
            <div class="p-4 sm:p-5 rounded-2xl bg-slate-50 border border-slate-200 hover:border-emerald-400 transition space-y-1.5 sm:space-y-2">
                <div class="w-7 h-7 sm:w-8 sm:h-8 rounded-lg bg-emerald-600 text-white flex items-center justify-center text-xs sm:text-sm font-bold">
                    ১
                </div>
                <h3 class="font-bold text-sm sm:text-base text-slate-900">আপনি কি সাংবাদিকতা করছেন?</h3>
                <p class="text-xs text-slate-600 leading-relaxed">
                    পেশাগত ক্যারিয়ারে আরও শক্ত অবস্থান তৈরি করতে চান, ক্রাইম রিপোর্টিং ও তথ্য যাচাইয়ের আধুনিক কৌশল শিখতে চান।
                </p>
            </div>

            <div class="p-4 sm:p-5 rounded-2xl bg-slate-50 border border-slate-200 hover:border-emerald-400 transition space-y-1.5 sm:space-y-2">
                <div class="w-7 h-7 sm:w-8 sm:h-8 rounded-lg bg-emerald-600 text-white flex items-center justify-center text-xs sm:text-sm font-bold">
                    ২
                </div>
                <h3 class="font-bold text-sm sm:text-base text-slate-900">নাকি সাংবাদিকতা পেশায় নতুন আসতে চান?</h3>
                <p class="text-xs text-slate-600 leading-relaxed">
                    কোনো পূর্ব অভিজ্ঞতা ছাড়াই প্রিন্ট, অনলাইন বা টেলিভিশন মিডিয়ায় পেশাদার সাংবাদিক হিসেবে আত্মপ্রকাশ করতে চান।
                </p>
            </div>

            <div class="p-4 sm:p-5 rounded-2xl bg-slate-50 border border-slate-200 hover:border-emerald-400 transition space-y-1.5 sm:space-y-2">
                <div class="w-7 h-7 sm:w-8 sm:h-8 rounded-lg bg-emerald-600 text-white flex items-center justify-center text-xs sm:text-sm font-bold">
                    ৩
                </div>
                <h3 class="font-bold text-sm sm:text-base text-slate-900">সঠিকভাবে সংবাদ তৈরি ও সোর্স ম্যানেজমেন্ট করতে চান?</h3>
                <p class="text-xs text-slate-600 leading-relaxed">
                    সংবাদ সংগ্রহ করতে চান, কিন্তু জানেন না কীভাবে একটি সংবাদ সঠিকভাবে তৈরি ও তথ্য যাচাই (Fact Checking) করতে হয়।
                </p>
            </div>

            <div class="p-4 sm:p-5 rounded-2xl bg-slate-50 border border-slate-200 hover:border-emerald-400 transition space-y-1.5 sm:space-y-2">
                <div class="w-7 h-7 sm:w-8 sm:h-8 rounded-lg bg-emerald-600 text-white flex items-center justify-center text-xs sm:text-sm font-bold">
                    ৪
                </div>
                <h3 class="font-bold text-sm sm:text-base text-slate-900">সাংবাদিকতার আইন ও সুরক্ষা জানতে চান?</h3>
                <p class="text-xs text-slate-600 leading-relaxed">
                    সাংবিধানিক অধিকার, মানহানি মামলা থেকে বাঁচার উপায়, সাইবার আইন এবং আদালতবিষয়ক রিপোর্টিংয়ের সতর্কতা আয়ত্ত করতে চান।
                </p>
            </div>

            <div class="p-4 sm:p-5 rounded-2xl bg-slate-50 border border-slate-200 hover:border-emerald-400 transition space-y-1.5 sm:space-y-2 md:col-span-2">
                <div class="w-7 h-7 sm:w-8 sm:h-8 rounded-lg bg-emerald-600 text-white flex items-center justify-center text-xs sm:text-sm font-bold">
                    ৫
                </div>
                <h3 class="font-bold text-sm sm:text-base text-slate-900">ক্যামেরার সামনে আত্মবিশ্বাসের সঙ্গে সংবাদ উপস্থাপনা করতে চান?</h3>
                <p class="text-xs text-slate-600 leading-relaxed">
                    শুদ্ধ বাংলা উচ্চারণ, ভয়েস মড্যুলেশন, টেলিপ্রম্পটার পাঠ এবং লাইভ ব্রডকাস্টিংয়ে জড়তা কাটিয়ে পেশাদার উপস্থাপক হতে চান।
                </p>
            </div>

        </div>

        <div class="mt-6 sm:mt-8 p-4 rounded-2xl bg-emerald-50 border border-emerald-200 text-center text-emerald-900 text-xs sm:text-sm font-semibold">
            ✨ আপনার উত্তর যদি ‘হ্যাঁ’ হয়ে থাকে, তবে “সাংবাদিকতা, আইন ও উপস্থাপনা” কোর্সটি আপনার দক্ষতা ও ক্যারিয়ার গড়ার সেরা সুযোগ!
        </div>

    </div>
</section>


<!-- ==================== 3. WHAT YOU WILL LEARN (কোর্সে যা শিখবেন) ==================== -->
<section class="py-12 sm:py-20 bg-slate-50">
    <div class="max-w-6xl mx-auto px-4 sm:px-6 lg:px-8">
        
        <div class="text-center max-w-2xl mx-auto mb-8 sm:mb-12">
            <span class="text-xs font-bold text-emerald-800 uppercase tracking-widest bg-emerald-100 px-3 py-1 rounded-full">
                ব্যবহারিক আউটকাম
            </span>
            <h2 class="text-2xl sm:text-3xl font-extrabold text-slate-900 mt-2">
                কোর্সে যা যা শিখবেন
            </h2>
            <p class="text-slate-500 text-xs sm:text-sm mt-1">
                তাত্ত্বিক নয়, সম্পূর্ণ বাস্তবধর্মী ও প্রায়োগিক প্রশিক্ষণ
            </p>
        </div>

        <div class="grid grid-cols-1 sm:grid-cols-2 lg:grid-cols-3 gap-4 sm:gap-6">
            
            <div class="bg-white p-5 sm:p-6 rounded-2xl border border-slate-200/80 shadow-xs space-y-2.5 hover:border-emerald-500 hover:shadow-md transition">
                <div class="w-10 h-10 sm:w-12 sm:h-12 rounded-xl bg-emerald-100 text-emerald-700 flex items-center justify-center text-lg sm:text-xl">
                    <i class="fa-solid fa-magnifying-glass"></i>
                </div>
                <h3 class="font-bold text-sm sm:text-base text-slate-900">সংবাদ সংগ্রহ ও তথ্য যাচাই</h3>
                <p class="text-xs text-slate-600 leading-relaxed">
                    নির্ভরযোগ্য তথ্য সংগ্রহ, সোর্স তৈরির আধুনিক কৌশল, ফ্যাক্ট চেকিং ও ডিজিটাল টুলস দিয়ে ভুয়া তথ্য ফিল্টারিং।
                </p>
            </div>

            <div class="bg-white p-5 sm:p-6 rounded-2xl border border-slate-200/80 shadow-xs space-y-2.5 hover:border-emerald-500 hover:shadow-md transition">
                <div class="w-10 h-10 sm:w-12 sm:h-12 rounded-xl bg-blue-100 text-blue-700 flex items-center justify-center text-lg sm:text-xl">
                    <i class="fa-solid fa-newspaper"></i>
                </div>
                <h3 class="font-bold text-sm sm:text-base text-slate-900">রিপোর্ট লেখা ও ক্রাইম রিপোর্টিং</h3>
                <p class="text-xs text-slate-600 leading-relaxed">
                    ইনভার্টেড পিরামিড পদ্ধতি, আকর্ষণীয় শিরোনাম তৈরি, ক্রাইম সিন কভার ও গভীর অনুসন্ধানী প্রতিবেদন লেখার কৌশল।
                </p>
            </div>

            <div class="bg-white p-5 sm:p-6 rounded-2xl border border-slate-200/80 shadow-xs space-y-2.5 hover:border-emerald-500 hover:shadow-md transition">
                <div class="w-10 h-10 sm:w-12 sm:h-12 rounded-xl bg-amber-100 text-amber-700 flex items-center justify-center text-lg sm:text-xl">
                    <i class="fa-solid fa-user-secret"></i>
                </div>
                <h3 class="font-bold text-sm sm:text-base text-slate-900">অনুসন্ধানী সাংবাদিকতার কৌশল</h3>
                <p class="text-xs text-slate-600 leading-relaxed">
                    গোপনীয় নথিপত্র অনুসন্ধান, দুর্নীতির প্রমাণ সংগ্রহ এবং সাংবাদিকের ব্যক্তিগত সুরক্ষার গুরুত্বপূর্ণ গাইডলাইন।
                </p>
            </div>

            <div class="bg-white p-5 sm:p-6 rounded-2xl border border-slate-200/80 shadow-xs space-y-2.5 hover:border-emerald-500 hover:shadow-md transition">
                <div class="w-10 h-10 sm:w-12 sm:h-12 rounded-xl bg-purple-100 text-purple-700 flex items-center justify-center text-lg sm:text-xl">
                    <i class="fa-solid fa-scale-balanced"></i>
                </div>
                <h3 class="font-bold text-sm sm:text-base text-slate-900">সংবিধান ও সাংবাদিকতার আইন</h3>
                <p class="text-xs text-slate-600 leading-relaxed">
                    সাংবাদিকের সাংবিধানিক অধিকার, গণমাধ্যমের স্বাধীনতা, মানহানি মামলা থেকে বাঁচার কৌশল ও আদালত রিপোর্টিং।
                </p>
            </div>

            <div class="bg-white p-5 sm:p-6 rounded-2xl border border-slate-200/80 shadow-xs space-y-2.5 hover:border-emerald-500 hover:shadow-md transition">
                <div class="w-10 h-10 sm:w-12 sm:h-12 rounded-xl bg-rose-100 text-rose-700 flex items-center justify-center text-lg sm:text-xl">
                    <i class="fa-solid fa-tv"></i>
                </div>
                <h3 class="font-bold text-sm sm:text-base text-slate-900">নিউজ প্রেজেন্টেশন ও টেলিপ্রম্পটার</h3>
                <p class="text-xs text-slate-600 leading-relaxed">
                    টেলিভিশন ও ডিজিটাল স্টুডিওতে টেলিপ্রম্পটার ব্যবহার, ক্যামেরা ফেসিং এবং লাইভ ব্রডকাস্টিংয়ে সাবলীল কথা বলা।
                </p>
            </div>

            <div class="bg-white p-5 sm:p-6 rounded-2xl border border-slate-200/80 shadow-xs space-y-2.5 hover:border-emerald-500 hover:shadow-md transition">
                <div class="w-10 h-10 sm:w-12 sm:h-12 rounded-xl bg-teal-100 text-teal-700 flex items-center justify-center text-lg sm:text-xl">
                    <i class="fa-solid fa-microphone"></i>
                </div>
                <h3 class="font-bold text-sm sm:text-base text-slate-900">সাক্ষাৎকার ও লাইভ ক্যামেরা ট্রেনিং</h3>
                <p class="text-xs text-slate-600 leading-relaxed">
                    গুরুত্বপূর্ণ ব্যক্তিদের মুখোমুখি সাক্ষাৎকার গ্রহণ, ক্রস-কোশ্চেনিং এবং ব্রেকিং নিউজে স্পট রিপোর্টিং কলাকৌশল।
                </p>
            </div>

        </div>

    </div>
</section>


<!-- ==================== 4. INSTRUCTORS SHOWCASE (প্রশিক্ষকবৃন্দ) ==================== -->
<section id="instructors" class="py-12 sm:py-20 bg-white border-t border-slate-200">
    <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8">
        
        <div class="text-center max-w-2xl mx-auto mb-10 sm:mb-16">
            <span class="text-xs font-bold text-emerald-800 uppercase tracking-widest bg-emerald-100 px-3 py-1 rounded-full">
                ফ্যাকাল্টি প্যানেল
            </span>
            <h2 class="text-2xl sm:text-3xl font-extrabold text-slate-900 mt-2">
                কোর্সের সম্মানিত প্রশিক্ষকবৃন্দ
            </h2>
            <p class="text-slate-500 text-xs sm:text-sm mt-1">
                দেশবরেণ্য ক্রাইম রিপোর্টার, সুপ্রিম কোর্টের আইনজীবী ও টিভি উপস্থাপকের সরাসরি দিকনির্দেশনা
            </p>
        </div>

        <div class="grid grid-cols-1 md:grid-cols-3 gap-6 sm:gap-8">
            
            <!-- Instructor 1: Nazir Ahmed Khan -->
            <div class="bg-slate-50 rounded-2xl p-5 sm:p-6 border border-slate-200 hover:border-emerald-400 hover:shadow-lg transition-all duration-300 flex flex-col justify-between space-y-4">
                <div class="space-y-3">
                    <div class="flex items-center gap-3.5">
                        <img src="{{ asset('images/instructors/nazir-ahmed-khan.jpg') }}" 
                             alt="সাংবাদিক নাজীর আহম্মেদ খান" 
                             class="w-16 h-16 sm:w-20 sm:h-20 rounded-full object-cover object-top border-2 border-emerald-500 shadow-md flex-shrink-0">
                        <div>
                            <span class="px-2 py-0.5 rounded text-[10px] font-bold bg-amber-100 text-amber-800 uppercase">সাংবাদিকতা ও ক্রাইম</span>
                            <h3 class="text-base sm:text-lg font-bold text-slate-900 mt-0.5">সাংবাদিক নাজীর আহম্মেদ খান</h3>
                            <p class="text-xs text-emerald-700 font-semibold">সিনিয়র ক্রাইম রিপোর্টার</p>
                        </div>
                    </div>

                    <p class="text-xs text-slate-600 leading-relaxed">
                        ২০০৮ সাল থেকে বিভিন্ন গণমাধ্যমে কাজের অভিজ্ঞতা এবং দীর্ঘদিনের ক্রাইম রিপোর্টিংয়ের বাস্তব অভিজ্ঞতা থেকে তিনি শেখাবেন সংবাদ সংগ্রহ, তথ্য যাচাই, সোর্স ম্যানেজমেন্ট, ক্রাইম রিপোর্টিং ও সংবাদ প্রতিবেদন তৈরির কৌশল।
                    </p>
                </div>

                <div class="pt-3 border-t border-slate-200 flex items-center justify-between text-xs text-slate-500">
                    <span class="font-semibold text-slate-700"><i class="fa-solid fa-clock mr-1 text-emerald-600"></i> ২০০৮ সাল থেকে অভিজ্ঞ</span>
                    <span class="text-emerald-700 font-bold">কোর্স ব্যবস্থাপক</span>
                </div>
            </div>

            <!-- Instructor 2: Barrister Mehedi Hasan -->
            <div class="bg-slate-50 rounded-2xl p-5 sm:p-6 border border-slate-200 hover:border-emerald-400 hover:shadow-lg transition-all duration-300 flex flex-col justify-between space-y-4">
                <div class="space-y-3">
                    <div class="flex items-center gap-3.5">
                        <img src="{{ asset('images/instructors/mehedi-hasan.jpg') }}" 
                             alt="ব্যারিস্টার মেহেদী হাসান" 
                             class="w-16 h-16 sm:w-20 sm:h-20 rounded-full object-cover object-top border-2 border-teal-500 shadow-md flex-shrink-0">
                        <div>
                            <span class="px-2 py-0.5 rounded text-[10px] font-bold bg-teal-100 text-teal-800 uppercase">আইন ও সংবিধান</span>
                            <h3 class="text-base sm:text-lg font-bold text-slate-900 mt-0.5">ব্যারিস্টার মেহেদী হাসান</h3>
                            <p class="text-xs text-teal-700 font-semibold">আইনজীবী, বাংলাদেশ সুপ্রিম কোর্ট</p>
                        </div>
                    </div>

                    <p class="text-xs text-slate-600 leading-relaxed">
                        তাঁর কাছ থেকে জানতে পারবেন সাংবাদিকের আইনগত অধিকার ও দায়িত্ব, সংবিধান, মানহানি, ব্যক্তিগত গোপনীয়তা, আদালতবিষয়ক রিপোর্টিং এবং সংবাদ প্রকাশের ক্ষেত্রে প্রয়োজনীয় আইনগত সতর্কতা।
                    </p>
                </div>

                <div class="pt-3 border-t border-slate-200 flex items-center justify-between text-xs text-slate-500">
                    <span class="font-semibold text-slate-700"><i class="fa-solid fa-scale-balanced mr-1 text-teal-600"></i> সুপ্রিম কোর্ট আইনজীবী</span>
                    <span class="text-teal-700 font-bold">আইন বিশেষজ্ঞ</span>
                </div>
            </div>

            <!-- Instructor 3: Arif Sarker -->
            <div class="bg-slate-50 rounded-2xl p-5 sm:p-6 border border-slate-200 hover:border-emerald-400 hover:shadow-lg transition-all duration-300 flex flex-col justify-between space-y-4">
                <div class="space-y-3">
                    <div class="flex items-center gap-3.5">
                        <img src="{{ asset('images/instructors/arif-sarkar.jpg') }}" 
                             alt="আরিফ সরকার" 
                             class="w-16 h-16 sm:w-20 sm:h-20 rounded-full object-cover object-top border-2 border-rose-500 shadow-md flex-shrink-0">
                        <div>
                            <span class="px-2 py-0.5 rounded text-[10px] font-bold bg-rose-100 text-rose-800 uppercase">উপস্থাপনা ও ভয়েস</span>
                            <h3 class="text-base sm:text-lg font-bold text-slate-900 mt-0.5">আরিফ সরকার</h3>
                            <p class="text-xs text-rose-700 font-semibold">সংবাদ উপস্থাপক (জি টিভিসহ বিভিন্ন চ্যানেল)</p>
                        </div>
                    </div>

                    <p class="text-xs text-slate-600 leading-relaxed">
                        জি টিভিসহ বিভিন্ন টেলিভিশন চ্যানেলের উপস্থাপক হিসেবে রয়েছে দীর্ঘদিনের বাস্তব অভিজ্ঞতা। তিনি শেখাবেন সংবাদ উপস্থাপনা, শুদ্ধ উচ্চারণ, কণ্ঠস্বর নিয়ন্ত্রণ, টেলিপ্রম্পটার ব্যবহার, সাক্ষাৎকার পরিচালনা ও ক্যামেরার সামনে সাবলীল কথা বলার কৌশল।
                    </p>
                </div>

                <div class="pt-3 border-t border-slate-200 flex items-center justify-between text-xs text-slate-500">
                    <span class="font-semibold text-slate-700"><i class="fa-solid fa-tv mr-1 text-rose-600"></i> টিভি নিউজ প্রেজেন্টার</span>
                    <span class="text-rose-700 font-bold">উপস্থাপনা ট্রেইনার</span>
                </div>
            </div>

        </div>

    </div>
</section>


<!-- ==================== 5. COMPLETE 12 MODULES CURRICULUM ==================== -->
<section id="course-curriculum" class="py-12 sm:py-20 bg-slate-50" x-data="{ openMod: 1 }">
    <div class="max-w-5xl mx-auto px-4 sm:px-6 lg:px-8">
        
        <div class="text-center max-w-2xl mx-auto mb-8 sm:mb-12">
            <span class="text-xs font-bold text-emerald-800 uppercase tracking-widest bg-emerald-100 px-3 py-1 rounded-full">
                সম্পূর্ণ সিলেবাস
            </span>
            <h2 class="text-2xl sm:text-3xl font-extrabold text-slate-900 mt-2">
                কোর্স কারিকুলাম ও ১২টি বিশেষায়িত মডিউল
            </h2>
            <p class="text-slate-500 text-xs sm:text-sm mt-1">
                সাংবাদিকতা, আইন এবং সংবাদ উপস্থাপনার সমন্বিত পূর্ণাঙ্গ প্রশিক্ষণ রূপরেখা
            </p>
            <div class="flex items-center justify-center gap-2 mt-4 text-xs">
                <button @click="openMod = 'all'" class="px-3 py-1.5 rounded-lg bg-white border border-slate-200 text-slate-700 font-semibold hover:bg-slate-100 transition">সব মডিউল খুলুন</button>
                <button @click="openMod = null" class="px-3 py-1.5 rounded-lg bg-white border border-slate-200 text-slate-700 font-semibold hover:bg-slate-100 transition">সব বন্ধ করুন</button>
            </div>
        </div>

        <div class="space-y-3">
            @foreach($flagshipCourse->modules as $mod)
                <div class="bg-white border border-slate-200 rounded-2xl overflow-hidden shadow-xs transition duration-150">
                    
                    <button @click="openMod = (openMod === {{ $mod->order }} ? null : (openMod === 'all' ? null : {{ $mod->order }}))" 
                            type="button" 
                            class="w-full p-4 sm:p-5 flex items-center justify-between text-left hover:bg-emerald-50/50 transition duration-150">
                        <div class="flex items-center gap-3 sm:gap-4 pr-2">
                            <span class="w-8 h-8 sm:w-9 sm:h-9 rounded-xl bg-gradient-to-tr from-emerald-700 to-teal-600 text-white font-bold text-xs sm:text-sm flex items-center justify-center flex-shrink-0 shadow-xs">
                                {{ $mod->order }}
                            </span>
                            <div>
                                <h3 class="text-sm sm:text-base font-bold text-slate-900">
                                    {{ $mod->title }}
                                </h3>
                                <div class="flex items-center gap-2 sm:gap-3 text-xs text-slate-500 mt-0.5 sm:mt-1">
                                    <span><i class="fa-regular fa-clock mr-1 text-slate-400"></i>{{ $mod->duration_text }}</span>
                                    <span>•</span>
                                    <span class="text-emerald-700 font-semibold"><i class="fa-solid fa-video mr-1"></i>লাইভ ক্লাস</span>
                                </div>
                            </div>
                        </div>
                        <i class="fa-solid fa-chevron-down text-slate-400 text-xs transition-transform duration-200" 
                           :class="(openMod === {{ $mod->order }} || openMod === 'all') ? 'rotate-180 text-emerald-600' : ''"></i>
                    </button>

                    <div x-show="openMod === {{ $mod->order }} || openMod === 'all'" 
                         x-transition 
                         class="p-4 sm:p-5 bg-slate-50/70 border-t border-slate-100 text-xs text-slate-700 space-y-2.5">
                        <p class="leading-relaxed text-slate-600">{{ $mod->description }}</p>
                        
                        <div class="pt-2 flex flex-wrap items-center justify-between gap-2 border-t border-slate-200/60">
                            <span class="text-[11px] text-slate-500 font-medium">
                                <i class="fa-solid fa-circle-check text-emerald-600 mr-1"></i> লাইভ প্রশ্নোত্তর ও হ্যান্ডস-অন প্র্যাকটিস সেশন
                            </span>
                            <span class="px-2.5 py-0.5 rounded bg-emerald-100 text-emerald-800 font-bold text-[10px] uppercase">
                                ক্লাস রেকর্ডিং দেওয়া হবে
                            </span>
                        </div>
                    </div>

                </div>
            @endforeach
        </div>

    </div>
</section>


<!-- ==================== 6. FEE BREAKDOWN & PAYMENT GATEWAY ==================== -->
<section id="fees-payment" class="py-12 sm:py-20 bg-white">
    <div class="max-w-4xl mx-auto px-4 sm:px-6 lg:px-8">
        
        <div class="text-center max-w-2xl mx-auto mb-8 sm:mb-12">
            <span class="text-xs font-bold text-emerald-800 uppercase tracking-widest bg-emerald-100 px-3 py-1 rounded-full">
                ভর্তি প্রক্রিয়া ও ফি
            </span>
            <h2 class="text-2xl sm:text-3xl font-extrabold text-slate-900 mt-2">
                কোর্স ফি ও বিশেষ ছাড়ের সুবিধা
            </h2>
            <p class="text-slate-500 text-xs sm:text-sm mt-1">
                ঘরে বসেই বিকাশ, নগদ বা রকেটের মাধ্যমে মাত্র ৯৯৯ টাকায় ভর্তি নিশ্চিত করুন
            </p>
        </div>

        <div class="bg-gradient-to-br from-slate-900 via-emerald-950 to-slate-900 text-white rounded-3xl p-5 sm:p-10 shadow-2xl border border-emerald-500/40 space-y-6 sm:space-y-8">
            
            <div class="flex flex-col sm:flex-row items-center justify-between pb-6 border-b border-slate-800 gap-4 text-center sm:text-left">
                <div>
                    <span class="px-3 py-1 rounded-full text-xs font-bold bg-amber-400 text-slate-950">
                        ১ম ব্যাচে ৫০% এর বেশি স্পেশাল ছাড়
                    </span>
                    <h3 class="text-xl sm:text-2xl font-bold mt-2 text-white">সাংবাদিকতা, আইন ও উপস্থাপনা</h3>
                    <p class="text-xs text-slate-300 mt-1">১ মাসের প্রশিক্ষণ কোর্স • মোট ১২টি অনলাইন লাইভ ক্লাস</p>
                </div>
                <div class="text-center sm:text-right">
                    <div class="flex items-baseline justify-center sm:justify-end gap-2.5">
                        <span class="text-4xl sm:text-5xl font-black text-amber-400">৳৯৯৯</span>
                        <span class="text-base text-slate-400 line-through">৳৩,০০০</span>
                    </div>
                    <span class="text-xs text-rose-400 font-semibold block mt-1">প্রথম ৫০ জনের জন্য নির্ধারিত</span>
                </div>
            </div>

            <!-- Features Included -->
            <div class="grid grid-cols-1 sm:grid-cols-2 gap-2.5 text-xs text-slate-300">
                <div class="flex items-center gap-2">
                    <i class="fa-solid fa-circle-check text-emerald-400"></i>
                    <span>১২টি পূর্ণাঙ্গ লাইভ ক্লাস (Zoom & Google Meet)</span>
                </div>
                <div class="flex items-center gap-2">
                    <i class="fa-solid fa-circle-check text-emerald-400"></i>
                    <span>৩ জন শীর্ষ এক্সপার্ট ট্রেইনারের দিকনির্দেশনা</span>
                </div>
                <div class="flex items-center gap-2">
                    <i class="fa-solid fa-circle-check text-emerald-400"></i>
                    <span>প্রতিটি ক্লাসের ফুল এইচডি রেকর্ডিং ও লেকচার শিট</span>
                </div>
                <div class="flex items-center gap-2">
                    <i class="fa-solid fa-circle-check text-emerald-400"></i>
                    <span>ন্যাশনাল জার্নালিস্ট ক্লাব অনুমোদিত সার্টিফিকেট</span>
                </div>
                <div class="flex items-center gap-2">
                    <i class="fa-solid fa-circle-check text-emerald-400"></i>
                    <span>ব্যবহারিক ক্রাইম রিপোর্ট তৈরি ও মূল্যায়ন</span>
                </div>
                <div class="flex items-center gap-2">
                    <i class="fa-solid fa-circle-check text-emerald-400"></i>
                    <span>প্রাইভেট সাপোর্ট গ্রুপ ও নেটওয়ার্কিং সুযোগ</span>
                </div>
            </div>

            <!-- Payment Badges & Action -->
            <div class="pt-4 border-t border-slate-800 space-y-4">
                <div class="flex flex-wrap items-center justify-between gap-2.5 text-xs text-slate-400">
                    <span class="font-semibold text-slate-200">গৃহীত পেমেন্ট মাধ্যম:</span>
                    <div class="flex items-center gap-1.5 sm:gap-2">
                        <span class="px-2 sm:px-2.5 py-1 rounded bg-pink-600 text-white font-bold text-xs">বিকাশ</span>
                        <span class="px-2 sm:px-2.5 py-1 rounded bg-orange-600 text-white font-bold text-xs">নগদ</span>
                        <span class="px-2 sm:px-2.5 py-1 rounded bg-purple-600 text-white font-bold text-xs">রকেট</span>
                        <span class="px-2 sm:px-2.5 py-1 rounded bg-blue-600 text-white font-bold text-xs">কার্ড</span>
                    </div>
                </div>

                <a href="{{ route('courses.checkout', $flagshipCourse->slug) }}" 
                   class="w-full py-3.5 sm:py-4 rounded-2xl bg-gradient-to-r from-emerald-500 via-teal-500 to-emerald-600 hover:from-emerald-600 hover:to-teal-700 text-white font-black text-sm sm:text-base shadow-lg shadow-emerald-500/30 flex items-center justify-center gap-2 transition duration-200 hover:scale-[1.01]">
                    <i class="fa-solid fa-lock text-sm"></i>
                    <span>অনলাইনে এখনই ভর্তি নিশ্চিত করুন (৳৯৯৯)</span>
                </a>

                <p class="text-center text-[11px] text-slate-400">
                    <i class="fa-solid fa-headset mr-1 text-emerald-400"></i> যেকোনো প্রয়োজনে ফোন করুন: <strong>+৮৮০ ১৭০০-০০০০০০</strong> (সকাল ৯টা - রাত ১১টা)
                </p>
            </div>

        </div>

    </div>
</section>


<!-- ==================== 7. FAQ SECTION ==================== -->
<section class="py-12 sm:py-16 bg-slate-50" x-data="{ openFaq: 1 }">
    <div class="max-w-4xl mx-auto px-4 sm:px-6 lg:px-8">
        
        <div class="text-center mb-8">
            <span class="text-xs font-bold text-emerald-800 uppercase tracking-widest bg-emerald-100 px-3 py-1 rounded-full">
                সচরাচর জিজ্ঞাসা
            </span>
            <h2 class="text-2xl sm:text-3xl font-extrabold text-slate-900 mt-2">
                কোর্স সম্পর্কে প্রয়োজনীয় তথ্যাবলি
            </h2>
        </div>

        <div class="space-y-3 text-xs sm:text-sm">
            
            <div class="bg-white border border-slate-200 rounded-xl overflow-hidden">
                <button @click="openFaq = (openFaq === 1 ? null : 1)" class="w-full p-4 sm:p-5 text-left flex items-center justify-between font-bold text-slate-900 hover:bg-emerald-50/40 transition">
                    <span>কোর্সের ক্লাসগুলো কীভাবে অনুষ্ঠিত হবে?</span>
                    <i class="fa-solid fa-chevron-down text-slate-400 text-xs transition-transform" :class="openFaq === 1 ? 'rotate-180 text-emerald-600' : ''"></i>
                </button>
                <div x-show="openFaq === 1" class="p-4 sm:p-5 pt-0 text-slate-600 leading-relaxed border-t border-slate-100">
                    প্রতিটি ক্লাস Zoom এবং Google Meet এর মাধ্যমে সরাসরি লাইভ অনুষ্ঠিত হবে। আপনি ঘরে বসেই কম্পিউটার বা স্মার্টফোন দিয়ে সরাসরি ক্লাসে অংশ নিতে পারবেন এবং প্রশিক্ষকদের সাথে সরাসরি প্রশ্নোত্তর করতে পারবেন।
                </div>
            </div>

            <div class="bg-white border border-slate-200 rounded-xl overflow-hidden">
                <button @click="openFaq = (openFaq === 2 ? null : 2)" class="w-full p-4 sm:p-5 text-left flex items-center justify-between font-bold text-slate-900 hover:bg-emerald-50/40 transition">
                    <span>কোন কারণে ক্লাস মিস হলে কি রেকর্ডিং পাওয়া যাবে?</span>
                    <i class="fa-solid fa-chevron-down text-slate-400 text-xs transition-transform" :class="openFaq === 2 ? 'rotate-180 text-emerald-600' : ''"></i>
                </button>
                <div x-show="openFaq === 2" class="p-4 sm:p-5 pt-0 text-slate-600 leading-relaxed border-t border-slate-100">
                    হ্যাঁ! প্রতিটি লাইভ ক্লাস শেষে সম্পূর্ণ ক্লাসের ফুল এইচডি ভিডিও রেকর্ডিং এবং লেকচার শিট আপনার স্টুডেন্ট পোর্টালে যুক্ত হবে, যা আপনি সুবিধাজনক সময়ে যতবার খুশি দেখতে পারবেন।
                </div>
            </div>

            <div class="bg-white border border-slate-200 rounded-xl overflow-hidden">
                <button @click="openFaq = (openFaq === 3 ? null : 3)" class="w-full p-4 sm:p-5 text-left flex items-center justify-between font-bold text-slate-900 hover:bg-emerald-50/40 transition">
                    <span>কোর্স শেষে কি সার্টিফিকেট প্রদান করা হবে?</span>
                    <i class="fa-solid fa-chevron-down text-slate-400 text-xs transition-transform" :class="openFaq === 3 ? 'rotate-180 text-emerald-600' : ''"></i>
                </button>
                <div x-show="openFaq === 3" class="p-4 sm:p-5 pt-0 text-slate-600 leading-relaxed border-t border-slate-100">
                    হ্যাঁ, সফলভাবে ১২টি মডিউল সম্পন্ন করার পর ন্যাশনাল জার্নালিস্ট ক্লাব এবং আমার বাংলা একাডেমির পক্ষ থেকে অফিসিয়াল ভেরিফায়েবল কোর্স কমপ্লিশন সার্টিফিকেট প্রদান করা হবে।
                </div>
            </div>

            <div class="bg-white border border-slate-200 rounded-xl overflow-hidden">
                <button @click="openFaq = (openFaq === 4 ? null : 4)" class="w-full p-4 sm:p-5 text-left flex items-center justify-between font-bold text-slate-900 hover:bg-emerald-50/40 transition">
                    <span>কীভাবে ভর্তি নিশ্চিত করব?</span>
                    <i class="fa-solid fa-chevron-down text-slate-400 text-xs transition-transform" :class="openFaq === 4 ? 'rotate-180 text-emerald-600' : ''"></i>
                </button>
                <div x-show="openFaq === 4" class="p-4 sm:p-5 pt-0 text-slate-600 leading-relaxed border-t border-slate-100">
                    ‘ভর্তি নিশ্চিত করুন’ বোতামে ক্লিক করে আপনার নাম, মোবাইল নম্বর ও ইমেইল দিয়ে বিকাশ, নগদ বা রকেটের মাধ্যমে মাত্র ৯৯৯ টাকা ফি পরিশোধ করুন। পেমেন্ট সম্পূর্ণ হওয়ামাত্রই লাইভ ক্লাসের লিংক ও প্রাইভেট গ্রুপের ইনভাইটেশন পেয়ে যাবেন।
                </div>
            </div>

        </div>

    </div>
</section>


<!-- ==================== 8. FINAL CTA (BOTTOM PADDING FOR STICKY BAR) ==================== -->
<section class="py-12 sm:py-16 bg-gradient-to-r from-emerald-900 via-teal-900 to-slate-950 text-white text-center pb-24 sm:pb-16">
    <div class="max-w-4xl mx-auto px-4 sm:px-6 lg:px-8 space-y-4">
        <span class="inline-block px-3 py-1 rounded-full text-xs font-bold bg-amber-400 text-slate-950 uppercase">
            ১ম ব্যাচ শুরু: ৫ নভেম্বর ২০২৬
        </span>
        <h2 class="text-2xl sm:text-4xl font-black">
            সাংবাদিকতা ও উপস্থাপনায় নিজেকে একধাপ এগিয়ে রাখুন
        </h2>
        <p class="text-emerald-100 text-xs sm:text-sm max-w-xl mx-auto">
            প্রথম ব্যাচের ৫০টি সীমিত আসনের মধ্যে আপনার আসনটি নিশ্চিত করতে আজই যোগাযোগ করুন।
        </p>
        <div class="flex flex-col sm:flex-row items-center justify-center gap-3 pt-2">
            <a href="{{ route('courses.checkout', $flagshipCourse->slug) }}" class="w-full sm:w-auto px-7 py-3.5 rounded-xl bg-amber-400 hover:bg-amber-500 text-slate-950 font-black text-sm shadow-xl transition">
                ভর্তি নিশ্চিত করুন (৳৯৯৯)
            </a>
            <a href="https://wa.me/8801711774263" target="_blank" class="w-full sm:w-auto px-6 py-3.5 rounded-xl bg-slate-800/80 hover:bg-slate-800 border border-slate-700 text-white font-bold text-xs sm:text-sm transition flex items-center justify-center gap-2">
                <i class="fa-brands fa-whatsapp text-emerald-400 text-base"></i>
                <span>হোয়াটসঅ্যাপে কাউন্সেলিং নিন (০১৭১১-৭৭৪২৬৩)</span>
            </a>
        </div>
    </div>
</section>

@endsection
