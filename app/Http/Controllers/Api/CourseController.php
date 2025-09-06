<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Models\Course;
use Illuminate\Http\Request;
use Illuminate\Http\JsonResponse;
use Illuminate\Validation\ValidationException;

/**
 * @OA\Tag(name="Courses")
 */
class CourseController extends Controller
{
    /**
     * @OA\Get(
     *     path="/api/courses",
     *     summary="getAll - Listar todos os cursos",
     *     description="Retorna uma lista de todos os cursos cadastrados no sistema",
     *     tags={"Courses"},
     *     @OA\Response(
     *         response=200,
     *         description="Lista de cursos retornada com sucesso",
     *         @OA\JsonContent(
     *             @OA\Property(property="success", type="boolean", example=true),
     *             @OA\Property(
     *                 property="data",
     *                 type="array",
     *                 @OA\Items(ref="#/components/schemas/Course")
     *             )
     *         )
     *     )
     * )
     */
    public function getAll(): JsonResponse
    {
        $courses = Course::with('subjects')->get();

        return response()->json([
            'success' => true,
            'message' => 'FIND_SUCCESS',
            'data' => $courses
        ]);
    }

    /**
     * @OA\Post(
     *     path="/api/courses",
     *     summary="create - Criar novo curso",
     *     description="Cria um novo curso no sistema",
     *     tags={"Courses"},
     *     @OA\RequestBody(
     *         required=true,
     *         @OA\JsonContent(
     *             required={"name", "description", "workload"},
     *             @OA\Property(property="name", type="string", example="Sistemas de Informação"),
     *             @OA\Property(property="description", type="string", example="Curso de graduação em Sistemas de Informação"),
     *             @OA\Property(property="workload", type="integer", example=3200)
     *         )
     *     ),
     *     @OA\Response(
     *         response=201,
     *         description="Curso criado com sucesso",
     *         @OA\JsonContent(
     *             @OA\Property(property="success", type="boolean", example=true),
     *             @OA\Property(property="data", ref="#/components/schemas/Course")
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
                'description' => 'required|string',
                'workload' => 'required|integer|min:1',
            ]);

            $course = Course::create($validated);

            return response()->json([
                'success' => true,
                'message' => 'CREATE_SUCCESS',
                'data' => $course
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
     *     path="/api/courses/{id}",
     *     summary="search - Buscar curso específico",
     *     description="Retorna os detalhes de um curso específico pelo ID",
     *     tags={"Courses"},
     *     @OA\Parameter(
     *         name="id",
     *         in="path",
     *         required=true,
     *         description="ID do curso",
     *         @OA\Schema(type="integer")
     *     ),
     *     @OA\Response(
     *         response=200,
     *         description="Curso encontrado com sucesso",
     *         @OA\JsonContent(
     *             @OA\Property(property="success", type="boolean", example=true),
     *             @OA\Property(property="data", ref="#/components/schemas/Course")
     *         )
     *     ),
     *     @OA\Response(
     *         response=404,
     *         description="Curso não encontrado"
     *     )
     * )
     */
    public function search(string $id): JsonResponse
    {
        $course = Course::with('subjects', 'students')->find($id);

        if (!$course) {
            return response()->json([
                'success' => false,
                'message' => 'FIND_NOTFOUND'
            ], 404);
        }

        return response()->json([
            'success' => true,
            'message' => 'FIND_SUCCESS',
            'data' => $course
        ]);
    }

    /**
     * @OA\Patch(
     *     path="/api/courses/{id}",
     *     summary="update - Atualizar curso",
     *     description="Atualiza os dados de um curso existente",
     *     tags={"Courses"},
     *     @OA\Parameter(
     *         name="id",
     *         in="path",
     *         required=true,
     *         description="ID do curso",
     *         @OA\Schema(type="integer")
     *     ),
     *     @OA\RequestBody(
     *         required=true,
     *         @OA\JsonContent(
     *             @OA\Property(property="name", type="string", example="Sistemas de Informação - Atualizado"),
     *             @OA\Property(property="description", type="string", example="Curso de graduação em Sistemas de Informação - Descrição atualizada"),
     *             @OA\Property(property="workload", type="integer", example=3400)
     *         )
     *     ),
     *     @OA\Response(
     *         response=200,
     *         description="Curso atualizado com sucesso",
     *         @OA\JsonContent(
     *             @OA\Property(property="success", type="boolean", example=true),
     *             @OA\Property(property="data", ref="#/components/schemas/Course")
     *         )
     *     ),
     *     @OA\Response(
     *         response=404,
     *         description="Curso não encontrado"
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
            $course = Course::find($id);

            if (!$course) {
                return response()->json([
                    'success' => false,
                    'message' => 'FIND_NOTFOUND'
                ], 404);
            }

            $validated = $request->validate([
                'name' => 'sometimes|string|max:255',
                'description' => 'sometimes|string',
                'workload' => 'sometimes|integer|min:1',
            ]);

            $course->update($validated);

            return response()->json([
                'success' => true,
                'message' => 'UPDATE_SUCCESS',
                'data' => $course
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
     *     path="/api/courses/{id}",
     *     summary="delete - Excluir curso",
     *     description="Exclui um curso do sistema (apenas se não tiver matrículas)",
     *     tags={"Courses"},
     *     @OA\Parameter(
     *         name="id",
     *         in="path",
     *         required=true,
     *         description="ID do curso",
     *         @OA\Schema(type="integer")
     *     ),
     *     @OA\Response(
     *         response=200,
     *         description="Curso excluído com sucesso",
     *         @OA\JsonContent(
     *             @OA\Property(property="success", type="boolean", example=true),
     *             @OA\Property(property="message", type="string", example="Course deleted successfully")
     *         )
     *     ),
     *     @OA\Response(
     *         response=404,
     *         description="Curso não encontrado"
     *     ),
     *     @OA\Response(
     *         response=422,
     *         description="Não é possível excluir curso com matrículas ativas"
     *     )
     * )
     */
    public function delete(string $id): JsonResponse
    {
        $course = Course::find($id);

        if (!$course) {
            return response()->json([
                'success' => false,
                'message' => 'FIND_NOTFOUND'
            ], 404);
        }

        // Check if course has any enrollments
        if ($course->enrollments()->exists()) {
            return response()->json([
                'success' => false,
                'message' => 'DELETE_FAILED'
            ], 422);
        }

        $course->delete();

        return response()->json([
            'success' => true,
            'message' => 'DELETE_SUCCESS'
        ]);
    }
}
