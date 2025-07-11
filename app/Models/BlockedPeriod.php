<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class BlockedPeriod extends Model
{
    use HasFactory;

    protected $fillable = [
        'menu_id',
        'start_datetime',
        'end_datetime',
        'reason',
        'all_menus',
    ];

    protected $casts = [
        'start_datetime' => 'datetime',
        'end_datetime' => 'datetime',
        'all_menus' => 'boolean',
    ];

    public function menu()
    {
        return $this->belongsTo(Menu::class);
    }
}