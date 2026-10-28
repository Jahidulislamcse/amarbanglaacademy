<?php

namespace Database\Seeders;

use App\Models\Course;
use App\Models\Exam;
use App\Models\Instructor;
use App\Models\Lesson;
use App\Models\Module;
use App\Models\User;
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Schema;

class LmsSeeder extends Seeder
{
    public function run(): void
    {
        // Disable foreign keys temporarily for clean reset (supports both MySQL and SQLite)
        Schema::disableForeignKeyConstraints();
        DB::table('payments')->truncate();
        DB::table('enrollments')->truncate();
        DB::table('exams')->truncate();
        DB::table('lessons')->truncate();
        DB::table('modules')->truncate();
        DB::table('course_instructor')->truncate();
        DB::table('courses')->truncate();
        DB::table('instructors')->truncate();
        Schema::enableForeignKeyConstraints();

        // 1. Create Demo Users
        User::firstOrCreate(
            ['email' => 'admin@amar-bangla.edu'],
            [
                'name' => 'Amar Bangla Academy Admin',
                'password' => Hash::make('password123'),
            ]
        );
        User::firstOrCreate(
            ['email' => 'admin@amarbanglaacademy.com'],
            [
                'name' => 'Amar Bangla Academy Admin',
                'password' => Hash::make('password123'),
            ]
        );

        // 2. Instructors (প্রশিক্ষকবৃন্দ)
        $nazir = Instructor::create([
            'name' => 'Nazir Ahmed Khan',
            'bangla_name' => 'সাংবাদিক নাজীর আহম্মেদ খান',
            'title' => 'সিনিয়র ক্রাইম রিপোর্টার ও অনুসন্ধানী সাংবাদিক',
            'organization' => '২০০৮ সাল থেকে বিভিন্ন গণমাধ্যমে কাজের বাস্তব অভিজ্ঞতা',
            'bio' => '২০০৮ সাল থেকে বিভিন্ন শীর্ষ গণমাধ্যমে কাজের অভিজ্ঞতা এবং দীর্ঘদিনের ক্রাইম রিপোর্টিংয়ের বাস্তব অভিজ্ঞতা থেকে তিনি শেখাবেন সংবাদ সংগ্রহ, তথ্য যাচাই, সোর্স ম্যানেজমেন্ট, ক্রাইম রিপোর্টিং ও সংবাদ প্রতিবেদন তৈরির কৌশল।',
            'avatar' => '/images/instructors/nazir-ahmed-khan.jpg',
            'rating' => 4.98,
            'students_count' => 3500,
            'experience_years' => 16,
            'social_links' => [
                'facebook' => 'https://www.facebook.com/reel/1401194435417527',
            ],
        ]);

        $mehedi = Instructor::create([
            'name' => 'Barrister Mehedi Hasan',
            'bangla_name' => 'ব্যারিস্টার মেহেদী হাসান',
            'title' => 'আইনজীবী, বাংলাদেশ সুপ্রিম কোর্ট',
            'organization' => 'আইন ও সংবিধান বিশেষজ্ঞ',
            'bio' => 'সুপ্রিম কোর্টের প্রথিতযশা আইনজীবী। তাঁর কাছ থেকে জানতে পারবেন সাংবাদিকের আইনগত অধিকার ও দায়িত্ব, সংবিধান, মানহানি, ব্যক্তিগত গোপনীয়তা, আদালতবিষয়ক রিপোর্টিং এবং সংবাদ প্রকাশের ক্ষেত্রে প্রয়োজনীয় আইনগত সতর্কতা।',
            'avatar' => '/images/instructors/mehedi-hasan.jpg',
            'rating' => 4.96,
            'students_count' => 2800,
            'experience_years' => 12,
            'social_links' => [],
        ]);

        $arif = Instructor::create([
            'name' => 'Arif Sarker',
            'bangla_name' => 'আরিফ সরকার',
            'title' => 'সিনিয়র সংবাদ উপস্থাপক ও ট্রেইনার',
            'organization' => 'জি টিভিসহ বিভিন্ন টেলিভিশন চ্যানেলের সাবেক ও বর্তমান উপস্থাপক',
            'bio' => 'জি টিভিসহ বিভিন্ন টেলিভিশন চ্যানেলের উপস্থাপক হিসেবে রয়েছে দীর্ঘদিনের বাস্তব অভিজ্ঞতা। তিনি শেখাবেন সংবাদ উপস্থাপনা, শুদ্ধ উচ্চারণ, কণ্ঠস্বর নিয়ন্ত্রণ, টেলিপ্রম্পটার ব্যবহার, সাক্ষাৎকার পরিচালনা এবং ক্যামেরার সামনে পেশাদারভাবে কথা বলার কৌশল।',
            'avatar' => '/images/instructors/arif-sarkar.jpg',
            'rating' => 4.99,
            'students_count' => 4200,
            'experience_years' => 14,
            'social_links' => [],
        ]);

        // 3. Featured Single Course: সাংবাদিকতা, আইন ও উপস্থাপনা
        $course = Course::create([
            'title' => 'Journalism, Law and News Presentation',
            'bangla_title' => 'সাংবাদিকতা, আইন ও উপস্থাপনা',
            'slug' => 'journalism-law-presentation',
            'subtitle' => 'একটি কোর্সে সাংবাদিকতার তিনটি গুরুত্বপূর্ণ ক্ষেত্র শেখার সুযোগ: সাংবাদিকতা, আইন এবং ক্যামেরার সামনে উপস্থাপনা। ১ মাসের অনলাইন ব্যবহারিক প্রশিক্ষণ কোর্স।',
            'category' => 'সাংবাদিকতা ও মিডিয়া স্টাডিজ',
            'level' => 'নতুন ও বর্তমান সাংবাদিকদের জন্য',
            'description' => "আপনি কি সাংবাদিকতা করছেন? নাকি সাংবাদিকতা পেশায় নতুন করে আসতে চান? সংবাদ সংগ্রহ করতে চান, কিন্তু জানেন না কীভাবে একটি সংবাদ সঠিকভাবে তৈরি করতে হয়? সাংবাদিকতার আইন সম্পর্কে জানতে চান? অথবা ক্যামেরার সামনে আত্মবিশ্বাসের সঙ্গে সংবাদ উপস্থাপন করতে চান?\n\nতাহলে আপনার জন্য রয়েছে একটি বিশেষ সুযোগ! শুরু হচ্ছে আমার বাংলা একাডেমীর “সাংবাদিকতা, আইন ও উপস্থাপনা” ১ মাসের অনলাইন প্রশিক্ষণ কোর্স।\n\nএকটি কোর্সে সাংবাদিকতার তিনটি গুরুত্বপূর্ণ ক্ষেত্র শেখার সুযোগ:\n১. সাংবাদিকতা (সংবাদ সংগ্রহ, তথ্য যাচাই, ক্রাইম রিপোর্টিং ও প্রতিবেদন তৈরি)\n২. আইন (সংবিধান, সাংবাদিকের অধিকার, মানহানি, আদালত রিপোর্টিং ও আইনি সতর্কতা)\n৩. উপস্থাপনা (শুদ্ধ উচ্চারণ, কণ্ঠস্বর নিয়ন্ত্রণ, টেলিপ্রম্পটার ও ক্যামেরার সামনে আত্মবিশ্বাসী প্রেজেন্টেশন)\n\nপৃষ্ঠপোষকতায়: ন্যাশনাল জার্নালিস্ট ক্লাব\nব্যবস্থাপনায়: সাংবাদিক নাজীর আহম্মেদ খান।",
            'thumbnail' => 'https://images.unsplash.com/photo-1585829365295-ab7cd400c167?auto=format&fit=crop&w=1000&q=80',
            'preview_video_url' => 'https://www.facebook.com/reel/1401194435417527',
            'regular_price' => 3000.00,
            'discount_price' => 999.00,
            'currency' => 'BDT',
            'batch_no' => '১ম ব্যাচ (শুরু: ৫ নভেম্বর ২০২৬)',
            'start_date' => '2026-11-05',
            'total_weeks' => 4,
            'total_hours' => 24,
            'total_lectures' => 12,
            'total_exams' => 2,
            'total_projects' => 3,
            'live_platform' => 'Zoom & Google Meet (সরাসরি লাইভ ক্লাস)',
            'live_schedule_text' => 'প্রতি বৃহস্পতিবার, শুক্রবার ও শনিবার রাত ৯টায়',
            'goals' => [
                'সংবাদ সংগ্রহ ও তথ্য যাচাইয়ের আধুনিক কৌশল ও সোর্স ম্যানেজমেন্ট',
                'রিপোর্ট লেখা ও ক্রাইম রিপোর্টিংয়ের বাস্তব কলাকৌশল',
                'অনুসন্ধানী সাংবাদিকতার (Investigative Journalism) কার্যকর পদ্ধতি',
                'বাংলাদেশের সংবিধান, গণমাধ্যমের স্বাধীনতা ও সাংবাদিকতার আইন',
                'মানহানি, ব্যক্তিগত গোপনীয়তা, সাইবার আইন ও আদালতবিষয়ক রিপোর্টিংয়ে আইনি সতর্কতা',
                'নিউজ প্রেজেন্টেশন, শুদ্ধ বাংলা উচ্চারণ ও কণ্ঠস্বর নিয়ন্ত্রণ (Voice Modulation)',
                'টেলিপ্রম্পটার ব্যবহার ও ক্যামেরার সামনে সাবলীল ও পেশাদার উপস্থিতি',
                'সাক্ষাৎকার পরিচালনা, লাইভ প্রেজেন্টেশন ও ব্যবহারিক ফিল্ড রিপোর্টিং'
            ],
            'requirements' => [
                'আপনি বর্তমান সাংবাদিক হোন, নতুন সাংবাদিক হোন কিংবা টেলিভিশন ও অনলাইন মিডিয়ায় ক্যারিয়ার গড়তে আগ্রহী হোন — কোর্সটি সবার জন্য উন্মুক্ত',
                'একটি সাধারণ স্মার্টফোন অথবা কম্পিউটার এবং ইন্টারনেট সংযোগ',
                'সাংবাদিকতা শেখার তীব্র আগ্রহ ও ক্লাসে নিয়মিত উপস্থিত থাকার মানসিকতা'
            ],
            'features' => [
                '১২টি বিশেষায়িত অনলাইন ক্লাস (Zoom ও Google Meet এ সরাসরি লাইভ)',
                'দেশের শীর্ষ ৩ জন প্রথিতযশা বিশেষজ্ঞের সরাসরি মেন্টরশিপ',
                'প্রতিটি লাইভ ক্লাসের ফুল এইচডি ভিডিও রেকর্ডিং ও লাইফটাইম অ্যাক্সেস',
                'বাস্তব অ্যাসাইনমেন্ট ও ফিল্ড প্র্যাকটিস ইভ্যালুয়েশন',
                'ন্যাশনাল জার্নালিস্ট ক্লাবের পৃষ্ঠপোষকতায় কোর্স কমপ্লিশন সার্টিফিকেট',
                'বিশেষ টেলিগ্রাম ও হোয়াটসঅ্যাপ প্রাইভেট ডাউট সলভিং কমিউনিটি',
                'প্রথম ব্যাচের প্রথম ৫০ জনের জন্য বিশেষ স্কলারশিপ মূল্য মাত্র ৯৯৯ টাকা!'
            ],
            'supports' => [
                'সরাসরি ইন্সট্রাক্টরদের সাথে লাইভ প্রশ্নোত্তর ও ফিডব্যাক',
                'ব্যবহারিক অ্যাসাইনমেন্ট ও রিপোর্ট কারেকশন',
                'কোর্স শেষ হলেও লাইফটাইম নেটওয়ার্কিং ও ক্যারিয়ার গাইডলাইন',
                'ন্যাশনাল জার্নালিস্ট ক্লাব ও মিডিয়া হাউজে সংযোগের সুযোগ'
            ],
            'has_certificate' => true,
            'is_featured' => true,
            'is_popular' => true,
            'status' => 'published',
        ]);

        // Attach Instructors
        $course->instructors()->attach([
            $nazir->id => ['is_lead' => true],
            $mehedi->id => ['is_lead' => false],
            $arif->id => ['is_lead' => false],
        ]);

        // 12 Exact Modules (এই কোর্সে থাকছে ১২টি মডিউল)
        $modules = [
            [
                'order' => 1,
                'title' => 'সাংবাদিকতার মৌলিক ধারণা ও গণমাধ্যমের ভূমিকা',
                'duration' => 'ক্লাস ১ | ২ ঘণ্টা',
                'desc' => 'সাংবাদিকতা কী, সংবাদের উপাদান, গণমাধ্যমের ধরন ও নৈতিক দায়িত্ব। প্রশিক্ষক: সাংবাদিক নাজীর আহম্মেদ খান।',
                'instructor' => 'সাংবাদিক নাজীর আহম্মেদ খান',
                'type' => 'live_zoom',
            ],
            [
                'order' => 2,
                'title' => 'সংবাদ সংগ্রহ, সোর্স তৈরি ও সোর্স ম্যানেজমেন্ট',
                'duration' => 'ক্লাস ২ | ২ ঘণ্টা',
                'desc' => 'কীভাবে বিশ্বাসযোগ্য সোর্স তৈরি করবেন, সোর্সের গোপনীয়তা রক্ষা এবং সরেজমিনে তথ্য সংগ্রহের বাস্তব কৌশল।',
                'instructor' => 'সাংবাদিক নাজীর আহম্মেদ খান',
                'type' => 'live_zoom',
            ],
            [
                'order' => 3,
                'title' => 'তথ্য যাচাই ও ফ্যাক্ট-চেকিংয়ের আধুনিক কৌশল',
                'duration' => 'ক্লাস ৩ | ২ ঘণ্টা',
                'desc' => 'ভুয়া খবর বা ফেইক নিউজ শনাক্তকরণ, ডিজিটাল টুলসের মাধ্যমে তথ্য যাচাই ও ক্রস-ভেরিফিকেশন।',
                'instructor' => 'সাংবাদিক নাজীর আহম্মেদ খান',
                'type' => 'live_zoom',
            ],
            [
                'order' => 4,
                'title' => 'সংবাদ রিপোর্ট তৈরি ও প্রতিবেদন লেখার কৌশল',
                'duration' => 'ক্লাস ৪ | ২ ঘণ্টা',
                'desc' => '৫W+1H ফর্মুলা, ইনভার্টেড পিরামিড পদ্ধতি, আকর্ষণীয় শিরোনাম (Headline) ও ইন্ট্রো লেখার নিয়ম।',
                'instructor' => 'সাংবাদিক নাজীর আহম্মেদ খান',
                'type' => 'live_zoom',
            ],
            [
                'order' => 5,
                'title' => 'ক্রাইম রিপোর্টিং ও অনুসন্ধানী সাংবাদিকতার কৌশল',
                'duration' => 'ক্লাস ৫ | ২ ঘণ্টা',
                'desc' => 'ক্রাইম সিন রিপোর্টিং, অপরাধ তদন্তের খবর প্রকাশ, গোপনীয় নথিপত্র অনুসন্ধান ও জীবনের নিরাপত্তা নিশ্চিতের কৌশল।',
                'instructor' => 'সাংবাদিক নাজীর আহম্মেদ খান',
                'type' => 'live_zoom',
            ],
            [
                'order' => 6,
                'title' => 'বাংলাদেশের সংবিধান ও সাংবাদিকতার স্বাধীনতা',
                'duration' => 'ক্লাস ৬ | ২ ঘণ্টা',
                'desc' => 'সংবিধানের ৩৯ অনুচ্ছেদ (বাক ও ভাব প্রকাশের স্বাধীনতা), গণমাধ্যমের স্বাধীনতা ও আইনি সীমানা। প্রশিক্ষক: ব্যারিস্টার মেহেদী হাসান।',
                'instructor' => 'ব্যারিস্টার মেহেদী হাসান',
                'type' => 'live_meet',
            ],
            [
                'order' => 7,
                'title' => 'সাংবাদিকতার আইন ও সাংবাদিকদের আইনি সুরক্ষা',
                'duration' => 'ক্লাস ৭ | ২ ঘণ্টা',
                'desc' => 'সাংবাদিকের আইনগত অধিকার, আইনি প্রতিকার, গ্রেপ্তার ও হয়রানি থেকে সুরক্ষার উপায়।',
                'instructor' => 'ব্যারিস্টার মেহেদী হাসান',
                'type' => 'live_meet',
            ],
            [
                'order' => 8,
                'title' => 'মানহানি, ব্যক্তিগত গোপনীয়তা ও আদালতবিষয়ক রিপোর্টিং',
                'duration' => 'ক্লাস ৮ | ২ ঘণ্টা',
                'desc' => 'মানহানি মামলা থেকে বাঁচার কৌশল, বিচারাধীন বিষয়ে সংবাদ প্রকাশের সতর্কতা এবং কোর্ট রিপোর্টিংয়ের নিয়ম।',
                'instructor' => 'ব্যারিস্টার মেহেদী হাসান',
                'type' => 'live_meet',
            ],
            [
                'order' => 9,
                'title' => 'সাইবার আইন, তথ্য অধিকার আইন ও ডিজিটাল মিডিয়া সতর্কতা',
                'duration' => 'ক্লাস ৯ | ২ ঘণ্টা',
                'desc' => 'সাইবার সিকিউরিটি আইন, সামাজিক যোগাযোগ মাধ্যমে সংবাদ প্রচারের সতর্কতা ও তথ্য অধিকার (RTI) আইনের প্রয়োগ।',
                'instructor' => 'ব্যারিস্টার মেহেদী হাসান',
                'type' => 'live_meet',
            ],
            [
                'order' => 10,
                'title' => 'সংবাদ উপস্থাপনা, শুদ্ধ উচ্চারণ ও কণ্ঠস্বর নিয়ন্ত্রণ',
                'duration' => 'ক্লাস ১০ | ২ ঘণ্টা',
                'desc' => 'টিভি ও ডিজিটাল নিউজ প্রেজেন্টেশন, শুদ্ধ বাংলা উচ্চারণ, ভয়েস মড্যুলেশন ও শ্বাস-প্রশ্বাস নিয়ন্ত্রণ। প্রশিক্ষক: আরিফ সরকার।',
                'instructor' => 'আরিফ সরকার',
                'type' => 'live_zoom',
            ],
            [
                'order' => 11,
                'title' => 'টেলিপ্রম্পটার ব্যবহার ও ক্যামেরার সামনে আত্মবিশ্বাসী উপস্থাপনা',
                'duration' => 'ক্লাস ১১ | ২ ঘণ্টা',
                'desc' => 'টেলিপ্রম্পটার পড়ার সঠিক টেকনিক, বডি ল্যাঙ্গুয়েজ, চোখের দৃষ্টি (Eye Contact) ও ক্যামেরার ভয় কাটানোর উপায়।',
                'instructor' => 'আরিফ সরকার',
                'type' => 'live_zoom',
            ],
            [
                'order' => 12,
                'title' => 'সাক্ষাৎকার পরিচালনা, লাইভ প্রেজেন্টেশন ও চূড়ান্ত ব্যবহারিক মূল্যায়ন',
                'duration' => 'ক্লাস ১২ | ২ ঘণ্টা',
                'desc' => 'সরাসরি প্রশ্ন করার কৌশল, ব্রেকিং নিউজ হ্যান্ডলিং, সরাসরি লাইভ রিপোর্টিং ডেমো ও ব্যবহারিক মূল্যায়ন।',
                'instructor' => 'যৌথ ফ্যাকাল্টি সেশন',
                'type' => 'live_zoom',
            ],
        ];

        foreach ($modules as $mod) {
            $m = Module::create([
                'course_id' => $course->id,
                'order' => $mod['order'],
                'title' => $mod['title'],
                'bangla_title' => 'মডিউল ' . $mod['order'] . ': ' . $mod['title'],
                'duration_text' => $mod['duration'],
                'description' => $mod['desc'],
            ]);

            Lesson::create([
                'module_id' => $m->id,
                'order' => 1,
                'title' => $mod['title'] . ' (লাইভ ক্লাস ও প্রশ্নোত্তর)',
                'type' => $mod['type'],
                'duration_minutes' => 120,
                'is_free_preview' => ($mod['order'] === 1),
                'live_meeting_url' => 'https://zoom.us/j/amarbangla2026',
                'live_meeting_id' => '850 1234 5678',
                'live_meeting_passcode' => 'BANGLA2026',
                'summary' => $mod['desc'] . ' | প্রশিক্ষক: ' . $mod['instructor'],
            ]);

            if ($mod['order'] === 6 || $mod['order'] === 12) {
                Exam::create([
                    'course_id' => $course->id,
                    'module_id' => $m->id,
                    'title' => $mod['order'] === 6 ? 'মধ্যবর্তী ব্যবহারিক অ্যাসাইনমেন্ট: ক্রাইম রিপোর্ট তৈরি' : 'চূড়ান্ত ব্যবহারিক মূল্যায়ন ও উপস্থাপনা টেস্ট',
                    'type' => 'practical',
                    'total_marks' => 100,
                    'pass_marks' => 50,
                    'duration_minutes' => 60,
                    'questions_count' => 10,
                    'is_active' => true,
                ]);
            }
        }
    }
}
