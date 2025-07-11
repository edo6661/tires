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
        if (!isset($data['published_at'])) {
            $data['published_at'] = now();
        }

        return $this->announcementRepository->create($data);
    }

    public function updateAnnouncement(int $id, array $data): ?Announcement
    {
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
}
