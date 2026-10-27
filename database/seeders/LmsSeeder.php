<?php

namespace Database\Seeders;

use App\Models\Course;
use App\Models\Exam;
use App\Models\Instructor;
use App\Models\Lesson;
use App\Models\Module;
use App\Models\User;
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\Hash;

class LmsSeeder extends Seeder
{
    public function run(): void
    {
        // 1. Create Demo Users
        $admin = User::firstOrCreate(
            ['email' => 'admin@amar-bangla.test'],
            [
                'name' => 'Admin - Amar Bangla Academy',
                'password' => Hash::make('password123'),
            ]
        );

        $student = User::firstOrCreate(
            ['email' => 'student@amar-bangla.test'],
            [
                'name' => 'Rahim Uddin (Student)',
                'password' => Hash::make('password123'),
            ]
        );

        // 2. Create Instructors
        $instructor1 = Instructor::create([
            'name' => 'Engr. Tanvir Ahmed',
            'bangla_name' => 'প্রকৌ. তানভীর আহমেদ',
            'title' => 'Principal Software Architect & Tech Lead',
            'organization' => 'Ex-Brain Station 23 | 10+ Years in Laravel & Cloud',
            'bio' => 'তানভীর আহমেদ গত ১০ বছর ধরে আন্তর্জাতিক ও দেশীয় শীর্ষ প্রতিষ্ঠানে সফটওয়্যার আর্কিটেকচার, লারাবেল, মাইক্রোসার্ভিস ও ক্লাউড সিস্টেম ডিজাইনে কাজ করছেন। তিনি ২০,০০০+ শিক্ষার্থীকে সফটওয়্যার ডেভেলপমেন্টে প্রশিক্ষণ দিয়েছেন।',
            'avatar' => 'https://images.unsplash.com/photo-1534528741775-53994a69daeb?auto=format&fit=crop&w=400&q=80',
            'rating' => 4.95,
            'students_count' => 14500,
            'experience_years' => 11,
            'social_links' => [
                'linkedin' => 'https://linkedin.com',
                'github' => 'https://github.com',
                'youtube' => 'https://youtube.com',
            ],
        ]);

        $instructor2 = Instructor::create([
            'name' => 'Sumaiya Rahman',
            'bangla_name' => 'সুমাইয়া রহমান',
            'title' => 'Senior Frontend Architect & UI/UX Specialist',
            'organization' => 'Toptal Freelancer & Full-Stack Consultant',
            'bio' => 'সুমাইয়া রহমান আধুনিক ফ্রন্টএন্ড ইকোসিস্টেম (React, Next.js, Tailwind, Vue) এবং হাই-পারফরম্যান্স ওয়েব অ্যাপ ডিজাইনে পারদর্শী। তিনি সিলিকন ভ্যালির একাধিক স্টার্টআপের সাথে রিমোট ডেভেলপার হিসেবে কাজ করছেন।',
            'avatar' => 'https://images.unsplash.com/photo-1580489944761-15a19d654956?auto=format&fit=crop&w=400&q=80',
            'rating' => 4.92,
            'students_count' => 9800,
            'experience_years' => 8,
            'social_links' => [
                'linkedin' => 'https://linkedin.com',
                'github' => 'https://github.com',
            ],
        ]);

        $instructor3 = Instructor::create([
            'name' => 'Dr. Kazi Mahfuzur Rahman',
            'bangla_name' => 'ড. কাজী মাহফুজুর রহমান',
            'title' => 'IELTS 8.5 Master Trainer & English Faculty',
            'organization' => 'British Council Certified Trainer',
            'bio' => 'ড. মাহফুজুর রহমান দীর্ঘ ১৫ বছর ধরে আইইএলটিএস, প্রফেশনাল স্পোকেন ইংলিশ ও ক্যারিয়ার কমিউনিকেশন নিয়ে কাজ করছেন। তার নির্দেশনায় সহস্রাধিক শিক্ষার্থী বিদেশে উচ্চশিক্ষা ও চাকরি লাভ করেছেন।',
            'avatar' => 'https://images.unsplash.com/photo-1507003211169-0a1dd7228f2d?auto=format&fit=crop&w=400&q=80',
            'rating' => 4.98,
            'students_count' => 18200,
            'experience_years' => 15,
            'social_links' => [
                'linkedin' => 'https://linkedin.com',
            ],
        ]);

        // 3. Create Flagship Course: Laravel & React Full-Stack
        $course1 = Course::create([
            'title' => 'Full-Stack Web & Software Engineering with Laravel 12 & React',
            'bangla_title' => 'লারাবেল ১২ ও রিয়্যাক্ট দিয়ে প্রফেশনাল ফুল-স্ট্যাক সফটওয়্যার ইঞ্জিনিয়ারিং',
            'slug' => 'full-stack-laravel-react-mastery',
            'subtitle' => 'জিরো থেকে হিরো: বাংলা ভাষায় ইন্ডাস্ট্রি-লেভেল লারাবেল ১২, রিয়্যাক্ট ১৯, জুম/মিট লাইভ ক্লাস, পেমেন্ট গেটওয়ে ও রিয়েল-ওয়ার্ল্ড প্রজেক্ট মাস্টারক্লাস।',
            'category' => 'Web & Software Development',
            'level' => 'Beginner to Advanced (জিরো থেকে প্রফেশনাল)',
            'description' => 'এই কোর্সটি বিশেষভাবে ডিজাইন করা হয়েছে যেন একজন শিক্ষার্থী সম্পূর্ণ বিগিনার লেভেল থেকে শুরু করে আন্তর্জাতিক ও স্থানীয় সফটওয়্যার ইন্ডাস্ট্রির উপযোগী ফুল-স্ট্যাক ডেভেলপার হিসেবে নিজেকে গড়ে তুলতে পারেন। কোর্স চলাকালীন প্রতিটি লাইভ ক্লাসে কোডিং শেখানো হবে, প্রতিদিন থাকবে গুগল মিটে লাইভ ডাউট সলভিং, প্রতি মডিউল শেষে লাইভ এক্সাম ও মক টেস্ট, এবং রিয়েল ক্লায়েন্ট লেভেলের ৫টি বড় প্রজেক্ট তৈরি ও লাইভ সার্ভারে ডিপ্লয়মেন্ট।',
            'thumbnail' => 'https://images.unsplash.com/photo-1517694712202-14dd9538aa97?auto=format&fit=crop&w=800&q=80',
            'preview_video_url' => 'https://www.youtube.com/embed/dQw4w9WgXcQ',
            'regular_price' => 12000.00,
            'discount_price' => 4500.00,
            'currency' => 'BDT',
            'batch_no' => 'Batch 14 (লাইভ ব্যাচ)',
            'start_date' => now()->addDays(7),
            'total_weeks' => 16,
            'total_hours' => 120,
            'total_lectures' => 64,
            'total_exams' => 12,
            'total_projects' => 6,
            'live_platform' => 'Zoom & Google Meet (লাইভ ইন্টারঅ্যাক্টিভ)',
            'live_schedule_text' => 'প্রতি শুক্র ও শনি রাত ৯:০০ টা - ১১:০০ টা (লাইভ)',
            'goals' => [
                'লারাবেল ১২ (Laravel 12) ফ্রেমওয়ার্কের আধুনিক ফিচার, সার্ভিস কন্টেইনার, মিডিয়া ও জব কিউ সম্পূর্ণ আয়ত্ত করা।',
                'রিয়্যাক্ট ১৯ (React 19), টেইলউইন্ড সিএসএস এবং স্টেট ম্যানেজমেন্ট দিয়ে ডায়নামিক সিঙ্গেল পেজ অ্যাপ্লিকেশন তৈরি।',
                'বিকাশ (bKash), নগদ (Nagad), রকেট ও SSLCommerz অনলাইন পেমেন্ট গেটওয়ে ইন্টিগ্রেশন শিখা।',
                'Zoom API ও Google Meet ইন্টিগ্রেশন করে লাইভ ক্লাসরুম ও কনফারেন্সিং মডিউল তৈরি।',
                'সম্পূর্ণ রিয়েল-টাইম মাল্টি-ইউজার LMS সিস্টেম, ই-কমার্স এবং জব পোর্টাল প্রজেক্ট ডেভেলপমেন্ট।',
                'গিটহাব কোলাবোরেশন, CI/CD পাইপলাইন, ডকার বেসিক এবং cPanel ও VPS ক্লাউড ডিপ্লয়মেন্ট।',
                'ইন্ডাস্ট্রি স্ট্যান্ডার্ড রেজুমে তৈরি, মক টেকনিক্যাল ইন্টারভিউ এবং জব প্লেসমেন্ট প্রস্তুতি।'
            ],
            'requirements' => [
                'একটি সাধারণ কম্পিউটার বা ল্যাপটপ এবং স্থিতিশীল ইন্টারনেট সংযোগ।',
                'সপ্তাহে অন্তত ৮-১০ ঘণ্টা প্র্যাকটিস করার মানসিকতা ও শেখার তীব্র আগ্রহ।',
                'পূর্বে কোনো প্রোগ্রামিং জ্ঞানের প্রয়োজন নেই — আমরা একদম শূন্য থেকে শুরু করব।'
            ],
            'features' => [
                '৬৪+ লাইভ ইন্টারঅ্যাক্টিভ ক্লাস (Zoom & Google Meet)',
                'প্রতিটি লাইভ ক্লাসের ফুল এইচডি রেকর্ডিং ও লাইফটাইম অ্যাক্সেস',
                '১২টি সাপ্তাহিক লাইভ এক্সাম ও কোডিং কুইজ',
                '৬টি পূর্ণাঙ্গ ইন্ডাস্ট্রি প্রজেক্ট (LMS, ই-কমার্স, পেমেন্ট গেটওয়ে সহ)',
                'প্রতিদিন ২ ঘণ্টা গুগল মিটে লাইভ ওয়ান-টু-ওয়ান প্রব্লেম সলভিং',
                'ডেডিকেটেড প্রাইভেট ডিসকর্ড ও টেলিগ্রাম ফোরাম',
                'ভেরিফায়েবল কিউআর কোড ডিজিটাল সার্টিফিকেট',
                'টপ পারফর্মারদের জন্য নিশ্চিত ইন্টার্নশিপ ও জব রেফারেল সাপোর্ট'
            ],
            'supports' => [
                'সরাসরি মেন্টরদের সাথে গুগল মিট স্ক্রিন শেয়ারিং সাপোর্ট (প্রতিদিন বিকেল ৫টা - রাত ১২টা)',
                'কোড রিভিউ ও পুশ রিকোয়েস্ট ফিডব্যাক উইদিন ২৪ ঘণ্টা',
                'কোর্স শেষ হলেও লাইফটাইম কমিউনিটি ও আপডেটেড মেটেরিয়ালস অ্যাক্সেস',
                'ইন্টারভিউ ও ফ্রিল্যান্সিং মার্কেটপ্লেস গাইডলাইন সেশন'
            ],
            'has_certificate' => true,
            'is_featured' => true,
            'is_popular' => true,
            'status' => 'published',
        ]);

        $course1->instructors()->attach([
            $instructor1->id => ['is_lead' => true],
            $instructor2->id => ['is_lead' => false],
        ]);

        // Modules for Course 1
        $modulesData = [
            [
                'title' => 'Orientation, Web Fundamentals & Environment Setup',
                'bangla_title' => 'মডিউল ১: ওরিয়েন্টেশন, ওয়েব ফান্ডামেন্টালস ও ডেভেলপমেন্ট এনভায়রনমেন্ট',
                'duration_text' => 'সপ্তাহ ১ | ৪টি ক্লাস',
                'description' => 'ওয়েব কিভাবে কাজ করে, ক্লায়েন্ট-সার্ভার আর্কিটেকচার, VS Code, Git, GitHub ও লোকাল ডেভেলপমেন্ট টুলস সেটআপ।',
                'lessons' => [
                    ['title' => 'কোর্স রোডম্যাপ, টুলস সেটআপ ও গিট পরিচিতি', 'type' => 'live_zoom', 'duration' => 90, 'preview' => true],
                    ['title' => 'HTML5 সিম্যান্টিক স্ট্রাকচার ও আধুনিক মেটা ট্যাগস', 'type' => 'recorded', 'duration' => 60, 'preview' => true],
                    ['title' => 'Tailwind CSS দিয়ে মডার্ন রেসপনসিভ UI ডিজাইন', 'type' => 'live_meet', 'duration' => 90, 'preview' => false],
                    ['title' => 'লাইভ ওয়ার্কশপ: প্রথম রেসপনসিভ ল্যান্ডিং পেজ তৈরি', 'type' => 'live_zoom', 'duration' => 120, 'preview' => false],
                ],
                'exam' => ['title' => 'উইক ১ কুইজ: HTML, CSS ও রেসপনসিভ ডিজাইন টেস্ট', 'marks' => 50, 'duration' => 30]
            ],
            [
                'title' => 'Modern JavaScript (ES6+), DOM & Asynchronous Programming',
                'bangla_title' => 'মডিউল ২: আধুনিক জাভাস্ক্রিপ্ট (ES6+), ডম ও অ্যাসিনক্রোনাস প্রোগ্রামিং',
                'duration_text' => 'সপ্তাহ ২-৩ | ৬টি ক্লাস',
                'description' => 'ভেরিয়েবল, ফাংশন, ক্লিপিং, অ্যারো ফাংশন, প্রমিজ, অ্যাসিঙ্ক-অ্যাওয়েট এবং ফেচ এপিআই হ্যান্ডলিং।',
                'lessons' => [
                    ['title' => 'JS ভেরিয়েবল, ডেটা টাইপ, অ্যারো ফাংশন ও স্প্রেড অপারেটর', 'type' => 'live_zoom', 'duration' => 90, 'preview' => false],
                    ['title' => 'DOM ম্যানিপুলেশন ও ইভেন্ট হ্যান্ডলিং ইন-ডেপথ', 'type' => 'recorded', 'duration' => 75, 'preview' => false],
                    ['title' => 'Promises, Async/Await ও REST API কনজিউম করা', 'type' => 'live_meet', 'duration' => 90, 'preview' => false],
                    ['title' => 'হাতে-কলমে প্রজেক্ট: ডায়নামিক কোর্স ফিল্টারিং অ্যাপ', 'type' => 'live_zoom', 'duration' => 100, 'preview' => false],
                ],
                'exam' => ['title' => 'জাভাস্ক্রিপ্ট ও প্রমিজ লাইভ এক্সাম', 'marks' => 100, 'duration' => 45]
            ],
            [
                'title' => 'Object Oriented PHP 8.2+ & Database Design (MySQL)',
                'bangla_title' => 'মডিউল ৩: অবজেক্ট ওরিয়েন্টেড পিএইচপি ৮.২+ এবং মাইএসকিউএল ডাটাবেজ আর্কিটেকচার',
                'duration_text' => 'সপ্তাহ ৪-৫ | ৬টি ক্লাস',
                'description' => 'OOP কনসেপ্ট (Classes, Inheritance, Polymorphism, Interfaces), PDO, ডাটাবেজ নরমালাইজেশন ও সিকিউরিটি।',
                'lessons' => [
                    ['title' => 'PHP 8.2 এর আধুনিক সিনট্যাক্স ও OOP ফান্ডামেন্টালস', 'type' => 'live_zoom', 'duration' => 90, 'preview' => false],
                    ['title' => 'ইন্টারফেস, অ্যাবস্ট্রাক্ট ক্লাস ও ট্রেইটস এর বাস্তব ব্যবহার', 'type' => 'recorded', 'duration' => 80, 'preview' => false],
                    ['title' => 'MySQL রিলেশনাল স্কিমা, ইনডেক্সিং ও অপ্টিমাইজড কুয়েরি', 'type' => 'live_meet', 'duration' => 90, 'preview' => false],
                    ['title' => 'কম্পোজার ও PSR স্ট্যান্ডার্ড নিয়ে আধুনিক প্যাকেজিং', 'type' => 'recorded', 'duration' => 60, 'preview' => false],
                ],
                'exam' => ['title' => 'OOP PHP ও ডাটাবেজ কুয়েরি টেস্ট', 'marks' => 100, 'duration' => 45]
            ],
            [
                'title' => 'Laravel 12 Architecture, Eloquent ORM & Blade Mastery',
                'bangla_title' => 'মডিউল ৪: লারাবেল ১২ আর্কিটেকচার, ইলোকয়েন্ট ও ব্লেড টেমপ্লেটিং',
                'duration_text' => 'সপ্তাহ ৬-৮ | ৮টি ক্লাস',
                'description' => 'রাউটিং, কন্ট্রোলার, মিডলওয়্যার, রিকোয়েস্ট ভ্যালিডেশন, ইলোকয়েন্ট রিলেশনশিপস এবং ব্লেড কম্পোনেন্টস।',
                'lessons' => [
                    ['title' => 'Laravel 12 লাইফসাইকেল, ডিরেক্টরি স্ট্রাকচার ও সার্ভিস কনফিগ', 'type' => 'live_zoom', 'duration' => 100, 'preview' => false],
                    ['title' => 'ব্লেড লেআউট, কম্পোনেন্ট, ইনক্লুডস ও সেশন ম্যানেজমেন্ট', 'type' => 'live_meet', 'duration' => 90, 'preview' => false],
                    ['title' => 'Eloquent রিলেশনস: One-to-Many, Many-to-Many ও Eager Loading', 'type' => 'live_zoom', 'duration' => 110, 'preview' => false],
                    ['title' => 'মাইগ্রেশন, সিডার, ফ্যাক্টরি ও ডাটাবেজ ট্রানজ্যাকশন', 'type' => 'recorded', 'duration' => 75, 'preview' => false],
                    ['title' => 'অথেনটিকেশন, রোলস ও পারমিশন (Spatie Permission)', 'type' => 'live_zoom', 'duration' => 100, 'preview' => false],
                ],
                'exam' => ['title' => 'লারাবেল ১২ মিড-টার্ম প্র্যাক্টিক্যাল কোডিং এক্সাম', 'marks' => 100, 'duration' => 60]
            ],
            [
                'title' => 'Live Interactive Classrooms: Zoom & Google Meet Integration',
                'bangla_title' => 'মডিউল ৫: লাইভ ক্লাসরুম — জুম ও গুগল মিট এপিআই ইন্টিগ্রেশন',
                'duration_text' => 'সপ্তাহ ৯-১০ | ৫টি ক্লাস',
                'description' => 'লারাবেল থেকে স্বয়ংক্রিয়ভাবে জুম মিটিং ও গুগল মিট তৈরি, পাসকোড জেনারেশন, শিডিউল ক্যালেন্ডার ও এটেন্ডেন্স ট্র্যাকিং।',
                'lessons' => [
                    ['title' => 'Zoom Server-to-Server OAuth API কনফিগারেশন', 'type' => 'live_zoom', 'duration' => 90, 'preview' => false],
                    ['title' => 'Google Calendar & Google Meet API লিংক জেনারেশন', 'type' => 'live_meet', 'duration' => 90, 'preview' => false],
                    ['title' => 'কোর্স ভিত্তিক লাইভ ক্লাস শিডিউলিং ও ইমেইল/এসএমএস নোটিফিকেশন', 'type' => 'live_zoom', 'duration' => 90, 'preview' => false],
                    ['title' => 'লাইভ সেশনে ক্লাউড রেকর্ডিং সেভ ও স্টুডেন্ট ড্যাশবোর্ডে স্ট্রিমিং', 'type' => 'recorded', 'duration' => 70, 'preview' => false],
                ],
                'exam' => ['title' => 'এপিআই ইন্টিগ্রেশন প্রজেক্ট এক্সাম', 'marks' => 100, 'duration' => 60]
            ],
            [
                'title' => 'Online Payment Gateways: bKash, Nagad, Rocket & Cards',
                'bangla_title' => 'মডিউল ৬: অনলাইন পেমেন্ট সিস্টেম — বিকাশ, নগদ, রকেট ও কার্ড ইন্টিগ্রেশন',
                'duration_text' => 'সপ্তাহ ১১-১২ | ৬টি ক্লাস',
                'description' => 'টোকেনাইজড পেমেন্ট চেকআউট, আইপিএন (IPN) হ্যান্ডলার, সিকিউর ট্রানজ্যাকশন ও অটো এনরোলমেন্ট অ্যাক্টিভেশন।',
                'lessons' => [
                    ['title' => 'বিকাশ পেমেন্ট গেটওয়ে (Checkout API) স্টেপ বাই স্টেপ', 'type' => 'live_zoom', 'duration' => 100, 'preview' => false],
                    ['title' => 'নগদ ও রকেট পেমেন্ট ভেরিফিকেশন এবং ইনভয়েস জেনারেশন', 'type' => 'live_meet', 'duration' => 90, 'preview' => false],
                    ['title' => 'SSLCommerz মাল্টি-কার্ড ও ইন্টারনেট ব্যাংকিং গেটওয়ে', 'type' => 'live_zoom', 'duration' => 90, 'preview' => false],
                    ['title' => 'পেমেন্ট ফেইলিউর, রিফান্ড ম্যানেজমেন্ট ও লেজার অডিট লগ', 'type' => 'recorded', 'duration' => 60, 'preview' => false],
                ],
                'exam' => ['title' => 'পেমেন্ট গেটওয়ে সিকিউরিটি ও আইপিএন হ্যান্ডলিং টেস্ট', 'marks' => 100, 'duration' => 45]
            ],
            [
                'title' => 'Exams, Quizzes, Grading & Digital Certificate Generation',
                'bangla_title' => 'মডিউল ৭: অনলাইন এক্সাম সিস্টেম, অটোমেটেড গ্রেডিং ও সার্টিফিকেট জেনারেটর',
                'duration_text' => 'সপ্তাহ ১৩ | ৪টি ক্লাস',
                'description' => 'টাইমার কুইজ, নেগেটিভ মার্কিং, ইনস্ট্যান্ট রেজাল্ট, পিডিএফ সার্টিফিকেট উইথ ভেরিফিকেশন কিউআর কোড।',
                'lessons' => [
                    ['title' => 'রিয়েল-টাইম টাইমার কুইজ ও অবজেক্টিভ টেস্ট আর্কিটেকচার', 'type' => 'live_zoom', 'duration' => 90, 'preview' => false],
                    ['title' => 'অটোমেটেড মার্কশিট ও পারফরম্যান্স অ্যানালিটিক্স চার্ট', 'type' => 'recorded', 'duration' => 75, 'preview' => false],
                    ['title' => 'ডায়নামিক PDF সার্টিফিকেট জেনারেটর ও QR Code ভেরিফায়ার', 'type' => 'live_meet', 'duration' => 90, 'preview' => false],
                ],
                'exam' => ['title' => 'এক্সাম ইঞ্জিন টেস্ট ও সার্টিফিকেট ভ্যালিডেশন এক্সাম', 'marks' => 100, 'duration' => 45]
            ],
            [
                'title' => 'Deployment to cPanel, VPS Cloud & CI/CD Pipelines',
                'bangla_title' => 'মডিউল ৮: সিপ্যানেল ও ভিপিএস ক্লাউড ডিপ্লয়মেন্ট, সিকিউরিটি ও অপ্টিমাইজেশন',
                'duration_text' => 'সপ্তাহ ১৪-১৬ | ৫টি ক্লাস',
                'description' => 'সিপ্যানেল শেয়ার্ড হোস্টিংয়ে স্মুথ সেটআপ, .htaccess রুলস, উবুন্টু ভিপিএস, Nginx, SSL, ক্লাউডফ্লেয়ার ও পারফরম্যান্স ক্যাশিং।',
                'lessons' => [
                    ['title' => 'সিপ্যানেল হোস্টিংয়ে লারাবেল ডিপ্লয়মেন্টের প্রফেশনাল গাইড', 'type' => 'live_zoom', 'duration' => 90, 'preview' => false],
                    ['title' => 'উবুন্টু ভিপিএস কনফিগারেশন, Nginx ও ফ্রি SSL সার্টিফিকেট', 'type' => 'live_meet', 'duration' => 90, 'preview' => false],
                    ['title' => 'GitHub Actions দিয়ে অটোমেটেড CI/CD ডেপ্লয়মেন্ট পাইপলাইন', 'type' => 'live_zoom', 'duration' => 90, 'preview' => false],
                    ['title' => 'ক্যাপস্টোন প্রজেক্ট প্রেজেন্টেশন ও মক টেকনিক্যাল ইন্টারভিউ', 'type' => 'live_zoom', 'duration' => 150, 'preview' => false],
                ],
                'exam' => ['title' => 'ফাইনাল ক্যাপস্টোন প্রজেক্ট ডিফেন্স ও সার্টিফিকেশন এক্সাম', 'marks' => 200, 'duration' => 120]
            ],
        ];

        foreach ($modulesData as $index => $mod) {
            $module = Module::create([
                'course_id' => $course1->id,
                'order' => $index + 1,
                'title' => $mod['title'],
                'bangla_title' => $mod['bangla_title'],
                'duration_text' => $mod['duration_text'],
                'description' => $mod['description'],
            ]);

            foreach ($mod['lessons'] as $lIndex => $les) {
                Lesson::create([
                    'module_id' => $module->id,
                    'order' => $lIndex + 1,
                    'title' => $les['title'],
                    'type' => $les['type'],
                    'duration_minutes' => $les['duration'],
                    'is_free_preview' => $les['preview'],
                    'live_meeting_url' => 'https://zoom.us/j/demo123456789',
                    'live_meeting_id' => '987 654 3210',
                    'live_meeting_passcode' => 'AMAR2026',
                    'live_starts_at' => now()->addDays($index * 5 + $lIndex),
                    'video_url' => 'https://www.youtube.com/watch?v=dQw4w9WgXcQ',
                    'summary' => 'ক্লাসের বিস্তারিত কোড স্ন্যাপশট ও হ্যান্ডস-অন রিসোর্স প্যাকেজ যুক্ত রয়েছে।'
                ]);
            }

            if (isset($mod['exam'])) {
                Exam::create([
                    'course_id' => $course1->id,
                    'module_id' => $module->id,
                    'title' => $mod['exam']['title'],
                    'type' => 'quiz',
                    'total_marks' => $mod['exam']['marks'],
                    'pass_marks' => (int)($mod['exam']['marks'] * 0.6),
                    'duration_minutes' => $mod['exam']['duration'],
                    'questions_count' => 20,
                    'is_active' => true,
                ]);
            }
        }

        // 4. Create Secondary Course: Python & Machine Learning
        $course2 = Course::create([
            'title' => 'Python, Django & Machine Learning Bootcamp',
            'bangla_title' => 'পাইথন, জ্যাঙ্গো এবং মেশিন লার্নিং কমপ্লিট বুটক্যাম্প',
            'slug' => 'python-django-machine-learning',
            'subtitle' => 'আধুনিক ডেটা অ্যানালাইটিক্স, ওয়েব ব্যাকএন্ড ও মেশিন লার্নিং মডেল ডেভেলপমেন্ট শিখুন বাংলা ভাষায়।',
            'category' => 'Data Science & AI',
            'level' => 'Beginner to Advanced',
            'description' => 'পাইথনের মৌলিক থেকে শুরু করে অবজেক্ট ওরিয়েন্টেড ডিজাইন, জ্যাঙ্গো রেস্ট ফ্রেমওয়ার্ক, পান্ডাস, নামপাই, স্কিকিট-লার্ন ও লাইভ মডেল ডিপ্লয়মেন্ট।',
            'thumbnail' => 'https://images.unsplash.com/photo-1526374965328-7f61d4dc18c5?auto=format&fit=crop&w=800&q=80',
            'regular_price' => 10000.00,
            'discount_price' => 3800.00,
            'currency' => 'BDT',
            'batch_no' => 'Batch 08',
            'start_date' => now()->addDays(12),
            'total_weeks' => 14,
            'total_hours' => 90,
            'total_lectures' => 48,
            'total_exams' => 8,
            'total_projects' => 4,
            'live_platform' => 'Zoom & Google Meet',
            'live_schedule_text' => 'প্রতি রবি ও মঙ্গলবার রাত ৯:০০ টা',
            'goals' => [
                'পাইথন ফান্ডামেন্টালস ও ডাটা স্ট্রাকচার আয়ত্ত করা',
                'জ্যাঙ্গো ফ্রেমওয়ার্ক দিয়ে ফুল স্কেল ব্যাকএন্ড তৈরি',
                'মেশিন লার্নিং ক্লাসিফিকেশন ও রিগ্রেশন মডেল তৈরি ও ফাইন টিউনিং'
            ],
            'features' => [
                '৪৮টি লাইভ ক্লাস জুম ও গুগল মিটে',
                'লাইভ সাপোর্ট ও ডেডিকেটেড কমিউনিটি',
                'সার্টিফিকেট ও প্রজেক্ট পোর্টফোলিও'
            ],
            'supports' => [
                'প্রতিদিন লাইভ গুগল মিট সাপোর্ট সেশন',
                'প্রাইভেট টেলিগ্রাম সাপোর্ট গ্রুপ'
            ],
            'has_certificate' => true,
            'is_featured' => true,
            'is_popular' => false,
            'status' => 'published',
        ]);
        $course2->instructors()->attach([$instructor1->id => ['is_lead' => true]]);

        // 5. Create Secondary Course: Spoken English & IELTS
        $course3 = Course::create([
            'title' => 'IELTS 7.5+ & Professional Spoken English Live Masterclass',
            'bangla_title' => 'আইইএলটিএস ৭.৫+ এবং প্রফেশনাল স্পোকেন ইংলিশ লাইভ মাস্টারক্লাস',
            'slug' => 'ielts-spoken-english-masterclass',
            'subtitle' => 'লাইভ স্পিকিং ড্রিল, লিসেনিং, রিডিং ও রাইটিং ট্যাকটিকস — ব্রিটিশ কাউন্সিল সার্টিফাইড ট্রেনারের সাথে।',
            'category' => 'Language & Communication',
            'level' => 'All Levels (সবার জন্য উন্মুক্ত)',
            'description' => 'উচ্চশিক্ষা কিংবা আন্তর্জাতিক কর্মক্ষেত্রে ইংরেজিতে কথা বলার জড়তা কাটিয়ে তুলুন। প্রতিদিনের লাইভ ওয়ান-অন-ওয়ান স্পিকিং প্র্যাকটিস ও রেগুলার মক টেস্ট।',
            'thumbnail' => 'https://images.unsplash.com/photo-1543269865-cbf427effbad?auto=format&fit=crop&w=800&q=80',
            'regular_price' => 8000.00,
            'discount_price' => 3200.00,
            'currency' => 'BDT',
            'batch_no' => 'Batch 21',
            'start_date' => now()->addDays(5),
            'total_weeks' => 12,
            'total_hours' => 70,
            'total_lectures' => 36,
            'total_exams' => 10,
            'total_projects' => 2,
            'live_platform' => 'Zoom Live Interactive',
            'live_schedule_text' => 'প্রতি শনি, সোম ও বুধবার রাত ৮:০০ টা',
            'goals' => [
                'ফ্লুয়েন্টলি ইংরেজিতে নিজের মতামত ও প্রেজেন্টেশন দিতে পারা',
                'IELTS রিডিং ও লিসেনিং মক টেস্টে ৭.৫+ ব্যান্ড স্কোর নিশ্চিত করা',
                'রিয়েল-টাইম ব্রিটিশ/আমেরিকান অ্যাকসেন্ট ও ন্যাচারাল প্রোনাউন্সিয়েশন অর্জন'
            ],
            'features' => [
                'সরাসরি জুমে ওয়ান-টু-ওয়ান স্পিকিং ইন্টারভিউ',
                '১০টি ফুল-লেংথ আনলিমিটেড মক টেস্ট',
                'কোর্স কমপ্লিশন ভেরিফায়েড সার্টিফিকেট'
            ],
            'supports' => [
                'প্রতিদিন স্পিকিং পার্টনার ক্লাব',
                'সরাসরি ইন্সট্রাক্টর ফিডব্যাক'
            ],
            'has_certificate' => true,
            'is_featured' => true,
            'is_popular' => true,
            'status' => 'published',
        ]);
        $course3->instructors()->attach([$instructor3->id => ['is_lead' => true]]);
    }
}
