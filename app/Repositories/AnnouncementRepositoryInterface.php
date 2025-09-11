<?php


namespace App\Repositories;

use Illuminate\Database\Eloquent\Collection;
use Illuminate\Pagination\LengthAwarePaginator;
use App\Models\Announcement;
use Illuminate\Pagination\CursorPaginator;

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
    public function getPaginatedWithCursor(int $perPage, ?string $cursor = null, array $filters = []): CursorPaginator;
    public function count(): int;
    public function countByStatus(string $status): int;
    public function countTodayAnnouncements(): int;
    public function getFiltered(array $filters, int $perPage = null): Collection;
    public function search(string $query, int $perPage = 15): Collection;
}
