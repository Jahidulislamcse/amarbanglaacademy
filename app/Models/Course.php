<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsToMany;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Database\Eloquent\Relations\HasManyThrough;

class Course extends Model
{
    use HasFactory;

    protected $guarded = ['id'];

    protected $casts = [
        'regular_price' => 'float',
        'discount_price' => 'float',
        'start_date' => 'date',
        'goals' => 'array',
        'requirements' => 'array',
        'features' => 'array',
        'supports' => 'array',
        'has_certificate' => 'boolean',
        'is_featured' => 'boolean',
        'is_popular' => 'boolean',
        'is_live_now' => 'boolean',
    ];

    public function instructors(): BelongsToMany
    {
        return $this->belongsToMany(Instructor::class, 'course_instructor')
            ->withPivot('is_lead')
            ->withTimestamps();
    }

    public function leadInstructor()
    {
        return $this->instructors()->wherePivot('is_lead', true)->first() 
            ?? $this->instructors()->first();
    }

    public function modules(): HasMany
    {
        return $this->hasMany(Module::class)->orderBy('order', 'asc');
    }

    public function lessons(): HasManyThrough
    {
        return $this->hasManyThrough(Lesson::class, Module::class);
    }

    public function exams(): HasMany
    {
        return $this->hasMany(Exam::class);
    }

    public function enrollments(): HasMany
    {
        return $this->hasMany(Enrollment::class);
    }

    public function payments(): HasMany
    {
        return $this->hasMany(Payment::class);
    }

    public function attendanceSessions(): HasMany
    {
        return $this->hasMany(AttendanceSession::class)->orderBy('session_date', 'desc');
    }

    public function attendances(): HasMany
    {
        return $this->hasMany(Attendance::class);
    }

    public function liveMeetings(): HasMany
    {
        return $this->hasMany(LiveMeeting::class)->orderBy('starts_at', 'asc');
    }

    /**
     * Get current active live meeting, or next upcoming scheduled meeting
     */
    public function currentOrNextMeeting(): ?LiveMeeting
    {
        // 1. Check if any meeting is explicitly marked live now
        $liveMeeting = $this->liveMeetings()->where('is_live_now', true)->latest('starts_at')->first();
        if ($liveMeeting) {
            return $liveMeeting;
        }

        // 2. Next upcoming scheduled meeting (today or future)
        $nextMeeting = $this->liveMeetings()
            ->where('status', '!=', 'cancelled')
            ->where('starts_at', '>=', now()->subHours(2))
            ->orderBy('starts_at', 'asc')
            ->first();

        if ($nextMeeting) {
            return $nextMeeting;
        }

        // 3. Fallback to latest meeting
        return $this->liveMeetings()->latest('starts_at')->first();
    }

    public function getEffectivePriceAttribute(): float
    {
        return $this->discount_price !== null && $this->discount_price > 0 
            ? $this->discount_price 
            : $this->regular_price;
    }

    public function getDiscountPercentageAttribute(): ?int
    {
        if ($this->regular_price > 0 && $this->discount_price && $this->discount_price < $this->regular_price) {
            return round((($this->regular_price - $this->discount_price) / $this->regular_price) * 100);
        }
        return null;
    }
}
