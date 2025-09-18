<?php

namespace App\Http\Resources;

use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\JsonResource;
use Illuminate\Support\Facades\App;
use Carbon\Carbon;

class CustomerResource extends JsonResource
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

        // Handle both Eloquent models and stdClass objects from database queries
        $data = is_object($this->resource) ? $this->resource : (object) $this->resource;

        return [
            'id' => $data->customer_id ?? $data->id ?? null,
            'user_id' => $data->user_id ?? null,
            'email' => $data->email ?? null,
            'full_name' => $data->full_name ?? null,
            'full_name_kana' => $data->full_name_kana ?? null,
            'phone_number' => $data->phone_number ?? null,
            'company_name' => $data->company_name ?? null,
            'department' => $data->department ?? null,
            'company_address' => $data->company_address ?? null,
            'home_address' => $data->home_address ?? null,
            'date_of_birth' => isset($data->date_of_birth) ? Carbon::parse($data->date_of_birth)->toISOString() : null,
            'gender' => $data->gender ?? null,
            'is_registered' => $data->is_registered ?? (isset($data->user_id) && $data->user_id ? true : false),
            'reservation_count' => $data->reservation_count ?? 0,
            'latest_reservation' => isset($data->latest_reservation) ? Carbon::parse($data->latest_reservation)->toISOString() : null,
            'total_amount' => $data->total_amount ?? 0,
            'created_at' => isset($data->created_at) ? Carbon::parse($data->created_at)->toISOString() : null,
            'updated_at' => isset($data->updated_at) ? Carbon::parse($data->updated_at)->toISOString() : null,

            // Always include basic translations structure for consistency with AnnouncementResource
            'translations' => [
                'en' => [
                    'full_name' => $data->full_name ?? null,
                    'company_name' => $data->company_name ?? null
                ],
                'ja' => [
                    'full_name' => $data->full_name_kana ?? $data->full_name ?? null,
                    'company_name' => $data->company_name ?? null
                ]
            ],

            'meta' => [
                'locale' => $locale,
                'fallback_used' => false
            ]
        ];
    }
}
