<?php

namespace App\Services;

use App\Models\Announcement;
use Illuminate\Database\Eloquent\Collection;
use Illuminate\Pagination\LengthAwarePaginator;

interface AnnouncementServiceInterface
{
    public function getAllAnnouncements(): Collection;
    public function getActiveAnnouncements(): Collection;
    public function getPaginatedAnnouncements(int $perPage = 15): LengthAwarePaginator;
    public function findAnnouncement(int $id): ?Announcement;
    public function createAnnouncement(array $data): Announcement;
    public function updateAnnouncement(int $id, array $data): ?Announcement;
    public function deleteAnnouncement(int $id): bool;
    public function toggleAnnouncementStatus(int $id): bool;
}
