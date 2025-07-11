<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use App\Enums\ContactStatus;

class Contact extends Model
{
    use HasFactory;

    protected $fillable = [
        'user_id',
        'full_name',
        'email',
        'phone_number',
        'subject',
        'message',
        'status',
        'admin_reply',
        'replied_at',
    ];

    protected $casts = [
        'status' => ContactStatus::class,
        'replied_at' => 'datetime',
    ];

    public function user()
    {
        return $this->belongsTo(User::class);
    }
}