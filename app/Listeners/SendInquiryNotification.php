<?php

namespace App\Listeners;

use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Queue\InteractsWithQueue;
use App\Events\InquirySubmitted;
use App\Mail\InquiryThankYouMail;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\Mail;

class SendInquiryNotification implements ShouldQueue
{
    use InteractsWithQueue;

    /**
     * Ubah handle method
     */
    public function handle(InquirySubmitted $event): void
    {
        $contact = $event->contact;

        Log::info('Sending inquiry notification emails', [
            'contact_id' => $contact->id,
            'email' => $contact->getEmail(),
            'subject' => $contact->subject
        ]);

        Mail::to($contact->getEmail())->send(new InquiryThankYouMail($contact));
    }
}