<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use Illuminate\Http\JsonResponse;

abstract class BaseController extends Controller
{
    protected function successResponse($data = null, string $message = 'Operação realizada com sucesso', string $code = 'SUCCESS', int $statusCode = 200): JsonResponse
    {
        $response = [
            'success' => true,
            'code' => $code,
            'message' => $message,
        ];

        if ($data !== null) {
            $response['data'] = $data;
        }

        return response()->json($response, $statusCode);
    }

    protected function errorResponse(string $message, int $statusCode = 400, $errors = null, string $code = 'ERROR'): JsonResponse
    {
        $response = [
            'success' => false,
            'code' => $code,
            'message' => $message,
        ];

        if ($errors !== null) {
            $response['errors'] = $errors;
        }

        return response()->json($response, $statusCode);
    }


    protected function validationErrorResponse($errors, string $message = 'Falha na validação dos dados', string $code = 'VALIDATION_FAILED'): JsonResponse
    {
        return $this->errorResponse($message, 422, $errors, $code);
    }

    protected function notFoundResponse(string $resource = 'Recurso', string $code = 'NOT_FOUND'): JsonResponse
    {
        return $this->errorResponse("{$resource} não encontrado", 404, null, $code);
    }

    protected function conflictResponse(string $message, string $code = 'CONFLICT'): JsonResponse
    {
        return $this->errorResponse($message, 422, null, $code);
    }


    protected function forbiddenResponse(string $message, string $code = 'FORBIDDEN'): JsonResponse
    {
        return $this->errorResponse($message, 403, null, $code);
    }
}
