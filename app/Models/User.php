<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Foundation\Auth\User as Authenticatable;
use Illuminate\Notifications\Notifiable;
use App\Enums\UserRole;
use App\Enums\Gender;

class User extends Authenticatable
{
    use HasFactory, Notifiable;

    protected $fillable = [
        'email',
        'password',
        'full_name',
        'full_name_kana',
        'phone_number',
        'company_name',
        'department',
        'company_address',
        'home_address',
        'date_of_birth',
        'gender',
        'role',
    ];

    protected $hidden = [
        'password',
        'remember_token',
    ];

    protected $casts = [
        'email_verified_at' => 'datetime',
        'password' => 'hashed',
        'role' => UserRole::class,
        'gender' => Gender::class,
        'date_of_birth' => 'date',
    ];

    public function reservations()
    {
        return $this->hasMany(Reservation::class);
    }

    public function tireStorage()
    {
        return $this->hasMany(TireStorage::class);
    }

    public function contacts()
    {
        return $this->hasMany(Contact::class);
    }

    public function payments()
    {
        return $this->hasMany(Payment::class);
    }
}

