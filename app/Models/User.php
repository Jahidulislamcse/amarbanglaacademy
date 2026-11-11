<?php

namespace App\Models;

// use Illuminate\Contracts\Auth\MustVerifyEmail;
use Database\Factories\UserFactory;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Foundation\Auth\User as Authenticatable;
use Illuminate\Notifications\Notifiable;

class User extends Authenticatable
{
    /** @use HasFactory<UserFactory> */
    use HasFactory, Notifiable;

    /**
     * The attributes that are mass assignable.
     *
     * @var list<string>
     */
    protected $fillable = [
        'name',
        'role',
        'email',
        'phone',
        'username',
        'password',
        'plain_password',
    ];

    public function isAdmin(): bool
    {
        return $this->role === 'admin' 
            || in_array(strtolower($this->email), ['admin@amar-bangla.edu', 'admin@amarbanglaacademy.com']);
    }

    public function isStudent(): bool
    {
        return $this->role === 'student' && !$this->isAdmin();
    }

    public function enrollments()
    {
        return $this->hasMany(Enrollment::class);
    }

    public function attendances()
    {
        return $this->hasMany(Attendance::class);
    }

    /**
     * Get attendance statistics for the user in a course (optionally scoped to batch)
     */
    public function attendanceStatsForCourse(?int $courseId = null, ?int $batchId = null): array
    {
        $sessionQuery = AttendanceSession::where('status', '!=', 'cancelled');
        if ($courseId) {
            $sessionQuery->where('course_id', $courseId);
        }
        if ($batchId) {
            $sessionQuery->where(function ($q) use ($batchId) {
                $q->where('batch_id', $batchId)->orWhereNull('batch_id');
            });
        }
        $sessionIds = $sessionQuery->pluck('id');
        $totalSessions = $sessionIds->count();

        $attQuery = $this->attendances()->whereIn('status', ['present', 'late']);
        if ($courseId) {
            $attQuery->where('course_id', $courseId);
        }
        if ($batchId) {
            $attQuery->whereIn('attendance_session_id', $sessionIds);
        }
        $presentCount = $attQuery->count();

        $percentage = $totalSessions > 0 ? (int) round(($presentCount / $totalSessions) * 100) : 100;

        return [
            'total_sessions' => $totalSessions,
            'present_count' => $presentCount,
            'absent_count' => max(0, $totalSessions - $presentCount),
            'percentage' => $percentage,
        ];
    }

    /**
     * The attributes that should be hidden for serialization.
     *
     * @var list<string>
     */
    protected $hidden = [
        'password',
        'remember_token',
    ];

    /**
     * Get the attributes that should be cast.
     *
     * @return array<string, string>
     */
    protected function casts(): array
    {
        return [
            'email_verified_at' => 'datetime',
            'password' => 'hashed',
        ];
    }
}
