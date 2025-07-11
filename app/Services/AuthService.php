<?php
namespace App\Services;
use App\Enums\UserRole;
use App\Events\PasswordResetRequested;
use App\Models\User;
use App\Repositories\AuthRepositoryInterface;
use App\Repositories\UserRepositoryInterface;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Str;
use Illuminate\Support\Facades\DB;
use Carbon\Carbon;
class AuthService implements AuthServiceInterface
{
    public function __construct(
        protected AuthRepositoryInterface $authRepository,
        protected UserRepositoryInterface $userRepository
    ) {}
    public function login(array $credentials): bool
    {
        return $this->authRepository->attempt($credentials);
    }
    public function register(array $data): User
    {
        $data['password'] = Hash::make($data['password']);
        $data['role'] = $data['role'] ?? UserRole::CUSTOMER;
        $user = $this->userRepository->create($data);
        $this->authRepository->login($user);
        return $user;
    }
    public function logout(): void
    {
        $this->authRepository->logout();
    }
    public function getCurrentUser(): ?User
    {
        return $this->authRepository->user();
    }
    public function isAuthenticated(): bool
    {
        return $this->authRepository->check();
    }
     public function sendPasswordResetLink(string $email): bool
    {
        $user = $this->userRepository->findByEmail($email);
        if (!$user) {
            return false;
        }
        $token = Str::random(60);
        DB::table('password_reset_tokens')->updateOrInsert(
            ['email' => $email],
            [
                'token' => Hash::make($token),
                'created_at' => Carbon::now()
            ]
        );
        event(new PasswordResetRequested($user, $token));
        return true;
    }
    public function resetPassword(string $email, string $token, string $password): bool
    {
        $user = $this->userRepository->findByEmail($email);
        if (!$user) {
            return false;
        }
        $resetRecord = DB::table('password_reset_tokens')
            ->where('email', $email)
            ->first();
        if (!$resetRecord || !Hash::check($token, $resetRecord->token)) {
            return false;
        }
        if (Carbon::parse($resetRecord->created_at)->addMinutes(60)->isPast()) {
            return false;
        }
        $updated = $this->userRepository->update($user->id, [
            'password' => Hash::make($password)
        ]);
        if ($updated) {
            DB::table('password_reset_tokens')
                ->where('email', $email)
                ->delete();
            return true;
        }
        return false;
    }
}
