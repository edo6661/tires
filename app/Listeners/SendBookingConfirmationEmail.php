<?php

namespace App\Listeners;

use App\Events\BookingCompleted;
use App\Mail\BookingConfirmationMail;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Queue\InteractsWithQueue;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\Mail;

class SendBookingConfirmationEmail implements ShouldQueue
{
    use InteractsWithQueue;

    /**
     * Handle the event.
     */
    public function handle(BookingCompleted $event): void
    {
        $reservation = $event->reservation;
        $email = $reservation->getEmail();
        try {
            Mail::to($email)->send(new BookingConfirmationMail($reservation));
            
            Log::info('Booking confirmation email sent successfully', [
                'reservation_id' => $reservation->id,
                'email' => $email
            ]);
        } catch (\Exception $e) {
            Log::error('Failed to send booking confirmation email', [
                'reservation_id' => $reservation->id,
                'email' => $email,
                'error' => $e->getMessage()
            ]);
        }
    }
}