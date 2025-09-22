<?php

namespace App\Http\Controllers\Api\Admin;

use App\Http\Controllers\Controller;
use App\Services\FaqServiceInterface;
use App\Http\Traits\ApiResponseTrait;
use App\Http\Resources\FaqResource;
use App\Http\Requests\FaqRequest;
use Illuminate\Http\Request;
use Illuminate\Http\JsonResponse;
use Illuminate\Support\Facades\App;

/**
 * @mixin \Illuminate\Http\Request
 * @tags Admin - FAQ Management
 */
class FaqController extends Controller
{
    use ApiResponseTrait;

    public function __construct(
        protected FaqServiceInterface $faqService
    ) {}

    /**
     * List All FAQs (paginate / non-paginate) with filtering and search
     */
    public function index(Request $request): JsonResponse
    {
        try {
            // Validate query parameters
            $request->validate([
                'per_page' => 'nullable|integer|min:1|max:100',
                'paginate' => 'sometimes|in:true,false',
                'cursor' => 'nullable|string',
                'status' => 'nullable|in:active,inactive',
                'search' => 'nullable|string|max:255'
            ]);

            $perPage = min($request->get('per_page', 15), 100);
            $locale = App::getLocale();

            // Get filters from request
            $filters = [
                'status' => $request->get('status'),
                'search' => $request->get('search')
            ];

            // Remove empty filters
            $filters = array_filter($filters);

            if ($request->has('paginate') && $request->get('paginate') !== 'false') {
                // Paginated response with cursor and filters
                $cursor = $request->get('cursor');
                $faqs = $this->faqService->getPaginatedFaqsWithCursor($perPage, $cursor, $filters);
                $collection = FaqResource::collection($faqs);

                $cursorInfo = $this->generateCursor($faqs);

                return $this->successResponseWithCursor(
                    $collection->resolve(),
                    $cursorInfo,
                    'FAQs retrieved successfully'
                );
            } else {
                // Simple response without pagination but with filters
                if (!empty($filters)) {
                    $faqs = $this->faqService->getFilteredFaqs($filters, $perPage);
                } else {
                    $faqs = $this->faqService->getActiveFaqs()->take($perPage);
                }
                $collection = FaqResource::collection($faqs);

                return $this->successResponse(
                    [
                        $collection->resolve(),
                        'filters_applied' => $filters,
                        'total_filtered' => $faqs->count()
                    ],
                    'FAQs retrieved successfully'
                );
            }
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
                'Failed to retrieve FAQs',
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
     * Create New FAQ
     */
    public function store(FaqRequest $request): JsonResponse
    {
        try {
            $data = $request->validated();
            $faq = $this->faqService->createFaq($data);

            return $this->successResponse(
                new FaqResource($faq),
                'FAQ created successfully',
                201
            );
        } catch (\Exception $e) {
            return $this->errorResponse(
                'Failed to create FAQ',
                500,
                [[
                    'field' => 'general',
                    'tag' => 'server_error',
                    'value' => $e->getMessage(),
                    'message' => 'An unexpected error occurred'
                ]]
            );
        }
    }

    /**
     * Get FAQ Detail
     */
    public function show(int $id): JsonResponse
    {
        $faq = $this->faqService->findFaq($id);

        if (!$faq) {
            return $this->errorResponse('FAQ not found', 404, [
                [
                    'field' => 'general',
                    'tag' => 'not_found',
                    'value' => null,
                    'message' => 'FAQ not found'
                ]
            ]);
        }

        return $this->successResponse(
            new FaqResource($faq),
            'FAQ retrieved successfully'
        );
    }

    /**
     * Update FAQ
     */
    public function update(FaqRequest $request, int $id): JsonResponse
    {
        try {
            $data = $request->validated();
            $faq = $this->faqService->updateFaq($id, $data);

            if (!$faq) {
                return $this->errorResponse('FAQ not found', 404, [
                    [
                        'field' => 'general',
                        'tag' => 'not_found',
                        'value' => null,
                        'message' => 'FAQ not found'
                    ]
                ]);
            }

            return $this->successResponse(
                new FaqResource($faq),
                'FAQ updated successfully'
            );
        } catch (\Exception $e) {
            return $this->errorResponse(
                'Failed to update FAQ',
                500,
                [[
                    'field' => 'general',
                    'tag' => 'server_error',
                    'value' => $e->getMessage(),
                    'message' => 'An unexpected error occurred'
                ]]
            );
        }
    }

    /**
     * Delete FAQ
     */
    public function destroy(int $id): JsonResponse
    {
        try {
            $deleted = $this->faqService->deleteFaq($id);

            if (!$deleted) {
                return $this->errorResponse('FAQ not found', 404, [
                    [
                        'field' => 'general',
                        'tag' => 'not_found',
                        'value' => null,
                        'message' => 'FAQ not found'
                    ]
                ]);
            }

            return $this->successResponse(null, 'FAQ deleted successfully');
        } catch (\Exception $e) {
            return $this->errorResponse(
                'Failed to delete FAQ',
                500,
                [[
                    'field' => 'general',
                    'tag' => 'server_error',
                    'value' => $e->getMessage(),
                    'message' => 'An unexpected error occurred'
                ]]
            );
        }
    }

    /**
     * Get FAQ statistics
     */
    public function statistics(): JsonResponse
    {
        try {
            $stats = $this->faqService->getFaqStatistics();

            return $this->successResponse(
                $stats,
                'FAQ statistics retrieved successfully'
            );
        } catch (\Exception $e) {
            return $this->errorResponse(
                'Failed to retrieve FAQ statistics',
                500,
                [[
                    'field' => 'general',
                    'tag' => 'server_error',
                    'value' => $e->getMessage(),
                    'message' => 'An unexpected error occurred'
                ]]
            );
        }
    }

    /**
     * Toggle FAQ status (active/inactive)
     */
    public function toggleStatus(int $id): JsonResponse
    {
        try {
            $faq = $this->faqService->findFaq($id);

            if (!$faq) {
                return $this->errorResponse('FAQ not found', 404, [
                    [
                        'field' => 'general',
                        'tag' => 'not_found',
                        'value' => null,
                        'message' => 'FAQ not found'
                    ]
                ]);
            }

            $newStatus = $faq->is_active ? 'inactive' : 'active';
            $updatedFaq = $this->faqService->updateFaq($id, ['is_active' => !$faq->is_active]);

            return $this->successResponse(
                new FaqResource($updatedFaq),
                "FAQ status changed to {$newStatus} successfully"
            );
        } catch (\Exception $e) {
            return $this->errorResponse(
                'Failed to toggle FAQ status',
                500,
                [[
                    'field' => 'general',
                    'tag' => 'server_error',
                    'value' => $e->getMessage(),
                    'message' => 'An unexpected error occurred'
                ]]
            );
        }
    }

    /**
     * Bulk toggle FAQ status
     */
    public function bulkToggleStatus(Request $request): JsonResponse
    {
        try {
            $request->validate([
                'ids' => 'required|array',
                'ids.*' => 'integer|exists:faqs,id',
                'status' => 'required|in:active,inactive'
            ]);

            $ids = $request->get('ids');
            $status = $request->get('status');
            $isActive = $status === 'active';
            $updatedCount = 0;
            $errors = [];

            foreach ($ids as $id) {
                try {
                    $faq = $this->faqService->updateFaq($id, ['is_active' => $isActive]);
                    if ($faq) {
                        $updatedCount++;
                    }
                } catch (\Exception $e) {
                    $errors[] = [
                        'id' => $id,
                        'error' => $e->getMessage()
                    ];
                }
            }

            return $this->successResponse([
                'updated_count' => $updatedCount,
                'total_requested' => count($ids),
                'errors' => $errors,
                'status' => $status
            ], "Successfully updated {$updatedCount} FAQs to {$status} status");
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
                'Failed to bulk toggle FAQ status',
                500,
                [[
                    'field' => 'general',
                    'tag' => 'server_error',
                    'value' => $e->getMessage(),
                    'message' => 'An unexpected error occurred'
                ]]
            );
        }
    }

    /**
     * Bulk delete FAQs
     */
    public function bulkDelete(Request $request): JsonResponse
    {
        try {
            $request->validate([
                'ids' => 'required|array',
                'ids.*' => 'integer|exists:faqs,id'
            ]);

            $ids = $request->get('ids');
            $deletedCount = 0;
            $errors = [];

            foreach ($ids as $id) {
                try {
                    $deleted = $this->faqService->deleteFaq($id);
                    if ($deleted) {
                        $deletedCount++;
                    }
                } catch (\Exception $e) {
                    $errors[] = [
                        'id' => $id,
                        'error' => $e->getMessage()
                    ];
                }
            }

            return $this->successResponse([
                'deleted_count' => $deletedCount,
                'total_requested' => count($ids),
                'errors' => $errors
            ], "Successfully deleted {$deletedCount} FAQs");
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
                'Failed to bulk delete FAQs',
                500,
                [[
                    'field' => 'general',
                    'tag' => 'server_error',
                    'value' => $e->getMessage(),
                    'message' => 'An unexpected error occurred'
                ]]
            );
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
                'order.*.id' => 'required|integer|exists:faqs,id',
                'order.*.display_order' => 'required|integer',
            ]);

            $reordered = $this->faqService->reorderFaqs($request->input('order'));

            if (!$reordered) {
                return $this->errorResponse(
                    'Failed to reorder FAQs',
                    500,
                    [[
                        'field' => 'order',
                        'tag' => 'reorder_failed',
                        'value' => $request->input('order'),
                        'message' => 'Could not reorder the specified FAQs'
                    ]]
                );
            }

            return $this->successResponse(
                ['reordered_count' => count($request->input('order'))],
                'FAQs reordered successfully'
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
                'Failed to reorder FAQs',
                500,
                [[
                    'field' => 'general',
                    'tag' => 'reorder_error',
                    'value' => $e->getMessage(),
                    'message' => 'Reorder operation failed'
                ]]
            );
        }
    }
}
