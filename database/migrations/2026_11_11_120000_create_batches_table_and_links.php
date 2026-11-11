<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    /**
     * Run the migrations.
     */
    public function up(): void
    {
        // 1. Create batches table
        Schema::create('batches', function (Blueprint $table) {
            $table->id();
            $table->foreignId('course_id')->constrained()->cascadeOnDelete();
            $table->string('name'); // e.g. '১ম ব্যাচ', '২য় ব্যাচ'
            $table->unsignedInteger('batch_number')->default(1);
            $table->date('start_date')->nullable();
            $table->string('start_date_text')->nullable(); // e.g. '৫ নভেম্বর ২০২৬'
            $table->string('schedule_text')->nullable()->default('প্রতি বৃহস্পতি, শুক্র ও শনিবার রাত ৯:০০ টা');
            $table->unsignedInteger('seat_limit')->default(50);
            $table->unsignedInteger('seats_remaining_override')->nullable();
            $table->decimal('regular_price', 10, 2)->nullable();
            $table->decimal('discount_price', 10, 2)->nullable();
            $table->string('discount_badge_text')->nullable();
            $table->string('offer_label')->nullable();
            $table->boolean('enrollment_open')->default(true);
            $table->string('status')->default('upcoming'); // upcoming, running, completed
            $table->boolean('is_active')->default(true);
            $table->text('notes')->nullable();
            $table->timestamps();
        });

        // 2. Link batch_id across enrollments, payments, live_meetings, and attendance_sessions
        Schema::table('enrollments', function (Blueprint $table) {
            if (!Schema::hasColumn('enrollments', 'batch_id')) {
                $table->foreignId('batch_id')->nullable()->after('course_id')->constrained('batches')->nullOnDelete();
            }
        });

        Schema::table('payments', function (Blueprint $table) {
            if (!Schema::hasColumn('payments', 'batch_id')) {
                $table->foreignId('batch_id')->nullable()->after('course_id')->constrained('batches')->nullOnDelete();
            }
        });

        Schema::table('live_meetings', function (Blueprint $table) {
            if (!Schema::hasColumn('live_meetings', 'batch_id')) {
                $table->foreignId('batch_id')->nullable()->after('course_id')->constrained('batches')->nullOnDelete();
            }
        });

        Schema::table('attendance_sessions', function (Blueprint $table) {
            if (!Schema::hasColumn('attendance_sessions', 'batch_id')) {
                $table->foreignId('batch_id')->nullable()->after('course_id')->constrained('batches')->nullOnDelete();
            }
        });

        // 3. Backfill 1st Batch (closed enrollment) for any existing courses and attach existing records
        $courses = DB::table('courses')->get();
        foreach ($courses as $course) {
            $batchId = DB::table('batches')->insertGetId([
                'course_id' => $course->id,
                'name' => $course->batch_no ?: '১ম ব্যাচ',
                'batch_number' => 1,
                'start_date' => $course->start_date ?: '2026-11-05',
                'start_date_text' => '৫ নভেম্বর ২০২৬',
                'schedule_text' => $course->live_schedule_text ?: 'প্রতি বৃহস্পতি, শুক্র ও শনিবার রাত ৯:০০ টা',
                'seat_limit' => 50,
                'seats_remaining_override' => null,
                'regular_price' => $course->regular_price,
                'discount_price' => $course->discount_price,
                'discount_badge_text' => '১ম ব্যাচে ৫০% এর বেশি স্পেশাল ছাড়',
                'offer_label' => 'প্রথম ৫০ জনের জন্য',
                'enrollment_open' => false, // Close 1st batch enrollment now
                'status' => 'running',
                'is_active' => true,
                'created_at' => now(),
                'updated_at' => now(),
            ]);

            DB::table('enrollments')
                ->where('course_id', $course->id)
                ->whereNull('batch_id')
                ->update(['batch_id' => $batchId]);

            DB::table('payments')
                ->where('course_id', $course->id)
                ->whereNull('batch_id')
                ->update(['batch_id' => $batchId]);

            DB::table('live_meetings')
                ->where('course_id', $course->id)
                ->whereNull('batch_id')
                ->update(['batch_id' => $batchId]);

            DB::table('attendance_sessions')
                ->where('course_id', $course->id)
                ->whereNull('batch_id')
                ->update(['batch_id' => $batchId]);
        }
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('attendance_sessions', function (Blueprint $table) {
            if (Schema::hasColumn('attendance_sessions', 'batch_id')) {
                $table->dropConstrainedForeignId('batch_id');
            }
        });

        Schema::table('live_meetings', function (Blueprint $table) {
            if (Schema::hasColumn('live_meetings', 'batch_id')) {
                $table->dropConstrainedForeignId('batch_id');
            }
        });

        Schema::table('payments', function (Blueprint $table) {
            if (Schema::hasColumn('payments', 'batch_id')) {
                $table->dropConstrainedForeignId('batch_id');
            }
        });

        Schema::table('enrollments', function (Blueprint $table) {
            if (Schema::hasColumn('enrollments', 'batch_id')) {
                $table->dropConstrainedForeignId('batch_id');
            }
        });

        Schema::dropIfExists('batches');
    }
};
