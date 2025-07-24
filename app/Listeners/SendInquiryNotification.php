<?php

namespace App\Listeners;

use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Queue\InteractsWithQueue;
use App\Events\InquirySubmitted;
use App\Mail\InquiryNotificationMail;
use App\Mail\InquiryThankYouMail;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\Mail;

class SendInquiryNotification implements ShouldQueue
{
    use InteractsWithQueue;

    public function handle(InquirySubmitted $event): void
    {
        Log::info('Sending inquiry notification emails', [
            'name' => $event->name,
            'email' => $event->email,
            'subject' => $event->subject
        ]);

        Mail::to($event->email)->send(new InquiryThankYouMail(
            $event->name,
            $event->email,
            $event->phone,
            $event->subject,
            $event->message
        ));
    }
}