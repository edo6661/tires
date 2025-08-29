<?php

namespace App\Http\Controllers\Api\Admin;

use App\Http\Controllers\Controller;
use App\Services\ContactServiceInterface;
use App\Http\Traits\ApiResponseTrait;
use Illuminate\Http\Request;
use Illuminate\Http\JsonResponse;

class ContactController extends Controller
{
    use ApiResponseTrait;

    public function __construct(protected ContactServiceInterface $contactService)
    {
    }

    /**
     * Display a listing of contacts
     */
    public function index(Request $request): JsonResponse
    {
        try {
            $filters = $request->only(['status', 'start_date', 'end_date', 'search']);

            if (array_filter($filters)) {
                $filters['per_page'] = $request->get('per_page', 15);
                $contacts = $this->contactService->getFilteredContacts($filters);
            } else {
                $contacts = $this->contactService->getPaginatedContacts($request->get('per_page', 15));
            }

            $stats = $this->contactService->getContactStats();

            return $this->successResponse([
                'contacts' => $contacts,
                'stats' => $stats,
                'filters' => $filters
            ], 'Contacts retrieved successfully');
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
