<?php

namespace App\Repositories;

use App\Models\Menu;
use App\Repositories\MenuRepositoryInterface;
use Illuminate\Database\Eloquent\Collection;
use Illuminate\Pagination\LengthAwarePaginator;

class MenuRepository implements MenuRepositoryInterface
{
    protected $model;

    public function __construct(Menu $model)
    {
        $this->model = $model;
    }

    public function getAll(): Collection
    {
        return $this->model->orderBy('display_order')->get();
    }

    public function getActive(): Collection
    {
        return $this->model->where('is_active', true)
            ->orderBy('display_order')
            ->get();
    }

    public function getPaginated(int $perPage = 15): LengthAwarePaginator
    {
        return $this->model->orderBy('display_order')
            ->paginate($perPage);
    }

    public function findById(int $id): ?Menu
    {
        return $this->model->find($id);
    }

    public function create(array $data): Menu
    {
        if (!isset($data['display_order'])) {
            $data['display_order'] = $this->model->max('display_order') + 1;
        }
        return $this->model->create($data);
    }

    public function update(int $id, array $data): ?Menu
    {
        $menu = $this->findById($id);
        if ($menu) {
            $menu->update($data);
            return $menu;
        }
        return null;
    }

    public function delete(int $id): bool
    {
        $menu = $this->findById($id);
        if ($menu) {
            return $menu->delete();
        }
        return false;
    }

    public function toggleActive(int $id): bool
    {
        $menu = $this->findById($id);
        if ($menu) {
            $menu->is_active = !$menu->is_active;
            return $menu->save();
        }
        return false;
    }

    public function reorder(array $orderData): bool
    {
        foreach ($orderData as $order) {
            $this->model->where('id', $order['id'])
                ->update(['display_order' => $order['display_order']]);
        }
        return true;
    }

    public function getByDisplayOrder(): Collection
    {
        return $this->model->where('is_active', true)
            ->orderBy('display_order')
            ->get();
    }
    public function bulkDelete(array $ids): bool
    {
        try {
            $count = $this->model->whereIn('id', $ids)->delete();
            return $count > 0;
        } catch (\Exception $e) {
            return false;
        }
    }
}
