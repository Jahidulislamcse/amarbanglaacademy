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
    <div class="bg-gradient-to-r from-emerald-800 via-teal-700 to-emerald-900 text-white text-xs sm:text-sm py-2 px-4 shadow-sm">
        <div class="max-w-7xl mx-auto flex flex-wrap items-center justify-between gap-2">
            <div class="flex items-center space-x-2 space-x-reverse">
                <span class="inline-flex items-center px-2 py-0.5 rounded text-[11px] font-semibold bg-amber-400 text-slate-900">
                    <i class="fa-solid fa-bolt mr-1 text-xs"></i> স্পেশাল অফার
                </span>
                <span class="hidden sm:inline">নতুন লাইভ ব্যাচে ৬০% পর্যন্ত স্কলারশিপ চলছে!</span>
                <span class="sm:hidden font-medium">লাইভ ব্যাচে ৬০% ছাড়!</span>
            </div>
            <div class="flex items-center space-x-4 space-x-reverse text-emerald-100 text-xs">
                <span class="flex items-center gap-1.5"><i class="fa-solid fa-phone text-amber-300"></i> হেল্পলাইন: +৮৮০ ১৭০০-০০০০০০</span>
                <span class="hidden md:flex items-center gap-1.5"><i class="fa-solid fa-envelope text-amber-300"></i> support@amar-bangla.edu</span>
            </div>
        </div>
    </div>

    <!-- Navigation Header -->
    <header class="sticky top-0 z-50 bg-white/95 backdrop-blur-md border-b border-slate-200/80 shadow-xs">
        <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8">
            <div class="flex items-center justify-between h-20">
                
                <!-- Logo -->
                <a href="{{ route('home') }}" class="flex items-center gap-3 group">
                    <div class="w-12 h-12 rounded-xl bg-gradient-to-tr from-emerald-700 to-teal-500 flex items-center justify-center text-white shadow-md shadow-emerald-500/20 group-hover:scale-105 transition-transform duration-200">
                        <i class="fa-solid fa-graduation-cap text-2xl"></i>
                    </div>
                    <div>
                        <span class="text-xl sm:text-2xl font-bold tracking-tight text-slate-900 block leading-tight">
                            আমার বাংলা <span class="text-emerald-700">একাডেমি</span>
                        </span>
                        <span class="text-[11px] font-medium text-slate-500 tracking-wide uppercase">Amar Bangla Academy LMS</span>
                    </div>
                </a>

                <!-- Desktop Navigation Links -->
                <nav class="hidden lg:flex items-center space-x-1 space-x-reverse text-sm font-semibold text-slate-700">
                    <a href="{{ route('home') }}" class="px-3.5 py-2 rounded-lg hover:text-emerald-700 hover:bg-emerald-50 transition {{ request()->routeIs('home') ? 'text-emerald-700 bg-emerald-50/80' : '' }}">
                        <i class="fa-solid fa-house mr-1 text-xs"></i> হোম
                    </a>
                    <a href="{{ route('home') }}#featured-course" class="px-3.5 py-2 rounded-lg hover:text-emerald-700 hover:bg-emerald-50 transition">
                        <i class="fa-solid fa-laptop-code mr-1 text-xs"></i> ফ্ল্যাগশিপ কোর্স
                    </a>
                    <a href="{{ route('home') }}#all-courses" class="px-3.5 py-2 rounded-lg hover:text-emerald-700 hover:bg-emerald-50 transition">
                        <i class="fa-solid fa-book-open mr-1 text-xs"></i> সকল কোর্স
                    </a>
                    <a href="{{ route('live.classes') }}" class="px-3.5 py-2 rounded-lg hover:text-emerald-700 hover:bg-emerald-50 transition {{ request()->routeIs('live.classes') ? 'text-emerald-700 bg-emerald-50/80' : '' }}">
                        <span class="relative flex h-2 w-2 mr-1 inline-block">
                          <span class="animate-ping absolute inline-flex h-full w-full rounded-full bg-rose-400 opacity-75"></span>
                          <span class="relative inline-flex rounded-full h-2 w-2 bg-rose-500"></span>
                        </span>
                        লাইভ ক্লাস (Zoom & Meet)
                    </a>
                    <a href="{{ route('exams.index') }}" class="px-3.5 py-2 rounded-lg hover:text-emerald-700 hover:bg-emerald-50 transition {{ request()->routeIs('exams.index') ? 'text-emerald-700 bg-emerald-50/80' : '' }}">
                        <i class="fa-solid fa-clipboard-check mr-1 text-xs text-amber-500"></i> অনলাইন এক্সাম
                    </a>
                    <a href="{{ route('home') }}#instructors" class="px-3.5 py-2 rounded-lg hover:text-emerald-700 hover:bg-emerald-50 transition">
                        <i class="fa-solid fa-chalkboard-user mr-1 text-xs"></i> মেন্টরবৃন্দ
                    </a>
                </nav>

                <!-- Action CTA & Student Portal -->
                <div class="hidden sm:flex items-center gap-3">
                    <a href="{{ route('home') }}#course-curriculum" class="text-xs font-semibold text-slate-700 hover:text-emerald-700 px-3 py-2 rounded-lg border border-slate-200 hover:border-emerald-300 transition">
                        <i class="fa-solid fa-list-check mr-1 text-emerald-600"></i> কারিকুলাম দেখুন
                    </a>
                    <a href="{{ route('courses.checkout', 'full-stack-laravel-react-mastery') }}" class="inline-flex items-center gap-2 px-5 py-2.5 rounded-xl bg-gradient-to-r from-emerald-600 to-teal-600 hover:from-emerald-700 hover:to-teal-700 text-white text-sm font-bold shadow-md shadow-emerald-600/25 hover:shadow-lg transition-all duration-200">
                        <span>ভর্তি নিশ্চিত করুন</span>
                        <i class="fa-solid fa-arrow-left text-xs rotate-180"></i>
                    </a>
                </div>

                <!-- Mobile Menu Button -->
                <div class="flex lg:hidden">
                    <button @click="mobileMenuOpen = !mobileMenuOpen" type="button" class="p-2.5 rounded-lg text-slate-600 hover:text-slate-900 hover:bg-slate-100 transition focus:outline-none">
                        <i :class="mobileMenuOpen ? 'fa-solid fa-xmark text-xl' : 'fa-solid fa-bars text-xl'"></i>
                    </button>
                </div>
            </div>
        </div>

        <!-- Mobile Drawer Menu -->
        <div x-show="mobileMenuOpen" 
             x-transition:enter="transition ease-out duration-200"
             x-transition:enter-start="opacity-0 -translate-y-2"
             x-transition:enter-end="opacity-100 translate-y-0"
             x-transition:leave="transition ease-in duration-150"
             x-transition:leave-start="opacity-100 translate-y-0"
             x-transition:leave-end="opacity-0 -translate-y-2"
             class="lg:hidden border-b border-slate-200 bg-white px-4 pt-2 pb-6 space-y-2 shadow-lg">
            <a @click="mobileMenuOpen = false" href="{{ route('home') }}" class="block px-3 py-2.5 rounded-lg text-base font-semibold text-slate-700 hover:bg-emerald-50 hover:text-emerald-700">
                <i class="fa-solid fa-house w-6 text-emerald-600"></i> হোম
            </a>
            <a @click="mobileMenuOpen = false" href="{{ route('home') }}#featured-course" class="block px-3 py-2.5 rounded-lg text-base font-semibold text-slate-700 hover:bg-emerald-50 hover:text-emerald-700">
                <i class="fa-solid fa-laptop-code w-6 text-emerald-600"></i> ফ্ল্যাগশিপ কোর্স বিস্তারিত
            </a>
            <a @click="mobileMenuOpen = false" href="{{ route('home') }}#course-curriculum" class="block px-3 py-2.5 rounded-lg text-base font-semibold text-slate-700 hover:bg-emerald-50 hover:text-emerald-700">
                <i class="fa-solid fa-list-check w-6 text-emerald-600"></i> মডিউল ও কারিকুলাম
            </a>
            <a @click="mobileMenuOpen = false" href="{{ route('live.classes') }}" class="block px-3 py-2.5 rounded-lg text-base font-semibold text-slate-700 hover:bg-emerald-50 hover:text-emerald-700">
                <i class="fa-solid fa-video w-6 text-rose-500"></i> লাইভ ক্লাস (Zoom ও Google Meet)
            </a>
            <a @click="mobileMenuOpen = false" href="{{ route('exams.index') }}" class="block px-3 py-2.5 rounded-lg text-base font-semibold text-slate-700 hover:bg-emerald-50 hover:text-emerald-700">
                <i class="fa-solid fa-clipboard-check w-6 text-amber-500"></i> অনলাইন এক্সাম ও সার্টিফিকেট
            </a>
            <a @click="mobileMenuOpen = false" href="{{ route('home') }}#instructors" class="block px-3 py-2.5 rounded-lg text-base font-semibold text-slate-700 hover:bg-emerald-50 hover:text-emerald-700">
                <i class="fa-solid fa-chalkboard-user w-6 text-emerald-600"></i> মেন্টর ও ফ্যাকাল্টি
            </a>
            <div class="pt-4 border-t border-slate-100 flex flex-col gap-2">
                <a href="{{ route('courses.checkout', 'full-stack-laravel-react-mastery') }}" class="w-full text-center py-3 rounded-xl bg-emerald-600 text-white font-bold shadow hover:bg-emerald-700 transition">
                    ভর্তি নিশ্চিত করুন (বিকাশ / নগদ)
                </a>
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
                    <h3 class="text-white font-bold text-base mb-4 tracking-wide">জনপ্রিয় কোর্সসমূহ</h3>
                    <ul class="space-y-2.5 text-sm">
                        <li><a href="{{ route('courses.show', 'full-stack-laravel-react-mastery') }}" class="hover:text-emerald-400 transition">ফুল-স্ট্যাক লারাবেল ১২ ও রিয়্যাক্ট</a></li>
                        <li><a href="{{ route('courses.show', 'python-django-machine-learning') }}" class="hover:text-emerald-400 transition">পাইথন ও মেশিন লার্নিং বুটক্যাম্প</a></li>
                        <li><a href="{{ route('courses.show', 'ielts-spoken-english-masterclass') }}" class="hover:text-emerald-400 transition">আইইএলটিএস ও স্পোকেন ইংলিশ</a></li>
                        <li><a href="{{ route('home') }}#all-courses" class="text-emerald-400 hover:underline inline-flex items-center gap-1 mt-1 font-semibold">সকল কোর্স দেখুন &rarr;</a></li>
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

    @stack('scripts')
</body>
</html>
