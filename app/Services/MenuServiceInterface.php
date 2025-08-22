<?php
namespace App\Services;

use App\Models\Menu;
use Illuminate\Contracts\Pagination\CursorPaginator;
use Illuminate\Contracts\Pagination\Paginator;
use Illuminate\Database\Eloquent\Collection;
use Illuminate\Pagination\Cursor;
use Illuminate\Pagination\LengthAwarePaginator;

interface MenuServiceInterface
{
    public function getAllMenus(): Collection;
    public function getActiveMenus(): Collection;
    public function getPaginatedMenus(int $perPage = 15, ?string $cursor = null): CursorPaginator;
    public function getPaginatedActiveMenus(int $perPage = 15): LengthAwarePaginator;
    public function findMenu(int $id): ?Menu;
    public function createMenu(array $data): Menu;
    public function updateMenu(int $id, array $data): ?Menu;
    public function deleteMenu(int $id): bool;
    public function toggleMenuStatus(int $id): ?Menu;
    public function reorderMenus(array $orderData): bool;
    public function getMenusByDisplayOrder(): Collection;
    public function calculateMenuEndTime(int $menuId, string $startTime): ?string;
    public function getMenuColorsMapping(): array;
    public function bulkDeleteMenus(array $ids): bool;
    public function bulkUpdateMenuStatus(array $ids, bool $status): bool;
    public function searchMenus(string $query, string $locale = 'en', bool $activeOnly = true, int $perPage = 15): LengthAwarePaginator;
    public function getAvailableTimeSlots(int $menuId, string $date): array;
    public function getPopularMenus(int $limit = 10, string $period = 'month'): Collection;

    // Cursor pagination methods - DIUBAH
    // public function getCursorPaginatedMenus(int $limit = 15, ?string $cursor = null): array;
    // public function getCursorPaginatedActiveMenus(int $limit = 15, ?string $cursor = null): array;
    // public function searchMenusWithCursor(string $query, string $locale = 'en', bool $activeOnly = true, int $limit = 15, ?string $cursor = null): array;
}
