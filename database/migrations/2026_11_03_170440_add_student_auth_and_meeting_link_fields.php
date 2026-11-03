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
        Schema::table('users', function (Blueprint $table) {
            if (!Schema::hasColumn('users', 'role')) {
                $table->string('role')->default('student')->after('name');
            }
            if (!Schema::hasColumn('users', 'phone')) {
                $table->string('phone')->nullable()->index()->after('email');
            }
            if (!Schema::hasColumn('users', 'username')) {
                $table->string('username')->nullable()->unique()->after('phone');
            }
            if (!Schema::hasColumn('users', 'plain_password')) {
                $table->string('plain_password')->nullable()->after('password');
            }
        });

        Schema::table('courses', function (Blueprint $table) {
            if (!Schema::hasColumn('courses', 'live_meeting_url')) {
                $table->string('live_meeting_url', 1000)->nullable()->after('live_schedule_text');
            }
            if (!Schema::hasColumn('courses', 'live_meeting_id')) {
                $table->string('live_meeting_id')->nullable()->after('live_meeting_url');
            }
            if (!Schema::hasColumn('courses', 'live_meeting_passcode')) {
                $table->string('live_meeting_passcode')->nullable()->after('live_meeting_id');
            }
            if (!Schema::hasColumn('courses', 'live_class_topic')) {
                $table->string('live_class_topic')->nullable()->after('live_meeting_passcode');
            }
            if (!Schema::hasColumn('courses', 'is_live_now')) {
                $table->boolean('is_live_now')->default(false)->after('live_class_topic');
            }
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('users', function (Blueprint $table) {
            $table->dropColumn(['role', 'phone', 'username', 'plain_password']);
        });

        Schema::table('courses', function (Blueprint $table) {
            $table->dropColumn([
                'live_meeting_url',
                'live_meeting_id',
                'live_meeting_passcode',
                'live_class_topic',
                'is_live_now',
            ]);
        });
    }
};
