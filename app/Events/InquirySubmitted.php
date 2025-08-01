<?php

namespace App\Events;

use App\Models\Contact; 
use Illuminate\Broadcasting\InteractsWithSockets;
use Illuminate\Foundation\Events\Dispatchable;
use Illuminate\Queue\SerializesModels;

class InquirySubmitted
{
    use Dispatchable, InteractsWithSockets, SerializesModels;

   
    public function __construct(
        public Contact $contact
    ) {}
}