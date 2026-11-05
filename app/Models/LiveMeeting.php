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
