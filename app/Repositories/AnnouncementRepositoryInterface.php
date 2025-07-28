<?php


namespace App\Repositories;

use Illuminate\Database\Eloquent\Collection;
use Illuminate\Pagination\LengthAwarePaginator;
use App\Models\Announcement;

interface AnnouncementRepositoryInterface
{
    public function getAll(): Collection;
    public function getActive(): Collection;
    public function getPaginated(int $perPage = 15): LengthAwarePaginator;
    public function findById(int $id): ?Announcement;
    public function create(array $data): Announcement;
    public function update(int $id, array $data): ?Announcement;
    public function delete(int $id): bool;
    public function toggleActive(int $id): bool;
    public function bulkDelete(array $ids): bool;
    public function searchByTitle(string $search, ?string $locale = null): Collection;
}
