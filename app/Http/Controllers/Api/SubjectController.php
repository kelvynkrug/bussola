<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Models\Subject;
use App\Models\Course;
use Illuminate\Http\Request;
use Illuminate\Http\JsonResponse;
use Illuminate\Validation\ValidationException;

/**
 * @OA\Tag(name="Subjects")
 */
class SubjectController extends Controller
{
    /**
     * @OA\Get(
     *     path="/api/subjects",
     *     summary="getAll - Listar todas as disciplinas",
     *     description="Retorna uma lista de todas as disciplinas cadastradas no sistema, com opção de filtrar por curso",
     *     tags={"Subjects"},
     *     @OA\Parameter(
     *         name="course_id",
     *         in="query",
     *         required=false,
     *         description="ID do curso para filtrar disciplinas",
     *         @OA\Schema(type="integer")
     *     ),
     *     @OA\Response(
     *         response=200,
     *         description="Lista de disciplinas retornada com sucesso",
     *         @OA\JsonContent(
     *             @OA\Property(property="success", type="boolean", example=true),
     *             @OA\Property(
     *                 property="data",
     *                 type="array",
     *                 @OA\Items(ref="#/components/schemas/Subject")
     *             )
     *         )
     *     )
     * )
     */
    public function getAll(Request $request): JsonResponse
    {
        $query = Subject::with('courses');

        if ($request->has('course_id')) {
            $query->whereHas('courses', function ($q) use ($request) {
                $q->where('courses.id', $request->course_id);
            });
        }

        $subjects = $query->get();

        return response()->json([
            'success' => true,
            'message' => 'SEARCH_SUCCESS',
            'data' => $subjects
        ]);
    }

    /**
     * @OA\Post(
     *     path="/api/subjects",
     *     summary="create - Criar nova disciplina",
     *     description="Cria uma nova disciplina no sistema, com opção de vincular a cursos",
     *     tags={"Subjects"},
     *     @OA\RequestBody(
     *         required=true,
     *         @OA\JsonContent(
     *             required={"name", "description", "workload"},
     *             @OA\Property(property="name", type="string", example="Introdução à Administração"),
     *             @OA\Property(property="description", type="string", example="Fundamentos básicos de administração"),
     *             @OA\Property(property="workload", type="integer", example=60),
     *             @OA\Property(
     *                 property="course_ids",
     *                 type="array",
     *                 @OA\Items(type="integer"),
     *                 example={1, 2},
     *                 description="IDs dos cursos para vincular a disciplina"
     *             )
     *         )
     *     ),
     *     @OA\Response(
     *         response=201,
     *         description="Disciplina criada com sucesso",
     *         @OA\JsonContent(
     *             @OA\Property(property="success", type="boolean", example=true),
     *             @OA\Property(property="message", type="string", example="Subject created successfully"),
     *             @OA\Property(property="data", ref="#/components/schemas/Subject")
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
                'course_ids' => 'sometimes|array',
                'course_ids.*' => 'exists:courses,id',
            ]);

            $subject = Subject::create([
                'name' => $validated['name'],
                'description' => $validated['description'],
                'workload' => $validated['workload'],
            ]);

            if (isset($validated['course_ids'])) {
                $subject->courses()->attach($validated['course_ids']);
            }

            $subject->load('courses');

            return response()->json([
                'success' => true,
                'message' => 'CREATE_SUCCESS',
                'data' => $subject
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
     *     path="/api/subjects/{id}",
     *     summary="search - Buscar disciplina específica",
     *     description="Retorna os detalhes de uma disciplina específica pelo ID",
     *     tags={"Subjects"},
     *     @OA\Parameter(
     *         name="id",
     *         in="path",
     *         required=true,
     *         description="ID da disciplina",
     *         @OA\Schema(type="integer")
     *     ),
     *     @OA\Response(
     *         response=200,
     *         description="Disciplina encontrada com sucesso",
     *         @OA\JsonContent(
     *             @OA\Property(property="success", type="boolean", example=true),
     *             @OA\Property(property="data", ref="#/components/schemas/Subject")
     *         )
     *     ),
     *     @OA\Response(
     *         response=404,
     *         description="Disciplina não encontrada"
     *     )
     * )
     */
    public function search(string $id): JsonResponse
    {
        $subject = Subject::with('courses')->find($id);

        if (!$subject) {
            return response()->json([
                'success' => false,
                'message' => 'FIND_NOTFOUND'
            ], 404);
        }

        return response()->json([
            'success' => true,
            'message' => 'SEARCH_SUCCESS',
            'data' => $subject
        ]);
    }

    /**
     * @OA\Patch(
     *     path="/api/subjects/{id}",
     *     summary="update - Atualizar disciplina",
     *     description="Atualiza os dados de uma disciplina existente",
     *     tags={"Subjects"},
     *     @OA\Parameter(
     *         name="id",
     *         in="path",
     *         required=true,
     *         description="ID da disciplina",
     *         @OA\Schema(type="integer")
     *     ),
     *     @OA\RequestBody(
     *         required=true,
     *         @OA\JsonContent(
     *             @OA\Property(property="name", type="string", example="Introdução à Administração - Atualizada"),
     *             @OA\Property(property="description", type="string", example="Fundamentos básicos de administração - Descrição atualizada"),
     *             @OA\Property(property="workload", type="integer", example=80),
     *             @OA\Property(
     *                 property="course_ids",
     *                 type="array",
     *                 @OA\Items(type="integer"),
     *                 example={1, 2, 3},
     *                 description="IDs dos cursos para vincular a disciplina"
     *             )
     *         )
     *     ),
     *     @OA\Response(
     *         response=200,
     *         description="Disciplina atualizada com sucesso",
     *         @OA\JsonContent(
     *             @OA\Property(property="success", type="boolean", example=true),
     *             @OA\Property(property="message", type="string", example="Subject updated successfully"),
     *             @OA\Property(property="data", ref="#/components/schemas/Subject")
     *         )
     *     ),
     *     @OA\Response(
     *         response=404,
     *         description="Disciplina não encontrada"
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
            $subject = Subject::find($id);

            if (!$subject) {
                return response()->json([
                    'success' => false,
                    'message' => 'FIND_NOTFOUND'
                ], 404);
            }

            $validated = $request->validate([
                'name' => 'sometimes|string|max:255',
                'description' => 'sometimes|string',
                'workload' => 'sometimes|integer|min:1',
                'course_ids' => 'sometimes|array',
                'course_ids.*' => 'exists:courses,id',
            ]);

            $subject->update([
                'name' => $validated['name'] ?? $subject->name,
                'description' => $validated['description'] ?? $subject->description,
                'workload' => $validated['workload'] ?? $subject->workload,
            ]);

            if (isset($validated['course_ids'])) {
                $subject->courses()->sync($validated['course_ids']);
            }

            $subject->load('courses');

            return response()->json([
                'success' => true,
                'message' => 'UPDATE_SUCCESS',
                'data' => $subject
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
     *     path="/api/subjects/{id}",
     *     summary="delete - Excluir disciplina",
     *     description="Exclui uma disciplina do sistema",
     *     tags={"Subjects"},
     *     @OA\Parameter(
     *         name="id",
     *         in="path",
     *         required=true,
     *         description="ID da disciplina",
     *         @OA\Schema(type="integer")
     *     ),
     *     @OA\Response(
     *         response=200,
     *         description="Disciplina excluída com sucesso",
     *         @OA\JsonContent(
     *             @OA\Property(property="success", type="boolean", example=true),
     *             @OA\Property(property="message", type="string", example="Subject deleted successfully")
     *         )
     *     ),
     *     @OA\Response(
     *         response=404,
     *         description="Disciplina não encontrada"
     *     )
     * )
     */
    public function delete(string $id): JsonResponse
    {
        $subject = Subject::find($id);

        if (!$subject) {
            return response()->json([
                'success' => false,
                'message' => 'FIND_NOTFOUND'
            ], 404);
        }

        $subject->delete();

        return response()->json([
            'success' => true,
            'message' => 'DELETE_SUCCESS'
        ]);
    }

    /**
     * @OA\Post(
     *     path="/api/subjects/{id}/attach-course",
     *     summary="attachToCourse - Vincular disciplina a curso",
     *     description="Vincula uma disciplina existente a um curso",
     *     tags={"Subjects"},
     *     @OA\Parameter(
     *         name="id",
     *         in="path",
     *         required=true,
     *         description="ID da disciplina",
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
     *         description="Disciplina vinculada ao curso com sucesso",
     *         @OA\JsonContent(
     *             @OA\Property(property="success", type="boolean", example=true),
     *             @OA\Property(property="message", type="string", example="Subject attached to course successfully"),
     *             @OA\Property(property="data", ref="#/components/schemas/Subject")
     *         )
     *     ),
     *     @OA\Response(
     *         response=404,
     *         description="Disciplina não encontrada"
     *     ),
     *     @OA\Response(
     *         response=422,
     *         description="Dados de validação inválidos ou disciplina já vinculada ao curso"
     *     )
     * )
     */
    public function attachToCourse(Request $request, string $id): JsonResponse
    {
        try {
            $subject = Subject::find($id);

            if (!$subject) {
                return response()->json([
                    'success' => false,
                    'message' => 'FIND_NOTFOUND'
                ], 404);
            }

            $validated = $request->validate([
                'course_id' => 'required|exists:courses,id',
            ]);

            $course = Course::find($validated['course_id']);

            if ($subject->courses()->where('course_id', $course->id)->exists()) {
                return response()->json([
                    'success' => false,
                    'message' => 'UPDATE_FAILED'
                ], 422);
            }

            $subject->courses()->attach($course->id);

            return response()->json([
                'success' => true,
                'message' => 'UPDATE_SUCCESS',
                'data' => $subject->load('courses')
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
