<?php

namespace App\Http\Resources;

use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\JsonResource;

class UserResource extends JsonResource
{
    public function toArray(Request $request): array
    {
        return [
            'id' => $this->id,
            'email' => $this->email,
            'full_name' => $this->full_name,
            'full_name_kana' => $this->full_name_kana,
            'phone_number' => $this->phone_number,
            'company_name' => $this->company_name,
            'department' => $this->department,
            'company_address' => $this->company_address,
            'home_address' => $this->home_address,
            'date_of_birth' => $this->date_of_birth?->toJSONString(),
            'gender' => $this->gender,
            'role' => $this->role ?? 'customer',
            'is_admin' => $this->isAdmin(),
            'is_customer' => $this->isCustomer(),
            'email_verified_at' => $this->email_verified_at?->toISOString(),
            'created_at' => $this->created_at?->toISOString(),
            'updated_at' => $this->updated_at?->toISOString(),

            // Conditional data
            'reservations_count' => $this->whenCounted('reservations'),
            'tire_storage_count' => $this->whenCounted('tireStorage'),
        ];
    }
}
