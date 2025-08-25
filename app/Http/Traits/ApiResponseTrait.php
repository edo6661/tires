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
    public function errorResponse(string $message, ?int $code, ?array $errors, int $statusCode = 400): JsonResponse
    {
        $response = [
            'message' => $message,
        ];

        if ($code !== null) {
            $response['code'] = $code;
        }

        if ($errors !== null) {
            $response['error'] = $errors; // Note: keeping 'error' as per your Dart model
        }

        return response()->json($response, $statusCode);
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
