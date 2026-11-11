<!DOCTYPE html>
<html lang="{{ str_replace('_', '-', app()->getLocale()) }}" class="scroll-smooth">
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <meta name="csrf-token" content="{{ csrf_token() }}">

    <title>@yield('title', 'Amar Bangla Academy - আধুনিক লার্নিং ম্যানেজমেন্ট সিস্টেম (LMS)')</title>
    <meta name="description" content="@yield('meta_description', 'আমার বাংলা একাডেমি — লাইভ জুম ও গুগল মিট ক্লাস, মডিউল ভিত্তিক কারিকুলাম, অনলাইন এক্সাম, এবং বিকাশ/নগদ পেমেন্ট সহ সম্পূর্ণ আধুনিক LMS।')">

    <!-- Favicon -->
    <link rel="icon" type="image/png" href="{{ asset('images/favicon.png') }}">
    <link rel="shortcut icon" href="{{ asset('favicon.ico') }}">
    <link rel="apple-touch-icon" href="{{ asset('images/favicon.png') }}">

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

    @php
        $layoutCourse = \App\Models\Course::with('batches')->where('is_featured', true)->first()
            ?? \App\Models\Course::with('batches')->first();
        $layoutOpenBatch = $layoutCourse?->openEnrollmentBatch();
        $layoutDisplayBatch = $layoutOpenBatch ?? $layoutCourse?->currentOrLatestBatch();
        $layoutEnrollmentOpen = !is_null($layoutOpenBatch);
        $layoutCheckoutUrl = $layoutCourse
            ? route('courses.checkout', $layoutCourse->slug)
            : route('courses.checkout', 'journalism-law-presentation');
        $layoutPrice = (int) ($layoutDisplayBatch?->effective_price ?? $layoutCourse?->effective_price ?? 999);
        $layoutRegularPrice = (int) ($layoutDisplayBatch?->effective_regular_price ?? $layoutCourse?->regular_price ?? 3000);
        $layoutPriceBn = \App\Models\Batch::toBanglaNumber(number_format($layoutPrice));
        $layoutRegularPriceBn = \App\Models\Batch::toBanglaNumber(number_format($layoutRegularPrice));
    @endphp

    <!-- Top Announcement Bar (Mobile Responsive & Properly Spaced) -->
    <div class="bg-gradient-to-r from-emerald-950 via-teal-900 to-slate-950 text-white text-xs py-2 sm:py-2.5 px-3 sm:px-6 shadow-sm border-b border-emerald-800/40">
        <div class="max-w-7xl mx-auto flex flex-col sm:flex-row items-center justify-between gap-2.5 text-center sm:text-left">
            
            <!-- Left: Urgency Badge & Offer Text -->
            <div class="flex items-center justify-center sm:justify-start gap-2.5 sm:gap-3 flex-wrap">
                @if($layoutEnrollmentOpen)
                    <span class="inline-flex items-center gap-1.5 px-2.5 py-0.5 rounded-full text-[10px] sm:text-[11px] font-black bg-amber-400 text-slate-950 shadow-xs flex-shrink-0">
                        <i class="fa-solid fa-fire text-rose-600 text-[11px]"></i> {{ $layoutOpenBatch->name }}-এ ভর্তি চলছে
                    </span>
                    <span class="hidden md:inline text-xs font-medium text-slate-200">
                        ন্যাশনাল জার্নালিস্ট ক্লাবের পৃষ্ঠপোষকতায় “{{ $layoutCourse?->bangla_title ?? 'সাংবাদিকতা, আইন ও উপস্থাপনা' }}” কোর্স — {{ $layoutOpenBatch->offer_label ?: ('প্রথম ' . \App\Models\Batch::toBanglaNumber($layoutOpenBatch->seat_limit) . ' জনের জন্য') }} মাত্র ৳{{ $layoutPriceBn }} টাকা!
                    </span>
                    <span class="md:hidden text-[11px] sm:text-xs font-semibold text-emerald-100">
                        {{ $layoutOpenBatch->offer_label ?: ($layoutOpenBatch->name . ' বিশেষ অফার') }} — মাত্র ৳{{ $layoutPriceBn }} টাকা!
                    </span>
                @else
                    <span class="inline-flex items-center gap-1.5 px-2.5 py-0.5 rounded-full text-[10px] sm:text-[11px] font-black bg-rose-600 text-white shadow-xs flex-shrink-0">
                        <i class="fa-solid fa-lock text-[10px]"></i> {{ $layoutDisplayBatch?->name ?? '১ম ব্যাচ' }} — ভর্তি বন্ধ
                    </span>
                    <span class="hidden md:inline text-xs font-medium text-slate-200">
                        “{{ $layoutCourse?->bangla_title ?? 'সাংবাদিকতা, আইন ও উপস্থাপনা' }}” কোর্সের {{ $layoutDisplayBatch?->name ?? 'চলমান ব্যাচ' }}-এর অনলাইন ভর্তি বন্ধ রয়েছে। পরবর্তী ব্যাচের আপডেটের জন্য WhatsApp-এ যুক্ত হন।
                    </span>
                    <span class="md:hidden text-[11px] sm:text-xs font-semibold text-amber-200">
                        ভর্তি বন্ধ রয়েছে • পরবর্তী ব্যাচের আপডেট পেতে যোগাযোগ করুন
                    </span>
                @endif
            </div>

            <!-- Right: Student Login, WhatsApp Helpline & Email -->
            <div class="flex items-center justify-center sm:justify-end gap-2.5 sm:gap-3 text-emerald-100 text-[11px] sm:text-xs flex-wrap">
                @auth
                    @if(auth()->user()->isAdmin())
                        <a href="{{ route('admin.students') }}" class="inline-flex items-center gap-1 text-emerald-300 hover:text-white font-bold bg-slate-900/80 px-2.5 py-1 rounded-full border border-emerald-500/40">
                            <i class="fa-solid fa-shield-halved text-emerald-400"></i>
                            <span>এডমিন ড্যাশবোর্ড</span>
                        </a>
                    @else
                        <a href="{{ route('student.dashboard') }}" class="inline-flex items-center gap-1 text-amber-300 hover:text-white font-bold bg-slate-900/80 px-2.5 py-1 rounded-full border border-amber-400/40">
                            <i class="fa-solid fa-graduation-cap text-amber-400"></i>
                            <span>আমার LMS ড্যাশবোর্ড</span>
                        </a>
                    @endif
                @else
                    <a href="{{ route('login') }}" class="inline-flex items-center gap-1 text-amber-300 hover:text-white font-bold bg-slate-900/80 px-2.5 py-1 rounded-full border border-amber-400/40">
                        <i class="fa-solid fa-user-graduate text-amber-400"></i>
                        <span>শিক্ষার্থী লগইন</span>
                    </a>
                @endauth
                <a href="https://wa.me/8801332549549" target="_blank" class="inline-flex items-center gap-1.5 hover:text-white transition group bg-emerald-900/70 hover:bg-emerald-800/80 px-2.5 py-1 rounded-full border border-emerald-700/50">
                    <i class="fa-brands fa-whatsapp text-emerald-300 text-sm group-hover:scale-110 transition-transform"></i>
                    <span>হোয়াটসঅ্যাপ: <strong class="text-white font-mono font-bold tracking-wide">01332-549549</strong></span>
                </a>
                <a href="mailto:info@amarbanglaacademy.com" class="hidden lg:inline-flex items-center gap-1.5 hover:text-white transition text-slate-300">
                    <i class="fa-solid fa-envelope text-amber-300 text-[11px]"></i>
                    <span>info@amarbanglaacademy.com</span>
                </a>
            </div>

        </div>
    </div>

    <!-- Clean Conversion Header (No Menu) -->
    <header class="sticky top-0 z-50 bg-white/95 backdrop-blur-md border-b border-slate-200/80 shadow-xs">
        <div class="max-w-7xl mx-auto px-3 sm:px-6 lg:px-8">
            <div class="flex items-center justify-between h-16 sm:h-20 gap-2">
                
                <!-- Logo & Academy Name -->
                <a href="{{ route('home') }}" class="flex items-center gap-2 group shrink-0">
                    <img src="{{ asset('images/AmarBanglaAcademy.png') }}"
                         alt="আমার বাংলা একাডেমি — Amar Bangla Academy"
                         class="h-8 min-[380px]:h-10 sm:h-12 w-auto object-contain group-hover:scale-[1.03] transition-transform duration-200">
                    <span class="sr-only">আমার বাংলা একাডেমি (Amar Bangla Academy)</span>
                </a>

                <!-- Quick Action Contact, Login & Enroll -->
                <div class="flex items-center gap-2 sm:gap-3">
                    <!-- Student Login Button (Clean, Visible & High Contrast) -->
                    @auth
                        @if(auth()->user()->isAdmin())
                            <a href="{{ route('admin.students') }}" class="inline-flex items-center gap-1.5 px-3 sm:px-4 py-2 sm:py-2.5 rounded-xl bg-slate-900 hover:bg-slate-800 text-white text-xs sm:text-sm font-bold shadow-sm transition">
                                <i class="fa-solid fa-shield-halved text-emerald-400"></i>
                                <span>এডমিন প্যানেল</span>
                            </a>
                        @else
                            <a href="{{ route('student.dashboard') }}" class="inline-flex items-center gap-1.5 px-3 sm:px-4 py-2 sm:py-2.5 rounded-xl bg-emerald-700 hover:bg-emerald-800 text-white text-xs sm:text-sm font-bold shadow-sm transition">
                                <i class="fa-solid fa-graduation-cap text-amber-300"></i>
                                <span>আমার পোর্টাল</span>
                            </a>
                        @endif
                    @else
                        <a href="{{ route('login') }}" class="inline-flex items-center gap-1.5 px-3 sm:px-4 py-2 sm:py-2.5 rounded-xl bg-slate-100 hover:bg-emerald-50 text-slate-800 hover:text-emerald-700 border border-slate-300 hover:border-emerald-300 text-xs sm:text-sm font-bold shadow-xs transition">
                            <i class="fa-solid fa-arrow-right-to-bracket text-emerald-600"></i>
                            <span>লগইন</span>
                        </a>
                    @endauth

                    <a href="https://wa.me/8801332549549" target="_blank" class="hidden md:inline-flex items-center gap-1.5 px-3 py-2 rounded-xl bg-emerald-50 hover:bg-emerald-100 text-emerald-800 text-xs sm:text-sm font-bold border border-emerald-200 transition">
                        <i class="fa-brands fa-whatsapp text-emerald-600 text-sm sm:text-base"></i>
                        <span class="hidden lg:inline">০১৩৩২-৫৪৯৫৪৯ (হোয়াটসঅ্যাপ)</span>
                        <span class="lg:hidden">হোয়াটসঅ্যাপ</span>
                    </a>
                    @if($layoutEnrollmentOpen)
                        <a href="{{ $layoutCheckoutUrl }}" class="inline-flex items-center gap-1.5 px-3.5 sm:px-5 py-2 sm:py-2.5 rounded-xl bg-gradient-to-r from-emerald-600 to-teal-600 hover:from-emerald-700 hover:to-teal-700 text-white text-xs sm:text-sm font-extrabold shadow-md shadow-emerald-600/25 transition">
                            <span>ভর্তি নিশ্চিত করুন</span>
                            <i class="fa-solid fa-arrow-right text-[10px]"></i>
                        </a>
                    @else
                        <a href="{{ $layoutCheckoutUrl }}" class="inline-flex items-center gap-1.5 px-3.5 sm:px-5 py-2 sm:py-2.5 rounded-xl bg-slate-800 hover:bg-slate-900 text-amber-300 border border-slate-700 text-xs sm:text-sm font-extrabold shadow-xs transition">
                            <i class="fa-solid fa-lock text-[11px]"></i>
                            <span>ভর্তি বন্ধ</span>
                        </a>
                    @endif
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

        @if(session('error'))
            <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8 mt-4">
                <div class="p-4 rounded-xl bg-rose-50 border border-rose-200 text-rose-800 flex items-center justify-between">
                    <div class="flex items-center gap-3">
                        <i class="fa-solid fa-circle-exclamation text-rose-600 text-xl"></i>
                        <span class="font-semibold">{{ session('error') }}</span>
                    </div>
                    <button onclick="this.parentElement.remove()" class="text-rose-600 hover:text-rose-900">
                        <i class="fa-solid fa-xmark"></i>
                    </button>
                </div>
            </div>
        @endif

        @if(session('info'))
            <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8 mt-4">
                <div class="p-4 rounded-xl bg-blue-50 border border-blue-200 text-blue-800 flex items-center justify-between">
                    <div class="flex items-center gap-3">
                        <i class="fa-solid fa-circle-info text-blue-600 text-xl"></i>
                        <span class="font-semibold">{{ session('info') }}</span>
                    </div>
                    <button onclick="this.parentElement.remove()" class="text-blue-600 hover:text-blue-900">
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
                    <div class="inline-flex items-center gap-3">
                        <div class="bg-white rounded-xl px-3 py-1.5 shadow-sm inline-flex items-center">
                            <img src="{{ asset('images/AmarBanglaAcademy.png') }}"
                                 alt="আমার বাংলা একাডেমি"
                                 class="h-9 sm:h-10 w-auto object-contain">
                        </div>
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
                        <li><a href="{{ route('login') }}" class="text-amber-300 hover:text-white transition flex items-center gap-2 font-bold"><i class="fa-solid fa-graduation-cap text-xs text-amber-400"></i> শিক্ষার্থী লগইন পোর্টাল &rarr;</a></li>
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
                        @if($layoutEnrollmentOpen)
                            <li><a href="{{ route('home') }}#fees-payment" class="text-amber-400 hover:underline inline-flex items-center gap-1 mt-1 font-semibold">{{ $layoutOpenBatch->name }} • কোর্স ফি মাত্র ৳{{ $layoutPriceBn }} &rarr;</a></li>
                        @else
                            <li><a href="{{ route('home') }}#fees-payment" class="text-rose-400 hover:underline inline-flex items-center gap-1 mt-1 font-semibold">{{ $layoutDisplayBatch?->name ?? '১ম ব্যাচ' }} — ভর্তি বন্ধ &rarr;</a></li>
                        @endif
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
                        <span class="text-emerald-400 font-semibold block mb-1"><i class="fa-solid fa-headset mr-1"></i> জরুরি সহায়তা / হোয়াটসঅ্যাপ</span>
                        <a href="https://wa.me/8801332549549" target="_blank" class="text-slate-200 font-bold hover:text-emerald-400 flex items-center gap-1.5 mt-0.5">
                            <i class="fa-brands fa-whatsapp text-emerald-400"></i>
                            <span>০১৩৩২-৫৪৯৫৪৯ (01332-549549)</span>
                        </a>
                        <span class="text-[11px] text-slate-400 block mt-1">সকাল ৯:০০ - রাত ১১:০০ টা</span>
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
                    <span>•</span>
                    <a href="{{ route('admin.login') }}" class="hover:text-emerald-400 text-slate-400 transition inline-flex items-center gap-1">
                        <i class="fa-solid fa-lock text-[10px]"></i>
                        <span>এডমিন</span>
                    </a>
                </div>
            </div>
        </div>
    </footer>

    <!-- Floating WhatsApp Button (Desktop & Tablet) -->
    <a href="https://wa.me/8801332549549" target="_blank" 
       class="hidden sm:inline-flex fixed bottom-6 right-6 z-40 items-center gap-2.5 px-4 py-3 rounded-full bg-emerald-600 hover:bg-emerald-500 text-white font-bold text-xs shadow-2xl shadow-emerald-700/50 hover:scale-105 transition-all duration-200 group border border-emerald-400/40"
       title="হোয়াটসঅ্যাপে সরাসরি যোগাযোগ করুন">
        <i class="fa-brands fa-whatsapp text-xl text-white group-hover:scale-110 transition-transform"></i>
        <span>হোয়াটসঅ্যাপ হেল্পলাইন</span>
    </a>

    <!-- Floating Sticky Mobile Conversion Bar (Bottom) -->
    <div class="fixed bottom-0 left-0 right-0 z-50 bg-slate-950/95 backdrop-blur-md border-t border-slate-700/80 p-3 sm:hidden shadow-2xl">
        @if($layoutEnrollmentOpen)
            <div class="flex items-center justify-between gap-3">
                <div>
                    <div class="flex items-baseline gap-1.5">
                        <span class="text-xl font-black text-amber-400">৳{{ $layoutPriceBn }}</span>
                        @if($layoutRegularPrice > $layoutPrice)
                            <span class="text-xs text-slate-400 line-through">৳{{ $layoutRegularPriceBn }}</span>
                        @endif
                    </div>
                    <span class="text-[10px] text-emerald-400 font-semibold block leading-tight">{{ $layoutOpenBatch->offer_label ?: ($layoutOpenBatch->name . ' — আসন সীমিত') }}</span>
                </div>
                <a href="{{ $layoutCheckoutUrl }}" class="flex-1 max-w-[210px] text-center py-2.5 px-4 rounded-xl bg-gradient-to-r from-emerald-500 to-teal-600 hover:from-emerald-600 hover:to-teal-700 text-white font-black text-xs shadow-lg shadow-emerald-600/40 flex items-center justify-center gap-1.5 transition">
                    <span>ভর্তি নিশ্চিত করুন</span>
                    <i class="fa-solid fa-arrow-right text-[10px]"></i>
                </a>
            </div>
        @else
            <div class="flex items-center justify-between gap-3">
                <div>
                    <span class="inline-flex items-center gap-1 text-xs font-black text-rose-400">
                        <i class="fa-solid fa-lock text-[10px]"></i> ভর্তি বন্ধ রয়েছে
                    </span>
                    <span class="text-[10px] text-slate-300 font-medium block leading-tight">{{ $layoutDisplayBatch?->name ?? '১ম ব্যাচ' }} • পরবর্তী ব্যাচের জন্য যোগাযোগ করুন</span>
                </div>
                <a href="https://wa.me/8801332549549" target="_blank" class="flex-1 max-w-[190px] text-center py-2.5 px-3 rounded-xl bg-emerald-600 hover:bg-emerald-700 text-white font-bold text-xs shadow-lg flex items-center justify-center gap-1.5 transition">
                    <i class="fa-brands fa-whatsapp text-sm"></i>
                    <span>পরবর্তী ব্যাচের তথ্য</span>
                </a>
            </div>
        @endif
    </div>

    @stack('scripts')
</body>
</html>
