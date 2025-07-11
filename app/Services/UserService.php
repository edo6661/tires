<?php

namespace App\Services;

use App\Models\User;
use App\Repositories\UserRepositoryInterface;
use App\Services\UserServiceInterface;
use Illuminate\Database\Eloquent\Collection;
use Illuminate\Pagination\LengthAwarePaginator;
use Illuminate\Support\Facades\Hash;

class UserService implements UserServiceInterface
{
    protected $userRepository;

    public function __construct(UserRepositoryInterface $userRepository)
    {
        $this->userRepository = $userRepository;
    }

    public function getAllUsers(): Collection
    {
        return $this->userRepository->getAll();
    }

    public function getPaginatedUsers(int $perPage = 15): LengthAwarePaginator
    {
        return $this->userRepository->getPaginated($perPage);
    }

    public function findUser(int $id): ?User
    {
        return $this->userRepository->findById($id);
    }

    public function createUser(array $data): User
    {
        
        if ($this->findUserByEmail($data['email'])) {
            throw new \Exception('Email sudah digunakan');
        }

        
        if (isset($data['password'])) {
            $data['password'] = Hash::make($data['password']);
        }

        
        if (!isset($data['role'])) {
            $data['role'] = 'customer';
        }

        
        $allowedRoles = ['customer', 'admin'];
        if (!in_array($data['role'], $allowedRoles)) {
            throw new \InvalidArgumentException('Role tidak valid: ' . $data['role']);
        }

        return $this->userRepository->create($data);
    }

    public function updateUser(int $id, array $data): ?User
    {
        $user = $this->findUser($id);
        if (!$user) {
            return null;
        }

        if (isset($data['password'])) {
            $data['password'] = Hash::make($data['password']);
        }

        return $this->userRepository->update($id, $data);
    }

    public function deleteUser(int $id): bool
    {
        $user = $this->findUser($id);
        if (!$user) {
            return false;
        }

        
        if ($user->reservations()->whereIn('status', ['pending', 'confirmed'])->exists()) {
            throw new \Exception('Tidak bisa menghapus user yang memiliki reservasi aktif');
        }

        
        if ($user->tireStorage()->where('status', 'active')->exists()) {
            throw new \Exception('Tidak bisa menghapus user yang memiliki penyimpanan ban aktif');
        }

        return $this->userRepository->delete($id);
    }

    public function findUserByEmail(string $email): ?User
    {
        return $this->userRepository->findByEmail($email);
    }

    public function getCustomers(): Collection
    {
        return $this->userRepository->getCustomers();
    }

    public function getAdmins(): Collection
    {
        return $this->userRepository->getAdmins();
    }

    public function getUsersWithTireStorage(): Collection
    {
        return $this->userRepository->getWithTireStorage();
    }

    public function getFirstTimeCustomers(): Collection
    {
        return $this->userRepository->getFirstTimeCustomers();
    }

    public function getRepeatCustomers(): Collection
    {
        return $this->userRepository->getRepeatCustomers();
    }

    public function getDormantCustomers(): Collection
    {
        return $this->userRepository->getDormantCustomers();
    }

    public function searchUsers(string $query): Collection
    {
        if (empty(trim($query))) {
            throw new \InvalidArgumentException('Query pencarian tidak boleh kosong');
        }

        return $this->userRepository->search($query);
    }

    public function getUsersByRole(string $role): Collection
    {
        $allowedRoles = ['customer', 'admin'];
        if (!in_array($role, $allowedRoles)) {
            throw new \InvalidArgumentException('Role tidak valid: ' . $role);
        }

        return $this->userRepository->getByRole($role);
    }

    public function changePassword(int $id, string $currentPassword, string $newPassword): bool
    {
        $user = $this->findUser($id);
        if (!$user) {
            return false;
        }

        if (!Hash::check($currentPassword, $user->password)) {
            throw new \Exception('Password saat ini salah');
        }

        $updated = $this->userRepository->update($id, [
            'password' => Hash::make($newPassword)
        ]);

        return $updated !== null;
    }

    public function resetPassword(int $id, string $newPassword): bool
    {
        $user = $this->findUser($id);
        if (!$user) {
            return false;
        }

        $updated = $this->userRepository->update($id, [
            'password' => Hash::make($newPassword)
        ]);

        return $updated !== null;
    }

}