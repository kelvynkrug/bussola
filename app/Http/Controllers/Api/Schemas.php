<?php

namespace App\Http\Controllers\Api;

/**
 * @OA\Schema(
 *     schema="Course",
 *     type="object",
 *     title="Course",
 *     description="Modelo de Curso",
 *     @OA\Property(property="id", type="integer", example=1),
 *     @OA\Property(property="name", type="string", example="Computer Science"),
 *     @OA\Property(property="description", type="string", example="A comprehensive computer science program"),
 *     @OA\Property(property="workload", type="integer", example=120),
 *     @OA\Property(property="created_at", type="string", format="date-time"),
 *     @OA\Property(property="updated_at", type="string", format="date-time"),
 *     @OA\Property(
 *         property="subjects",
 *         type="array",
 *         @OA\Items(ref="#/components/schemas/Subject")
 *     )
 * )
 *
 * @OA\Schema(
 *     schema="Subject",
 *     type="object",
 *     title="Subject",
 *     description="Modelo de Disciplina",
 *     @OA\Property(property="id", type="integer", example=1),
 *     @OA\Property(property="name", type="string", example="Introduction to Programming"),
 *     @OA\Property(property="description", type="string", example="Basic programming concepts"),
 *     @OA\Property(property="workload", type="integer", example=40),
 *     @OA\Property(property="created_at", type="string", format="date-time"),
 *     @OA\Property(property="updated_at", type="string", format="date-time"),
 *     @OA\Property(
 *         property="courses",
 *         type="array",
 *         @OA\Items(ref="#/components/schemas/Course")
 *     )
 * )
 *
 * @OA\Schema(
 *     schema="Student",
 *     type="object",
 *     title="Student",
 *     description="Modelo de Aluno",
 *     @OA\Property(property="id", type="integer", example=1),
 *     @OA\Property(property="name", type="string", example="João Silva"),
 *     @OA\Property(property="cpf", type="string", example="12345678901"),
 *     @OA\Property(property="birth_date", type="string", format="date", example="1995-03-15"),
 *     @OA\Property(property="email", type="string", format="email", example="joao.silva@email.com"),
 *     @OA\Property(property="created_at", type="string", format="date-time"),
 *     @OA\Property(property="updated_at", type="string", format="date-time"),
 *     @OA\Property(
 *         property="courses",
 *         type="array",
 *         @OA\Items(ref="#/components/schemas/Course")
 *     )
 * )
 *
 * @OA\Schema(
 *     schema="Enrollment",
 *     type="object",
 *     title="Enrollment",
 *     description="Modelo de Matrícula",
 *     @OA\Property(property="id", type="integer", example=1),
 *     @OA\Property(property="student_id", type="integer", example=1),
 *     @OA\Property(property="course_id", type="integer", example=1),
 *     @OA\Property(property="status", type="string", enum={"active", "suspended", "cancelled"}, example="active"),
 *     @OA\Property(property="enrolled_at", type="string", format="date-time"),
 *     @OA\Property(property="suspended_at", type="string", format="date-time", nullable=true),
 *     @OA\Property(property="created_at", type="string", format="date-time"),
 *     @OA\Property(property="updated_at", type="string", format="date-time"),
 *     @OA\Property(property="student", ref="#/components/schemas/Student"),
 *     @OA\Property(property="course", ref="#/components/schemas/Course")
 * )
 *
 * @OA\Schema(
 *     schema="Error",
 *     type="object",
 *     title="Error",
 *     description="Modelo de Erro",
 *     @OA\Property(property="success", type="boolean", example=false),
 *     @OA\Property(property="message", type="string", example="Error message"),
 *     @OA\Property(
 *         property="errors",
 *         type="object",
 *         additionalProperties=@OA\Schema(type="array", @OA\Items(type="string"))
 *     )
 * )
 *
 * @OA\Schema(
 *     schema="Success",
 *     type="object",
 *     title="Success",
 *     description="Modelo de Sucesso",
 *     @OA\Property(property="success", type="boolean", example=true),
 *     @OA\Property(property="message", type="string", example="Operation completed successfully"),
 *     @OA\Property(property="data", type="object")
 * )
 */
class Schemas
{
    //
}
