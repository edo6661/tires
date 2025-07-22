<?php

namespace App\Repositories;

use Illuminate\Database\Eloquent\Collection;
use Illuminate\Pagination\LengthAwarePaginator;
use App\Models\Menu;

interface MenuRepositoryInterface
{
    public function getAll(): Collection;
    public function getActive(): Collection;
    public function getPaginated(int $perPage = 15): LengthAwarePaginator;
    public function findById(int $id): ?Menu;
    public function create(array $data): Menu;
    public function update(int $id, array $data): ?Menu;
    public function delete(int $id): bool;
    public function toggleActive(int $id): bool;
    public function reorder(array $orderData): bool;
    public function getByDisplayOrder(): Collection;
    public function bulkDelete(array $ids): bool;
}