<?php
namespace App\Repositories;

use Illuminate\Database\Eloquent\Collection;
use Illuminate\Pagination\LengthAwarePaginator;
use App\Models\Contact;

interface ContactRepositoryInterface
{
    public function getAll(): Collection;
    public function getPaginated(int $perPage = 15): LengthAwarePaginator;
    public function findById(int $id): ?Contact;
    public function create(array $data): Contact;
    public function update(int $id, array $data): ?Contact;
    public function delete(int $id): bool;
    public function getByUserId(int $userId): Collection;
    public function getByStatus(string $status): Collection;
    public function markAsReplied(int $id, string $reply): bool;
    public function getPending(): Collection;
}