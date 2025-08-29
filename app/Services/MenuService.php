<?php

namespace App\Services;

use App\Models\Menu;
use App\Repositories\MenuRepositoryInterface;
use Illuminate\Database\Eloquent\Collection;
use Illuminate\Pagination\LengthAwarePaginator;
use Carbon\Carbon;
use Illuminate\Pagination\CursorPaginator;

class MenuService implements MenuServiceInterface
{
    protected $menuRepository;

    public function __construct(MenuRepositoryInterface $menuRepository)
    {
        $this->menuRepository = $menuRepository;
    }

    public function getAllMenus(): Collection
    {
        return $this->menuRepository->getAll();
    }


    public function getActiveMenus(): Collection
    {
        return $this->menuRepository->getActive();
    }
    public function getPaginatedMenus(int $perPage = 15): LengthAwarePaginator
    {
        return $this->menuRepository->getPaginated($perPage);
    }

    public function getPaginatedMenusWithCursor(int $perPage = 15, ?string $cursor = null): CursorPaginator
    {
        return $this->menuRepository->getPaginatedWithCursor($perPage, $cursor);
    }

    /**
     * Get cursor paginated menus
     */
    // public function getCursorPaginatedMenus(int $limit = 15, ?string $cursor = null): array
    // {
    //     return $this->menuRepository->getCursorPaginated($limit, $cursor);
    // }

    // /**
    //  * Get cursor paginated active menus
    //  */
    // public function getCursorPaginatedActiveMenus(int $limit = 15, ?string $cursor = null): array
    // {
    //     return $this->menuRepository->getCursorPaginatedActive($limit, $cursor);
    // }

    /**
     * Search menus with cursor pagination
     */
    // public function searchMenusWithCursor(string $query, string $locale = 'en', bool $activeOnly = true, int $limit = 15, ?string $cursor = null): array
    // {
    //     return $this->menuRepository->searchWithCursor($query, $locale, $activeOnly, $limit, $cursor);
    // }

    public function findMenu(int $id): ?Menu
    {
        return $this->menuRepository->findById($id);
    }

    public function createMenu(array $data): Menu
    {
        if (!isset($data['is_active'])) {
            $data['is_active'] = true;
        }
        if (!isset($data['color'])) {
            $data['color'] = '#3B82F6';
        }
        if (
            !isset($data['translations']) ||
            !isset($data['translations']['en']) ||
            !isset($data['translations']['ja'])
        ) {
            throw new \InvalidArgumentException('Translations for English and Japanese are required.');
        }
        if (
            empty($data['translations']['en']['name']) ||
            empty($data['translations']['ja']['name'])
        ) {
            throw new \InvalidArgumentException('Menu names for English and Japanese are required.');
        }
        return $this->menuRepository->create($data);
    }

    public function updateMenu(int $id, array $data): ?Menu
    {
        if (isset($data['translations'])) {
            if (
                !isset($data['translations']['en']) ||
                !isset($data['translations']['ja'])
            ) {
                throw new \InvalidArgumentException('Translations for English and Japanese are required.');
            }
            if (
                empty($data['translations']['en']['name']) ||
                empty($data['translations']['ja']['name'])
            ) {
                throw new \InvalidArgumentException('Menu names for English and Japanese are required.');
            }
        }
        return $this->menuRepository->update($id, $data);
    }

    public function deleteMenu(int $id): bool
    {
        return $this->menuRepository->delete($id);
    }

    /**
     * Get paginated active menus
     */
    public function getPaginatedActiveMenus(int $perPage = 15): LengthAwarePaginator
    {
        return $this->menuRepository->getPaginatedActive($perPage);
    }

    /**
     * Bulk update menu status
     */
    public function bulkUpdateMenuStatus(array $ids, bool $status): bool
    {
        if (empty($ids)) {
            throw new \InvalidArgumentException('IDs cannot be empty.');
        }
        return $this->menuRepository->bulkUpdateStatus($ids, $status);
    }

    /**
     * Search menus by query string
     */
    public function searchMenus(string $query, string $locale = 'en', bool $activeOnly = true, int $perPage = 15): LengthAwarePaginator
    {
        return $this->menuRepository->search($query, $locale, $activeOnly, $perPage);
    }

    /**
     * Get available time slots for a menu on a specific date
     */
    public function getAvailableTimeSlots(int $menuId, string $date): array
    {
        $menu = $this->findMenu($menuId);
        if (!$menu) {
            return [];
        }

        // This is a basic implementation - you might want to integrate with your reservation system
        $slots = [];
        $startTime = Carbon::parse($date . ' 09:00:00');
        $endTime = Carbon::parse($date . ' 17:00:00');
        $slotDuration = $menu->required_time;

        while ($startTime->lt($endTime)) {
            $slotEnd = $startTime->copy()->addMinutes($slotDuration);
            if ($slotEnd->lte($endTime)) {
                $slots[] = [
                    'start_time' => $startTime->format('H:i'),
                    'end_time' => $slotEnd->format('H:i'),
                    'start_datetime' => $startTime->format('Y-m-d H:i:s'),
                    'end_datetime' => $slotEnd->format('Y-m-d H:i:s'),
                    'available' => true // You might want to check against reservations
                ];
            }
            $startTime->addMinutes(30); // 30-minute intervals
        }

        return $slots;
    }

    /**
     * Get popular menus based on reservations
     */
    public function getPopularMenus(int $limit = 10, string $period = 'month'): Collection
    {
        return $this->menuRepository->getPopular($limit, $period);
    }

    /**
     * Toggle menu status and return updated menu
     */
    public function toggleMenuStatus(int $id): ?Menu
    {
        $success = $this->menuRepository->toggleActive($id);
        if ($success) {
            return $this->findMenu($id); // Return updated menu
        }
        return null;
    }

    public function reorderMenus(array $orderData): bool
    {
        return $this->menuRepository->reorder($orderData);
    }

    public function getMenusByDisplayOrder(): Collection
    {
        return $this->menuRepository->getByDisplayOrder();
    }

    public function calculateMenuEndTime(int $menuId, string $startTime): ?string
    {
        $menu = $this->findMenu($menuId);
        if (!$menu) {
            return null;
        }
        $startDateTime = Carbon::parse($startTime);
        $endDateTime = $startDateTime->copy()->addMinutes($menu->required_time);
        return $endDateTime->format('Y-m-d H:i:s');
    }

    public function getMenuColorsMapping(): array
    {
        return $this->menuRepository->getAll()
            ->pluck('color', 'id')
            ->toArray();
    }

    public function bulkDeleteMenus(array $ids): bool
    {
        if (empty($ids)) {
            throw new \InvalidArgumentException('IDs cannot be empty.');
        }
        return $this->menuRepository->bulkDelete($ids);
    }
}
