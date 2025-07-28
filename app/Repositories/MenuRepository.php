<?php

namespace App\Repositories;

use App\Models\Menu;
use Illuminate\Database\Eloquent\Collection;
use Illuminate\Pagination\LengthAwarePaginator;
use Illuminate\Support\Facades\App;

class MenuRepository implements MenuRepositoryInterface
{
    protected $model;

    public function __construct(Menu $model)
    {
        $this->model = $model;
    }

    public function getAll(): Collection
    {
        return $this->model->withTranslations()
            ->ordered()
            ->get();
    }

    public function getActive(): Collection
    {
        return $this->model->active()
            ->withTranslations()
            ->ordered()
            ->get();
    }

    public function getPaginated(int $perPage = 15): LengthAwarePaginator
    {
        return $this->model->withTranslations()
            ->ordered()
            ->paginate($perPage);
    }

    public function findById(int $id): ?Menu
    {
        return $this->model->withTranslations()
            ->find($id);
    }

    public function findByIdWithLocale(int $id, string $locale = null): ?Menu
    {
        return $this->model->withTranslations($locale)
            ->find($id);
    }

    public function create(array $data): Menu
    {
        if (!isset($data['display_order'])) {
            $data['display_order'] = $this->model->max('display_order') + 1;
        }

        $translations = $data['translations'] ?? [];
        unset($data['translations']);

        $menu = $this->model->create($data);

        // Set translations
        foreach ($translations as $locale => $translationData) {
            $menu->setTranslation($locale, $translationData);
        }

        return $menu->fresh(['translations']);
    }

    public function update(int $id, array $data): ?Menu
    {
        $menu = $this->findById($id);
        if (!$menu) {
            return null;
        }

        $translations = $data['translations'] ?? [];
        unset($data['translations']);

        $menu->update($data);

        // Update translations
        foreach ($translations as $locale => $translationData) {
            $menu->setTranslation($locale, $translationData);
        }

        return $menu->fresh(['translations']);
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
        return $this->model->active()
            ->withTranslations()
            ->ordered()
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

    /**
     * Search menus by translated name
     */
    public function searchByName(string $search, string $locale = null): Collection
    {
        $locale = $locale ?: App::getLocale();
        
        return $this->model->whereTranslation('name', 'ILIKE', "%{$search}%", $locale)
            ->withTranslations($locale)
            ->ordered()
            ->get();
    }
}