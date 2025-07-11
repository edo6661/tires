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
}
