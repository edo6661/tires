<?php

namespace App\Http\Controllers\Api\Admin;

use App\Http\Controllers\Controller;
use App\Http\Requests\QuestionnaireRequest;
use App\Http\Resources\QuestionnaireResource;
use App\Http\Traits\ApiResponseTrait;
use App\Services\QuestionnaireServiceInterface;
use Illuminate\Http\Request;
use Illuminate\Http\JsonResponse;
use Illuminate\Support\Facades\Validator;
use Illuminate\Pagination\CursorPaginator;

/**
 * @tags Admin - Questionnaire Management
 */
class QuestionnaireController extends Controller
{
    use ApiResponseTrait;

    public function __construct(
        protected QuestionnaireServiceInterface $questionnaireService
    ) {}

    /**
     * Get all questionnaires with cursor pagination
     */
    public function index(Request $request): JsonResponse
    {
        try {
            $perPage = min($request->get('per_page', 15), 100);

            if ($request->has('paginate') && $request->get('paginate') !== 'false') {
                // Paginated response with cursor
                $cursor = $request->get('cursor');
                $questionnaires = $this->questionnaireService->getPaginatedQuestionnairesWithCursor($perPage, $cursor);
                $collection = QuestionnaireResource::collection($questionnaires);

                $cursorInfo = $this->generateCursor($questionnaires);

                return $this->successResponseWithCursor(
                    $collection->resolve(),
                    $cursorInfo,
                    'Questionnaires retrieved successfully'
                );
            } else {
                // Simple response without pagination
                $questionnaires = $this->questionnaireService->getAllQuestionnaires();
                $collection = QuestionnaireResource::collection($questionnaires);

                return $this->successResponse(
                    $collection->resolve(),
                    'Questionnaires retrieved successfully'
                );
            }
        } catch (\Exception $e) {
            return $this->errorResponse(
                'Failed to retrieve questionnaires',
                500,
                [
                    [
                        'field' => 'general',
                        'tag' => 'server_error',
                        'value' => $e->getMessage(),
                        'message' => 'An unexpected error occurred'
                    ]
                ]
            );
        }
    }

    /**
     * Store a newly created questionnaire
     */
    public function store(QuestionnaireRequest $request): JsonResponse
    {
        try {
            $questionnaire = $this->questionnaireService->createQuestionnaire($request->validated());

            return $this->successResponse(
                new QuestionnaireResource($questionnaire),
                'Questionnaire created successfully',
                201
            );
        } catch (\InvalidArgumentException $e) {
            return $this->errorResponse(
                'Invalid questionnaire format',
                400,
                [
                    [
                        'field' => 'questions_and_answers',
                        'tag' => 'invalid_format',
                        'value' => null,
                        'message' => 'Questionnaire format is invalid'
                    ]
                ]
            );
        } catch (\Exception $e) {
            return $this->errorResponse(
                'Failed to create questionnaire',
                500,
                [
                    [
                        'field' => 'general',
                        'tag' => 'creation_failed',
                        'value' => $e->getMessage(),
                        'message' => 'Questionnaire creation failed'
                    ]
                ]
            );
        }
    }

    /**
     * Display the specified questionnaire - UPDATED
     */
    public function show(string $id): JsonResponse
    {
        try {
            // Convert string to int and validate
            $questionnaireId = (int) $id;

            if ($questionnaireId <= 0) {
                return $this->errorResponse(
                    'Invalid questionnaire ID',
                    400,
                    [
                        [
                            'field' => 'id',
                            'tag' => 'invalid_id',
                            'value' => $id,
                            'message' => 'Questionnaire ID must be a positive integer'
                        ]
                    ]
                );
            }

            $questionnaire = $this->questionnaireService->findQuestionnaire($questionnaireId);

            if (!$questionnaire) {
                return $this->errorResponse(
                    'Questionnaire not found',
                    404,
                    [
                        [
                            'field' => 'id',
                            'tag' => 'not_found',
                            'value' => $questionnaireId,
                            'message' => 'Questionnaire with given ID does not exist'
                        ]
                    ]
                );
            }

            return $this->successResponse(
                new QuestionnaireResource($questionnaire),
                'Questionnaire retrieved successfully'
            );
        } catch (\Exception $e) {
            return $this->errorResponse(
                'Failed to retrieve questionnaire',
                500,
                [
                    [
                        'field' => 'general',
                        'tag' => 'retrieval_failed',
                        'value' => $e->getMessage(),
                        'message' => 'Questionnaire retrieval failed'
                    ]
                ]
            );
        }
    }

    /**
     * Update the specified questionnaire - UPDATED
     */
    public function update(QuestionnaireRequest $request, string $id): JsonResponse
    {
        try {
            // Convert string to int and validate
            $questionnaireId = (int) $id;

            if ($questionnaireId <= 0) {
                return $this->errorResponse(
                    'Invalid questionnaire ID',
                    400,
                    [
                        [
                            'field' => 'id',
                            'tag' => 'invalid_id',
                            'value' => $id,
                            'message' => 'Questionnaire ID must be a positive integer'
                        ]
                    ]
                );
            }

            $questionnaire = $this->questionnaireService->updateQuestionnaire($questionnaireId, $request->validated());

            if (!$questionnaire) {
                return $this->errorResponse(
                    'Questionnaire not found',
                    404,
                    [
                        [
                            'field' => 'id',
                            'tag' => 'not_found',
                            'value' => $questionnaireId,
                            'message' => 'Questionnaire with given ID does not exist'
                        ]
                    ]
                );
            }

            return $this->successResponse(
                new QuestionnaireResource($questionnaire),
                'Questionnaire updated successfully'
            );
        } catch (\InvalidArgumentException $e) {
            return $this->errorResponse(
                'Invalid questionnaire format',
                400,
                [
                    [
                        'field' => 'questions_and_answers',
                        'tag' => 'invalid_format',
                        'value' => null,
                        'message' => 'Questionnaire format is invalid'
                    ]
                ]
            );
        } catch (\Exception $e) {
            return $this->errorResponse(
                'Failed to update questionnaire',
                500,
                [
                    [
                        'field' => 'general',
                        'tag' => 'update_failed',
                        'value' => $e->getMessage(),
                        'message' => 'Questionnaire update failed'
                    ]
                ]
            );
        }
    }

    /**
     * Remove the specified questionnaire - UPDATED
     */
    public function destroy(string $id): JsonResponse
    {
        try {
            // Convert string to int and validate
            $questionnaireId = (int) $id;

            if ($questionnaireId <= 0) {
                return $this->errorResponse(
                    'Invalid questionnaire ID',
                    400,
                    [
                        [
                            'field' => 'id',
                            'tag' => 'invalid_id',
                            'value' => $id,
                            'message' => 'Questionnaire ID must be a positive integer'
                        ]
                    ]
                );
            }

            $success = $this->questionnaireService->deleteQuestionnaire($questionnaireId);

            if (!$success) {
                return $this->errorResponse(
                    'Questionnaire not found',
                    404,
                    [
                        [
                            'field' => 'id',
                            'tag' => 'not_found',
                            'value' => $questionnaireId,
                            'message' => 'Questionnaire with given ID does not exist'
                        ]
                    ]
                );
            }

            return $this->successResponse(
                null,
                'Questionnaire deleted successfully'
            );
        } catch (\Exception $e) {
            return $this->errorResponse(
                'Failed to delete questionnaire',
                500,
                [
                    [
                        'field' => 'general',
                        'tag' => 'deletion_failed',
                        'value' => $e->getMessage(),
                        'message' => 'Questionnaire deletion failed'
                    ]
                ]
            );
        }
    }

    /**
     * Get questionnaire by reservation ID - UPDATED
     */
    public function getByReservation(string $reservationId): JsonResponse
    {
        try {
            // Convert string to int and validate
            $reservationIdInt = (int) $reservationId;

            if ($reservationIdInt <= 0) {
                return $this->errorResponse(
                    'Invalid reservation ID',
                    400,
                    [
                        [
                            'field' => 'reservation_id',
                            'tag' => 'invalid_id',
                            'value' => $reservationId,
                            'message' => 'Reservation ID must be a positive integer'
                        ]
                    ]
                );
            }

            $questionnaire = $this->questionnaireService->findQuestionnaireByReservation($reservationIdInt);

            if (!$questionnaire) {
                return $this->errorResponse(
                    'Questionnaire not found for this reservation',
                    404,
                    [
                        [
                            'field' => 'reservation_id',
                            'tag' => 'not_found',
                            'value' => $reservationIdInt,
                            'message' => 'No questionnaire found for the given reservation ID'
                        ]
                    ]
                );
            }

            return $this->successResponse(
                new QuestionnaireResource($questionnaire),
                'Questionnaire retrieved successfully'
            );

        } catch (\Exception $e) {
            return $this->errorResponse(
                'Failed to retrieve questionnaire',
                500,
                [
                    [
                        'field' => 'general',
                        'tag' => 'retrieval_failed',
                        'value' => $e->getMessage(),
                        'message' => 'Questionnaire retrieval failed'
                    ]
                ]
            );
        }
    }

    /**
     * Validate questionnaire answers
     */
    public function validateAnswers(Request $request): JsonResponse
    {
        try {
            $validated = $request->validate([
                'questions_and_answers' => 'required|array',
                'questions_and_answers.*.question' => 'required|string',
                'questions_and_answers.*.answer' => 'required|string',
            ]);

            $valid = $this->questionnaireService->validateQuestionnaireAnswers(
                $validated['questions_and_answers']
            );

            return $this->successResponse(
                [
                    'valid' => $valid,
                    'questions_count' => count($validated['questions_and_answers']),
                    'validation_result' => $valid ? 'All answers are valid' : 'Some answers are invalid'
                ],
                'Questionnaire answers validated successfully'
            );

        } catch (\Illuminate\Validation\ValidationException $e) {
            return $this->errorResponse(
                'Validation failed',
                422,
                collect($e->errors())->map(function ($messages, $field) {
                    return [
                        'field' => $field,
                        'tag' => 'validation_error',
                        'value' => request($field),
                        'message' => $messages[0]
                    ];
                })->values()->toArray()
            );
        } catch (\Exception $e) {
            return $this->errorResponse(
                'Failed to validate answers',
                500,
                [
                    [
                        'field' => 'general',
                        'tag' => 'validation_failed',
                        'value' => $e->getMessage(),
                        'message' => 'Answer validation failed'
                    ]
                ]
            );
        }
    }

    /**
     * Submit questionnaire answers
     */
    public function submitAnswers(Request $request): JsonResponse
    {
        try {
            $validated = $request->validate([
                'reservation_id' => 'required|integer|exists:reservations,id',
                'questions_and_answers' => 'required|array',
                'questions_and_answers.*.question' => 'required|string',
                'questions_and_answers.*.answer' => 'required|string',
            ]);

            // Check if questionnaire already exists for this reservation
            $existingQuestionnaire = $this->questionnaireService->findQuestionnaireByReservation(
                $validated['reservation_id']
            );

            if ($existingQuestionnaire) {
                // Update existing questionnaire
                $questionnaire = $this->questionnaireService->updateQuestionnaire(
                    $existingQuestionnaire->id,
                    $validated
                );
            } else {
                // Create new questionnaire
                $questionnaire = $this->questionnaireService->createQuestionnaire($validated);
            }

            return $this->successResponse(
                new QuestionnaireResource($questionnaire),
                'Questionnaire answers submitted successfully',
                $existingQuestionnaire ? 200 : 201
            );

        } catch (\Illuminate\Validation\ValidationException $e) {
            return $this->errorResponse(
                'Validation failed',
                422,
                collect($e->errors())->map(function ($messages, $field) {
                    return [
                        'field' => $field,
                        'tag' => 'validation_error',
                        'value' => request($field),
                        'message' => $messages[0]
                    ];
                })->values()->toArray()
            );
        } catch (\Exception $e) {
            return $this->errorResponse(
                'Failed to submit answers',
                500,
                [
                    [
                        'field' => 'general',
                        'tag' => 'submission_failed',
                        'value' => $e->getMessage(),
                        'message' => 'Answer submission failed'
                    ]
                ]
            );
        }
    }

    /**
     * Get questionnaire completion statistics
     */
    public function getCompletionStats(): JsonResponse
    {
        try {
            $stats = $this->questionnaireService->getCompletionStatistics();

            return $this->successResponse(
                $stats,
                'Questionnaire completion statistics retrieved successfully'
            );

        } catch (\Exception $e) {
            return $this->errorResponse(
                'Failed to retrieve statistics',
                500,
                [
                    [
                        'field' => 'general',
                        'tag' => 'statistics_failed',
                        'value' => $e->getMessage(),
                        'message' => 'Statistics retrieval failed'
                    ]
                ]
            );
        }
    }

    /**
     * Search questionnaires
     */
    public function search(Request $request): JsonResponse
    {
        try {
            $validated = $request->validate([
                'q' => 'required|string|min:1|max:255',
                'per_page' => 'sometimes|integer|min:1|max:100',
                'cursor' => 'sometimes|string'
            ]);

            $query = $validated['q'];
            $perPage = $validated['per_page'] ?? 15;
            $cursor = $validated['cursor'] ?? null;

            $questionnaires = $this->questionnaireService->searchQuestionnairesWithCursor($query, $perPage, $cursor);
            $collection = QuestionnaireResource::collection($questionnaires);

            $cursorInfo = $this->generateCursor($questionnaires);
            return $this->successResponseWithCursor(
                $collection->resolve(),
                $cursorInfo,
                'Questionnaire search completed successfully'
            );

        } catch (\Illuminate\Validation\ValidationException $e) {
            return $this->errorResponse(
                'Validation failed',
                422,
                collect($e->errors())->map(function ($messages, $field) {
                    return [
                        'field' => $field,
                        'tag' => 'validation_error',
                        'value' => request($field),
                        'message' => $messages[0]
                    ];
                })->values()->toArray()
            );
        } catch (\Exception $e) {
            return $this->errorResponse(
                'Search failed',
                500,
                [
                    [
                        'field' => 'general',
                        'tag' => 'search_failed',
                        'value' => $e->getMessage(),
                        'message' => 'Search operation failed'
                    ]
                ]
            );
        }
    }

    /**
     * Get questionnaires by completion status - UPDATED
     */
    public function byCompletionStatus(string $status, Request $request): JsonResponse
    {
        try {
            // Validate status parameter from URL
            $allowedStatuses = ['completed', 'incomplete', 'partial'];
            if (!in_array($status, $allowedStatuses)) {
                return $this->errorResponse(
                    'Invalid completion status',
                    400,
                    [
                        [
                            'field' => 'status',
                            'tag' => 'invalid_status',
                            'value' => $status,
                            'message' => 'Status must be one of: ' . implode(', ', $allowedStatuses)
                        ]
                    ]
                );
            }

            $validated = $request->validate([
                'per_page' => 'sometimes|integer|min:1|max:100',
                'cursor' => 'sometimes|string'
            ]);

            $perPage = $validated['per_page'] ?? 15;
            $cursor = $validated['cursor'] ?? null;

            $questionnaires = $this->questionnaireService->getQuestionnairesByCompletionStatusWithCursor(
                $status,
                $perPage,
                $cursor
            );
            $collection = QuestionnaireResource::collection($questionnaires);

            $cursorInfo = $this->generateCursor($questionnaires);
            return $this->successResponseWithCursor(
                $collection->resolve(),
                $cursorInfo,
                "Questionnaires with status '{$status}' retrieved successfully"
            );

        } catch (\Illuminate\Validation\ValidationException $e) {
            return $this->errorResponse(
                'Validation failed',
                422,
                collect($e->errors())->map(function ($messages, $field) {
                    return [
                        'field' => $field,
                        'tag' => 'validation_error',
                        'value' => request($field),
                        'message' => $messages[0]
                    ];
                })->values()->toArray()
            );
        } catch (\Exception $e) {
            return $this->errorResponse(
                'Failed to retrieve questionnaires',
                500,
                [
                    [
                        'field' => 'general',
                        'tag' => 'retrieval_failed',
                        'value' => $e->getMessage(),
                        'message' => 'Failed to retrieve questionnaires'
                    ]
                ]
            );
        }
    }

    /**
     * Get questionnaire answer summary
     */
    // public function getAnswerSummary(int $id): JsonResponse
    // {
    //     try {
    //         $summary = $this->questionnaireService->getAnswerSummary($id);

    //         return $this->successResponse(
    //             $summary,
    //             'Questionnaire answer summary retrieved successfully'
    //         );

    //     } catch (\InvalidArgumentException $e) {
    //         return $this->errorResponse(
    //             'Questionnaire not found',
    //             404,
    //             [
    //                 [
    //                     'field' => 'id',
    //                     'tag' => 'not_found',
    //                     'value' => $id,
    //                     'message' => $e->getMessage()
    //                 ]
    //             ]
    //         );
    //     } catch (\Exception $e) {
    //         return $this->errorResponse(
    //             'Failed to retrieve answer summary',
    //             500,
    //             [
    //                 [
    //                     'field' => 'general',
    //                     'tag' => 'summary_failed',
    //                     'value' => $e->getMessage(),
    //                     'message' => 'Answer summary retrieval failed'
    //                 ]
    //             ]
    //         );
    //     }
    // }

    /**
     * Get filtered questionnaires
     */
    // public function filtered(Request $request): JsonResponse
    // {
    //     try {
    //         $validated = $request->validate([
    //             'completion_status' => 'sometimes|in:completed,incomplete,partial',
    //             'search' => 'sometimes|string|min:1|max:255',
    //             'per_page' => 'sometimes|integer|min:1|max:100',
    //             'cursor' => 'sometimes|string'
    //         ]);

    //         $filters = array_filter([
    //             'completion_status' => $validated['completion_status'] ?? null,
    //             'search' => $validated['search'] ?? null,
    //         ]);

    //         $perPage = $validated['per_page'] ?? 15;
    //         $cursor = $validated['cursor'] ?? null;

    //         $questionnaires = $this->questionnaireService->getFilteredQuestionnaires($filters, $perPage, $cursor);
    //         $collection = QuestionnaireResource::collection($questionnaires);

    //         $cursorInfo = $this->generateCursor($questionnaires);
    //         return $this->successResponseWithCursor(
    //             $collection->resolve(),
    //             $cursorInfo,
    //             'Filtered questionnaires retrieved successfully'
    //         );

    //     } catch (\Illuminate\Validation\ValidationException $e) {
    //         return $this->errorResponse(
    //             'Validation failed',
    //             422,
    //             collect($e->errors())->map(function ($messages, $field) {
    //                 return [
    //                     'field' => $field,
    //                     'tag' => 'validation_error',
    //                     'value' => request($field),
    //                     'message' => $messages[0]
    //                 ];
    //             })->values()->toArray()
    //         );
    //     } catch (\Exception $e) {
    //         return $this->errorResponse(
    //             'Failed to retrieve filtered questionnaires',
    //             500,
    //             [
    //                 [
    //                     'field' => 'general',
    //                     'tag' => 'filter_failed',
    //                     'value' => $e->getMessage(),
    //                     'message' => 'Filter operation failed'
    //                 ]
    //             ]
    //         );
    //     }
    // }

    /**
     * Helper methods
     */
    private function generateCursor(CursorPaginator $paginator): array
    {
        return [
            'next_cursor' => $paginator->nextCursor() ? $paginator->nextCursor()->encode() : null,
            'previous_cursor' => $paginator->previousCursor() ? $paginator->previousCursor()->encode() : null,
            'has_next_page' => $paginator->hasMorePages(),
            'per_page' => $paginator->perPage()
        ];
    }
}
