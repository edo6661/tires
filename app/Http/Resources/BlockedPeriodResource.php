<?php

namespace App\Http\Resources;

use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\JsonResource;
use Illuminate\Support\Facades\App;

class BlockedPeriodResource extends JsonResource
{
    public function toArray(Request $request): array
    {
        // Check for X-Locale header first, then fall back to App::getLocale()
        $locale = $request->header('X-Locale') ?? App::getLocale();

        return [
            'id' => $this->id,
            'menu_id' => $this->menu_id,
            'start_datetime' => $this->start_datetime?->toISOString(),
            'end_datetime' => $this->end_datetime?->toISOString(),
            'reason' => $this->reason,
            'all_menus' => $this->all_menus,
            'created_at' => $this->created_at?->toISOString(),
            'updated_at' => $this->updated_at?->toISOString(),

            // Menu information
            'menu' => $this->when($this->menu, [
                'id' => $this->menu?->id,
                'name' => $this->menu?->name,
                'color' => $this->menu?->color ?? '#3b82f6',
            ]),

            // Duration information
            'duration' => [
                'hours' => $this->getDurationInHours(),
                'minutes' => $this->getDurationInMinutes(),
                'text' => $this->getDurationText(),
                'is_short' => $this->isShortDuration(),
            ],

            // Status information
            'status' => $this->getStatus(),

            // Translations (if applicable in the future)
            // 'translations' => $this->translations ?? (object)[],

            'meta' => [
                'locale' => $locale,
                'fallback_used' => false
            ]
        ];
    }

    /**
     * Get the current status of the blocked period
     */
    protected function getStatus(): string
    {
        $now = now();

        if ($now->lt($this->start_datetime)) {
            return 'upcoming';
        } elseif ($now->between($this->start_datetime, $this->end_datetime)) {
            return 'active';
        } else {
            return 'expired';
        }
    }
}
