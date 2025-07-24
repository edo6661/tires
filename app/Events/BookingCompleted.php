<?php

namespace App\Events;

use App\Models\Reservation; // Pastikan Anda mengimpor model Reservation
use Illuminate\Broadcasting\InteractsWithSockets;
use Illuminate\Foundation\Events\Dispatchable;
use Illuminate\Queue\SerializesModels;

class BookingCompleted
{
    use Dispatchable, InteractsWithSockets, SerializesModels;

    /**
     * Create a new event instance.
     *
     * @param \App\Models\Reservation $reservation
     */
    public function __construct(public Reservation $reservation)
    {
        //
    }
}