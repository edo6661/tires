<?php

namespace App\Mail;

use App\Models\Contact; // Tambahkan ini
use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Mail\Mailable;
use Illuminate\Mail\Mailables\Content;
use Illuminate\Mail\Mailables\Envelope;
use Illuminate\Queue\SerializesModels;

class InquiryThankYouMail extends Mailable implements ShouldQueue
{
    use Queueable, SerializesModels;

    public Contact $contact;

    /**
     * Ubah constructor untuk menerima objek Contact
     */
    public function __construct(Contact $contact)
    {
        $this->contact = $contact;
    }

    public function envelope(): Envelope
    {
        return new Envelope(
            subject: 'Thank You for Your Inquiry - ' . config('app.name'),
        );
    }

    public function content(): Content
    {
        return new Content(
            view: 'emails.customer-inquiry-thankyou',
        );
    }
}
