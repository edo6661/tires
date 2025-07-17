<?php
namespace App\Models;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use App\Enums\ReservationStatus;
class Reservation extends Model
{
    use HasFactory;
    protected $fillable = [
        'reservation_number',
        'user_id',
        'menu_id',
        'reservation_datetime',
        'number_of_people',
        'amount',
        'status',
        'notes',
        'full_name',
        'full_name_kana',
        'email',
        'phone_number',
    ];
    protected $casts = [
        'reservation_datetime' => 'datetime',
        'amount' => 'decimal:2',
        'status' => ReservationStatus::class,
    ];
    public function user()
    {
        return $this->belongsTo(User::class);
    }
    public function menu()
    {
        return $this->belongsTo(Menu::class);
    }
    public function questionnaire()
    {
        return $this->hasOne(Questionnaire::class);
    }
    public function payments()
    {
        return $this->hasMany(Payment::class);
    }
    public function getFullName(): string
    {
        return $this->full_name ?? $this->user?->full_name ?? 'N/A';
    }
    public function getFullNameKana(): string
    {
        return $this->full_name_kana ?? $this->user?->full_name_kana ?? 'N/A';
    }
    public function getEmail(): string
    {
        return $this->email ?? $this->user?->email ?? 'N/A';
    }
    public function getPhoneNumber(): string
    {
        return $this->phone_number ?? $this->user?->phone_number ?? 'N/A';
    }
    public function isGuestReservation(): bool
    {
        return is_null($this->user_id);
    }
    public function isUserReservation(): bool
    {
        return !is_null($this->user_id);
    }
}
