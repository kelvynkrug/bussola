<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Models\Student;
use App\Models\Course;
use Illuminate\Http\Request;
use Illuminate\Http\JsonResponse;
use Illuminate\Validation\ValidationException;

/**
 * @OA\Tag(name="Students")
 */
class StudentController extends Controller
{
    /**
     * @OA\Get(
     *     path="/api/students",
     *     summary="getAll - Listar todos os alunos",
     *     description="Retorna uma lista de todos os alunos cadastrados no sistema, com opção de filtrar por curso",
     *     tags={"Students"},
     *     @OA\Parameter(
     *         name="course_id",
     *         in="query",
     *         required=false,
     *         description="ID do curso para filtrar alunos",
     *         @OA\Schema(type="integer")
     *     ),
     *     @OA\Response(
     *         response=200,
     *         description="Lista de alunos retornada com sucesso",
     *         @OA\JsonContent(
     *             @OA\Property(property="success", type="boolean", example=true),
     *             @OA\Property(
     *                 property="data",
     *                 type="array",
     *                 @OA\Items(ref="#/components/schemas/Student")
     *             )
     *         )
     *     )
     * )
     */
    public function getAll(Request $request): JsonResponse
    {
        $query = Student::with('courses');

        if ($request->has('course_id')) {
            $query->whereHas('courses', function ($q) use ($request) {
                $q->where('courses.id', $request->course_id);
            });
        }

        $students = $query->get();

        return response()->json([
            'success' => true,
            'message' => 'FIND_SUCCESS',
            'data' => $students
        ]);
    }

    /**
     * @OA\Post(
     *     path="/api/students",
     *     summary="create - Criar novo aluno",
     *     description="Cria um novo aluno no sistema, vinculando-o a pelo menos um curso",
     *     tags={"Students"},
     *     @OA\RequestBody(
     *         required=true,
     *         @OA\JsonContent(
     *             required={"name", "cpf", "birth_date", "email", "course_ids"},
     *             @OA\Property(property="name", type="string", example="João Silva"),
     *             @OA\Property(property="cpf", type="string", example="12345678901", description="CPF com 11 dígitos"),
     *             @OA\Property(property="birth_date", type="string", format="date", example="1995-05-15"),
     *             @OA\Property(property="email", type="string", format="email", example="joao.silva@email.com"),
     *             @OA\Property(
     *                 property="course_ids",
     *                 type="array",
     *                 @OA\Items(type="integer"),
     *                 example={1, 2},
     *                 description="IDs dos cursos para vincular o aluno (mínimo 1)"
     *             )
     *         )
     *     ),
     *     @OA\Response(
     *         response=201,
     *         description="Aluno criado com sucesso",
     *         @OA\JsonContent(
     *             @OA\Property(property="success", type="boolean", example=true),
     *             @OA\Property(property="message", type="string", example="Student created successfully"),
     *             @OA\Property(property="data", ref="#/components/schemas/Student")
     *         )
     *     ),
     *     @OA\Response(
     *         response=422,
     *         description="Dados de validação inválidos"
     *     )
     * )
     */
    public function create(Request $request): JsonResponse
    {
        try {
            $validated = $request->validate([
                'name' => 'required|string|max:255',
                'cpf' => 'required|string|unique:students,cpf|size:11',
                'birth_date' => 'required|date|before:today',
                'email' => 'required|email|unique:students,email',
                'course_ids' => 'required|array|min:1',
                'course_ids.*' => 'exists:courses,id',
            ]);

            $student = Student::create([
                'name' => $validated['name'],
                'cpf' => $validated['cpf'],
                'birth_date' => $validated['birth_date'],
                'email' => $validated['email'],
            ]);

            $student->courses()->attach($validated['course_ids']);

            $student->load('courses');

            return response()->json([
                'success' => true,
                'message' => 'CREATE_SUCCESS',
                'data' => $student
            ], 201);
        } catch (ValidationException $e) {
            return response()->json([
                'success' => false,
                'message' => 'CREATE_FAILED',
                'errors' => $e->errors()
            ], 422);
        }
    }

    /**
     * @OA\Get(
     *     path="/api/students/{id}",
     *     summary="search - Buscar aluno específico",
     *     description="Retorna os detalhes de um aluno específico pelo ID",
     *     tags={"Students"},
     *     @OA\Parameter(
     *         name="id",
     *         in="path",
     *         required=true,
     *         description="ID do aluno",
     *         @OA\Schema(type="integer")
     *     ),
     *     @OA\Response(
     *         response=200,
     *         description="Aluno encontrado com sucesso",
     *         @OA\JsonContent(
     *             @OA\Property(property="success", type="boolean", example=true),
     *             @OA\Property(property="data", ref="#/components/schemas/Student")
     *         )
     *     ),
     *     @OA\Response(
     *         response=404,
     *         description="Aluno não encontrado"
     *     )
     * )
     */
    public function search(string $id): JsonResponse
    {
        $student = Student::with('courses')->find($id);

        if (!$student) {
            return response()->json([
                'success' => false,
                'message' => 'FIND_NOTFOUND'
            ], 404);
        }

        return response()->json([
            'success' => true,
            'message' => 'FIND_SUCCESS',
            'data' => $student
        ]);
    }

    /**
     * @OA\Patch(
     *     path="/api/students/{id}",
     *     summary="update - Atualizar aluno",
     *     description="Atualiza os dados de um aluno existente",
     *     tags={"Students"},
     *     @OA\Parameter(
     *         name="id",
     *         in="path",
     *         required=true,
     *         description="ID do aluno",
     *         @OA\Schema(type="integer")
     *     ),
     *     @OA\RequestBody(
     *         required=true,
     *         @OA\JsonContent(
     *             @OA\Property(property="name", type="string", example="João Silva Santos"),
     *             @OA\Property(property="cpf", type="string", example="12345678901", description="CPF com 11 dígitos"),
     *             @OA\Property(property="birth_date", type="string", format="date", example="1995-05-15"),
     *             @OA\Property(property="email", type="string", format="email", example="joao.santos@email.com"),
     *             @OA\Property(
     *                 property="course_ids",
     *                 type="array",
     *                 @OA\Items(type="integer"),
     *                 example={1, 2, 3},
     *                 description="IDs dos cursos para vincular o aluno"
     *             )
     *         )
     *     ),
     *     @OA\Response(
     *         response=200,
     *         description="Aluno atualizado com sucesso",
     *         @OA\JsonContent(
     *             @OA\Property(property="success", type="boolean", example=true),
     *             @OA\Property(property="message", type="string", example="Student updated successfully"),
     *             @OA\Property(property="data", ref="#/components/schemas/Student")
     *         )
     *     ),
     *     @OA\Response(
     *         response=404,
     *         description="Aluno não encontrado"
     *     ),
     *     @OA\Response(
     *         response=422,
     *         description="Dados de validação inválidos"
     *     )
     * )
     */
    public function update(Request $request, string $id): JsonResponse
    {
        try {
            $student = Student::find($id);

            if (!$student) {
                return response()->json([
                    'success' => false,
                    'message' => 'FIND_NOTFOUND'
                ], 404);
            }

            $validated = $request->validate([
                'name' => 'sometimes|string|max:255',
                'cpf' => 'sometimes|string|unique:students,cpf,' . $id . '|size:11',
                'birth_date' => 'sometimes|date|before:today',
                'email' => 'sometimes|email|unique:students,email,' . $id,
                'course_ids' => 'sometimes|array',
                'course_ids.*' => 'exists:courses,id',
            ]);

            $student->update([
                'name' => $validated['name'] ?? $student->name,
                'cpf' => $validated['cpf'] ?? $student->cpf,
                'birth_date' => $validated['birth_date'] ?? $student->birth_date,
                'email' => $validated['email'] ?? $student->email,
            ]);

            if (isset($validated['course_ids'])) {
                $student->courses()->sync($validated['course_ids']);
            }

            $student->load('courses');

            return response()->json([
                'success' => true,
                'message' => 'UPDATE_SUCCESS',
                'data' => $student
            ]);
        } catch (ValidationException $e) {
            return response()->json([
                'success' => false,
                'message' => 'UPDATE_FAILED',
                'errors' => $e->errors()
            ], 422);
        }
    }

    /**
     * @OA\Delete(
     *     path="/api/students/{id}",
     *     summary="delete - Excluir aluno",
     *     description="Exclui um aluno do sistema",
     *     tags={"Students"},
     *     @OA\Parameter(
     *         name="id",
     *         in="path",
     *         required=true,
     *         description="ID do aluno",
     *         @OA\Schema(type="integer")
     *     ),
     *     @OA\Response(
     *         response=200,
     *         description="Aluno excluído com sucesso",
     *         @OA\JsonContent(
     *             @OA\Property(property="success", type="boolean", example=true),
     *             @OA\Property(property="message", type="string", example="Student deleted successfully")
     *         )
     *     ),
     *     @OA\Response(
     *         response=404,
     *         description="Aluno não encontrado"
     *     )
     * )
     */
    public function delete(string $id): JsonResponse
    {
        $student = Student::find($id);

        if (!$student) {
            return response()->json([
                'success' => false,
                'message' => 'FIND_NOTFOUND'
            ], 404);
        }

        $student->delete();

        return response()->json([
            'success' => true,
            'message' => 'DELETE_SUCCESS'
        ]);
    }

    /**
     * @OA\Get(
     *     path="/api/students/cpf/{cpf}",
     *     summary="findByCpf - Buscar aluno por CPF",
     *     description="Busca um aluno pelo CPF e retorna seus dados pessoais + curso(s) matriculado(s)",
     *     tags={"Students"},
     *     @OA\Parameter(
     *         name="cpf",
     *         in="path",
     *         required=true,
     *         description="CPF do aluno (11 dígitos)",
     *         @OA\Schema(type="string", example="12345678901")
     *     ),
     *     @OA\Response(
     *         response=200,
     *         description="Aluno encontrado com sucesso",
     *         @OA\JsonContent(
     *             @OA\Property(property="success", type="boolean", example=true),
     *             @OA\Property(property="data", ref="#/components/schemas/Student")
     *         )
     *     ),
     *     @OA\Response(
     *         response=404,
     *         description="Aluno não encontrado"
     *     )
     * )
     */
    public function findByCpf(string $cpf): JsonResponse
    {
        $student = Student::with('courses')->where('cpf', $cpf)->first();

        if (!$student) {
            return response()->json([
                'success' => false,
                'message' => 'FIND_NOTFOUND'
            ], 404);
        }

        return response()->json([
            'success' => true,
            'message' => 'FIND_SUCCESS',
            'data' => $student
        ]);
    }

    /**
     * @OA\Post(
     *     path="/api/students/{id}/attach-course",
     *     summary="attachToCourse - Vincular aluno a curso",
     *     description="Vincula um aluno existente a um curso adicional",
     *     tags={"Students"},
     *     @OA\Parameter(
     *         name="id",
     *         in="path",
     *         required=true,
     *         description="ID do aluno",
     *         @OA\Schema(type="integer")
     *     ),
     *     @OA\RequestBody(
     *         required=true,
     *         @OA\JsonContent(
     *             required={"course_id"},
     *             @OA\Property(property="course_id", type="integer", example=1, description="ID do curso")
     *         )
     *     ),
     *     @OA\Response(
     *         response=200,
     *         description="Aluno vinculado ao curso com sucesso",
     *         @OA\JsonContent(
     *             @OA\Property(property="success", type="boolean", example=true),
     *             @OA\Property(property="message", type="string", example="Student enrolled in course successfully"),
     *             @OA\Property(property="data", ref="#/components/schemas/Student")
     *         )
     *     ),
     *     @OA\Response(
     *         response=404,
     *         description="Aluno não encontrado"
     *     ),
     *     @OA\Response(
     *         response=422,
     *         description="Dados de validação inválidos ou aluno já matriculado no curso"
     *     )
     * )
     */
    public function attachToCourse(Request $request, string $id): JsonResponse
    {
        try {
            $student = Student::find($id);

            if (!$student) {
                return response()->json([
                    'success' => false,
                    'message' => 'FIND_NOTFOUND'
                ], 404);
            }

            $validated = $request->validate([
                'course_id' => 'required|exists:courses,id',
            ]);

            $course = Course::find($validated['course_id']);

            if ($student->courses()->where('course_id', $course->id)->exists()) {
                return response()->json([
                    'success' => false,
                    'message' => 'UPDATE_FAILED'
                ], 422);
            }

            $student->courses()->attach($course->id);

            return response()->json([
                'success' => true,
                'message' => 'UPDATE_SUCCESS',
                'data' => $student->load('courses')
            ]);
        } catch (ValidationException $e) {
            return response()->json([
                'success' => false,
                'message' => 'UPDATE_FAILED',
                'errors' => $e->errors()
            ], 422);
        }
    }
}
