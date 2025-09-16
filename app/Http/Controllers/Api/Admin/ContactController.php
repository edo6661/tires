<?php

namespace App\Http\Controllers\Api\Admin;

use App\Http\Controllers\Controller;
use App\Services\ContactServiceInterface;
use App\Http\Traits\ApiResponseTrait;
use Illuminate\Http\Request;
use Illuminate\Http\JsonResponse;

/**
 * @tags Admin - Contact Management
 */
class ContactController extends Controller
{
    use ApiResponseTrait;

    public function __construct(protected ContactServiceInterface $contactService)
    {
    }

    /**
     * Get contact statistics overview
     *
     * Returns counts for total, pending, replied contacts and today's count
     *
     * @return JsonResponse
     */
    public function getStatistics(): JsonResponse
    {
        try {
            $statistics = $this->contactService->getContactStats();

            return $this->successResponse([
                'statistics' => $statistics
            ], 'Contact statistics retrieved successfully');
        } catch (\Exception $e) {
            return $this->errorResponse(
                'Failed to retrieve contact statistics',
                500,
                [
                    [
                        'field' => 'general',
                        'tag' => 'statistics_error',
                        'value' => $e->getMessage(),
                        'message' => 'Statistics retrieval failed'
                    ]
                ]
            );
        }
    }

    /**
     * Search contacts with enhanced filtering
     *
     * @param Request $request
     * @return JsonResponse
     */
    public function search(Request $request): JsonResponse
    {
        try {
            $validated = $request->validate([
                'search' => 'nullable|string|max:255',
                'status' => 'nullable|string|in:pending,replied,all',
                'start_date' => 'nullable|date_format:Y-m-d',
                'end_date' => 'nullable|date_format:Y-m-d|after_or_equal:start_date',
                'per_page' => 'nullable|integer|min:1|max:100',
                'page' => 'nullable|integer|min:1'
            ]);

            $filters = [];

            if ($request->filled('search')) {
                $filters['search'] = $validated['search'];
            }

            if ($request->filled('status') && $validated['status'] !== 'all') {
                $filters['status'] = $validated['status'];
            }

            if ($request->filled('start_date')) {
                $filters['start_date'] = $validated['start_date'];
            }

            if ($request->filled('end_date')) {
                $filters['end_date'] = $validated['end_date'];
            }

            $filters['per_page'] = $validated['per_page'] ?? 15;
            $contacts = $this->contactService->getFilteredContacts($filters);
            $stats = $this->contactService->getContactStats();

            return $this->successResponse([
                'contacts' => $contacts,
                'statistics' => $stats,
                'filters' => $filters,
                'search_term' => $validated['search'] ?? null,
                'results_count' => $contacts->total(),
                'pagination_info' => [
                    'current_page' => $contacts->currentPage(),
                    'per_page' => $contacts->perPage(),
                    'total' => $contacts->total(),
                    'last_page' => $contacts->lastPage()
                ]
            ], 'Contact search completed successfully');
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
                'Contact search failed',
                500,
                [
                    [
                        'field' => 'general',
                        'tag' => 'search_error',
                        'value' => $e->getMessage(),
                        'message' => 'Search operation failed'
                    ]
                ]
            );
        }
    }

    /**
     * Display a listing of contacts
     */
    public function index(Request $request): JsonResponse
    {
        try {
            $validated = $request->validate([
                'status' => 'nullable|string|in:pending,replied,all',
                'start_date' => 'nullable|date_format:Y-m-d',
                'end_date' => 'nullable|date_format:Y-m-d|after_or_equal:start_date',
                'search' => 'nullable|string|max:255',
                'per_page' => 'nullable|integer|min:1|max:100'
            ]);

            $filters = array_filter($validated, function($value) {
                return $value !== null && $value !== '';
            });

            // Remove 'all' status as it means no filter
            if (isset($filters['status']) && $filters['status'] === 'all') {
                unset($filters['status']);
            }

            if (!empty($filters)) {
                $filters['per_page'] = $validated['per_page'] ?? 15;
                $contacts = $this->contactService->getFilteredContacts($filters);
            } else {
                $contacts = $this->contactService->getPaginatedContacts($validated['per_page'] ?? 15);
            }

            $stats = $this->contactService->getContactStats();

            return $this->successResponse([
                'contacts' => $contacts,
                'statistics' => $stats,
                'filters' => $filters,
                'pagination_info' => [
                    'current_page' => $contacts->currentPage(),
                    'per_page' => $contacts->perPage(),
                    'total' => $contacts->total(),
                    'last_page' => $contacts->lastPage()
                ]
            ], 'Contacts retrieved successfully');
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
            return $this->errorResponse('Failed to retrieve contacts: ' . $e->getMessage(), 500);
        }
    }

    /**
     * Display the specified contact
     */
    public function show(int $id): JsonResponse
    {
        try {
            $contact = $this->contactService->findContact($id);

            if (!$contact) {
                return $this->errorResponse('Contact not found', 404);
            }

            return $this->successResponse($contact, 'Contact retrieved successfully');
        } catch (\Exception $e) {
            return $this->errorResponse('Failed to retrieve contact: ' . $e->getMessage(), 500);
        }
    }

    /**
     * Update the specified contact
     */
    public function update(Request $request, int $id): JsonResponse
    {
        try {
            $validatedData = $request->validate([
                'status' => 'sometimes|in:pending,replied',
                'admin_reply' => 'sometimes|string|max:2000',
            ]);

            $contact = $this->contactService->updateContact($id, $validatedData);

            if (!$contact) {
                return $this->errorResponse('Contact not found', 404);
            }

            return $this->successResponse($contact, 'Contact updated successfully');
        } catch (\Illuminate\Validation\ValidationException $e) {
            return $this->validationErrorResponse($e->errors());
        } catch (\Exception $e) {
            return $this->errorResponse('Failed to update contact: ' . $e->getMessage(), 500);
        }
    }

    /**
     * Remove the specified contact
     */
    public function destroy(int $id): JsonResponse
    {
        try {
            $deleted = $this->contactService->deleteContact($id);

            if (!$deleted) {
                return $this->errorResponse('Contact not found', 404);
            }

            return $this->successResponse(null, 'Contact deleted successfully');
        } catch (\Exception $e) {
            return $this->errorResponse('Failed to delete contact: ' . $e->getMessage(), 500);
        }
    }

    /**
     * Reply to a contact
     */
    public function reply(Request $request, int $id): JsonResponse
    {
        try {
            $request->validate([
                'admin_reply' => 'required|string|max:2000',
            ]);

            $success = $this->contactService->replyToContact($id, $request->admin_reply);

            if (!$success) {
                return $this->errorResponse('Contact not found or reply failed', 404);
            }

            return $this->successResponse(null, 'Reply sent successfully');
        } catch (\Illuminate\Validation\ValidationException $e) {
            return $this->validationErrorResponse($e->errors());
        } catch (\Exception $e) {
            return $this->errorResponse('Failed to send reply: ' . $e->getMessage(), 500);
        }
    }

    /**
     * Bulk delete contacts
     */
    public function bulkDelete(Request $request): JsonResponse
    {
        try {
            $request->validate([
                'ids' => 'required|array',
                'ids.*' => 'integer|exists:contacts,id',
            ]);

            $success = $this->contactService->bulkDeleteContacts($request->ids);

            if (!$success) {
                return $this->errorResponse('Bulk delete operation failed', 400);
            }

            return $this->successResponse(null, 'Contacts deleted successfully');
        } catch (\Illuminate\Validation\ValidationException $e) {
            return $this->validationErrorResponse($e->errors());
        } catch (\Exception $e) {
            return $this->errorResponse('Failed to delete contacts: ' . $e->getMessage(), 500);
        }
    }

    /**
     * Mark multiple contacts as replied
     */
    public function markAsReplied(Request $request): JsonResponse
    {
        try {
            $request->validate([
                'ids' => 'required|array',
                'ids.*' => 'integer|exists:contacts,id',
                'admin_reply' => 'required|string|max:2000',
            ]);

            $successCount = 0;
            foreach ($request->ids as $id) {
                $success = $this->contactService->replyToContact($id, $request->admin_reply);
                if ($success) {
                    $successCount++;
                }
            }

            if ($successCount === 0) {
                return $this->errorResponse('No contacts were updated', 400);
            }

            return $this->successResponse([
                'updated_count' => $successCount,
                'total_count' => count($request->ids)
            ], "Successfully replied to {$successCount} contacts");
        } catch (\Illuminate\Validation\ValidationException $e) {
            return $this->validationErrorResponse($e->errors());
        } catch (\Exception $e) {
            return $this->errorResponse('Failed to mark contacts as replied: ' . $e->getMessage(), 500);
        }
    }
}
