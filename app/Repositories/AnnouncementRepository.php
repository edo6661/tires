<?php

namespace App\Repositories;

use App\Models\Announcement;
use App\Repositories\AnnouncementRepositoryInterface;
use Illuminate\Database\Eloquent\Collection;
use Illuminate\Pagination\LengthAwarePaginator;

class AnnouncementRepository implements AnnouncementRepositoryInterface
{
    protected $model;

    public function __construct(Announcement $model)
    {
        $this->model = $model;
    }

    public function getAll(): Collection
    {
        return $this->model->orderBy('published_at', 'desc')->get();
    }

    public function getActive(): Collection
    {
        return $this->model->where('is_active', true)
            ->orderBy('published_at', 'desc')
            ->get();
    }

    public function getPaginated(int $perPage = 15): LengthAwarePaginator
    {
        return $this->model->orderBy('published_at', 'desc')
            ->paginate($perPage);
    }

    public function findById(int $id): ?Announcement
    {
        return $this->model->find($id);
    }

    public function create(array $data): Announcement
    {
        return $this->model->create($data);
    }

    public function update(int $id, array $data): ?Announcement
    {
        $announcement = $this->findById($id);
        if ($announcement) {
            $announcement->update($data);
            return $announcement;
        }
        return null;
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
}
