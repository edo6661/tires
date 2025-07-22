<?php
namespace App\Services;

use App\Models\Menu;
use Illuminate\Database\Eloquent\Collection;
use Illuminate\Pagination\LengthAwarePaginator;

interface MenuServiceInterface
{
    public function getAllMenus(): Collection;
    public function getActiveMenus(): Collection;
    public function getPaginatedMenus(int $perPage = 15): LengthAwarePaginator;
    public function findMenu(int $id): ?Menu;
    public function createMenu(array $data): Menu;
    public function updateMenu(int $id, array $data): ?Menu;
    public function deleteMenu(int $id): bool;
    public function toggleMenuStatus(int $id): bool;
    public function reorderMenus(array $orderData): bool;
    public function getMenusByDisplayOrder(): Collection;
    public function calculateMenuEndTime(int $menuId, string $startTime): ?string;
    public function getMenuColorsMapping(): array;
    public function bulkDeleteMenus(array $ids): bool;
}
