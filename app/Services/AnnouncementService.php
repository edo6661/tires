<?php

namespace App\Services;

use App\Models\Announcement;
use App\Repositories\AnnouncementRepositoryInterface;
use Illuminate\Database\Eloquent\Collection;
use Illuminate\Pagination\LengthAwarePaginator;

class AnnouncementService implements AnnouncementServiceInterface
{
    protected $announcementRepository;

    public function __construct(AnnouncementRepositoryInterface $announcementRepository)
    {
        $this->announcementRepository = $announcementRepository;
    }

    public function getAllAnnouncements(): Collection
    {
        return $this->announcementRepository->getAll();
    }

    public function getActiveAnnouncements(): Collection
    {
        return $this->announcementRepository->getActive();
    }

    public function getPublishedAnnouncements(): Collection
    {
        return $this->announcementRepository->getPublished();
    }

    public function getPaginatedAnnouncements(int $perPage = 15): LengthAwarePaginator
    {
        return $this->announcementRepository->getPaginated($perPage);
    }

    public function findAnnouncement(int $id): ?Announcement
    {
        return $this->announcementRepository->findById($id);
    }

    public function createAnnouncement(array $data): Announcement
    {
        // Validasi bahwa translations untuk EN dan JA harus ada
        if (!isset($data['translations']) ||
            !isset($data['translations']['en']) ||
            !isset($data['translations']['ja'])) {
            throw new \InvalidArgumentException('Translations untuk English dan Japanese wajib diisi.');
        }

        if (empty($data['translations']['en']['title']) ||
            empty($data['translations']['ja']['title'])) {
            throw new \InvalidArgumentException('Title untuk English dan Japanese wajib diisi.');
        }

        if (!isset($data['published_at'])) {
            $data['published_at'] = now();
        }

        return $this->announcementRepository->create($data);
    }

    public function updateAnnouncement(int $id, array $data): ?Announcement
    {
        // Validasi translations jika ada
        if (isset($data['translations'])) {
            if (!isset($data['translations']['en']) ||
                !isset($data['translations']['ja'])) {
                throw new \InvalidArgumentException('Translations untuk English dan Japanese wajib diisi.');
            }

            if (empty($data['translations']['en']['title']) ||
                empty($data['translations']['ja']['title'])) {
                throw new \InvalidArgumentException('Title untuk English dan Japanese wajib diisi.');
            }
        }

        return $this->announcementRepository->update($id, $data);
    }

    public function deleteAnnouncement(int $id): bool
    {
        return $this->announcementRepository->delete($id);
    }

    public function toggleAnnouncementStatus(int $id): bool
    {
        return $this->announcementRepository->toggleActive($id);
    }

    public function bulkDeleteAnnouncements(array $ids): bool
    {
        if (empty($ids)) {
            throw new \InvalidArgumentException('IDs tidak boleh kosong.');
        }
        return $this->announcementRepository->bulkDelete($ids);
    }

    public function searchAnnouncementsByTitle(string $search, ?string $locale = null): Collection
    {
        return $this->announcementRepository->searchByTitle($search, $locale);
    }
}