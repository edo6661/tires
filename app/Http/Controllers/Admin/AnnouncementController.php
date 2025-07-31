<?php
namespace App\Http\Controllers\Admin;
use App\Http\Controllers\Controller;
use App\Services\AnnouncementServiceInterface;
use App\Http\Requests\AnnouncementRequest;
use Illuminate\Http\Request;
use Illuminate\Http\JsonResponse;
use Illuminate\Support\Facades\Log;
class AnnouncementController extends Controller
{
    public function __construct(protected AnnouncementServiceInterface $announcementService)
    {
    }
    public function index()
    {
        $announcements = $this->announcementService->getPaginatedAnnouncements(15);
        return view('admin.announcement.index', compact('announcements'));
    }
    public function create()
    {
        return view('admin.announcement.create');
    }
    public function store(AnnouncementRequest $request)
    {
        try {
            $data = $request->validated();
            $this->announcementService->createAnnouncement($data);
            return redirect()->route('admin.announcement.index')
                ->with('success', __('admin/announcement/general.notifications.created'));
        } catch (\Exception $e) {
            return redirect()->back()
                ->with('error', __('admin/announcement/general.notifications.error', ['message' => $e->getMessage()]))
                ->withInput();
        }
    }
    public function show(string $locale, int $id)
    {
        $announcement = $this->announcementService->findAnnouncement($id);
        if (!$announcement) {
            return redirect()->route('admin.announcement.index')
                ->with('error', __('admin/announcement/general.notifications.not_found'));
        }
        return view('admin.announcement.show', compact('announcement'));
    }
    public function edit(string $locale, int $id)
    {
        $announcement = $this->announcementService->findAnnouncement($id);
        if (!$announcement) {
            return redirect()->route('admin.announcement.index')
                ->with('error', __('admin/announcement/general.notifications.not_found'));
        }
        $announcement->load('translations');
        return view('admin.announcement.edit', compact('announcement'));
    }
    public function update(AnnouncementRequest $request, string $locale, int $id)
    {
        try {
            $data = $request->validated();
            $announcement = $this->announcementService->updateAnnouncement($id, $data);
            if (!$announcement) {
                return redirect()->route('admin.announcement.index')
                    ->with('error', __('admin/announcement/general.notifications.not_found'));
            }
            return redirect()->route('admin.announcement.index')
                ->with('success', __('admin/announcement/general.notifications.updated'));
        } catch (\Exception $e) {
            return redirect()->back()
                ->with('error', __('admin/announcement/general.notifications.error', ['message' => $e->getMessage()]))
                ->withInput();
        }
    }
    public function destroy(string $locale, int $id): JsonResponse
    {
        try {
            $deleted = $this->announcementService->deleteAnnouncement($id);
            if (!$deleted) {
                return response()->json([
                    'success' => false,
                    'message' => __('admin/announcement/general.notifications.not_found')
                ], 404);
            }
            return response()->json([
                'success' => true,
                'message' => __('admin/announcement/general.notifications.deleted')
            ]);
        } catch (\Exception $e) {
            return response()->json([
                'success' => false,
                'message' => __('admin/announcement/general.notifications.error', ['message' => $e->getMessage()])
            ], 500);
        }
    }
    public function toggleStatus(string $locale, int $id)
    {
        try {
            $toggled = $this->announcementService->toggleAnnouncementStatus($id);
            if (!$toggled) {
                return response()->json(['error' => __('admin/announcement/general.notifications.not_found')], 404);
            }
            return response()->json(['success' => __('admin/announcement/general.notifications.status_changed')]);
        } catch (\Exception $e) {
            return response()->json(['error' => __('admin/announcement/general.notifications.error', ['message' => $e->getMessage()])], 500);
        }
    }
    public function bulkToggleStatus(Request $request)
    {
        $idsInput = $request->input('ids');
        $statusInput = $request->input('status');
        $ids = json_decode($idsInput, true);
        $status = $statusInput === 'true';
        if (!$ids || !is_array($ids)) {
            return response()->json(['success' => false, 'message' => __('admin/announcement/general.notifications.invalid_data')], 400);
        }
        try {
            foreach ($ids as $id) {
                $announcement = $this->announcementService->findAnnouncement($id);
                if ($announcement) {
                    $announcement->is_active = $status;
                    $announcement->save();
                }
            }
            return response()->json(['success' => true, 'message' => __('admin/announcement/general.notifications.bulk_status_changed')]);
        } catch (\Exception $e) {
            return response()->json(['success' => false, 'message' => $e->getMessage()], 500);
        }
    }
    public function bulkDelete(Request $request): JsonResponse
    {
        Log::info("Bulk delete request received with IDs: " . json_encode($request->ids));
        try {
            $request->validate([
                'ids' => 'required|array',
                'ids.*' => 'integer|exists:announcements,id',
            ]);
            $success = $this->announcementService->bulkDeleteAnnouncements($request->ids);
            if (!$success) {
                return response()->json([
                    'success' => false,
                    'message' => __('admin/announcement/general.notifications.none_deleted')
                ], 400);
            }
            return response()->json([
                'success' => true,
                'message' => __('admin/announcement/general.notifications.bulk_deleted')
            ]);
        } catch (\Exception $e) {
            return response()->json([
                'success' => false,
                'message' => __('admin/announcement/general.notifications.error', ['message' => $e->getMessage()])
            ], 500);
        }
    }
}
