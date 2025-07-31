<?php

namespace App\Mail;

use App\Models\Reservation;
use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Mail\Mailable;
use Illuminate\Mail\Mailables\Content;
use Illuminate\Mail\Mailables\Envelope;
use Illuminate\Mail\Mailables\Attachment; // Import kelas Attachment
use Illuminate\Queue\SerializesModels;

class BookingConfirmationMail extends Mailable implements ShouldQueue
{
    use Queueable, SerializesModels;

    /**
     * Properti untuk menyimpan data iCalendar.
     *
     * @var string|null
     */
    public ?string $icalData;

    /**
     * Create a new message instance.
     * @param \App\Models\Reservation $reservation
     * @param string|null $icalData Data iCalendar dalam bentuk string
     */
    public function __construct(public Reservation $reservation, ?string $icalData = null)
    {
        $this->icalData = $icalData;
    }

    /**
     * Get the message envelope.
     */
    public function envelope(): Envelope
    {
        return new Envelope(
            subject: 'Your Booking is Confirmed! - ' . config('app.name'),
        );
    }

    /**
     * Get the message content definition.
     */
    public function content(): Content
    {
        return new Content(
            view: 'emails.booking-confirmation',
            with: [
                'reservation' => $this->reservation,
            ],
        );
    }

    /**
     * Get the attachments for the message.
     *
     * @return array<int, \Illuminate\Mail\Mailables\Attachment>
     */
    public function attachments(): array
    {
        if ($this->icalData) {
            return [
                Attachment::fromData(fn () => $this->icalData, 'invite.ics')
                    ->withMime('text/calendar;charset=UTF-8;method=REQUEST'),
            ];
        }

        return [];
    }
}