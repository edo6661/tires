<?php
namespace App\Http\Controllers\Admin;
use App\Http\Controllers\Controller;
use App\Services\ContactServiceInterface;
use App\Http\Requests\ContactRequest;
use Illuminate\Http\Request;
use Illuminate\Http\JsonResponse;
class ContactController extends Controller
{
    public function __construct(protected ContactServiceInterface $contactService)
    {
    }
    public function index(Request $request)
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
    public function show(int $id)
    {
        $contact = $this->contactService->findContact($id);
        if (!$contact) {
            return redirect()->route('admin.contact.index')
                ->with('error', 'Kontak tidak ditemukan.');
        }
        return view('admin.contact.show', compact('contact'));
    }
    public function update(Request $request, int $id)
    {
        try {
            $validatedData = $request->validate([
                'status' => 'sometimes|in:pending,replied',
                'admin_reply' => 'sometimes|string|max:2000',
            ]);
            $contact = $this->contactService->updateContact($id, $validatedData);
            if (!$contact) {
                return redirect()->route('admin.contact.index')
                    ->with('error', 'Kontak tidak ditemukan.');
            }
            return redirect()->route('admin.contact.show', $id)
                ->with('success', 'Kontak berhasil diperbarui.');
        } catch (\Exception $e) {
            return redirect()->back()
                ->with('error', 'Terjadi kesalahan: ' . $e->getMessage())
                ->withInput();
        }
    }
    public function destroy(int $id): JsonResponse
    {
        try {
            $deleted = $this->contactService->deleteContact($id);
            if (!$deleted) {
                return response()->json([
                    'success' => false,
                    'message' => 'Kontak tidak ditemukan.'
                ], 404);
            }
            return response()->json([
                'success' => true,
                'message' => 'Kontak berhasil dihapus.'
            ]);
        } catch (\Exception $e) {
            return response()->json([
                'success' => false,
                'message' => 'Terjadi kesalahan: ' . $e->getMessage()
            ], 500);
        }
    }
    public function reply(Request $request, int $id): JsonResponse
    {
        try {
            $request->validate([
                'admin_reply' => 'required|string|max:2000',
            ]);
            $success = $this->contactService->replyToContact($id, $request->admin_reply);
            if (!$success) {
                return response()->json([
                    'success' => false,
                    'message' => 'Kontak tidak ditemukan atau gagal memperbarui.'
                ], 404);
            }
            return response()->json([
                'success' => true,
                'message' => 'Reply berhasil dikirim dan status diperbarui.'
            ]);
        } catch (\Exception $e) {
            return response()->json([
                'success' => false,
                'message' => 'Terjadi kesalahan: ' . $e->getMessage()
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
                    'message' => 'Tidak ada kontak yang berhasil dihapus.'
                ], 400);
            }
            return response()->json([
                'success' => true,
                'message' => 'Kontak berhasil dihapus secara bulk.'
            ]);
        } catch (\Exception $e) {
            return response()->json([
                'success' => false,
                'message' => 'Terjadi kesalahan: ' . $e->getMessage()
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
                    'message' => 'Tidak ada kontak yang berhasil diperbarui.'
                ], 400);
            }
            return response()->json([
                'success' => true,
                'message' => "$successCount kontak berhasil ditandai sebagai replied."
            ]);
        } catch (\Exception $e) {
            return response()->json([
                'success' => false,
                'message' => 'Terjadi kesalahan: ' . $e->getMessage()
            ], 500);
        }
    }
}
