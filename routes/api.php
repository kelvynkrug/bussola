<?php

use Illuminate\Support\Facades\Route;
use App\Http\Controllers\Api\CourseController;
use App\Http\Controllers\Api\SubjectController;
use App\Http\Controllers\Api\StudentController;
use App\Http\Controllers\Api\EnrollmentController;

Route::get('courses', [CourseController::class, 'getAll']);
Route::post('courses', [CourseController::class, 'create']);
Route::get('courses/{course}', [CourseController::class, 'search']);
Route::patch('courses/{course}', [CourseController::class, 'update']);
Route::delete('courses/{course}', [CourseController::class, 'delete']);

Route::get('subjects', [SubjectController::class, 'getAll']);
Route::post('subjects', [SubjectController::class, 'create']);
Route::get('subjects/{subject}', [SubjectController::class, 'search']);
Route::patch('subjects/{subject}', [SubjectController::class, 'update']);
Route::delete('subjects/{subject}', [SubjectController::class, 'delete']);
Route::post('subjects/{subject}/attach-course', [SubjectController::class, 'attachToCourse']);

Route::get('students', [StudentController::class, 'getAll']);
Route::post('students', [StudentController::class, 'create']);
Route::get('students/{student}', [StudentController::class, 'search']);
Route::patch('students/{student}', [StudentController::class, 'update']);
Route::delete('students/{student}', [StudentController::class, 'delete']);
Route::get('students/cpf/{cpf}', [StudentController::class, 'findByCpf']);
Route::post('students/{student}/attach-course', [StudentController::class, 'attachToCourse']);

Route::get('enrollments', [EnrollmentController::class, 'getAll']);
Route::post('enrollments', [EnrollmentController::class, 'create']);
Route::get('enrollments/{enrollment}', [EnrollmentController::class, 'search']);
Route::patch('enrollments/{enrollment}', [EnrollmentController::class, 'update']);
Route::delete('enrollments/{enrollment}', [EnrollmentController::class, 'delete']);
Route::post('enrollments/{enrollment}/suspend', [EnrollmentController::class, 'suspend']);
Route::post('enrollments/{enrollment}/reactivate', [EnrollmentController::class, 'reactivate']);
