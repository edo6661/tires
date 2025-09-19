<?php

namespace App\Services;

use App\Models\Faq;
use Illuminate\Database\Eloquent\Collection;
use Illuminate\Pagination\LengthAwarePaginator;
use Illuminate\Contracts\Pagination\CursorPaginator;

interface FaqServiceInterface
{
    public function getAllFaqs(): Collection;
    public function getActiveFaqs(): Collection;
    public function getPaginatedFaqs(int $perPage = 15): LengthAwarePaginator;
    public function getPaginatedFaqsWithCursor(int $perPage = 15, ?string $cursor = null, array $filters = []): CursorPaginator;
    public function getFilteredFaqs(array $filters, int $perPage = 15): Collection;
    public function findFaq(int $id): ?Faq;
    public function createFaq(array $data): Faq;
    public function updateFaq(int $id, array $data): ?Faq;
    public function deleteFaq(int $id): bool;
    public function toggleFaqStatus(int $id): bool;
    public function reorderFaqs(array $orderData): bool;
    public function getFaqStatistics(): array;
}
