<?php

namespace App\Repositories;

use App\Models\User;

interface AuthRepositoryInterface
{
    public function attempt(array $credentials): bool;
    public function login(User $user): void;
    public function logout(): void;
    public function user(): ?User;
    public function check(): bool;
    public function guest(): bool;
}