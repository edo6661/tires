<?php
namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Menu extends Model
{
    use HasFactory;

    protected $fillable = [
        'name',
        'required_time',
        'price',
        'description',
        'photo_path',
        'display_order',
        'is_active',
    ];

    protected $casts = [
        'price' => 'decimal:2',
        'is_active' => 'boolean',
    ];

    public function reservations()
    {
        return $this->hasMany(Reservation::class);
    }

    public function blockedPeriods()
    {
        return $this->hasMany(BlockedPeriod::class);
    }
}

