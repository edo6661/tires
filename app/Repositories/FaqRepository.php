<?php

namespace App\Repositories;

use App\Models\Faq;
use App\Repositories\FaqRepositoryInterface;
use Illuminate\Database\Eloquent\Collection;
use Illuminate\Pagination\LengthAwarePaginator;

class FaqRepository implements FaqRepositoryInterface
{
    protected $model;

    public function __construct(Faq $model)
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

    public function findById(int $id): ?Faq
    {
        return $this->model->find($id);
    }

    public function create(array $data): Faq
    {
        if (!isset($data['display_order'])) {
            $data['display_order'] = $this->model->max('display_order') + 1;
        }
        return $this->model->create($data);
    }

    public function update(int $id, array $data): ?Faq
    {
        $faq = $this->findById($id);
        if ($faq) {
            $faq->update($data);
            return $faq;
        }
        return null;
    }

    public function delete(int $id): bool
    {
        $faq = $this->findById($id);
        if ($faq) {
            return $faq->delete();
        }
        return false;
    }

    public function toggleActive(int $id): bool
    {
        $faq = $this->findById($id);
        if ($faq) {
            $faq->is_active = !$faq->is_active;
            return $faq->save();
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
}