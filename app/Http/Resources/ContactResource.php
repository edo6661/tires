<?php

namespace App\Http\Resources;

use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\JsonResource;
use Illuminate\Support\Facades\App;

class ContactResource extends JsonResource
{
    public function toArray(Request $request): array
    {
        return [
            'id' => $this->id,
            'full_name' => $this->getFullName(),
            'email' => $this->getEmail(),
            'phone_number' => $this->getPhoneNumber(),
            'subject' => $this->subject,
            'message' => $this->message,
            'status' => $this->status,
            'admin_reply' => $this->admin_reply,
            'replied_at' => $this->replied_at?->toISOString(),
            'created_at' => $this->created_at?->toISOString(),
            'updated_at' => $this->updated_at?->toISOString(),

            // User relation (only include if loaded)
            'user' => $this->whenLoaded('user', function () {
                return [
                    'id' => $this->user->id,
                    'full_name' => $this->user->full_name,
                    'email' => $this->user->email,
                    'phone_number' => $this->user->phone_number,
                ];
            }),
        ];
    }
}
