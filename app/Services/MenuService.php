<?php


namespace App\Services;

use App\Models\Menu;
use App\Repositories\MenuRepositoryInterface;
use Illuminate\Database\Eloquent\Collection;
use Illuminate\Pagination\LengthAwarePaginator;
use Carbon\Carbon;
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

        return $this->menuRepository->create($data);
    }

    public function updateMenu(int $id, array $data): ?Menu
    {
        return $this->menuRepository->update($id, $data);
    }

    public function deleteMenu(int $id): bool
    {
        return $this->menuRepository->delete($id);
    }

    public function toggleMenuStatus(int $id): bool
    {
        return $this->menuRepository->toggleActive($id);
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
}
