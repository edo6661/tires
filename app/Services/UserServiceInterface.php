<?php

namespace App\Services;

use App\Models\User;
use Illuminate\Database\Eloquent\Collection;
use Illuminate\Pagination\LengthAwarePaginator;
use Illuminate\Pagination\CursorPaginator;

interface UserServiceInterface
{
    public function getAllUsers(): Collection;
    public function getPaginatedUsers(int $perPage = 15): LengthAwarePaginator;
    public function findUser(int $id): ?User;
    public function createUser(array $data): User;
    public function updateUser(int $id, array $data): ?User;
    public function deleteUser(int $id): bool;
    public function findUserByEmail(string $email): ?User;
    public function getCustomers(): Collection;
    public function getAdmins(): Collection;
    public function getUsersWithTireStorage(): Collection;
    public function getFirstTimeCustomers(): Collection;
    public function getRepeatCustomers(): Collection;
    public function getDormantCustomers(): Collection;
    public function searchUsers(string $query): Collection;
    public function getUsersByRole(string $role): Collection;
    public function changePassword(int $id, string $currentPassword, string $newPassword): bool;
    public function resetPassword(int $id, string $newPassword): bool;

    // ADD THESE CURSOR PAGINATION METHODS
    public function getPaginatedUsersWithCursor(int $perPage = 15, ?string $cursor = null): CursorPaginator;
    public function searchUsersWithCursor(string $query, int $perPage = 15, ?string $cursor = null): CursorPaginator;
    public function getUsersByRoleWithCursor(string $role, int $perPage = 15, ?string $cursor = null): CursorPaginator;
    public function getUserReservations(int $userId, int $perPage = 15, ?string $cursor = null): CursorPaginator;

}
