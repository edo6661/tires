<?php


namespace App\Services;

use App\Models\Faq;
use App\Repositories\FaqRepositoryInterface;
use Illuminate\Database\Eloquent\Collection;
use Illuminate\Pagination\LengthAwarePaginator;
use Illuminate\Contracts\Pagination\CursorPaginator;

class FaqService implements FaqServiceInterface
{
    protected $faqRepository;

    public function __construct(FaqRepositoryInterface $faqRepository)
    {
        $this->faqRepository = $faqRepository;
    }

    public function getAllFaqs(): Collection
    {
        return $this->faqRepository->getAll();
    }

    public function getActiveFaqs(): Collection
    {
        return $this->faqRepository->getActive();
    }

    public function getPaginatedFaqs(int $perPage = 15): LengthAwarePaginator
    {
        return $this->faqRepository->getPaginated($perPage);
    }

    public function findFaq(int $id): ?Faq
    {
        return $this->faqRepository->findById($id);
    }

    public function createFaq(array $data): Faq
    {
        // Set default is_active if not provided
        if (!isset($data['is_active'])) {
            $data['is_active'] = true;
        }

        return $this->faqRepository->create($data);
    }

    public function updateFaq(int $id, array $data): ?Faq
    {
        return $this->faqRepository->update($id, $data);
    }

    public function deleteFaq(int $id): bool
    {
        return $this->faqRepository->delete($id);
    }

    public function toggleFaqStatus(int $id): bool
    {
        return $this->faqRepository->toggleActive($id);
    }

    public function reorderFaqs(array $orderData): bool
    {
        return $this->faqRepository->reorder($orderData);
    }

    public function getPaginatedFaqsWithCursor(int $perPage = 15, ?string $cursor = null, array $filters = []): CursorPaginator
    {
        return $this->faqRepository->getPaginatedWithCursor($perPage, $cursor, $filters);
    }

    public function getFilteredFaqs(array $filters, int $perPage = 15): Collection
    {
        $paginated = $this->faqRepository->getPaginatedWithFilters($filters, $perPage);
        return collect($paginated->items());
    }

    public function getFaqStatistics(): array
    {
        return $this->faqRepository->getStatistics([]);
    }
}
