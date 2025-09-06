<?php

namespace Tests\Feature;

use App\Models\Course;
use App\Models\Student;
use App\Models\Enrollment;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Foundation\Testing\WithFaker;
use Tests\TestCase;

class EnrollmentControllerTest extends TestCase
{
    use RefreshDatabase;

    public function test_can_list_enrollments(): void
    {
        Enrollment::factory()->count(3)->create();

        $response = $this->getJson('/api/enrollments');

        $response->assertStatus(200)
                ->assertJsonStructure([
                    'success',
                    'data' => [
                        '*' => [
                            'id',
                            'student_id',
                            'course_id',
                            'status',
                            'enrolled_at',
                            'suspended_at',
                            'created_at',
                            'updated_at'
                        ]
                    ]
                ]);
    }

    public function test_can_create_enrollment(): void
    {
        $student = Student::factory()->create();
        $course = Course::factory()->create();

        $enrollmentData = [
            'student_id' => $student->id,
            'course_id' => $course->id,
            'status' => 'active'
        ];

        $response = $this->postJson('/api/enrollments', $enrollmentData);

        $response->assertStatus(201)
                ->assertJson([
                    'success' => true,
                    'message' => 'CREATE_SUCCESS'
                ]);

        $this->assertDatabaseHas('enrollments', $enrollmentData);
    }
}
