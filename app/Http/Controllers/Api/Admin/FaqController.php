<?php

namespace App\Http\Controllers\Api\Admin;

use App\Http\Controllers\Controller;
use App\Services\FaqServiceInterface;
use App\Http\Traits\ApiResponseTrait;
use App\Http\Requests\FaqRequest;
use Illuminate\Http\Request;
use Illuminate\Http\JsonResponse;

/**
 * @tags Admin
 */
class FaqController extends Controller
{
    use ApiResponseTrait;

    public function __construct(protected FaqServiceInterface $faqService)
    {
    }

    /**
     * Display a listing of FAQs
     */
    public function index(Request $request): JsonResponse
    {
        try {
            $perPage = $request->get('per_page', 15);
            $faqs = $this->faqService->getPaginatedFaqs($perPage);

            return $this->successResponse($faqs, 'FAQs retrieved successfully');
        } catch (\Exception $e) {
            return $this->errorResponse('Failed to retrieve FAQs: ' . $e->getMessage(), 500);
        }
    }

    /**
     * Store a newly created FAQ
     */
    public function store(FaqRequest $request): JsonResponse
    {
        try {
            $faq = $this->faqService->createFaq($request->validated());

            return $this->successResponse($faq, 'FAQ created successfully', 201);
        } catch (\Exception $e) {
            return $this->errorResponse('Failed to create FAQ: ' . $e->getMessage(), 500);
        }
    }

    /**
     * Display the specified FAQ
     */
    public function show(int $id): JsonResponse
    {
        try {
            $faq = $this->faqService->findFaq($id);

            if (!$faq) {
                return $this->errorResponse('FAQ not found', 404);
            }

            return $this->successResponse($faq, 'FAQ retrieved successfully');
        } catch (\Exception $e) {
            return $this->errorResponse('Failed to retrieve FAQ: ' . $e->getMessage(), 500);
        }
    }

    /**
     * Update the specified FAQ
     */
    public function update(FaqRequest $request, int $id): JsonResponse
    {
        try {
            $faq = $this->faqService->updateFaq($id, $request->validated());

            if (!$faq) {
                return $this->errorResponse('FAQ not found', 404);
            }

            return $this->successResponse($faq, 'FAQ updated successfully');
        } catch (\Exception $e) {
            return $this->errorResponse('Failed to update FAQ: ' . $e->getMessage(), 500);
        }
    }

    /**
     * Remove the specified FAQ
     */
    public function destroy(int $id): JsonResponse
    {
        try {
            $deleted = $this->faqService->deleteFaq($id);

            if (!$deleted) {
                return $this->errorResponse('FAQ not found', 404);
            }

            return $this->successResponse(null, 'FAQ deleted successfully');
        } catch (\Exception $e) {
            return $this->errorResponse('Failed to delete FAQ: ' . $e->getMessage(), 500);
        }
    }

    /**
     * Toggle FAQ status
     */
    public function toggleStatus(int $id): JsonResponse
    {
        try {
            $toggled = $this->faqService->toggleFaqStatus($id);

            if (!$toggled) {
                return $this->errorResponse('FAQ not found', 404);
            }

            // Get the updated FAQ to return current status
            $faq = $this->faqService->findFaq($id);

            return $this->successResponse($faq, 'FAQ status updated successfully');
        } catch (\Exception $e) {
            return $this->errorResponse('Failed to toggle FAQ status: ' . $e->getMessage(), 500);
        }
    }

    /**
     * Reorder FAQs
     */
    public function reorder(Request $request): JsonResponse
    {
        try {
            $request->validate([
                'order' => 'required|array',
                'order.*.id' => 'required|integer',
                'order.*.display_order' => 'required|integer',
            ]);

            $reordered = $this->faqService->reorderFaqs($request->input('order'));

            if (!$reordered) {
                return $this->errorResponse('Failed to reorder FAQs', 500);
            }

            return $this->successResponse(null, 'FAQs reordered successfully');
        } catch (\Illuminate\Validation\ValidationException $e) {
            return $this->validationErrorResponse($e->errors());
        } catch (\Exception $e) {
            return $this->errorResponse('Failed to reorder FAQs: ' . $e->getMessage(), 500);
        }
    }

    /**
     * Get active FAQs only
     */
    public function getActiveFaqs(): JsonResponse
    {
        try {
            $faqs = $this->faqService->getActiveFaqs();

            return $this->successResponse($faqs, 'Active FAQs retrieved successfully');
        } catch (\Exception $e) {
            return $this->errorResponse('Failed to retrieve active FAQs: ' . $e->getMessage(), 500);
        }
    }
}
