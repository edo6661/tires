<?php
namespace App\Http\Controllers\Admin;
use App\Http\Controllers\Controller;
use App\Services\ContactServiceInterface;
use Illuminate\Http\Request;
use Illuminate\Http\JsonResponse;
use Illuminate\View\View;
class ContactController extends Controller
{
    public function __construct(protected ContactServiceInterface $contactService)
    {
    }
    public function index(Request $request): View
    {
        $filters = $request->only(['status', 'start_date', 'end_date', 'search']);
        if (array_filter($filters)) {
            $filters['per_page'] = 15;
            $contacts = $this->contactService->getFilteredContacts($filters);
        } else {
            $contacts = $this->contactService->getPaginatedContacts(15);
        }
        $stats = $this->contactService->getContactStats();
        return view('admin.contact.index', compact('contacts', 'stats'));
    }
    public function show($locale, int $id): View
    {
        $contact = $this->contactService->findContact($id);
        if (!$contact) {
            abort(404, __('admin/contact/general.notifications.contact_not_found'));
        }
        return view('admin.contact.show', compact('contact'));
    }
    public function update(Request $request, $locale, int $id)
    {
        try {
            $validatedData = $request->validate([
                'status' => 'sometimes|in:pending,replied',
                'admin_reply' => 'sometimes|string|max:2000',
            ]);
            $contact = $this->contactService->updateContact($id, $validatedData);
            if (!$contact) {
                return redirect()->route('admin.contact.index', ['locale' => $locale])
                    ->with('error', __('admin/contact/general.notifications.contact_not_found'));
            }
            return redirect()->route('admin.contact.show', ['locale' => $locale, 'id' => $id])
                ->with('success', __('admin/contact/general.notifications.contact_updated'));
        } catch (\Exception $e) {
            return redirect()->back()
                ->with('error', __('admin/contact/general.notifications.error_occurred', ['message' => $e->getMessage()]))
                ->withInput();
        }
    }
    public function destroy($locale, int $id): JsonResponse
    {
        try {
            $deleted = $this->contactService->deleteContact($id);
            if (!$deleted) {
                return response()->json([
                    'success' => false,
                    'message' => __('admin/contact/general.notifications.contact_not_found')
                ], 404);
            }
            return response()->json([
                'success' => true,
                'message' => __('admin/contact/general.notifications.contact_deleted')
            ]);
        } catch (\Exception $e) {
            return response()->json([
                'success' => false,
                'message' => __('admin/contact/general.notifications.error_occurred', ['message' => $e->getMessage()])
            ], 500);
        }
    }
    public function reply(Request $request, $locale, int $id): JsonResponse
    {
        try {
            $request->validate([
                'admin_reply' => 'required|string|max:2000',
            ]);
            $success = $this->contactService->replyToContact($id, $request->admin_reply);
            if (!$success) {
                return response()->json([
                    'success' => false,
                    'message' => __('admin/contact/general.notifications.reply_error')
                ], 404);
            }
            return response()->json([
                'success' => true,
                'message' => __('admin/contact/general.notifications.reply_sent')
            ]);
        } catch (\Exception $e) {
            return response()->json([
                'success' => false,
                'message' => __('admin/contact/general.notifications.error_occurred', ['message' => $e->getMessage()])
            ], 500);
        }
    }
    public function bulkDelete(Request $request): JsonResponse
    {
        try {
            $request->validate([
                'ids' => 'required|array',
                'ids.*' => 'integer|exists:contacts,id',
            ]);
            $success = $this->contactService->bulkDeleteContacts($request->ids);
            if (!$success) {
                return response()->json([
                    'success' => false,
                    'message' => __('admin/contact/general.notifications.bulk_delete_error')
                ], 400);
            }
            return response()->json([
                'success' => true,
                'message' => __('admin/contact/general.notifications.bulk_delete_success')
            ]);
        } catch (\Exception $e) {
            return response()->json([
                'success' => false,
                'message' => __('admin/contact/general.notifications.error_occurred', ['message' => $e->getMessage()])
            ], 500);
        }
    }
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
                return response()->json([
                    'success' => false,
                    'message' => __('admin/contact/general.notifications.bulk_replied_error')
                ], 400);
            }
            return response()->json([
                'success' => true,
                'message' => __('admin/contact/general.notifications.bulk_replied_success', ['count' => $successCount])
            ]);
        } catch (\Exception $e) {
            return response()->json([
                'success' => false,
                'message' => __('admin/contact/general.notifications.error_occurred', ['message' => $e->getMessage()])
            ], 500);
        }
    }
}
