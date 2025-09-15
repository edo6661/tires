<?php

namespace App\Http\Resources;

use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\JsonResource;
use Illuminate\Support\Facades\App;

class TireStorageResource extends JsonResource
{
    /**
     * Transform the resource into an array.
     *
     * @return array<string, mixed>
     */
    public function toArray(Request $request): array
    {
        // Check for X-Locale header first, then fall back to App::getLocale()
        $locale = $request->header('X-Locale') ?? App::getLocale();

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
            'created_at' => $this->created_at?->toISOString(),
            'updated_at' => $this->updated_at?->toISOString(),

            'meta' => [
                'locale' => $locale
            ]
        ];
    }
}
