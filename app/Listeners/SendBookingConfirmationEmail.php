<?php
namespace App\Listeners;
use App\Events\BookingCompleted;
use App\Mail\BookingConfirmationMail;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Queue\InteractsWithQueue;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\Mail;
use Spatie\IcalendarGenerator\Components\Calendar;
use Spatie\IcalendarGenerator\Components\Event;
use Spatie\IcalendarGenerator\Properties\TextProperty;
use Spatie\IcalendarGenerator\Properties\Parameter; 
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
            $startTime = $reservation->reservation_datetime;
            $endTime = (clone $startTime)->addMinutes($reservation->menu->required_time);
            $calendarEvent = Event::create()
                ->name('Booking: ' . $reservation->menu->name)
                ->description("Your booking for " . $reservation->menu->name . " is confirmed. Reservation Number: " . $reservation->reservation_number)
                ->attendee($email, $reservation->getFullName())
                ->startsAt($startTime)
                ->endsAt($endTime);
            $calendarEvent->appendProperty(
                TextProperty::create('ORGANIZER', "mailto:" . config('mail.from.address'))
                    ->addParameter(Parameter::create('CN', config('app.name'))) 
            );
            $icalData = Calendar::create('Your Reservation at ' . config('app.name'))
                ->event($calendarEvent)
                ->get();
            Mail::to($email)->send(new BookingConfirmationMail($reservation, $icalData));
            Log::info('Booking confirmation email with calendar invite sent successfully', [
                'reservation_id' => $reservation->id,
                'email' => $email
            ]);
        } catch (\Exception $e) {
            Log::error('Failed to send booking confirmation email', [
                'reservation_id' => $reservation->id,
                'email' => $email,
                'error' => $e->getMessage(),
                'trace' => $e->getTraceAsString() 
            ]);
        }
    }
}