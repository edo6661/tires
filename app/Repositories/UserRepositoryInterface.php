<?php

namespace App\Repositories;

use Illuminate\Database\Eloquent\Collection;
use Illuminate\Pagination\LengthAwarePaginator;
use App\Models\User;

interface UserRepositoryInterface
{
    public function getAll(): Collection;
    public function getPaginated(int $perPage = 15): LengthAwarePaginator;
    public function findById(int $id): ?User;
    public function create(array $data): User;
    public function update(int $id, array $data): ?User;
    public function delete(int $id): bool;
    public function findByEmail(string $email): ?User;
    public function getCustomers(): Collection;
    public function getAdmins(): Collection;
    public function getWithTireStorage(): Collection;
    public function getFirstTimeCustomers(): Collection;
    public function getRepeatCustomers(): Collection;
    public function getDormantCustomers(): Collection;
    public function search(string $query): Collection;
    public function getByRole(string $role): Collection;
}