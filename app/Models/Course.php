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
