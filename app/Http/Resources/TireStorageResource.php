<?php

namespace App\Http\Resources;

use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\JsonResource;

class TireStorageResource extends JsonResource
{
    /**
     * Transform the resource into an array.
     *
     * @return array<string, mixed>
     */
    public function toArray(Request $request): array
    {
        return [
            'id' => $this->id,
            'user' => [
                'id' => $this->user->id ?? null,
                'name' => $this->user->name ?? null,
                'email' => $this->user->email ?? null,
            ],
            'tire_brand' => $this->tire_brand,
            'tire_size' => $this->tire_size,
            'storage_start_date' => $this->storage_start_date?->format('Y-m-d'),
            'planned_end_date' => $this->planned_end_date?->format('Y-m-d'),
            'storage_fee' => $this->storage_fee,
            'status' => $this->status->value, // enum cast ke string/value
            'notes' => $this->notes,
            'created_at' => $this->created_at?->toDateTimeString(),
            'updated_at' => $this->updated_at?->toDateTimeString(),
        ];
    }
}
