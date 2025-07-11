<?php

namespace App\Services;

use App\Models\Contact;
use Illuminate\Database\Eloquent\Collection;
use Illuminate\Pagination\LengthAwarePaginator;

interface ContactServiceInterface
{
    public function getAllContacts(): Collection;
    public function getPaginatedContacts(int $perPage = 15): LengthAwarePaginator;
    public function findContact(int $id): ?Contact;
    public function createContact(array $data): Contact;
    public function updateContact(int $id, array $data): ?Contact;
    public function deleteContact(int $id): bool;
    public function getContactsByUser(int $userId): Collection;
    public function getContactsByStatus(string $status): Collection;
    public function replyToContact(int $id, string $reply): bool;
    public function getPendingContacts(): Collection;
}