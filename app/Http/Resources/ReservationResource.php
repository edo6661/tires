<?php

namespace App\Http\Resources;


use App\Http\Resources\QuestionResource;
use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\JsonResource;
use Illuminate\Support\Facades\App;


class ReservationResource extends JsonResource
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
            'reservation_number' => $this->reservation_number,
            'reservation_datetime' => $this->reservation_datetime?->toISOString(),
            'number_of_people' => $this->number_of_people,
            'amount' => [
                'raw' => $this->amount,
                'formatted' => number_format($this->amount, 2)
            ],
            'status' => $this->status,
            'notes' => $this->notes,

            // Customer Information
            'customer_info' => [
                'full_name' => $this->getFullName(),
                'full_name_kana' => $this->getFullNameKana(),
                'email' => $this->getEmail(),
                'phone_number' => $this->getPhoneNumber(),
                'is_guest' => $this->isGuestReservation(),
            ],

            // Relations (only include if loaded)
            'user' => $this->whenLoaded('user', function () {
                return [
                    'id' => $this->user->id,
                    'full_name' => $this->user->full_name,
                    'email' => $this->user->email,
                    'phone_number' => $this->user->phone_number,
                ];
            }),

            'menu' => $this->whenLoaded('menu', function () {
                return [
                    'id' => $this->menu->id,
                    'name' => $this->menu->name,
                    'description' => $this->menu->description,
                    'required_time' => $this->menu->required_time,
                    'price' => $this->menu->price,
                    'color' => $this->menu->color,
                ];
            }),

            // Timestamps
            'created_at' => $this->created_at?->toISOString(),
            'updated_at' => $this->updated_at?->toISOString(),

            'meta' => [
                'locale' => $locale
            ]
        ];
    }
}
