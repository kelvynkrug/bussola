<?php

namespace App\Services;

use App\Models\Enrollment;
use App\Models\Student;
use App\Models\Course;
use App\Mail\EnrollmentConfirmationMail;
use Illuminate\Support\Facades\Mail;
use Illuminate\Support\Facades\DB;

class EnrollmentService
{
    public function enrollStudent(int $studentId, int $courseId): Enrollment
    {
        return DB::transaction(function () use ($studentId, $courseId) {
            $student = Student::findOrFail($studentId);
            $course = Course::findOrFail($courseId);

            $enrollment = Enrollment::create([
                'student_id' => $studentId,
                'course_id' => $courseId,
                'status' => 'active',
                'enrolled_at' => now(),
            ]);

            $this->sendEnrollmentConfirmation($enrollment);

            return $enrollment->load(['student', 'course']);
        });
    }

    protected function sendEnrollmentConfirmation(Enrollment $enrollment): void
    {
        try {
            Mail::to($enrollment->student->email)
                ->send(new EnrollmentConfirmationMail($enrollment));
        } catch (\Exception $e) {
            \Log::error('Failed to send enrollment confirmation email', [
                'enrollment_id' => $enrollment->id,
                'student_email' => $enrollment->student->email,
                'error' => $e->getMessage()
            ]);
        }
    }
}
