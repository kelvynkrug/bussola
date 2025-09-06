<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Models\Enrollment;
use App\Models\Student;
use App\Models\Course;
use App\Services\EnrollmentService;
use Illuminate\Http\Request;
use Illuminate\Http\JsonResponse;
use Illuminate\Validation\ValidationException;

/**
 * @OA\Tag(name="Enrollments")
 */
class EnrollmentController extends Controller
{
    protected $enrollmentService;

    public function __construct(EnrollmentService $enrollmentService)
    {
        $this->enrollmentService = $enrollmentService;
    }

    /**
     * @OA\Get(
     *     path="/api/enrollments",
     *     summary="getAll - Listar todas as matrículas",
     *     description="Retorna uma lista de todas as matrículas no sistema, com opções de filtro",
     *     tags={"Enrollments"},
     *     @OA\Parameter(
     *         name="student_id",
     *         in="query",
     *         required=false,
     *         description="ID do aluno para filtrar matrículas",
     *         @OA\Schema(type="integer")
     *     ),
     *     @OA\Parameter(
     *         name="course_id",
     *         in="query",
     *         required=false,
     *         description="ID do curso para filtrar matrículas",
     *         @OA\Schema(type="integer")
     *     ),
     *     @OA\Parameter(
     *         name="status",
     *         in="query",
     *         required=false,
     *         description="Status da matrícula (active, suspended, cancelled)",
     *         @OA\Schema(type="string", enum={"active", "suspended", "cancelled"})
     *     ),
     *     @OA\Response(
     *         response=200,
     *         description="Lista de matrículas retornada com sucesso",
     *         @OA\JsonContent(
     *             @OA\Property(property="success", type="boolean", example=true),
     *             @OA\Property(
     *                 property="data",
     *                 type="array",
     *                 @OA\Items(ref="#/components/schemas/Enrollment")
     *             )
     *         )
     *     )
     * )
     */
    public function getAll(Request $request): JsonResponse
    {
        $query = Enrollment::with(['student', 'course']);

        if ($request->has('student_id')) {
            $query->where('student_id', $request->student_id);
        }

        if ($request->has('course_id')) {
            $query->where('course_id', $request->course_id);
        }

        if ($request->has('status')) {
            $query->where('status', $request->status);
        }

        $enrollments = $query->get();

        return response()->json([
            'success' => true,
            'message' => 'FIND_SUCCESS',
            'data' => $enrollments
        ]);
    }

    /**
     * @OA\Post(
     *     path="/api/enrollments",
     *     summary="create - Realizar matrícula",
     *     description="Realiza a matrícula de um aluno em um curso e dispara e-mail de confirmação",
     *     tags={"Enrollments"},
     *     @OA\RequestBody(
     *         required=true,
     *         @OA\JsonContent(
     *             required={"student_id", "course_id"},
     *             @OA\Property(property="student_id", type="integer", example=1, description="ID do aluno"),
     *             @OA\Property(property="course_id", type="integer", example=1, description="ID do curso")
     *         )
     *     ),
     *     @OA\Response(
     *         response=201,
     *         description="Matrícula realizada com sucesso",
     *         @OA\JsonContent(
     *             @OA\Property(property="success", type="boolean", example=true),
     *             @OA\Property(property="message", type="string", example="Student enrolled successfully"),
     *             @OA\Property(property="data", ref="#/components/schemas/Enrollment")
     *         )
     *     ),
     *     @OA\Response(
     *         response=422,
     *         description="Dados de validação inválidos ou aluno já matriculado no curso"
     *     )
     * )
     */
    public function create(Request $request): JsonResponse
    {
        try {
            $validated = $request->validate([
                'student_id' => 'required|exists:students,id',
                'course_id' => 'required|exists:courses,id',
            ]);

            $existingEnrollment = Enrollment::where('student_id', $validated['student_id'])
                ->where('course_id', $validated['course_id'])
                ->first();

            if ($existingEnrollment) {
                return response()->json([
                    'success' => false,
                    'message' => 'CREATE_FAILED'
                ], 422);
            }

            $enrollment = $this->enrollmentService->enrollStudent(
                $validated['student_id'],
                $validated['course_id']
            );

            return response()->json([
                'success' => true,
                'message' => 'CREATE_SUCCESS',
                'data' => $enrollment
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
     *     path="/api/enrollments/{id}",
     *     summary="search - Buscar matrícula específica",
     *     description="Retorna os detalhes de uma matrícula específica pelo ID",
     *     tags={"Enrollments"},
     *     @OA\Parameter(
     *         name="id",
     *         in="path",
     *         required=true,
     *         description="ID da matrícula",
     *         @OA\Schema(type="integer")
     *     ),
     *     @OA\Response(
     *         response=200,
     *         description="Matrícula encontrada com sucesso",
     *         @OA\JsonContent(
     *             @OA\Property(property="success", type="boolean", example=true),
     *             @OA\Property(property="data", ref="#/components/schemas/Enrollment")
     *         )
     *     ),
     *     @OA\Response(
     *         response=404,
     *         description="Matrícula não encontrada"
     *     )
     * )
     */
    public function search(string $id): JsonResponse
    {
        $enrollment = Enrollment::with(['student', 'course'])->find($id);

        if (!$enrollment) {
            return response()->json([
                'success' => false,
                'message' => 'FIND_NOTFOUND'
            ], 404);
        }

        return response()->json([
            'success' => true,
            'message' => 'FIND_SUCCESS',
            'data' => $enrollment
        ]);
    }

    /**
     * @OA\Patch(
     *     path="/api/enrollments/{id}",
     *     summary="update - Atualizar matrícula",
     *     description="Atualiza o status de uma matrícula existente",
     *     tags={"Enrollments"},
     *     @OA\Parameter(
     *         name="id",
     *         in="path",
     *         required=true,
     *         description="ID da matrícula",
     *         @OA\Schema(type="integer")
     *     ),
     *     @OA\RequestBody(
     *         required=true,
     *         @OA\JsonContent(
     *             @OA\Property(
     *                 property="status",
     *                 type="string",
     *                 enum={"active", "suspended", "cancelled"},
     *                 example="active",
     *                 description="Novo status da matrícula"
     *             )
     *         )
     *     ),
     *     @OA\Response(
     *         response=200,
     *         description="Matrícula atualizada com sucesso",
     *         @OA\JsonContent(
     *             @OA\Property(property="success", type="boolean", example=true),
     *             @OA\Property(property="message", type="string", example="Enrollment updated successfully"),
     *             @OA\Property(property="data", ref="#/components/schemas/Enrollment")
     *         )
     *     ),
     *     @OA\Response(
     *         response=404,
     *         description="Matrícula não encontrada"
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
            $enrollment = Enrollment::find($id);

            if (!$enrollment) {
                return response()->json([
                    'success' => false,
                    'message' => 'FIND_NOTFOUND'
                ], 404);
            }

            $validated = $request->validate([
                'status' => 'sometimes|in:active,suspended,cancelled',
            ]);

            $enrollment->update($validated);

            return response()->json([
                'success' => true,
                'message' => 'UPDATE_SUCCESS',
                'data' => $enrollment
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
     *     path="/api/enrollments/{id}",
     *     summary="delete - Excluir matrícula",
     *     description="Exclui uma matrícula do sistema",
     *     tags={"Enrollments"},
     *     @OA\Parameter(
     *         name="id",
     *         in="path",
     *         required=true,
     *         description="ID da matrícula",
     *         @OA\Schema(type="integer")
     *     ),
     *     @OA\Response(
     *         response=200,
     *         description="Matrícula excluída com sucesso",
     *         @OA\JsonContent(
     *             @OA\Property(property="success", type="boolean", example=true),
     *             @OA\Property(property="message", type="string", example="Enrollment deleted successfully")
     *         )
     *     ),
     *     @OA\Response(
     *         response=404,
     *         description="Matrícula não encontrada"
     *     )
     * )
     */
    public function delete(string $id): JsonResponse
    {
        $enrollment = Enrollment::find($id);

        if (!$enrollment) {
            return response()->json([
                'success' => false,
                'message' => 'FIND_NOTFOUND'
            ], 404);
        }

        $enrollment->delete();

        return response()->json([
            'success' => true,
            'message' => 'DELETE_SUCCESS'
        ]);
    }

    /**
     * @OA\Post(
     *     path="/api/enrollments/{id}/suspend",
     *     summary="suspend - Trancar matrícula",
     *     description="Tranca uma matrícula ativa, alterando seu status para 'suspended'",
     *     tags={"Enrollments"},
     *     @OA\Parameter(
     *         name="id",
     *         in="path",
     *         required=true,
     *         description="ID da matrícula",
     *         @OA\Schema(type="integer")
     *     ),
     *     @OA\Response(
     *         response=200,
     *         description="Matrícula trancada com sucesso",
     *         @OA\JsonContent(
     *             @OA\Property(property="success", type="boolean", example=true),
     *             @OA\Property(property="message", type="string", example="Enrollment suspended successfully"),
     *             @OA\Property(property="data", ref="#/components/schemas/Enrollment")
     *         )
     *     ),
     *     @OA\Response(
     *         response=404,
     *         description="Matrícula não encontrada"
     *     ),
     *     @OA\Response(
     *         response=422,
     *         description="Matrícula já está trancada"
     *     )
     * )
     */
    public function suspend(string $id): JsonResponse
    {
        $enrollment = Enrollment::find($id);

        if (!$enrollment) {
            return response()->json([
                'success' => false,
                'message' => 'FIND_NOTFOUND'
            ], 404);
        }

        if ($enrollment->status === 'suspended') {
            return response()->json([
                'success' => false,
                'message' => 'UPDATE_FAILED'
            ], 422);
        }

        $enrollment->update([
            'status' => 'suspended',
            'suspended_at' => now()
        ]);

        return response()->json([
            'success' => true,
            'message' => 'UPDATE_SUCCESS',
            'data' => $enrollment
        ]);
    }

    /**
     * @OA\Post(
     *     path="/api/enrollments/{id}/reactivate",
     *     summary="reactivate - Reativar matrícula",
     *     description="Reativa uma matrícula trancada, alterando seu status para 'active'",
     *     tags={"Enrollments"},
     *     @OA\Parameter(
     *         name="id",
     *         in="path",
     *         required=true,
     *         description="ID da matrícula",
     *         @OA\Schema(type="integer")
     *     ),
     *     @OA\Response(
     *         response=200,
     *         description="Matrícula reativada com sucesso",
     *         @OA\JsonContent(
     *             @OA\Property(property="success", type="boolean", example=true),
     *             @OA\Property(property="message", type="string", example="Enrollment reactivated successfully"),
     *             @OA\Property(property="data", ref="#/components/schemas/Enrollment")
     *         )
     *     ),
     *     @OA\Response(
     *         response=404,
     *         description="Matrícula não encontrada"
     *     ),
     *     @OA\Response(
     *         response=422,
     *         description="Apenas matrículas trancadas podem ser reativadas"
     *     )
     * )
     */
    public function reactivate(string $id): JsonResponse
    {
        $enrollment = Enrollment::find($id);

        if (!$enrollment) {
            return response()->json([
                'success' => false,
                'message' => 'FIND_NOTFOUND'
            ], 404);
        }

        if ($enrollment->status !== 'suspended') {
            return response()->json([
                'success' => false,
                'message' => 'UPDATE_FAILED'
            ], 422);
        }

        $enrollment->update([
            'status' => 'active',
            'suspended_at' => null
        ]);

        return response()->json([
            'success' => true,
            'message' => 'UPDATE_SUCCESS',
            'data' => $enrollment
        ]);
    }
}
