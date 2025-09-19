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
            'pending' => $this->model->where('status', ContactStatus::PENDING->value)->count(),
            'replied' => $this->model->where('status', ContactStatus::REPLIED->value)->count(),
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
            return $contact->fresh(['user']);
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
        return $this->getByStatus(ContactStatus::PENDING->value);
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
    public function getFiltered(array $filters): LengthAwarePaginator
    {
        $query = $this->model->with('user');
        if (!empty($filters['status'])) {
            $query->where('status', $filters['status']);
        }
        if (!empty($filters['start_date'])) {
            $query->whereDate('created_at', '>=', $filters['start_date']);
        }
        if (!empty($filters['end_date'])) {
            $query->whereDate('created_at', '<=', $filters['end_date']);
        }
        if (!empty($filters['search'])) {
            $search = $filters['search'];
            $query->where(function ($q) use ($search) {
                $q->where('full_name', 'like', "%{$search}%")
                  ->orWhere('email', 'like', "%{$search}%")
                  ->orWhere('subject', 'like', "%{$search}%")
                  ->orWhere('message', 'like', "%{$search}%");
            });
        }
        return $query->orderBy('created_at', 'desc')
                     ->paginate($filters['per_page'] ?? 15);
    }

    public function getPaginatedWithCursor(int $perPage = 15, ?string $cursor = null, array $filters = []): \Illuminate\Contracts\Pagination\CursorPaginator
    {
        $query = $this->model->with('user');

        if (!empty($filters['status'])) {
            $query->where('status', $filters['status']);
        }

        if (!empty($filters['start_date'])) {
            $query->whereDate('created_at', '>=', $filters['start_date']);
        }

        if (!empty($filters['end_date'])) {
            $query->whereDate('created_at', '<=', $filters['end_date']);
        }

        if (!empty($filters['search'])) {
            $search = $filters['search'];
            $query->where(function ($q) use ($search) {
                $q->where('full_name', 'like', "%{$search}%")
                  ->orWhere('email', 'like', "%{$search}%")
                  ->orWhere('subject', 'like', "%{$search}%")
                  ->orWhere('message', 'like', "%{$search}%");
            });
        }

        return $query->orderBy('created_at', 'desc')
                     ->orderBy('id', 'desc')
                     ->cursorPaginate($perPage, ['*'], 'cursor', $cursor);
    }
}
