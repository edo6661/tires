<?php

namespace App\Repositories;

use App\Models\Menu;
use Illuminate\Contracts\Pagination\Paginator;
use Illuminate\Database\Eloquent\Collection;
use Illuminate\Pagination\CursorPaginator;
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

    public function getPaginated(int $perPage = 15, ?string $cursor = null): CursorPaginator
    {
        return $this->model->withTranslations()
            ->ordered()
            // ->cursorPaginate($perPage);
            ->cursorPaginate($perPage, ['*'], 'cursor', $cursor);
    }

    /**
     * Get cursor paginated menus
     */
    public function getCursorPaginated(int $limit = 15, ?string $cursor = null): array
    {
        $query = $this->model->withTranslations()->orderBy('created_at', 'desc')->orderBy('id', 'desc');

        if ($cursor) {
            $cursorData = $this->parseCursor($cursor);
            if ($cursorData) {
                $query->where(function ($q) use ($cursorData) {
                    $q->where('created_at', '<', $cursorData['timestamp']);

                    // Jika ada ID, gunakan untuk tie-breaking
                    if ($cursorData['id']) {
                        $q->orWhere(function ($subQ) use ($cursorData) {
                            $subQ->where('created_at', '=', $cursorData['timestamp'])
                                ->where('id', '<', $cursorData['id']);
                        });
                    }
                });
            }
        }

        $items = $query->limit($limit + 1)->get();
        $hasMore = $items->count() > $limit;

        if ($hasMore) {
            $items = $items->slice(0, $limit);
        }

        $nextCursor = null;
        if ($hasMore && $items->isNotEmpty()) {
            $lastItem = $items->last();
            $nextCursor = $this->createCursor($lastItem->created_at->toISOString(), $lastItem->id);
        }

        return [
            'data' => $items->values(),
            'has_more' => $hasMore,
            'next_cursor' => $nextCursor,
        ];
    }

    /**
     * Get cursor paginated active menus
     */
    public function getCursorPaginatedActive(int $limit = 15, ?string $cursor = null): array
    {
        $query = $this->model->active()->withTranslations()->orderBy('created_at', 'desc')->orderBy('id', 'desc');

        if ($cursor) {
            $cursorData = $this->parseCursor($cursor);
            if ($cursorData) {
                $query->where(function ($q) use ($cursorData) {
                    $q->where('created_at', '<', $cursorData['timestamp']);

                    if ($cursorData['id']) {
                        $q->orWhere(function ($subQ) use ($cursorData) {
                            $subQ->where('created_at', '=', $cursorData['timestamp'])
                                ->where('id', '<', $cursorData['id']);
                        });
                    }
                });
            }
        }

        $items = $query->limit($limit + 1)->get();
        $hasMore = $items->count() > $limit;

        if ($hasMore) {
            $items = $items->slice(0, $limit);
        }

        $nextCursor = null;
        if ($hasMore && $items->isNotEmpty()) {
            $lastItem = $items->last();
            $nextCursor = $this->createCursor($lastItem->created_at->toISOString(), $lastItem->id);
        }

        return [
            'data' => $items->values(),
            'has_more' => $hasMore,
            'next_cursor' => $nextCursor,
        ];
    }

    /**
     * Search menus with cursor pagination
     */
    public function searchWithCursor(string $query, string $locale = 'en', bool $activeOnly = true, int $limit = 15, ?string $cursor = null): array
    {
        $queryBuilder = $this->model->whereTranslation('name', 'ILIKE', "%{$query}%", $locale)
            ->orWhereTranslation('description', 'ILIKE', "%{$query}%", $locale)
            ->withTranslations($locale)
            ->orderBy('created_at', 'desc')
            ->orderBy('id', 'desc');

        if ($activeOnly) {
            $queryBuilder->active();
        }

        if ($cursor) {
            $cursorData = $this->parseCursor($cursor);
            if ($cursorData) {
                $queryBuilder->where(function ($q) use ($cursorData) {
                    $q->where('created_at', '<', $cursorData['timestamp']);

                    if ($cursorData['id']) {
                        $q->orWhere(function ($subQ) use ($cursorData) {
                            $subQ->where('created_at', '=', $cursorData['timestamp'])
                                ->where('id', '<', $cursorData['id']);
                        });
                    }
                });
            }
        }

        $items = $queryBuilder->limit($limit + 1)->get();
        $hasMore = $items->count() > $limit;

        if ($hasMore) {
            $items = $items->slice(0, $limit);
        }

        $nextCursor = null;
        if ($hasMore && $items->isNotEmpty()) {
            $lastItem = $items->last();
            $nextCursor = $this->createCursor($lastItem->created_at->toISOString(), $lastItem->id);
        }

        return [
            'data' => $items->values(),
            'has_more' => $hasMore,
            'next_cursor' => $nextCursor,
        ];
    }

    /**
     * Create cursor from timestamp and ID
     */
    private function createCursor(string $timestamp, int $id): string
    {
        $cursorData = "{$timestamp}:{$id}";
        return base64_encode($cursorData);
    }

    /**
     * Parse cursor to get timestamp and ID
     */
    private function parseCursor(?string $cursor): ?array
    {
        if (!$cursor) {
            return null;
        }

        try {
            $decoded = base64_decode($cursor);

            if (strpos($decoded, ':') !== false) {
                [$timestamp, $id] = explode(':', $decoded, 2);
                return [
                    'timestamp' => $timestamp,
                    'id' => (int) $id
                ];
            }

            return [
                'timestamp' => $decoded,
                'id' => null
            ];
        } catch (\Exception $e) {
            return null;
        }
    }

    /**
     * Get paginated active menus (traditional pagination)
     */
    public function getPaginatedActive(int $perPage = 15): LengthAwarePaginator
    {
        return $this->model->active()
            ->withTranslations()
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

    public function searchByName(string $search, string $locale = null): Collection
    {
        $locale = $locale ?: App::getLocale();

        return $this->model->whereTranslation('name', 'ILIKE', "%{$search}%", $locale)
            ->withTranslations($locale)
            ->ordered()
            ->get();
    }

    /**
     * Bulk update status for multiple menus
     */
    public function bulkUpdateStatus(array $ids, bool $status): bool
    {
        try {
            $count = $this->model->whereIn('id', $ids)
                ->update(['is_active' => $status]);
            return $count > 0;
        } catch (\Exception $e) {
            return false;
        }
    }

    /**
     * Search menus (traditional pagination)
     */
    public function search(string $query, string $locale = 'en', bool $activeOnly = true, int $perPage = 15): LengthAwarePaginator
    {
        $queryBuilder = $this->model->whereTranslation('name', 'ILIKE', "%{$query}%", $locale)
            ->orWhereTranslation('description', 'ILIKE', "%{$query}%", $locale)
            ->withTranslations($locale);

        if ($activeOnly) {
            $queryBuilder->active();
        }

        return $queryBuilder->ordered()->paginate($perPage);
    }

    /**
     * Get popular menus based on reservations
     */
    public function getPopular(int $limit = 10, string $period = 'month'): Collection
    {
        $query = $this->model->withTranslations()
            ->withCount(['reservations' => function ($query) use ($period) {
                $startDate = match ($period) {
                    'week' => now()->subWeek(),
                    'month' => now()->subMonth(),
                    'year' => now()->subYear(),
                    default => null
                };

                if ($startDate) {
                    $query->where('created_at', '>=', $startDate);
                }
            }])
            ->orderByDesc('reservations_count');

        return $query->limit($limit)->get();
    }

    /**
     * Count total menus
     */
    public function count(): int
    {
        return $this->model->count();
    }

    /**
     * Count active menus
     */
    public function countActive(): int
    {
        return $this->model->active()->count();
    }

    /**
     * Find menu by slug
     */
    public function findBySlug(string $slug): ?Menu
    {
        return $this->model->withTranslations()
            ->where('slug', $slug)
            ->first();
    }

    /**
     * Helper method to encode cursor
     */
    private function encodeCursor(array $data): string
    {
        return base64_encode(json_encode($data));
    }

    /**
     * Helper method to decode cursor
     */
    private function decodeCursor(?string $cursor): ?array
    {
        if (!$cursor) {
            return null;
        }

        try {
            $decoded = base64_decode($cursor);
            return json_decode($decoded, true);
        } catch (\Exception $e) {
            return null;
        }
    }
}
