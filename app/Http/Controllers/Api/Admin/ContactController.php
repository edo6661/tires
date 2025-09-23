<?php

namespace App\Http\Controllers\Api\Admin;

use App\Http\Controllers\Controller;
use App\Services\ContactServiceInterface;
use App\Http\Traits\ApiResponseTrait;
use App\Http\Resources\ContactResource;
use Illuminate\Http\Request;
use Illuminate\Http\JsonResponse;
use Illuminate\Support\Facades\App;

/**
 * @mixin \Illuminate\Http\Request
 * @tags Admin - Contact Management
 */
class ContactController extends Controller
{
    use ApiResponseTrait;

    public function __construct(
        protected ContactServiceInterface $contactService
    ) {}

    /**
     * List All Contacts (paginate / non-paginate) with filtering and search
     */
    public function index(Request $request): JsonResponse
    {
        try {
            // Validate query parameters
            $request->validate([
                'per_page' => 'nullable|integer|min:1|max:100',
                'paginate' => 'sometimes|in:true,false',
                'cursor' => 'nullable|string',
                'status' => 'nullable|in:pending,replied,all',
                'start_date' => 'nullable|date_format:Y-m-d',
                'end_date' => 'nullable|date_format:Y-m-d|after_or_equal:start_date',
                'search' => 'nullable|string|max:255'
            ]);

            $perPage = min($request->get('per_page', 15), 100);
            $locale = App::getLocale();

            // Get filters from request
            $filters = [
                'status' => $request->get('status'),
                'start_date' => $request->get('start_date'),
                'end_date' => $request->get('end_date'),
                'search' => $request->get('search')
            ];

            // Remove empty filters
            $filters = array_filter($filters, function ($value) {
                return $value !== null && $value !== '' && $value !== 'all';
            });

            if ($request->has('paginate') && $request->get('paginate') !== 'false') {
                // Paginated response with cursor and filters
                $cursor = $request->get('cursor');
                $contacts = $this->contactService->getPaginatedContactsWithCursor($perPage, $cursor, $filters);
                $collection = ContactResource::collection($contacts);

                $cursorInfo = $this->generateCursor($contacts);

                return $this->successResponseWithCursor(
                    $collection->resolve(),
                    $cursorInfo,
                    'Contacts retrieved successfully'
                );
            } else {
                // Simple response without pagination but with filters
                if (!empty($filters)) {
                    $filters['per_page'] = $perPage;
                    $contacts = $this->contactService->getFilteredContacts($filters);
                } else {
                    $contacts = $this->contactService->getPaginatedContacts($perPage);
                }
                $collection = ContactResource::collection($contacts);

                $stats = $this->contactService->getContactStats();

                return $this->successResponse([
                    'contacts' => [
                        $collection->resolve(),
                        'filters_applied' => $filters,
                        'total_filtered' => $contacts->total()
                    ],
                    'statistics' => $stats,
                    'pagination_info' => [
                        'current_page' => $contacts->currentPage(),
                        'per_page' => $contacts->perPage(),
                        'total' => $contacts->total(),
                        'last_page' => $contacts->lastPage()
                    ]
                ], 'Contacts retrieved successfully');
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
                'Failed to retrieve contacts',
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
     * Display the specified contact
     */
    public function show(int $id): JsonResponse
    {
        $contact = $this->contactService->findContact($id);

        if (!$contact) {
            return $this->errorResponse('Contact not found', 404, [
                [
                    'field' => 'general',
                    'tag' => 'not_found',
                    'value' => null,
                    'message' => 'Contact not found'
                ]
            ]);
        }

        return $this->successResponse(
            new ContactResource($contact),
            'Contact retrieved successfully'
        );
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
                return $this->errorResponse('Contact not found', 404, [
                    [
                        'field' => 'general',
                        'tag' => 'not_found',
                        'value' => null,
                        'message' => 'Contact not found'
                    ]
                ]);
            }

            return $this->successResponse(
                new ContactResource($contact),
                'Contact updated successfully'
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
                'Failed to update contact',
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
     * Remove the specified contact
     */
    public function destroy(int $id): JsonResponse
    {
        try {
            $deleted = $this->contactService->deleteContact($id);

            if (!$deleted) {
                return $this->errorResponse('Contact not found', 404, [
                    [
                        'field' => 'general',
                        'tag' => 'not_found',
                        'value' => null,
                        'message' => 'Contact not found'
                    ]
                ]);
            }

            return $this->successResponse(null, 'Contact deleted successfully');
        } catch (\Exception $e) {
            return $this->errorResponse(
                'Failed to delete contact',
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
     * Get contact statistics
     */
    public function getStatistics(): JsonResponse
    {
        try {
            $stats = $this->contactService->getContactStats();

            return $this->successResponse(
                $stats,
                'Contact statistics retrieved successfully'
            );
        } catch (\Exception $e) {
            return $this->errorResponse(
                'Failed to retrieve contact statistics',
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
                return $this->errorResponse('Contact not found or reply failed', 404, [
                    [
                        'field' => 'general',
                        'tag' => 'not_found',
                        'value' => null,
                        'message' => 'Contact not found or reply failed'
                    ]
                ]);
            }

            // Get updated contact
            $contact = $this->contactService->findContact($id);

            return $this->successResponse(
                new ContactResource($contact),
                'Reply sent successfully'
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
                'Failed to send reply',
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
     * Bulk delete contacts
     */
    public function bulkDelete(Request $request): JsonResponse
    {
        try {
            $request->validate([
                'ids' => 'required|array',
                'ids.*' => 'integer|exists:contacts,id',
            ]);

            $deletedCount = 0;
            $errors = [];

            foreach ($request->get('ids') as $id) {
                try {
                    $deleted = $this->contactService->deleteContact($id);
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
                'total_requested' => count($request->get('ids')),
                'errors' => $errors
            ], "Successfully deleted {$deletedCount} contacts");
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
                'Failed to bulk delete contacts',
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
            $errors = [];

            foreach ($request->ids as $id) {
                try {
                    $success = $this->contactService->replyToContact($id, $request->admin_reply);
                    if ($success) {
                        $successCount++;
                    }
                } catch (\Exception $e) {
                    $errors[] = [
                        'id' => $id,
                        'error' => $e->getMessage()
                    ];
                }
            }

            if ($successCount === 0) {
                return $this->errorResponse('No contacts were updated', 400, [
                    [
                        'field' => 'general',
                        'tag' => 'update_failed',
                        'value' => null,
                        'message' => 'No contacts were updated'
                    ]
                ]);
            }

            return $this->successResponse([
                'updated_count' => $successCount,
                'total_count' => count($request->ids),
                'errors' => $errors
            ], "Successfully replied to {$successCount} contacts");
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
                'Failed to mark contacts as replied',
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
}
