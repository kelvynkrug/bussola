<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;

/**
 * @OA\Info(
 *     title="Sistema de Gerenciamento Escolar API",
 *     version="1.0.0",
 *     description="API RESTful para gerenciamento de cursos, disciplinas, alunos e matrículas de uma escola.",
 *     @OA\Contact(
 *         email="admin@escola.com"
 *     )
 * )
 *
 * @OA\Server(
 *     url="http://localhost:8000",
 *     description="Servidor de Desenvolvimento"
 * )
 *
 * @OA\Tag(
 *     name="Courses",
 *     description="Operações relacionadas a cursos"
 * )
 *
 * @OA\Tag(
 *     name="Subjects",
 *     description="Operações relacionadas a disciplinas"
 * )
 *
 * @OA\Tag(
 *     name="Students",
 *     description="Operações relacionadas a alunos"
 * )
 *
 * @OA\Tag(
 *     name="Enrollments",
 *     description="Operações relacionadas a matrículas"
 * )
 */
class SwaggerController extends Controller
{
    //
}
