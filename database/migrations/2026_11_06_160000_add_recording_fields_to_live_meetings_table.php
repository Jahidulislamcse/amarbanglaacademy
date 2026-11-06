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
        Schema::table('live_meetings', function (Blueprint $table) {
            if (!Schema::hasColumn('live_meetings', 'recording_url')) {
                $table->string('recording_url', 1000)->nullable()->after('meeting_url');
            }
            if (!Schema::hasColumn('live_meetings', 'recording_duration')) {
                $table->string('recording_duration', 50)->nullable()->after('recording_url');
            }
            if (!Schema::hasColumn('live_meetings', 'has_recording')) {
                $table->boolean('has_recording')->default(false)->after('recording_duration');
            }
            if (!Schema::hasColumn('live_meetings', 'recording_notes')) {
                $table->text('recording_notes')->nullable()->after('has_recording');
            }
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('live_meetings', function (Blueprint $table) {
            $table->dropColumn([
                'recording_url',
                'recording_duration',
                'has_recording',
                'recording_notes',
            ]);
        });
    }
};
