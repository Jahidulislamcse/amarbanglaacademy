<!DOCTYPE html>
<html lang="{{ str_replace('_', '-', app()->getLocale()) }}" class="scroll-smooth">
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <meta name="csrf-token" content="{{ csrf_token() }}">

    <title>@yield('title', 'Amar Bangla Academy - আধুনিক লার্নিং ম্যানেজমেন্ট সিস্টেম (LMS)')</title>
    <meta name="description" content="@yield('meta_description', 'আমার বাংলা একাডেমি — লাইভ জুম ও গুগল মিট ক্লাস, মডিউল ভিত্তিক কারিকুলাম, অনলাইন এক্সাম, এবং বিকাশ/নগদ পেমেন্ট সহ সম্পূর্ণ আধুনিক LMS।')">

    <!-- Google Fonts: Hind Siliguri (Bangla) & Inter -->
    <link rel="preconnect" href="https://fonts.googleapis.com">
    <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
    <link href="https://fonts.googleapis.com/css2?family=Hind+Siliguri:wght@300;400;500;600;700&family=Inter:wght@400;500;600;700;800&display=swap" rel="stylesheet">

    <!-- Font Awesome Icons -->
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.5.1/css/all.min.css">

    <!-- Tailwind CSS CDN (Self-contained, works with zero build step on both local and cPanel) -->
    <script src="https://cdn.tailwindcss.com"></script>
    <script>
        tailwind.config = {
            theme: {
                extend: {
                    colors: {
                        emerald: {
                            50: '#ecfdf5',
                            100: '#d1fae5',
                            500: '#10b981',
                            600: '#059669',
                            700: '#047857',
                            800: '#065f46',
                            900: '#064e3b',
                        },
                        amber: {
                            400: '#fbbf24',
                            500: '#f59e0b',
                            600: '#d97706',
                        }
                    },
                    fontFamily: {
                        sans: ['"Hind Siliguri"', '"Inter"', 'sans-serif'],
                        bangla: ['"Hind Siliguri"', 'sans-serif'],
                    }
                }
            }
        }
    </script>

    <!-- Alpine.js CDN for reactivity without npm build -->
    <script defer src="https://cdn.jsdelivr.net/npm/alpinejs@3.14.3/dist/cdn.min.js"></script>

    <!-- Custom CSS -->
    <link rel="stylesheet" href="{{ asset('css/lms-custom.css') }}">

    @stack('styles')
</head>
<body class="bg-slate-50 text-slate-800 antialiased font-bangla flex flex-col min-h-screen" x-data="{ mobileMenuOpen: false }">

    <!-- Top Announcement Bar -->
    <div class="bg-gradient-to-r from-emerald-900 via-teal-800 to-slate-900 text-white text-xs sm:text-sm py-2 px-4 shadow-sm">
        <div class="max-w-7xl mx-auto flex flex-wrap items-center justify-between gap-2">
            <div class="flex items-center space-x-2 space-x-reverse">
                <span class="inline-flex items-center px-2 py-0.5 rounded text-[11px] font-semibold bg-amber-400 text-slate-900">
                    <i class="fa-solid fa-fire mr-1 text-xs"></i> ১ম ব্যাচে ভর্তি চলছে
                </span>
                <span class="hidden sm:inline">ন্যাশনাল জার্নালিস্ট ক্লাবের পৃষ্ঠপোষকতায় “সাংবাদিকতা, আইন ও উপস্থাপনা” কোর্স — প্রথম ৫০ জনের জন্য মাত্র ৯৯৯ টাকা!</span>
                <span class="sm:hidden font-medium">প্রথম ৫০ জনের জন্য কোর্স ফি মাত্র ৯৯৯ টাকা!</span>
            </div>
            <div class="flex items-center space-x-4 space-x-reverse text-emerald-100 text-xs">
                <span class="flex items-center gap-1.5"><i class="fa-solid fa-phone text-amber-300"></i> হেল্পলাইন: +৮৮০ ১৭০০-০০০০০০</span>
                <span class="hidden md:flex items-center gap-1.5"><i class="fa-solid fa-envelope text-amber-300"></i> info@amarbanglaacademy.com</span>
            </div>
        </div>
    </div>

    <!-- Clean Conversion Header (No Menu) -->
    <header class="sticky top-0 z-50 bg-white/95 backdrop-blur-md border-b border-slate-200/80 shadow-xs">
        <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8">
            <div class="flex items-center justify-between h-16 sm:h-20">
                
                <!-- Logo & Academy Name -->
                <a href="{{ route('home') }}" class="flex items-center gap-2.5 sm:gap-3 group">
                    <div class="w-10 h-10 sm:w-12 sm:h-12 rounded-xl bg-gradient-to-tr from-emerald-700 to-teal-500 flex items-center justify-center text-white shadow-md shadow-emerald-500/20 group-hover:scale-105 transition-transform duration-200">
                        <i class="fa-solid fa-graduation-cap text-xl sm:text-2xl"></i>
                    </div>
                    <div>
                        <span class="text-lg sm:text-2xl font-bold tracking-tight text-slate-900 block leading-tight">
                            আমার বাংলা <span class="text-emerald-700">একাডেমি</span>
                        </span>
                        <span class="text-[10px] sm:text-[11px] font-medium text-slate-500 tracking-wide uppercase">Amar Bangla Academy</span>
                    </div>
                </a>

                <!-- Quick Action Contact & Enroll -->
                <div class="flex items-center gap-2 sm:gap-3">
                    <a href="https://wa.me/8801700000000" target="_blank" class="inline-flex items-center gap-1.5 px-3 py-1.5 sm:py-2 rounded-xl bg-emerald-50 hover:bg-emerald-100 text-emerald-800 text-xs sm:text-sm font-bold border border-emerald-200 transition">
                        <i class="fa-brands fa-whatsapp text-emerald-600 text-sm sm:text-base"></i>
                        <span class="hidden sm:inline">হেল্পলাইন / হোয়াটসঅ্যাপ</span>
                        <span class="sm:hidden">হোয়াটসঅ্যাপ</span>
                    </a>
                    <a href="{{ route('courses.checkout', 'journalism-law-presentation') }}" class="inline-flex items-center gap-1.5 px-3.5 sm:px-5 py-2 sm:py-2.5 rounded-xl bg-gradient-to-r from-emerald-600 to-teal-600 hover:from-emerald-700 hover:to-teal-700 text-white text-xs sm:text-sm font-extrabold shadow-md shadow-emerald-600/25 transition">
                        <span>ভর্তি নিশ্চিত করুন</span>
                        <i class="fa-solid fa-arrow-right text-[10px]"></i>
                    </a>
                </div>

            </div>
        </div>
    </header>

    <!-- Main Content -->
    <main class="flex-grow">
        @if(session('success'))
            <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8 mt-4">
                <div class="p-4 rounded-xl bg-emerald-50 border border-emerald-200 text-emerald-800 flex items-center justify-between">
                    <div class="flex items-center gap-3">
                        <i class="fa-solid fa-circle-check text-emerald-600 text-xl"></i>
                        <span class="font-semibold">{{ session('success') }}</span>
                    </div>
                    <button onclick="this.parentElement.remove()" class="text-emerald-600 hover:text-emerald-900">
                        <i class="fa-solid fa-xmark"></i>
                    </button>
                </div>
            </div>
        @endif

        @yield('content')
    </main>

    <!-- Footer -->
    <footer class="bg-slate-900 text-slate-300 pt-16 pb-12 border-t border-slate-800">
        <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8">
            <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-5 gap-10 mb-12">
                
                <!-- About Academy -->
                <div class="lg:col-span-2 space-y-4">
                    <div class="flex items-center gap-3">
                        <div class="w-10 h-10 rounded-xl bg-emerald-600 flex items-center justify-center text-white shadow">
                            <i class="fa-solid fa-graduation-cap text-xl"></i>
                        </div>
                        <span class="text-xl font-bold text-white">আমার বাংলা একাডেমি</span>
                    </div>
                    <p class="text-sm text-slate-400 leading-relaxed max-w-sm">
                        দক্ষতা অর্জনে কোনো ভাষা বা ভৌগোলিক সীমাবদ্ধতা থাকতে পারে না। বিশ্বমানের সফটওয়্যার ইঞ্জিনিয়ারিং, আইটি স্কিলস ও প্রফেশনাল ক্যারিয়ার কোর্স এখন সম্পূর্ণ বাংলায়।
                    </p>
                    <div class="flex items-center gap-3 pt-2 text-slate-400">
                        <a href="#" class="w-9 h-9 rounded-lg bg-slate-800 hover:bg-emerald-600 hover:text-white flex items-center justify-center transition"><i class="fa-brands fa-facebook-f"></i></a>
                        <a href="#" class="w-9 h-9 rounded-lg bg-slate-800 hover:bg-emerald-600 hover:text-white flex items-center justify-center transition"><i class="fa-brands fa-youtube"></i></a>
                        <a href="#" class="w-9 h-9 rounded-lg bg-slate-800 hover:bg-emerald-600 hover:text-white flex items-center justify-center transition"><i class="fa-brands fa-linkedin-in"></i></a>
                        <a href="#" class="w-9 h-9 rounded-lg bg-slate-800 hover:bg-emerald-600 hover:text-white flex items-center justify-center transition"><i class="fa-brands fa-discord"></i></a>
                        <a href="#" class="w-9 h-9 rounded-lg bg-slate-800 hover:bg-emerald-600 hover:text-white flex items-center justify-center transition"><i class="fa-brands fa-github"></i></a>
                    </div>
                </div>

                <!-- LMS Features -->
                <div>
                    <h3 class="text-white font-bold text-base mb-4 tracking-wide">সিস্টেম ফিচারস</h3>
                    <ul class="space-y-2.5 text-sm">
                        <li><a href="{{ route('home') }}#course-curriculum" class="hover:text-emerald-400 transition flex items-center gap-2"><i class="fa-solid fa-angle-left text-xs rotate-180 text-emerald-500"></i> মডিউল ও লেকচার</a></li>
                        <li><a href="{{ route('live.classes') }}" class="hover:text-emerald-400 transition flex items-center gap-2"><i class="fa-solid fa-angle-left text-xs rotate-180 text-emerald-500"></i> জুম ও মিট লাইভ ক্লাস</a></li>
                        <li><a href="{{ route('exams.index') }}" class="hover:text-emerald-400 transition flex items-center gap-2"><i class="fa-solid fa-angle-left text-xs rotate-180 text-emerald-500"></i> লাইভ এক্সাম ও মক টেস্ট</a></li>
                        <li><a href="{{ route('home') }}#course-supports" class="hover:text-emerald-400 transition flex items-center gap-2"><i class="fa-solid fa-angle-left text-xs rotate-180 text-emerald-500"></i> ২৪/৭ লাইভ মেন্টর সাপোর্ট</a></li>
                        <li><a href="{{ route('home') }}#fees-payment" class="hover:text-emerald-400 transition flex items-center gap-2"><i class="fa-solid fa-angle-left text-xs rotate-180 text-emerald-500"></i> বিকাশ ও নগদ পেমেন্ট</a></li>
                    </ul>
                </div>

                <!-- Popular Courses -->
                <div>
                    <h3 class="text-white font-bold text-base mb-4 tracking-wide">চলমান কোর্স</h3>
                    <ul class="space-y-2.5 text-sm">
                        <li><a href="{{ route('courses.show', 'journalism-law-presentation') }}" class="hover:text-emerald-400 transition font-semibold text-white">সাংবাদিকতা, আইন ও উপস্থাপনা</a></li>
                        <li><a href="{{ route('home') }}#course-curriculum" class="hover:text-emerald-400 transition">১২টি মডিউল কারিকুলাম</a></li>
                        <li><a href="{{ route('home') }}#instructors" class="hover:text-emerald-400 transition">প্রশিক্ষক পরিচিতি</a></li>
                        <li><a href="{{ route('home') }}#fees-payment" class="text-amber-400 hover:underline inline-flex items-center gap-1 mt-1 font-semibold">বিশেষ ছাড় মাত্র ৯৯৯ টাকা &rarr;</a></li>
                    </ul>
                </div>

                <!-- Payment Methods & Support -->
                <div>
                    <h3 class="text-white font-bold text-base mb-4 tracking-wide">পেমেন্ট গেটওয়ে</h3>
                    <p class="text-xs text-slate-400 mb-3">আমরা তাৎক্ষণিক স্বয়ংক্রিয় ভেরিফিকেশন সহ সকল স্থানীয় পেমেন্ট সাপোর্ট করি:</p>
                    <div class="flex flex-wrap gap-2 mb-4">
                        <span class="px-2.5 py-1 rounded bg-pink-700/80 text-white text-xs font-bold flex items-center gap-1">
                            <i class="fa-solid fa-mobile-screen"></i> বিকাশ (bKash)
                        </span>
                        <span class="px-2.5 py-1 rounded bg-orange-600/80 text-white text-xs font-bold flex items-center gap-1">
                            <i class="fa-solid fa-money-bill-wave"></i> নগদ (Nagad)
                        </span>
                        <span class="px-2.5 py-1 rounded bg-purple-700/80 text-white text-xs font-bold flex items-center gap-1">
                            রকেট (Rocket)
                        </span>
                        <span class="px-2.5 py-1 rounded bg-blue-700/80 text-white text-xs font-bold flex items-center gap-1">
                            <i class="fa-solid fa-credit-card"></i> Visa/Mastercard
                        </span>
                    </div>
                    <div class="p-3 rounded-lg bg-slate-800 border border-slate-700 text-xs">
                        <span class="text-emerald-400 font-semibold block mb-1"><i class="fa-solid fa-headset mr-1"></i> জরুরি সহায়তা</span>
                        <span class="text-slate-300">সকাল ৯:০০ - রাত ১১:০০ টা</span>
                    </div>
                </div>

            </div>

            <!-- Bottom Copyright & Architecture Notice -->
            <div class="border-t border-slate-800 pt-8 flex flex-col sm:flex-row items-center justify-between text-xs text-slate-400 gap-4">
                <p>&copy; {{ date('Y') }} <strong>Amar Bangla Academy (আমার বাংলা একাডেমি)</strong>. সর্বস্বত্ব সংরক্ষিত।</p>
                <div class="flex items-center gap-4 text-slate-400">
                    <span class="inline-flex items-center gap-1 text-emerald-400">
                        <i class="fa-solid fa-shield-halved"></i> 100% CPanel & Local PC Compatible
                    </span>
                    <span>•</span>
                    <span>Laravel 12 LMS Edition</span>
                </div>
            </div>
        </div>
    </footer>

    <!-- Floating Sticky Mobile Conversion Bar (Bottom) -->
    <div class="fixed bottom-0 left-0 right-0 z-50 bg-slate-950/95 backdrop-blur-md border-t border-slate-700/80 p-3 sm:hidden shadow-2xl">
        <div class="flex items-center justify-between gap-3">
            <div>
                <div class="flex items-baseline gap-1.5">
                    <span class="text-xl font-black text-amber-400">৳৯৯৯</span>
                    <span class="text-xs text-slate-400 line-through">৳৩,০০০</span>
                </div>
                <span class="text-[10px] text-emerald-400 font-semibold block leading-tight">প্রথম ৫০ জনের বিশেষ অফার</span>
            </div>
            <a href="{{ route('courses.checkout', 'journalism-law-presentation') }}" class="flex-1 max-w-[210px] text-center py-2.5 px-4 rounded-xl bg-gradient-to-r from-emerald-500 to-teal-600 hover:from-emerald-600 hover:to-teal-700 text-white font-black text-xs shadow-lg shadow-emerald-600/40 flex items-center justify-center gap-1.5 transition">
                <span>ভর্তি নিশ্চিত করুন</span>
                <i class="fa-solid fa-arrow-right text-[10px]"></i>
            </a>
        </div>
    </div>

    @stack('scripts')
</body>
</html>
