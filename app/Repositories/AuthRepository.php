<?php

namespace App\Repositories;

use App\Models\User;
use App\Repositories\Interfaces\AuthRepositoryInterface;
use Illuminate\Support\Facades\Auth;
class AuthRepository implements AuthRepositoryInterface
{
    public function attempt(array $credentials): bool
    {
        return Auth::attempt($credentials);
    }

    public function login(User $user): void
    {
        Auth::login($user);
    }

    public function logout(): void
    {
        Auth::logout();
    }

    public function user(): ?User
    {
        return Auth::user();
    }

    public function check(): bool
    {
        return Auth::check();
    }

    public function guest(): bool
    {
        return Auth::guest();
    }
}