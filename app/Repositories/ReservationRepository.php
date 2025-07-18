<?php
namespace App\Repositories;
use App\Models\Menu;
use App\Models\Reservation;
use App\Repositories\ReservationRepositoryInterface;
use Illuminate\Database\Eloquent\Collection;
use Illuminate\Pagination\LengthAwarePaginator;
use Carbon\Carbon;
class ReservationRepository implements ReservationRepositoryInterface
{
    protected $model;
    public function __construct(Reservation $model, protected BlockedPeriodRepositoryInterface $blockedPeriodRepo)
    {
        $this->model = $model;
    }
    public function getAll(): Collection
    {
        return $this->model->with(['user', 'menu'])
            ->orderBy('reservation_datetime', 'desc')
            ->get();
    }
    public function getPaginated(int $perPage = 15): LengthAwarePaginator
    {
        return $this->model->with(['user', 'menu'])
            ->orderBy('reservation_datetime', 'desc')
            ->paginate($perPage);
    }
    public function findById(int $id): ?Reservation
    {
        return $this->model->with(['user', 'menu', 'questionnaire', 'payments'])->find($id);
    }
    public function create(array $data): Reservation
    {
        return $this->model->create($data);
    }
    public function update(int $id, array $data): ?Reservation
    {
        $reservation = $this->findById($id);
        if ($reservation) {
            $reservation->update($data);
            return $reservation;
        }
        return null;
    }
    public function delete(int $id): bool
    {
        $reservation = $this->findById($id);
        if ($reservation) {
            return $reservation->delete();
        }
        return false;
    }
    public function getByUserId(int $userId): Collection
    {
        return $this->model->with('menu')
            ->where('user_id', $userId)
            ->orderBy('reservation_datetime', 'desc')
            ->get();
    }
    public function getByMenuId(int $menuId): Collection
    {
        return $this->model->with('user')
            ->where('menu_id', $menuId)
            ->orderBy('reservation_datetime', 'desc')
            ->get();
    }
    public function getByStatus(string $status): Collection
    {
        return $this->model->with(['user', 'menu'])
            ->where('status', $status)
            ->orderBy('reservation_datetime', 'desc')
            ->get();
    }
    public function getByDateRange(string $startDate, string $endDate): Collection
    {
        return $this->model->with(['user', 'menu'])
            ->whereBetween('reservation_datetime', [
                Carbon::parse($startDate)->startOfDay(), 
                Carbon::parse($endDate)->endOfDay()      
            ])
            ->orderBy('reservation_datetime')
            ->get();
    }
    public function getUpcoming(): Collection
    {
        return $this->model->with(['user', 'menu'])
            ->where('reservation_datetime', '>=', Carbon::now())
            ->whereIn('status', ['pending', 'confirmed'])
            ->orderBy('reservation_datetime')
            ->get();
    }
    public function getCompleted(): Collection
    {
        return $this->getByStatus('completed');
    }
    public function getCancelled(): Collection
    {
        return $this->getByStatus('cancelled');
    }
    public function getTodayReservations(): Collection
    {
        return $this->model->with(['user', 'menu'])
            ->whereDate('reservation_datetime', Carbon::today())
            ->orderBy('reservation_datetime')
            ->get();
    }
    public function checkAvailability(int $menuId, string $datetime, ?int $excludeReservationId = null): bool
    {
        $menu = Menu::find($menuId);
        if (!$menu) {
            return false;
        }
        $reservationTime = Carbon::parse($datetime);
        $endTime = $reservationTime->copy()->addMinutes($menu->required_time);
        $existingReservations = $this->model
            ->where('menu_id', $menuId)
            ->whereIn('status', ['pending', 'confirmed'])
            ->when($excludeReservationId, function ($query) use ($excludeReservationId) {
                return $query->where('id', '!=', $excludeReservationId);
            })
            ->get();
        foreach ($existingReservations as $reservation) {
            $existingStart = Carbon::parse($reservation->reservation_datetime);
            $existingEnd = $existingStart->copy()->addMinutes($menu->required_time);
            if ($reservationTime->lt($existingEnd) && $endTime->gt($existingStart)) {
                return false;
            }
        }
        return !$this->blockedPeriodRepo->checkConflict(
            $menuId,
            $reservationTime->format('Y-m-d H:i:s'),
            $endTime->format('Y-m-d H:i:s')
        );
    }
    public function bulkUpdateStatus(array $ids, string $status): bool
    {
        return $this->model->whereIn('id', $ids)->update(['status' => $status]) > 0;
    }
}