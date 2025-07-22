<?php
namespace App\Repositories;

use App\Models\Contact;
use Illuminate\Database\Eloquent\Collection;
use Illuminate\Pagination\LengthAwarePaginator;

interface ContactRepositoryInterface
{
    public function getAll(): Collection;
    public function getPaginated(int $perPage = 15): LengthAwarePaginator;
    public function getContactStats(): array;
    public function findById(int $id): ?Contact;
    public function create(array $data): Contact;
    public function update(int $id, array $data): ?Contact;
    public function delete(int $id): bool;
    public function getByUserId(int $userId): Collection;
    public function getByStatus(string $status): Collection;
    public function markAsReplied(int $id, string $reply): bool;
    public function getPending(): Collection;
    public function bulkDelete(array $ids): bool;
    public function getFiltered(array $filters): LengthAwarePaginator;
}
