<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Services\AnnouncementServiceInterface;
use App\Http\Requests\AnnouncementRequest;
use Illuminate\Http\Request;

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
            $this->announcementService->createAnnouncement($request->validated());
            return redirect()->route('admin.announcement.index')
                ->with('success', 'Pengumuman berhasil dibuat.');
        } catch (\Exception $e) {
            return redirect()->back()
                ->with('error', 'Terjadi kesalahan: ' . $e->getMessage())
                ->withInput();
        }
    }

    public function show(int $id)
    {
        $announcement = $this->announcementService->findAnnouncement($id);
        if (!$announcement) {
            return redirect()->route('admin.announcement.index')
                ->with('error', 'Pengumuman tidak ditemukan.');
        }
        return view('admin.announcement.show', compact('announcement'));
    }

    public function edit(int $id)
    {
        $announcement = $this->announcementService->findAnnouncement($id);
        if (!$announcement) {
            return redirect()->route('admin.announcement.index')
                ->with('error', 'Pengumuman tidak ditemukan.');
        }
        return view('admin.announcement.edit', compact('announcement'));
    }

    public function update(AnnouncementRequest $request, int $id)
    {
        try {
            $announcement = $this->announcementService->updateAnnouncement($id, $request->validated());
            if (!$announcement) {
                return redirect()->route('admin.announcement.index')
                    ->with('error', 'Pengumuman tidak ditemukan.');
            }
            return redirect()->route('admin.announcement.index')
                ->with('success', 'Pengumuman berhasil diperbarui.');
        } catch (\Exception $e) {
            return redirect()->back()
                ->with('error', 'Terjadi kesalahan: ' . $e->getMessage())
                ->withInput();
        }
    }

    public function destroy(int $id)
    {
        try {
            $deleted = $this->announcementService->deleteAnnouncement($id);
            if (!$deleted) {
                return redirect()->route('admin.announcement.index')
                    ->with('error', 'Pengumuman tidak ditemukan.');
            }
            return redirect()->route('admin.announcement.index')
                ->with('success', 'Pengumuman berhasil dihapus.');
        } catch (\Exception $e) {
            return redirect()->back()
                ->with('error', 'Terjadi kesalahan: ' . $e->getMessage());
        }
    }

    public function toggleStatus(int $id)
    {
        try {
            $toggled = $this->announcementService->toggleAnnouncementStatus($id);
            if (!$toggled) {
                return redirect()->route('admin.announcement.index')
                    ->with('error', 'Pengumuman tidak ditemukan.');
            }
            return redirect()->route('admin.announcement.index')
                ->with('success', 'Status pengumuman berhasil diubah.');
        } catch (\Exception $e) {
            return redirect()->back()
                ->with('error', 'Terjadi kesalahan: ' . $e->getMessage());
        }
    }
}
