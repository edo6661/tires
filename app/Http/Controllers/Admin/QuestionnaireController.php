<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Services\QuestionnaireServiceInterface;
use App\Http\Requests\QuestionnaireRequest;
use Illuminate\Http\Request;

class QuestionnaireController extends Controller
{
    public function __construct(protected QuestionnaireServiceInterface $questionnaireService)
    {
        
    }

    public function index()
    {
        $questionnaires = $this->questionnaireService->getPaginatedQuestionnaires(15);
        return view('admin.questionnaire.index', compact('questionnaires'));
    }

    public function create()
    {
        return view('admin.questionnaire.create');
    }

    public function store(QuestionnaireRequest $request)
    {
        try {
            $this->questionnaireService->createQuestionnaire($request->validated());
            return redirect()->route('admin.questionnaire.index')
                ->with('success', 'Kuesioner berhasil dibuat.');
        } catch (\InvalidArgumentException $e) {
            return redirect()->back()
                ->with('error', 'Format kuesioner tidak valid.')
                ->withInput();
        } catch (\Exception $e) {
            return redirect()->back()
                ->with('error', 'Terjadi kesalahan: ' . $e->getMessage())
                ->withInput();
        }
    }

    public function show(int $id)
    {
        $questionnaire = $this->questionnaireService->findQuestionnaire($id);
        if (!$questionnaire) {
            return redirect()->route('admin.questionnaire.index')
                ->with('error', 'Kuesioner tidak ditemukan.');
        }
        return view('admin.questionnaire.show', compact('questionnaire'));
    }

    public function edit(int $id)
    {
        $questionnaire = $this->questionnaireService->findQuestionnaire($id);
        if (!$questionnaire) {
            return redirect()->route('admin.questionnaire.index')
                ->with('error', 'Kuesioner tidak ditemukan.');
        }
        return view('admin.questionnaire.edit', compact('questionnaire'));
    }

    public function update(QuestionnaireRequest $request, int $id)
    {
        try {
            $questionnaire = $this->questionnaireService->updateQuestionnaire($id, $request->validated());
            if (!$questionnaire) {
                return redirect()->route('admin.questionnaire.index')
                    ->with('error', 'Kuesioner tidak ditemukan.');
            }
            return redirect()->route('admin.questionnaire.index')
                ->with('success', 'Kuesioner berhasil diperbarui.');
        } catch (\InvalidArgumentException $e) {
            return redirect()->back()
                ->with('error', 'Format kuesioner tidak valid.')
                ->withInput();
        } catch (\Exception $e) {
            return redirect()->back()
                ->with('error', 'Terjadi kesalahan: ' . $e->getMessage())
                ->withInput();
        }
    }

    public function destroy(int $id)
    {
        try {
            $deleted = $this->questionnaireService->deleteQuestionnaire($id);
            if (!$deleted) {
                return redirect()->route('admin.questionnaire.index')
                    ->with('error', 'Kuesioner tidak ditemukan.');
            }
            return redirect()->route('admin.questionnaire.index')
                ->with('success', 'Kuesioner berhasil dihapus.');
        } catch (\Exception $e) {
            return redirect()->back()
                ->with('error', 'Terjadi kesalahan: ' . $e->getMessage());
        }
    }

    public function getByReservation(Request $request)
    {
        try {
            $request->validate([
                'reservation_id' => 'required|integer|exists:reservations,id',
            ]);

            $questionnaire = $this->questionnaireService->findQuestionnaireByReservation(
                $request->input('reservation_id')
            );

            if (!$questionnaire) {
                return response()->json(['error' => 'Kuesioner tidak ditemukan untuk reservasi ini.'], 404);
            }

            return response()->json(['questionnaire' => $questionnaire]);
        } catch (\Exception $e) {
            return response()->json(['error' => 'Terjadi kesalahan: ' . $e->getMessage()], 500);
        }
    }

    public function validateAnswers(Request $request)
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

            return response()->json(['valid' => $valid]);
        } catch (\Exception $e) {
            return response()->json(['error' => 'Terjadi kesalahan: ' . $e->getMessage()], 500);
        }
    }
}