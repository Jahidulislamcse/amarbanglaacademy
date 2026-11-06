<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class LiveMeeting extends Model
{
    use HasFactory;

    protected $guarded = ['id'];

    protected $casts = [
        'starts_at' => 'datetime',
        'ends_at' => 'datetime',
        'is_live_now' => 'boolean',
        'has_recording' => 'boolean',
    ];

    public function course(): BelongsTo
    {
        return $this->belongsTo(Course::class);
    }

    public function creator(): BelongsTo
    {
        return $this->belongsTo(User::class, 'created_by');
    }

    public function isLive(): bool
    {
        return (bool) $this->is_live_now;
    }

    /**
     * Whether this meeting has an available recording
     */
    public function hasRecording(): bool
    {
        return (bool) $this->has_recording && !empty($this->recording_url);
    }

    /**
     * Extract YouTube 11-character Video ID from recording URL
     */
    public function getYoutubeIdAttribute(): ?string
    {
        return self::extractYoutubeId($this->recording_url);
    }

    /**
     * Static helper to extract YouTube ID from any YouTube URL format
     */
    public static function extractYoutubeId(?string $url): ?string
    {
        if (empty($url)) {
            return null;
        }

        $url = trim($url);

        // Raw 11-char ID
        if (preg_match('/^[a-zA-Z0-9_-]{11}$/', $url)) {
            return $url;
        }

        // Standard YouTube URL formats (watch, share, embed, shorts, live)
        if (preg_match('/(?:youtu\.be\/|youtube\.com\/(?:embed\/|v\/|watch\?v=|watch\?.+&v=|live\/|shorts\/))([a-zA-Z0-9_-]{11})/i', $url, $matches)) {
            return $matches[1];
        }

        return null;
    }

    /**
     * Whether the recording is a direct video file (MP4, WebM, etc.)
     */
    public function isDirectVideo(): bool
    {
        if (empty($this->recording_url)) {
            return false;
        }

        $lower = strtolower($this->recording_url);
        return str_ends_with($lower, '.mp4') ||
               str_ends_with($lower, '.webm') ||
               str_ends_with($lower, '.m4v') ||
               str_contains($lower, '/storage/') ||
               str_contains($lower, '.mp4?');
    }

    /**
     * Scope meetings that have published recordings
     */
    public function scopeWithRecordings($query)
    {
        return $query->where('has_recording', true)
                     ->whereNotNull('recording_url')
                     ->where('recording_url', '!=', '');
    }

    /**
     * Helper to get formatted schedule text
     */
    public function getFormattedScheduleAttribute(): string
    {
        if (!empty($this->schedule_text)) {
            return $this->schedule_text;
        }

        if ($this->starts_at) {
            return $this->starts_at->format('d M, Y • h:i A');
        }

        return 'সময়সূচি শীঘ্রই জানানো হবে';
    }
}
