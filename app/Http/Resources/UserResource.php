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
            'date_of_birth' => $this->date_of_birth?->format('Y-m-d'),
            'gender' => [
                'value' => is_object($this->gender) ? $this->gender->value : $this->gender,
                'label' => is_object($this->gender) && method_exists($this->gender, 'label')
                    ? $this->gender->label()
                    : ucfirst($this->gender ?? 'unknown')
            ],
            'role' => [
                'value' => is_object($this->role) ? $this->role->value : $this->role,
                'label' => is_object($this->role) && method_exists($this->role, 'label')
                    ? $this->role->label()
                    : ucfirst($this->role ?? 'customer')
            ],
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
