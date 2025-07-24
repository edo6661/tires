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
    public function getFullName(): string
    {
        return $this->full_name ?? $this->user?->full_name ?? 'N/A';
    }
    public function getEmail(): string
    {
        return $this->email ?? $this->user?->email ?? 'N/A'; 
    }
    public function getPhoneNumber(): string
    {
        return $this->phone_number ?? $this->user?->phone_number ?? 'N/A';
    }
    public function getStatusLabel(): string
    {
        return match ($this->status) {
            ContactStatus::PENDING->value => 'Pending',
            ContactStatus::REPLIED->value => 'Replied',
            default => 'Unknown',
        };
    }
}