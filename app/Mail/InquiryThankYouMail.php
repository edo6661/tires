<?php

namespace App\Mail;

use Illuminate\Bus\Queueable;
use Illuminate\Mail\Mailable;
use Illuminate\Mail\Mailables\Content;
use Illuminate\Mail\Mailables\Envelope;
use Illuminate\Queue\SerializesModels;

class InquiryThankYouMail extends Mailable
{
    use Queueable, SerializesModels;

    public string $customerName;
    public string $customerEmail;
    public string $customerPhone;
    public string $inquirySubject;
    public string $inquiryMessage;

    public function __construct(
        string $name,
        string $email,
        string $phone,
        string $subject,
        string $message
    ) {
        $this->customerName = $name;
        $this->customerEmail = $email;
        $this->customerPhone = $phone;
        $this->inquirySubject = $subject;
        $this->inquiryMessage = $message;
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
            with: [
                'customerName' => $this->customerName,
                'customerEmail' => $this->customerEmail,
                'customerPhone' => $this->customerPhone,
                'inquirySubject' => $this->inquirySubject,
                'inquiryMessage' => $this->inquiryMessage,
            ],
        );
    }
}