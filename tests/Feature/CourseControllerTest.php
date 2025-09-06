<?php

namespace Tests\Feature;

use App\Models\Course;
use App\Models\Subject;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Foundation\Testing\WithFaker;
use Tests\TestCase;

class CourseControllerTest extends TestCase
{
    use RefreshDatabase;

    public function test_can_list_courses(): void
    {
        Course::factory()->count(3)->create();

        $response = $this->getJson('/api/courses');

        $response->assertStatus(200)
                ->assertJsonStructure([
                    'success',
                    'data' => [
                        '*' => [
                            'id',
                            'name',
                            'description',
                            'workload',
                            'created_at',
                            'updated_at',
                            'subjects'
                        ]
                    ]
                ]);
    }

    public function test_can_create_course(): void
    {
        $courseData = [
            'name' => 'Computer Science',
            'description' => 'A comprehensive computer science course',
            'workload' => 120
        ];

        $response = $this->postJson('/api/courses', $courseData);

        $response->assertStatus(201)
                ->assertJson([
                    'success' => true,
                    'message' => 'CREATE_SUCCESS'
                ]);

        $this->assertDatabaseHas('courses', $courseData);
    }

    public function test_can_show_course(): void
    {
        $course = Course::factory()->create();

        $response = $this->getJson("/api/courses/{$course->id}");

        $response->assertStatus(200)
                ->assertJson([
                    'success' => true,
                    'data' => [
                        'id' => $course->id,
                        'name' => $course->name,
                        'description' => $course->description,
                        'workload' => $course->workload
                    ]
                ]);
    }

    public function test_can_update_course(): void
    {
        $course = Course::factory()->create();
        $updateData = [
            'name' => 'Updated Course Name',
            'description' => 'Updated description',
            'workload' => 150
        ];

        $response = $this->patchJson("/api/courses/{$course->id}", $updateData);

        $response->assertStatus(200)
                ->assertJson([
                    'success' => true,
                    'message' => 'UPDATE_SUCCESS'
                ]);

        $this->assertDatabaseHas('courses', array_merge(['id' => $course->id], $updateData));
    }

    public function test_can_delete_course(): void
    {
        $course = Course::factory()->create();

        $response = $this->deleteJson("/api/courses/{$course->id}");

        $response->assertStatus(200)
                ->assertJson([
                    'success' => true,
                    'message' => 'DELETE_SUCCESS'
                ]);

        $this->assertDatabaseMissing('courses', ['id' => $course->id]);
    }

    public function test_cannot_delete_course_with_enrollments(): void
    {
        $course = Course::factory()->create();
        // Create an enrollment for this course
        $student = \App\Models\Student::factory()->create();
        \App\Models\Enrollment::create([
            'student_id' => $student->id,
            'course_id' => $course->id,
            'status' => 'active',
            'enrolled_at' => now()
        ]);

        $response = $this->deleteJson("/api/courses/{$course->id}");

        $response->assertStatus(422)
                ->assertJson([
                    'success' => false,
                    'message' => 'DELETE_FAILED'
                ]);
    }

    public function test_validation_errors_on_create(): void
    {
        $response = $this->postJson('/api/courses', []);

        $response->assertStatus(422)
                ->assertJson([
                    'success' => false,
                    'message' => 'CREATE_FAILED'
                ])
                ->assertJsonStructure([
                    'success',
                    'message',
                    'errors' => [
                        'name',
                        'description',
                        'workload'
                    ]
                ]);
    }
}
