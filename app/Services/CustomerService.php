<?php

namespace App\Services;

use App\Repositories\CustomerRepositoryInterface;
use App\Services\CustomerServiceInterface;
use Illuminate\Support\Collection;
use Illuminate\Pagination\LengthAwarePaginator;

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

    public function getCustomerDetail(int $id): ?array
    {
        $customer = $this->customerRepository->findById($id);
        
        if (!$customer) {
            return null;
        }
        
        $reservationHistory = $this->getCustomerReservationHistory($id, $customer['user_id']);
        $tireStorage = $this->getCustomerTireStorage($id, $customer['user_id']);
        $stats = $this->getCustomerStats($id, $customer['user_id']);
        
        return [
            'customer' => $customer,
            'reservation_history' => $reservationHistory,
            'tire_storage' => $tireStorage,
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

    public function getCustomerReservationHistory(int $customerId, ?int $userId = null): Collection
    {
        return $this->customerRepository->getCustomerReservationHistory($customerId, $userId);
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

    public function getCustomerTypeCounts(): array
    {
        return [
            'first_time' => $this->getFirstTimeCustomers()->count(),
            'repeat' => $this->getRepeatCustomers()->count(),
            'dormant' => $this->getDormantCustomers()->count(),
        ];
    }
}