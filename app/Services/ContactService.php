<?php
namespace App\Services;

use App\Models\Contact;
use App\Repositories\ContactRepositoryInterface;
use Illuminate\Database\Eloquent\Collection;
use Illuminate\Pagination\LengthAwarePaginator;

class ContactService implements ContactServiceInterface
{
    protected $contactRepository;

    public function __construct(ContactRepositoryInterface $contactRepository)
    {
        $this->contactRepository = $contactRepository;
    }

    public function getAllContacts(): Collection
    {
        return $this->contactRepository->getAll();
    }

    public function getPaginatedContacts(int $perPage = 15): LengthAwarePaginator
    {
        return $this->contactRepository->getPaginated($perPage);
    }

    public function getContactStats(): array
    {
        return $this->contactRepository->getContactStats();
    }

    public function findContact(int $id): ?Contact
    {
        return $this->contactRepository->findById($id);
    }

    public function createContact(array $data): Contact
    {
        if (!isset($data['status'])) {
            $data['status'] = 'pending';
        }

        return $this->contactRepository->create($data);
    }

    public function updateContact(int $id, array $data): ?Contact
    {
        // Validasi bahwa hanya status dan admin_reply yang bisa diupdate
        $allowedFields = ['status', 'admin_reply', 'replied_at'];
        $filteredData = array_intersect_key($data, array_flip($allowedFields));

        if (empty($filteredData)) {
            throw new \InvalidArgumentException('Tidak ada field yang valid untuk diupdate');
        }

        return $this->contactRepository->update($id, $filteredData);
    }

    public function deleteContact(int $id): bool
    {
        return $this->contactRepository->delete($id);
    }

    public function getContactsByUser(int $userId): Collection
    {
        return $this->contactRepository->getByUserId($userId);
    }

    public function getContactsByStatus(string $status): Collection
    {
        return $this->contactRepository->getByStatus($status);
    }

    public function replyToContact(int $id, string $reply): bool
    {
        if (empty(trim($reply))) {
            throw new \InvalidArgumentException('Reply tidak boleh kosong');
        }

        return $this->contactRepository->markAsReplied($id, $reply);
    }

    public function getPendingContacts(): Collection
    {
        return $this->contactRepository->getPending();
    }

    public function bulkDeleteContacts(array $ids): bool
    {
        if (empty($ids)) {
            throw new \InvalidArgumentException('ID tidak boleh kosong');
        }

        return $this->contactRepository->bulkDelete($ids);
    }

    public function getFilteredContacts(array $filters): LengthAwarePaginator
    {
        return $this->contactRepository->getFiltered($filters);
    }
}
