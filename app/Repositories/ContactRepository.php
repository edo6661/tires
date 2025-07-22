<?php

namespace App\Repositories;

use App\Enums\ContactStatus;
use App\Models\Contact;
use App\Repositories\ContactRepositoryInterface;
use Illuminate\Database\Eloquent\Collection;
use Illuminate\Pagination\LengthAwarePaginator;
use Carbon\Carbon;

class ContactRepository implements ContactRepositoryInterface
{
    protected $model;

    public function __construct(Contact $model)
    {
        $this->model = $model;
    }

    public function getAll(): Collection
    {
        return $this->model->with('user')->orderBy('created_at', 'desc')->get();
    }

    public function getPaginated(int $perPage = 15): LengthAwarePaginator
    {
        return $this->model->with('user')
            ->orderBy('created_at', 'desc')
            ->paginate($perPage);
    }
      public function getContactStats(): array
    {
        return [
            'total' => $this->model->count(),
            'pending' => $this->model->where('status', 'pending')->count(),
            'replied' => $this->model->where('status', 'replied')->count(),
            'today' => $this->model->whereDate('created_at', today())->count(),
        ];
    }

    public function findById(int $id): ?Contact
    {
        return $this->model->with('user')->find($id);
    }

    public function create(array $data): Contact
    {
        return $this->model->create($data);
    }

    public function update(int $id, array $data): ?Contact
    {
        $contact = $this->findById($id);
        if ($contact) {
            $contact->update($data);
            return $contact;
        }
        return null;
    }

    public function delete(int $id): bool
    {
        $contact = $this->findById($id);
        if ($contact) {
            return $contact->delete();
        }
        return false;
    }

    public function getByUserId(int $userId): Collection
    {
        return $this->model->where('user_id', $userId)
            ->orderBy('created_at', 'desc')
            ->get();
    }

    public function getByStatus(string $status): Collection
    {
        return $this->model->with('user')
            ->where('status', $status)
            ->orderBy('created_at', 'desc')
            ->get();
    }

    public function markAsReplied(int $id, string $reply): bool
    {
        $contact = $this->findById($id);
        if ($contact) {
            $contact->admin_reply = $reply;
            $contact->status = ContactStatus::REPLIED;
            $contact->replied_at = Carbon::now();
            return $contact->save();
        }
        return false;
    }

    public function getPending(): Collection
    {
        return $this->getByStatus('pending');
    }
}