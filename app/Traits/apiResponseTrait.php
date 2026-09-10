<?php

namespace App\Traits;

use Illuminate\Http\JsonResponse;

trait apiResponseTrait
{
    protected function successResponse(mixed $data, string $message, int $statusCode): JsonResponse
    {
        return response()->json([
            'status' => 'success',
            'message' => $message,
            'data' => $data,
        ], $statusCode);
    }

    protected function errorResponse(string $message, int $statusCode = null, array $errors = [], array $meta = []): JsonResponse
    {
        return response()->json([
            'status' => 'failure',
            'message' => $message,
            'errors' => $errors,
            'meta' => $meta
        ], $statusCode);
    }
}
