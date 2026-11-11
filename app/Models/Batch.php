<?php

namespace App\Models;

use Carbon\CarbonInterface;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;

class Batch extends Model
{
    use HasFactory;

    protected $guarded = ['id'];

    protected $casts = [
        'start_date' => 'date',
        'seat_limit' => 'integer',
        'seats_remaining_override' => 'integer',
        'regular_price' => 'float',
        'discount_price' => 'float',
        'enrollment_open' => 'boolean',
        'is_active' => 'boolean',
        'batch_number' => 'integer',
    ];

    public function course(): BelongsTo
    {
        return $this->belongsTo(Course::class);
    }

    public function enrollments(): HasMany
    {
        return $this->hasMany(Enrollment::class);
    }

    public function payments(): HasMany
    {
        return $this->hasMany(Payment::class);
    }

    public function liveMeetings(): HasMany
    {
        return $this->hasMany(LiveMeeting::class)->orderBy('starts_at', 'asc');
    }

    public function attendanceSessions(): HasMany
    {
        return $this->hasMany(AttendanceSession::class)->orderBy('session_date', 'desc');
    }

    /**
     * Count of active/completed enrolled students in this batch
     */
    public function getActiveStudentsCountAttribute(): int
    {
        return $this->enrollments()
            ->whereIn('status', ['active', 'completed'])
            ->count();
    }

    /**
     * Remaining seats (either manual override or computed from seat_limit)
     */
    public function getSeatsRemainingAttribute(): int
    {
        if (!is_null($this->seats_remaining_override)) {
            return max(0, (int) $this->seats_remaining_override);
        }

        $limit = $this->seat_limit ?: 50;
        return max(0, $limit - $this->active_students_count);
    }

    /**
     * Booked seats count
     */
    public function getSeatsBookedAttribute(): int
    {
        $limit = $this->seat_limit ?: 50;
        return max(0, min($limit, $limit - $this->seats_remaining));
    }

    /**
     * Effective regular price (falls back to course price if null)
     */
    public function getEffectiveRegularPriceAttribute(): float
    {
        return (float) ($this->regular_price ?? $this->course?->regular_price ?? 3000);
    }

    /**
     * Effective payable price (falls back to course effective price if null)
     */
    public function getEffectivePriceAttribute(): float
    {
        if (!is_null($this->discount_price)) {
            return (float) $this->discount_price;
        }

        if (!is_null($this->regular_price)) {
            return (float) $this->regular_price;
        }

        return (float) ($this->course?->effective_price ?? 999);
    }

    /**
     * Discount percentage for this batch
     */
    public function getDiscountPercentageAttribute(): int
    {
        $regular = $this->effective_regular_price;
        $sale = $this->effective_price;
        if ($regular > 0 && $sale < $regular) {
            return (int) round((($regular - $sale) / $regular) * 100);
        }
        return 0;
    }

    /**
     * Convert English digits/months to Bangla for display
     */
    public static function formatBanglaDate(?CarbonInterface $date): string
    {
        if (!$date) {
            return 'শীঘ্রই ঘোষণা হবে';
        }

        $months = [
            1 => 'জানুয়ারি',
            2 => 'ফেব্রুয়ারি',
            3 => 'মার্চ',
            4 => 'এপ্রিল',
            5 => 'মে',
            6 => 'জুন',
            7 => 'জুলাই',
            8 => 'আগস্ট',
            9 => 'সেপ্টেম্বর',
            10 => 'অক্টোবর',
            11 => 'নভেম্বর',
            12 => 'ডিসেম্বর',
        ];

        $enDigits = ['0', '1', '2', '3', '4', '5', '6', '7', '8', '9'];
        $bnDigits = ['০', '১', '২', '৩', '৪', '৫', '৬', '৭', '৮', '৯'];

        $day = str_replace($enDigits, $bnDigits, (string) $date->day);
        $month = $months[$date->month] ?? $date->format('M');
        $year = str_replace($enDigits, $bnDigits, (string) $date->year);

        return "{$day} {$month} {$year}";
    }

    /**
     * Formatted start date text
     */
    public function getFormattedStartDateAttribute(): string
    {
        if (!empty($this->start_date_text)) {
            return $this->start_date_text;
        }

        return self::formatBanglaDate($this->start_date);
    }

    /**
     * Formatted schedule text
     */
    public function getFormattedScheduleAttribute(): string
    {
        return $this->schedule_text ?: ($this->course?->live_schedule_text ?: 'প্রতি বৃহস্পতি, শুক্র ও শনিবার রাত ৯:০০ টা');
    }

    /**
     * Whether online enrollment is currently open for this batch
     */
    public function isEnrollmentOpen(): bool
    {
        return (bool) $this->enrollment_open && (bool) $this->is_active;
    }

    /**
     * Status label in Bangla
     */
    public function getStatusLabelAttribute(): string
    {
        return match ($this->status) {
            'upcoming' => 'আসন্ন ব্যাচ',
            'running' => 'ক্লাস চলমান',
            'completed' => 'সমাপ্ত',
            default => 'চলমান',
        };
    }

    /**
     * Helper to convert English number to Bangla digits
     */
    public static function toBanglaNumber($number): string
    {
        $en = ['0', '1', '2', '3', '4', '5', '6', '7', '8', '9'];
        $bn = ['০', '১', '২', '৩', '৪', '৫', '৬', '৭', '৮', '৯'];
        return str_replace($en, $bn, (string) $number);
    }
}
