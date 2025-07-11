<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use App\Enums\TireStorageStatus;

class TireStorage extends Model
{
    use HasFactory;

    protected $fillable = [
        'user_id',
        'tire_brand',
        'tire_size',
        'storage_start_date',
        'planned_end_date',
        'storage_fee',
        'status',
        'notes',
    ];

    protected $casts = [
        'storage_start_date' => 'date',
        'planned_end_date' => 'date',
        'storage_fee' => 'decimal:2',
        'status' => TireStorageStatus::class,
    ];

    public function user()
    {
        return $this->belongsTo(User::class);
    }
}