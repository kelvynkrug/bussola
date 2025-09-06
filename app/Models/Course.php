<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsToMany;
use Illuminate\Database\Eloquent\Relations\HasMany;

class Course extends Model
{
    use HasFactory;

    protected $fillable = [
        'name',
        'description',
        'workload',
    ];

    protected $casts = [
        'workload' => 'integer',
    ];

    /**
     * Get the subjects that belong to the course.
     */
    public function subjects(): BelongsToMany
    {
        return $this->belongsToMany(Subject::class);
    }

    /**
     * Get the enrollments for the course.
     */
    public function enrollments(): HasMany
    {
        return $this->hasMany(Enrollment::class);
    }

    /**
     * Get the students enrolled in the course.
     */
    public function students(): BelongsToMany
    {
        return $this->belongsToMany(Student::class, 'enrollments')
                    ->withPivot('status', 'enrolled_at', 'suspended_at')
                    ->withTimestamps();
    }
}
