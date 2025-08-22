<?php

namespace App\Repositories;

use App\Models\Menu;
use Illuminate\Pagination\CursorPaginator;
use Illuminate\Database\Eloquent\Collection;
use Illuminate\Contracts\Pagination\Paginator;
use Illuminate\Pagination\LengthAwarePaginator;

interface MenuRepositoryInterface
{
    public function getAll(): Collection;
    public function getActive(): Collection;
    public function getPaginated(int $perPage = 15, ?string $cursor = null): CursorPaginator;
    public function getPaginatedActive(int $perPage = 15): LengthAwarePaginator;
    public function findById(int $id): ?Menu;
    public function findByIdWithLocale(int $id, string $locale = null): ?Menu;
    public function create(array $data): Menu;
    public function update(int $id, array $data): ?Menu;
    public function delete(int $id): bool;
    public function toggleActive(int $id): bool;
    public function reorder(array $orderData): bool;
    public function getByDisplayOrder(): Collection;
    public function bulkDelete(array $ids): bool;
    public function bulkUpdateStatus(array $ids, bool $status): bool;
    public function search(string $query, string $locale = 'en', bool $activeOnly = true, int $perPage = 15): LengthAwarePaginator;
    public function getPopular(int $limit = 10, string $period = 'month'): Collection;
    public function count(): int;
    public function countActive(): int;
    public function findBySlug(string $slug): ?Menu;
    public function searchByName(string $search, string $locale = null): Collection;

    // Cursor pagination methods - PERBAIKI SIGNATURE
    // public function getCursorPaginated(int $limit = 15, ?string $cursor = null): array;
    // public function getCursorPaginatedActive(int $limit = 15, ?string $cursor = null): array;
    // public function searchWithCursor(string $query, string $locale = 'en', bool $activeOnly = true, int $limit = 15, ?string $cursor = null): array;
}
