<?php

namespace App\Repositories;

use App\Models\Announcement;
use Illuminate\Database\Eloquent\Collection;
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
        return $this->model->withTranslations()
            ->orderBy('published_at', 'desc')
            ->get();
    }

    public function getActive(): Collection
    {
        return $this->model->active()
            ->withTranslations()
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
        return $this->model->withTranslations()
            ->orderBy('published_at', 'desc')
            ->paginate($perPage);
    }

    public function findById(int $id): ?Announcement
    {
        return $this->model->withTranslations()
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
}