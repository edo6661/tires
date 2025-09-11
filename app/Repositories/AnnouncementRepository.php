<?php

namespace App\Repositories;

use App\Models\Announcement;
use Illuminate\Database\Eloquent\Collection;
use Illuminate\Pagination\CursorPaginator;
use Illuminate\Pagination\LengthAwarePaginator;
use Illuminate\Support\Facades\App;

class AnnouncementRepository implements AnnouncementRepositoryInterface
{
    protected $model;

    public function __construct(Announcement $model)
    {
        $this->model = $model;
    }

    public function getAll(): Collection
    {
        return $this->model->with('translations')
            ->orderBy('published_at', 'desc')
            ->get();
    }

    public function getActive(): Collection
    {
        return $this->model->active()
            ->with('translations')
            ->orderBy('published_at', 'desc')
            ->get();
    }

    public function getPublished(): Collection
    {
        return $this->model->active()
            ->published()
            ->withTranslations()
            ->orderBy('published_at', 'desc')
            ->get();
    }

    public function getPaginated(int $perPage = 15): LengthAwarePaginator
    {
        return $this->model->with('translations')
            ->orderBy('published_at', 'desc')
            ->paginate($perPage);
    }

    // getPaginatedWithCursor
    public function getPaginatedWithCursor(int $perPage = 15, ?string $cursor = null, array $filters = []): CursorPaginator
    {
        $query = $this->model->with('translations');

        // Apply filters
        if (!empty($filters['status'])) {
            if ($filters['status'] === 'active') {
                $query->where('is_active', true);
            } elseif ($filters['status'] === 'inactive') {
                $query->where('is_active', false);
            }
        }

        if (!empty($filters['published_at'])) {
            $query->whereDate('published_at', '<=', $filters['end_date']);
        }

        if (!empty($filters['search'])) {
            $search = $filters['search'];
            $query->where(function ($q) use ($search) {
                $q->whereHas('translations', function ($subQ) use ($search) {
                    $subQ->where('title', 'ILIKE', "%{$search}%")
                        ->orWhere('content', 'ILIKE', "%{$search}%");
                });
            });
        }

        return $query->orderBy('published_at', 'desc')
            ->cursorpaginate($perPage, ['*'], 'cursor', $cursor);
    }

    public function findById(int $id): ?Announcement
    {
        return $this->model->with('translations')
            ->find($id);
    }

    public function findByIdWithLocale(int $id, ?string $locale = null): ?Announcement
    {
        return $this->model->withTranslations($locale)
            ->find($id);
    }

    public function create(array $data): Announcement
    {
        if (!isset($data['published_at'])) {
            $data['published_at'] = now();
        }

        $translations = $data['translations'] ?? [];
        unset($data['translations']);

        $announcement = $this->model->create($data);

        // Simpan translations
        foreach ($translations as $locale => $translationData) {
            $announcement->setTranslation($locale, $translationData);
        }

        return $announcement->fresh(['translations']);
    }

    public function update(int $id, array $data): ?Announcement
    {
        $announcement = $this->findById($id);
        if (!$announcement) {
            return null;
        }

        $translations = $data['translations'] ?? [];
        unset($data['translations']);

        $announcement->update($data);

        // Update translations
        foreach ($translations as $locale => $translationData) {
            $announcement->setTranslation($locale, $translationData);
        }

        return $announcement->fresh(['translations']);
    }

    public function delete(int $id): bool
    {
        $announcement = $this->findById($id);
        if ($announcement) {
            return $announcement->delete();
        }
        return false;
    }

    public function toggleActive(int $id): bool
    {
        $announcement = $this->findById($id);
        if ($announcement) {
            return $this->model->where('id', $id)->update([
                'is_active' => !$announcement->is_active
            ]);
        }
        return false;
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

    public function searchByTitle(string $search, ?string $locale = null): Collection
    {
        $locale = $locale ?: App::getLocale();

        return $this->model->whereTranslation('title', 'ILIKE', "%{$search}%", $locale)
            ->withTranslations($locale)
            ->orderBy('published_at', 'desc')
            ->get();
    }

    public function count(): int
    {
        return $this->model->count();
    }

    public function countByStatus(string $status): int
    {
        if ($status === 'active') {
            return $this->model->where('is_active', true)->count();
        } elseif ($status === 'inactive') {
            return $this->model->where('is_active', false)->count();
        }
        return 0;
    }

    public function countTodayAnnouncements(): int
    {
        return $this->model->whereDate('created_at', today())->count();
    }

    public function getFiltered(array $filters, int $perPage = null): Collection
    {
        $query = $this->model->with('translations');

        // Apply filters
        if (!empty($filters['status'])) {
            if ($filters['status'] === 'active') {
                $query->where('is_active', true);
            } elseif ($filters['status'] === 'inactive') {
                $query->where('is_active', false);
            }
        }

        if (!empty($filters['start_date'])) {
            $query->whereDate('published_at', '>=', $filters['start_date']);
        }

        if (!empty($filters['end_date'])) {
            $query->whereDate('published_at', '<=', $filters['end_date']);
        }

        if (!empty($filters['search'])) {
            $search = $filters['search'];
            $query->where(function ($q) use ($search) {
                $q->whereHas('translations', function ($subQ) use ($search) {
                    $subQ->where('title', 'ILIKE', "%{$search}%")
                        ->orWhere('content', 'ILIKE', "%{$search}%");
                });
            });
        }

        $query->orderBy('published_at', 'desc');

        if ($perPage) {
            return $query->take($perPage)->get();
        }

        return $query->get();
    }

    public function search(string $query, int $perPage = 15): Collection
    {
        return $this->model->where(function ($q) use ($query) {
            $q->whereHas('translations', function ($subQ) use ($query) {
                $subQ->where('title', 'ILIKE', "%{$query}%")
                    ->orWhere('content', 'ILIKE', "%{$query}%");
            });
        })
            ->with('translations')
            ->orderBy('published_at', 'desc')
            ->take($perPage)
            ->get();
    }
}
