<?php

namespace App\Repositories;

use Illuminate\Database\Eloquent\Collection;
use Illuminate\Pagination\LengthAwarePaginator;
use Illuminate\Contracts\Pagination\CursorPaginator;
use App\Models\Faq;

interface FaqRepositoryInterface
{
    public function getAll(): Collection;
    public function getActive(): Collection;
    public function getPaginated(int $perPage = 15): LengthAwarePaginator;
    public function getPaginatedWithCursor(int $perPage = 15, ?string $cursor = null, array $filters = []): CursorPaginator;
    public function getPaginatedWithFilters(array $filters, int $perPage = 15): LengthAwarePaginator;
    public function findById(int $id): ?Faq;
    public function create(array $data): Faq;
    public function update(int $id, array $data): ?Faq;
    public function delete(int $id): bool;
    public function toggleActive(int $id): bool;
    public function reorder(array $orderData): bool;
    public function getStatistics(array $filters = []): array;
}
