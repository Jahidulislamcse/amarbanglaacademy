<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;

class AttendanceSession extends Model
{
    use HasFactory;

    protected $guarded = ['id'];

    protected $casts = [
        'session_date' => 'date',
        'started_at' => 'datetime',
        'ended_at' => 'datetime',
        'is_active' => 'boolean',
    ];

    public function course(): BelongsTo
    {
        return $this->belongsTo(Course::class);
    }

    public function attendances(): HasMany
    {
        return $this->hasMany(Attendance::class);
    }

    public function creator(): BelongsTo
    {
        return $this->belongsTo(User::class, 'created_by');
    }

    public function getPresentCountAttribute(): int
    {
        return $this->attendances()->whereIn('status', ['present', 'late'])->count();
    }

    public function getAttendancePercentage(int $totalEnrolled): int
    {
        if ($totalEnrolled <= 0) {
            return 0;
        }
        return (int) round(($this->present_count / $totalEnrolled) * 100);
    }
}
