<!DOCTYPE html>
<html lang="bn">
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <meta name="csrf-token" content="{{ csrf_token() }}">

    <title>এডমিন লগইন - আমার বাংলা একাডেমি</title>

    <!-- Google Fonts: Hind Siliguri & Inter -->
    <link rel="preconnect" href="https://fonts.googleapis.com">
    <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
    <link href="https://fonts.googleapis.com/css2?family=Hind+Siliguri:wght@400;500;600;700&family=Inter:wght@400;500;600;700&display=swap" rel="stylesheet">

    <!-- Font Awesome Icons -->
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.5.1/css/all.min.css">

    <!-- Tailwind CSS CDN -->
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

    <!-- Alpine.js -->
    <script defer src="https://cdn.jsdelivr.net/npm/alpinejs@3.14.3/dist/cdn.min.js"></script>

    <style>
        body { font-family: 'Hind Siliguri', 'Inter', sans-serif; }
    </style>
</head>
<body class="bg-gradient-to-br from-slate-900 via-slate-800 to-emerald-950 text-slate-100 min-h-screen flex items-center justify-center p-4">

    <div class="w-full max-w-md" x-data="{
        email: '{{ old('email', 'admin@amar-bangla.edu') }}',
        password: '',
        showPassword: false,
        fillCredentials(userEmail, userPass) {
            this.email = userEmail;
            this.password = userPass;
        }
    }">
        <!-- Brand Header -->
        <div class="text-center mb-6">
            <a href="{{ route('home') }}" class="inline-flex items-center gap-3 group">
                <div class="w-14 h-14 rounded-2xl bg-gradient-to-tr from-emerald-600 to-emerald-400 text-white flex items-center justify-center font-bold text-2xl shadow-lg shadow-emerald-500/20">
                    আ
                </div>
            </a>
            <h1 class="text-2xl font-bold text-white mt-3">আমার বাংলা একাডেমি</h1>
            <p class="text-xs text-emerald-300 font-medium tracking-wide mt-1">কোর্স এডমিন ও শিক্ষার্থী ম্যানেজমেন্ট পোর্টাল</p>
        </div>

        <!-- Login Card -->
        <div class="bg-white text-slate-800 rounded-3xl shadow-2xl p-6 sm:p-8 border border-white/20">
            <div class="mb-6">
                <h2 class="text-xl font-bold text-slate-900 flex items-center gap-2">
                    <i class="fa-solid fa-lock text-emerald-600"></i>
                    <span>এডমিন লগইন</span>
                </h2>
                <p class="text-xs text-slate-500 mt-1">ভর্তি হওয়া শিক্ষার্থীদের তথ্য ও পেমেন্ট দেখতে লগইন করুন</p>
            </div>

            <!-- Flash Error -->
            @if(session('error'))
                <div class="mb-4 bg-red-50 border border-red-200 text-red-700 text-xs rounded-xl p-3 flex items-center gap-2">
                    <i class="fa-solid fa-circle-exclamation text-red-500"></i>
                    <span>{{ session('error') }}</span>
                </div>
            @endif

            <!-- Flash Success -->
            @if(session('success'))
                <div class="mb-4 bg-emerald-50 border border-emerald-200 text-emerald-700 text-xs rounded-xl p-3 flex items-center gap-2">
                    <i class="fa-solid fa-circle-check text-emerald-500"></i>
                    <span>{{ session('success') }}</span>
                </div>
            @endif

            <!-- Form -->
            <form method="POST" action="{{ route('admin.login.post') }}" class="space-y-4">
                @csrf

                <!-- Email -->
                <div>
                    <label for="email" class="block text-xs font-semibold text-slate-700 mb-1.5">
                        এডমিন ইমেইল <span class="text-red-500">*</span>
                    </label>
                    <div class="relative">
                        <div class="absolute inset-y-0 left-0 pl-3.5 flex items-center pointer-events-none text-slate-400">
                            <i class="fa-solid fa-envelope text-sm"></i>
                        </div>
                        <input type="email" id="email" name="email" x-model="email" required autofocus
                            class="w-full pl-10 pr-4 py-2.5 text-sm bg-slate-50 border @error('email') border-red-400 bg-red-50/30 @else border-slate-300 @enderror rounded-xl focus:ring-2 focus:ring-emerald-500 focus:border-emerald-500 outline-none transition"
                            placeholder="admin@amar-bangla.edu">
                    </div>
                    @error('email')
                        <p class="text-red-500 text-xs mt-1 flex items-center gap-1">
                            <i class="fa-solid fa-triangle-exclamation"></i>
                            <span>{{ $message }}</span>
                        </p>
                    @enderror
                </div>

                <!-- Password -->
                <div>
                    <label for="password" class="block text-xs font-semibold text-slate-700 mb-1.5">
                        পাসওয়ার্ড <span class="text-red-500">*</span>
                    </label>
                    <div class="relative">
                        <div class="absolute inset-y-0 left-0 pl-3.5 flex items-center pointer-events-none text-slate-400">
                            <i class="fa-solid fa-key text-sm"></i>
                        </div>
                        <input :type="showPassword ? 'text' : 'password'" id="password" name="password" x-model="password" required
                            class="w-full pl-10 pr-10 py-2.5 text-sm bg-slate-50 border @error('password') border-red-400 bg-red-50/30 @else border-slate-300 @enderror rounded-xl focus:ring-2 focus:ring-emerald-500 focus:border-emerald-500 outline-none transition"
                            placeholder="••••••••">
                        <button type="button" @click="showPassword = !showPassword" class="absolute inset-y-0 right-0 pr-3.5 flex items-center text-slate-400 hover:text-slate-600 transition">
                            <i :class="showPassword ? 'fa-solid fa-eye-slash' : 'fa-solid fa-eye'" class="text-sm"></i>
                        </button>
                    </div>
                    @error('password')
                        <p class="text-red-500 text-xs mt-1 flex items-center gap-1">
                            <i class="fa-solid fa-triangle-exclamation"></i>
                            <span>{{ $message }}</span>
                        </p>
                    @enderror
                </div>

                <!-- Remember Me -->
                <div class="flex items-center justify-between text-xs">
                    <label class="inline-flex items-center gap-2 cursor-pointer select-none">
                        <input type="checkbox" name="remember" value="1" class="rounded text-emerald-600 focus:ring-emerald-500 border-slate-300">
                        <span class="text-slate-600">লগইন মনে রাখুন</span>
                    </label>
                </div>

                <!-- Submit Button -->
                <button type="submit"
                    class="w-full py-3 px-4 rounded-xl font-bold text-white bg-gradient-to-r from-emerald-600 to-emerald-700 hover:from-emerald-700 hover:to-emerald-800 shadow-md shadow-emerald-600/30 transition transform active:scale-[0.99] flex items-center justify-center gap-2 text-sm">
                    <i class="fa-solid fa-arrow-right-to-bracket"></i>
                    <span>লগইন করুন</span>
                </button>
            </form>

            <!-- Quick Auto-Fill Credentials Box -->
            <div class="mt-6 pt-5 border-t border-slate-100 bg-slate-50 rounded-2xl p-3.5 text-xs text-slate-600">
                <div class="flex items-center justify-between mb-2">
                    <span class="font-semibold text-slate-700 flex items-center gap-1.5">
                        <i class="fa-solid fa-circle-info text-emerald-600"></i>
                        <span>ডিফল্ট এডমিন তথ্য:</span>
                    </span>
                    <button type="button" @click="fillCredentials('admin@amar-bangla.edu', 'password123')"
                        class="text-[11px] text-emerald-700 font-semibold hover:underline bg-emerald-50 px-2 py-0.5 rounded border border-emerald-200">
                        অটো পূরণ করুন
                    </button>
                </div>
                <div class="space-y-1 font-mono text-[11px] text-slate-500 bg-white p-2 rounded-lg border border-slate-200">
                    <div>ইমেইল: <span class="text-slate-800 select-all font-semibold">admin@amar-bangla.edu</span></div>
                    <div>পাসওয়ার্ড: <span class="text-slate-800 select-all font-semibold">password123</span></div>
                </div>
            </div>

            <!-- Back to website -->
            <div class="mt-5 text-center">
                <a href="{{ route('home') }}" class="text-xs text-slate-500 hover:text-emerald-700 inline-flex items-center gap-1.5 transition">
                    <i class="fa-solid fa-arrow-left"></i>
                    <span>মূল ওয়েবসাইটে ফিরে যান</span>
                </a>
            </div>
        </div>
    </div>

</body>
</html>
