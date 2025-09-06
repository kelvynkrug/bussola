<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsToMany;
use Illuminate\Database\Eloquent\Relations\HasMany;

class Student extends Model
{
    use HasFactory;

    protected $fillable = [
        'name',
        'cpf',
        'birth_date',
        'email',
    ];

    protected $casts = [
        'birth_date' => 'date',
    ];

    /**
     * Get the enrollments for the student.
     */
    public function enrollments(): HasMany
    {
        return $this->hasMany(Enrollment::class);
    }

    /**
     * Get the courses the student is enrolled in.
     */
    public function courses(): BelongsToMany
    {
        return $this->belongsToMany(Course::class, 'enrollments')
                    ->withPivot('status', 'enrolled_at', 'suspended_at')
                    ->withTimestamps();
    }

    /**
     * Get the active enrollments for the student.
     */
    public function activeEnrollments(): HasMany
    {
        return $this->hasMany(Enrollment::class)->where('status', 'active');
    }
}
