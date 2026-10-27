<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    /**
     * Run the migrations.
     */
    public function up(): void
    {
        // 1. Instructors
        Schema::create('instructors', function (Blueprint $table) {
            $table->id();
            $table->string('name');
            $table->string('bangla_name')->nullable();
            $table->string('title');
            $table->string('organization')->nullable();
            $table->text('bio');
            $table->string('avatar')->nullable();
            $table->decimal('rating', 3, 2)->default(4.90);
            $table->unsignedInteger('students_count')->default(1200);
            $table->unsignedInteger('experience_years')->default(6);
            $table->json('social_links')->nullable();
            $table->timestamps();
        });

        // 2. Courses
        Schema::create('courses', function (Blueprint $table) {
            $table->id();
            $table->string('title');
            $table->string('bangla_title')->nullable();
            $table->string('slug')->unique();
            $table->string('subtitle', 500);
            $table->string('category')->default('Web & Software');
            $table->string('level')->default('Beginner to Advanced');
            $table->longText('description');
            $table->string('thumbnail')->nullable();
            $table->string('preview_video_url')->nullable();
            $table->decimal('regular_price', 10, 2);
            $table->decimal('discount_price', 10, 2)->nullable();
            $table->string('currency', 10)->default('BDT');
            $table->string('batch_no')->nullable()->default('Batch 01');
            $table->date('start_date')->nullable();
            $table->unsignedInteger('total_weeks')->default(16);
            $table->unsignedInteger('total_hours')->default(100);
            $table->unsignedInteger('total_lectures')->default(60);
            $table->unsignedInteger('total_exams')->default(10);
            $table->unsignedInteger('total_projects')->default(6);
            $table->string('live_platform')->default('Zoom & Google Meet');
            $table->string('live_schedule_text')->default('প্রতি শুক্র ও শনি রাত ৯:০০ টা - ১১:০০ টা');
            $table->json('goals')->nullable();
            $table->json('requirements')->nullable();
            $table->json('features')->nullable();
            $table->json('supports')->nullable();
            $table->boolean('has_certificate')->default(true);
            $table->boolean('is_featured')->default(false);
            $table->boolean('is_popular')->default(false);
            $table->string('status')->default('published');
            $table->timestamps();
        });

        // 3. Course-Instructor Pivot
        Schema::create('course_instructor', function (Blueprint $table) {
            $table->id();
            $table->foreignId('course_id')->constrained()->cascadeOnDelete();
            $table->foreignId('instructor_id')->constrained()->cascadeOnDelete();
            $table->boolean('is_lead')->default(true);
            $table->timestamps();
        });

        // 4. Modules
        Schema::create('modules', function (Blueprint $table) {
            $table->id();
            $table->foreignId('course_id')->constrained()->cascadeOnDelete();
            $table->unsignedInteger('order')->default(1);
            $table->string('title');
            $table->string('bangla_title')->nullable();
            $table->string('duration_text')->nullable();
            $table->text('description')->nullable();
            $table->timestamps();
        });

        // 5. Lessons
        Schema::create('lessons', function (Blueprint $table) {
            $table->id();
            $table->foreignId('module_id')->constrained()->cascadeOnDelete();
            $table->unsignedInteger('order')->default(1);
            $table->string('title');
            $table->string('type')->default('live_zoom'); // live_zoom, live_meet, recorded, quiz, project
            $table->unsignedInteger('duration_minutes')->default(60);
            $table->boolean('is_free_preview')->default(false);
            $table->string('live_meeting_url')->nullable();
            $table->string('live_meeting_id')->nullable();
            $table->string('live_meeting_passcode')->nullable();
            $table->dateTime('live_starts_at')->nullable();
            $table->string('video_url')->nullable();
            $table->text('summary')->nullable();
            $table->timestamps();
        });

        // 6. Exams & Quizzes
        Schema::create('exams', function (Blueprint $table) {
            $table->id();
            $table->foreignId('course_id')->constrained()->cascadeOnDelete();
            $table->foreignId('module_id')->nullable()->constrained()->nullOnDelete();
            $table->string('title');
            $table->string('type')->default('quiz'); // quiz, practical, mock_test, final_project
            $table->unsignedInteger('total_marks')->default(100);
            $table->unsignedInteger('pass_marks')->default(50);
            $table->unsignedInteger('duration_minutes')->default(45);
            $table->unsignedInteger('questions_count')->default(25);
            $table->boolean('is_active')->default(true);
            $table->timestamps();
        });

        // 7. Enrollments
        Schema::create('enrollments', function (Blueprint $table) {
            $table->id();
            $table->foreignId('user_id')->nullable()->constrained()->nullOnDelete();
            $table->foreignId('course_id')->constrained()->cascadeOnDelete();
            $table->string('student_name');
            $table->string('student_email');
            $table->string('student_phone');
            $table->string('status')->default('active'); // pending, active, completed, cancelled
            $table->timestamp('enrolled_at')->useCurrent();
            $table->timestamp('expires_at')->nullable();
            $table->timestamps();
        });

        // 8. Payments
        Schema::create('payments', function (Blueprint $table) {
            $table->id();
            $table->foreignId('enrollment_id')->nullable()->constrained()->nullOnDelete();
            $table->foreignId('course_id')->constrained()->cascadeOnDelete();
            $table->string('student_name');
            $table->string('student_email');
            $table->string('student_phone');
            $table->decimal('amount', 10, 2);
            $table->string('payment_method'); // bkash, nagad, rocket, card, bank
            $table->string('transaction_id')->unique();
            $table->string('sender_phone')->nullable();
            $table->string('status')->default('completed'); // pending, completed, failed
            $table->text('notes')->nullable();
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('payments');
        Schema::dropIfExists('enrollments');
        Schema::dropIfExists('exams');
        Schema::dropIfExists('lessons');
        Schema::dropIfExists('modules');
        Schema::dropIfExists('course_instructor');
        Schema::dropIfExists('courses');
        Schema::dropIfExists('instructors');
    }
};
