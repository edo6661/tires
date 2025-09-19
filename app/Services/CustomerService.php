<?php

namespace App\Services;

use App\Repositories\CustomerRepositoryInterface;
use App\Services\CustomerServiceInterface;
use Illuminate\Support\Collection;
use Illuminate\Pagination\LengthAwarePaginator;
use Illuminate\Contracts\Pagination\CursorPaginator;

class CustomerService implements CustomerServiceInterface
{
    protected $customerRepository;

    public function __construct(CustomerRepositoryInterface $customerRepository)
    {
        $this->customerRepository = $customerRepository;
    }

    public function getCustomers(array $filters = [], int $perPage = 15): LengthAwarePaginator
    {
        return $this->customerRepository->getPaginated($filters, $perPage);
    }

    public function getPaginatedCustomersWithCursor(int $perPage = 15, ?string $cursor = null, array $filters = []): CursorPaginator
    {
        return $this->customerRepository->getPaginatedWithCursor($filters, $perPage, $cursor);
    }

    public function getCustomerDetail(int $id): ?array
    {
        $customer = $this->customerRepository->findById($id);

        if (!$customer) {
            return null;
        }

        $reservationHistory = $this->getCustomerReservationHistory($id, $customer['user_id']);
        $tireStorage = $this->getCustomerTireStorage($id, $customer['user_id']);
        $stats = $this->getCustomerStats($id, $customer['user_id']);

        // Convert collections to arrays for proper JSON serialization
        return [
            'customer' => $customer,
            'reservation_history' => $reservationHistory->toArray(),
            'tire_storage' => $tireStorage->toArray(),
            'stats' => $stats
        ];
    }

    public function getFirstTimeCustomers(): Collection
    {
        return $this->customerRepository->getFirstTimeCustomers();
    }

    public function getRepeatCustomers(): Collection
    {
        return $this->customerRepository->getRepeatCustomers();
    }

    public function getDormantCustomers(): Collection
    {
        return $this->customerRepository->getDormantCustomers();
    }

    public function getFirstTimeCustomersWithCursor(int $perPage = 15, ?string $cursor = null): CursorPaginator
    {
        return $this->customerRepository->getFirstTimeCustomersWithCursor($perPage, $cursor);
    }

    public function getRepeatCustomersWithCursor(int $perPage = 15, ?string $cursor = null): CursorPaginator
    {
        return $this->customerRepository->getRepeatCustomersWithCursor($perPage, $cursor);
    }

    public function getDormantCustomersWithCursor(int $perPage = 15, ?string $cursor = null): CursorPaginator
    {
        return $this->customerRepository->getDormantCustomersWithCursor($perPage, $cursor);
    }


    public function getCustomerReservationHistory(int $customerId, ?int $userId = null): Collection
    {
        $reservations = $this->customerRepository->getCustomerReservationHistory($customerId, $userId);

        // Transform reservations to include formatted menu data using MenuResource
        return $reservations->map(function ($reservation) {
            // Create a copy of the reservation as an array
            $reservationArray = $reservation->toArray();

            if ($reservation->menu) {
                // Use MenuResource to format menu data
                $menuResource = new \App\Http\Resources\MenuResource($reservation->menu);
                $reservationArray['menu'] = $menuResource->resolve();
            }

            return $reservationArray;
        });
    }

    public function getCustomerTireStorage(int $customerId, ?int $userId = null): Collection
    {
        return $this->customerRepository->getCustomerTireStorage($customerId, $userId);
    }

    public function getCustomerStats(int $customerId, ?int $userId = null): array
    {
        return $this->customerRepository->getCustomerStats($customerId, $userId);
    }

    public function searchCustomers(string $search): Collection
    {
        return $this->customerRepository->searchCustomers($search);
    }

    public function searchCustomersWithCursor(string $search, int $perPage = 15, ?string $cursor = null, array $filters = []): CursorPaginator
    {
        $searchFilters = array_merge($filters, ['search' => $search]);
        return $this->customerRepository->getPaginatedWithCursor($searchFilters, $perPage, $cursor);
    }

    public function getCustomerTypeCounts(): array
    {
        return [
            'first_time' => $this->getFirstTimeCustomers()->count(),
            'repeat' => $this->getRepeatCustomers()->count(),
            'dormant' => $this->getDormantCustomers()->count(),
        ];
    }
}
