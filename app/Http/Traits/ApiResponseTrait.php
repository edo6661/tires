<?php

namespace App\Http\Traits;

use Illuminate\Contracts\Pagination\CursorPaginator;
use Illuminate\Http\JsonResponse;
use Illuminate\Pagination\Cursor;
use Illuminate\Pagination\LengthAwarePaginator;

trait ApiResponseTrait
{
    /**
     * Success response with data
     */
    public function successResponse($data = null, string $message = 'Success', int $statusCode = 200): JsonResponse
    {
        return response()->json([
            'status' => 'success',
            'message' => $message,
            'data' => $data
        ], $statusCode);
    }

    /**
     * Success response with cursor pagination
     */
    public function successResponseWithCursor($data, array $cursor, string $message = 'Success', int $statusCode = 200): JsonResponse
    {
        return response()->json([
            'status' => 'success',
            'message' => $message,
            'data' => $data,
            'cursor' => $cursor
        ], $statusCode);
    }

    /**
     * Error response
     */
    public function errorResponse(string $message, int $statusCode = 400, ?array $errors = null): JsonResponse
    {
        $response = [
            'status' => 'error',
            'message' => $message,
        ];

        if ($errors !== null) {
            $response['errors'] = $errors;
        }

        return response()->json($response, $statusCode);
    }

    /**
     * Validation error response
     */
    public function validationErrorResponse(array $errors, string $message = 'Validation failed', int $statusCode = 422): JsonResponse
    {
        return response()->json([
            'status' => 'error',
            'message' => $message,
            'errors' => $errors
        ], $statusCode);
    }

    /**
     * Generate cursor pagination info
     */
    protected function generateCursor(CursorPaginator $paginator): array
    {
        return [
            'next_cursor' => $paginator->nextCursor() ? $paginator->nextCursor()->encode() : null,
            'previous_cursor' => $paginator->previousCursor() ? $paginator->previousCursor()->encode() : null,
            'has_next_page' => $paginator->hasMorePages(),
            'per_page' => $paginator->perPage()
        ];
    }
}
