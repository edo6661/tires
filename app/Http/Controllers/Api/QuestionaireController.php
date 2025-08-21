<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Http\Requests\QuestionnaireRequest;
use App\Services\QuestionnaireServiceInterface;
use Illuminate\Http\Request;
use Illuminate\Http\JsonResponse;

class QuestionaireController extends Controller
{
     public function __construct(
        protected QuestionnaireServiceInterface $questionnaireService
    ) {}

    public function index(Request $request): JsonResponse
    {
        $perPage = $request->get('per_page', 15);
        $questionnaires = $this->questionnaireService->getPaginatedQuestionnaires($perPage);

        return response()->json([
            'success' => true,
            'data' => $questionnaires
        ]);
    }

    public function store(QuestionnaireRequest $request): JsonResponse
    {
        try {
            $questionnaire = $this->questionnaireService->createQuestionnaire($request->validated());

            return response()->json([
                'success' => true,
                'message' => 'Kuesioner berhasil dibuat.',
                'data' => $questionnaire
            ], 201);
        } catch (\InvalidArgumentException $e) {
            return response()->json([
                'success' => false,
                'message' => 'Format kuesioner tidak valid.'
            ], 422);
        } catch (\Exception $e) {
            return response()->json([
                'success' => false,
                'message' => 'Terjadi kesalahan: ' . $e->getMessage()
            ], 500);
        }
    }

    public function show(int $id): JsonResponse
    {
        $questionnaire = $this->questionnaireService->findQuestionnaire($id);

        if (!$questionnaire) {
            return response()->json([
                'success' => false,
                'message' => 'Kuesioner tidak ditemukan.'
            ], 404);
        }

        return response()->json([
            'success' => true,
            'data' => $questionnaire
        ]);
    }

    public function update(QuestionnaireRequest $request, int $id): JsonResponse
    {
        try {
            $questionnaire = $this->questionnaireService->updateQuestionnaire($id, $request->validated());

            if (!$questionnaire) {
                return response()->json([
                    'success' => false,
                    'message' => 'Kuesioner tidak ditemukan.'
                ], 404);
            }

            return response()->json([
                'success' => true,
                'message' => 'Kuesioner berhasil diperbarui.',
                'data' => $questionnaire
            ]);
        } catch (\InvalidArgumentException $e) {
            return response()->json([
                'success' => false,
                'message' => 'Format kuesioner tidak valid.'
            ], 422);
        } catch (\Exception $e) {
            return response()->json([
                'success' => false,
                'message' => 'Terjadi kesalahan: ' . $e->getMessage()
            ], 500);
        }
    }

    public function destroy(int $id): JsonResponse
    {
        try {
            $deleted = $this->questionnaireService->deleteQuestionnaire($id);

            if (!$deleted) {
                return response()->json([
                    'success' => false,
                    'message' => 'Kuesioner tidak ditemukan.'
                ], 404);
            }

            return response()->json([
                'success' => true,
                'message' => 'Kuesioner berhasil dihapus.'
            ]);
        } catch (\Exception $e) {
            return response()->json([
                'success' => false,
                'message' => 'Terjadi kesalahan: ' . $e->getMessage()
            ], 500);
        }
    }

    public function getByReservation(Request $request): JsonResponse
    {
        $request->validate([
            'reservation_id' => 'required|integer|exists:reservations,id',
        ]);

        $questionnaire = $this->questionnaireService->findQuestionnaireByReservation(
            $request->input('reservation_id')
        );

        if (!$questionnaire) {
            return response()->json([
                'success' => false,
                'message' => 'Kuesioner tidak ditemukan untuk reservasi ini.'
            ], 404);
        }

        return response()->json([
            'success' => true,
            'data' => $questionnaire
        ]);
    }

    public function validateAnswers(Request $request): JsonResponse
    {
        $request->validate([
            'questions_and_answers' => 'required|array',
            'questions_and_answers.*.question' => 'required|string',
            'questions_and_answers.*.answer' => 'required|string',
        ]);

        $valid = $this->questionnaireService->validateQuestionnaireAnswers(
            $request->input('questions_and_answers')
        );

        return response()->json([
            'success' => true,
            'valid' => $valid
        ]);
    }

}
