<?php

namespace App\Services;

use App\Models\User;
use Illuminate\Database\Eloquent\Collection;
use Illuminate\Pagination\LengthAwarePaginator;

interface AuthServiceInterface
{
    public function login(array $credentials): bool;
    public function register(array $data): User;
    public function logout(): void;
    public function getCurrentUser(): ?User;
    public function isAuthenticated(): bool;
    public function sendPasswordResetLink(string $email): bool;
    public function resetPassword(string $email, string $token, string $password): bool;
}