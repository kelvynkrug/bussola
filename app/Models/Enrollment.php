<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class Enrollment extends Model
{
    use HasFactory;

    protected $fillable = [
        'student_id',
        'course_id',
        'status',
        'enrolled_at',
        'suspended_at',
    ];

    protected $casts = [
        'enrolled_at' => 'datetime',
        'suspended_at' => 'datetime',
    ];

    /**
     * Get the student that owns the enrollment.
     */
    public function student(): BelongsTo
    {
        return $this->belongsTo(Student::class);
    }

    /**
     * Get the course that owns the enrollment.
     */
    public function course(): BelongsTo
    {
        return $this->belongsTo(Course::class);
    }

    /**
     * Check if the enrollment is active.
     */
    public function isActive(): bool
    {
        return $this->status === 'active';
    }

    /**
     * Check if the enrollment is suspended.
     */
    public function isSuspended(): bool
    {
        return $this->status === 'suspended';
    }

    /**
     * Check if the enrollment is cancelled.
     */
    public function isCancelled(): bool
    {
        return $this->status === 'cancelled';
    }
}
