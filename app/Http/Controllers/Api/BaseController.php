<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use Illuminate\Http\JsonResponse;

abstract class BaseController extends Controller
{
    protected function successResponse($data = null, string $message = 'Operação realizada com sucesso', int $statusCode = 200): JsonResponse
    {
        $response = [
            'success' => true,
            'message' => $message,
        ];

        if ($data !== null) {
            $response['data'] = $data;
        }

        return response()->json($response, $statusCode);
    }

    protected function errorResponse(string $message, int $statusCode = 400, $errors = null): JsonResponse
    {
        $response = [
            'success' => false,
            'message' => $message,
        ];

        if ($errors !== null) {
            $response['errors'] = $errors;
        }

        return response()->json($response, $statusCode);
    }


    protected function validationErrorResponse($errors, string $message = 'Falha na validação dos dados'): JsonResponse
    {
        return $this->errorResponse($message, 422, $errors);
    }

    protected function notFoundResponse(string $resource = 'Recurso'): JsonResponse
    {
        return $this->errorResponse("{$resource} não encontrado", 404);
    }

    protected function conflictResponse(string $message): JsonResponse
    {
        return $this->errorResponse($message, 422);
    }


    protected function forbiddenResponse(string $message): JsonResponse
    {
        return $this->errorResponse($message, 403);
    }
}
