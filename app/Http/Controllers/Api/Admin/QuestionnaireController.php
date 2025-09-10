<?php

namespace App\Http\Controllers\Api\Admin;

use App\Http\Controllers\Controller;
use App\Services\QuestionnaireServiceInterface;
use App\Http\Traits\ApiResponseTrait;
use App\Http\Requests\QuestionnaireRequest;
use Illuminate\Http\Request;
use Illuminate\Http\JsonResponse;

/**
 * @tags Admin
 */
class QuestionnaireController extends Controller
{
    use ApiResponseTrait;

    public function __construct(protected QuestionnaireServiceInterface $questionnaireService)
    {
    }

    /**
     * Display a listing of questionnaires
     */
    public function index(Request $request): JsonResponse
    {
        try {
            $perPage = $request->get('per_page', 15);
            $questionnaires = $this->questionnaireService->getPaginatedQuestionnaires($perPage);

            return $this->successResponse($questionnaires, 'Questionnaires retrieved successfully');
        } catch (\Exception $e) {
            return $this->errorResponse('Failed to retrieve questionnaires: ' . $e->getMessage(), 500);
        }
    }

    /**
     * Store a newly created questionnaire
     */
    public function store(QuestionnaireRequest $request): JsonResponse
    {
        try {
            $questionnaire = $this->questionnaireService->createQuestionnaire($request->validated());

            return $this->successResponse($questionnaire, 'Questionnaire created successfully', 201);
        } catch (\InvalidArgumentException $e) {
            return $this->errorResponse('Invalid questionnaire format: ' . $e->getMessage(), 422);
        } catch (\Exception $e) {
            return $this->errorResponse('Failed to create questionnaire: ' . $e->getMessage(), 500);
        }
    }

    /**
     * Display the specified questionnaire
     */
    public function show(int $id): JsonResponse
    {
        try {
            $questionnaire = $this->questionnaireService->findQuestionnaire($id);

            if (!$questionnaire) {
                return $this->errorResponse('Questionnaire not found', 404);
            }

            return $this->successResponse($questionnaire, 'Questionnaire retrieved successfully');
        } catch (\Exception $e) {
            return $this->errorResponse('Failed to retrieve questionnaire: ' . $e->getMessage(), 500);
        }
    }

    /**
     * Update the specified questionnaire
     */
    public function update(QuestionnaireRequest $request, int $id): JsonResponse
    {
        try {
            $questionnaire = $this->questionnaireService->updateQuestionnaire($id, $request->validated());

            if (!$questionnaire) {
                return $this->errorResponse('Questionnaire not found', 404);
            }

            return $this->successResponse($questionnaire, 'Questionnaire updated successfully');
        } catch (\InvalidArgumentException $e) {
            return $this->errorResponse('Invalid questionnaire format: ' . $e->getMessage(), 422);
        } catch (\Exception $e) {
            return $this->errorResponse('Failed to update questionnaire: ' . $e->getMessage(), 500);
        }
    }

    /**
     * Remove the specified questionnaire
     */
    public function destroy(int $id): JsonResponse
    {
        try {
            $deleted = $this->questionnaireService->deleteQuestionnaire($id);

            if (!$deleted) {
                return $this->errorResponse('Questionnaire not found', 404);
            }

            return $this->successResponse(null, 'Questionnaire deleted successfully');
        } catch (\Exception $e) {
            return $this->errorResponse('Failed to delete questionnaire: ' . $e->getMessage(), 500);
        }
    }

    /**
     * Get questionnaire by reservation
     */
    public function getByReservation(Request $request): JsonResponse
    {
        try {
            $request->validate([
                'reservation_id' => 'required|integer|exists:reservations,id',
            ]);

            $questionnaire = $this->questionnaireService->findQuestionnaireByReservation(
                $request->input('reservation_id')
            );

            if (!$questionnaire) {
                return $this->errorResponse('Questionnaire not found for this reservation', 404);
            }

            return $this->successResponse($questionnaire, 'Questionnaire retrieved successfully');
        } catch (\Illuminate\Validation\ValidationException $e) {
            return $this->validationErrorResponse($e->errors());
        } catch (\Exception $e) {
            return $this->errorResponse('Failed to retrieve questionnaire: ' . $e->getMessage(), 500);
        }
    }

    /**
     * Validate questionnaire answers
     */
    public function validateAnswers(Request $request): JsonResponse
    {
        try {
            $request->validate([
                'questions_and_answers' => 'required|array',
                'questions_and_answers.*.question' => 'required|string',
                'questions_and_answers.*.answer' => 'required|string',
            ]);

            $valid = $this->questionnaireService->validateQuestionnaireAnswers(
                $request->input('questions_and_answers')
            );

            return $this->successResponse([
                'valid' => $valid,
                'questions_count' => count($request->input('questions_and_answers'))
            ], 'Questionnaire answers validated successfully');
        } catch (\Illuminate\Validation\ValidationException $e) {
            return $this->validationErrorResponse($e->errors());
        } catch (\Exception $e) {
            return $this->errorResponse('Failed to validate answers: ' . $e->getMessage(), 500);
        }
    }
}
