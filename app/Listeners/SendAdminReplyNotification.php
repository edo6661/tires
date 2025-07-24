<?php

namespace App\Listeners;

use App\Events\ContactReplied;
use App\Mail\AdminReplyMail;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Queue\InteractsWithQueue;
use Illuminate\Support\Facades\Mail;

class SendAdminReplyNotification implements ShouldQueue
{
    use InteractsWithQueue;

    /**
     * Handle the event.
     */
    public function handle(ContactReplied $event): void
    {
        Mail::to($event->contact->getEmail())->send(new AdminReplyMail($event->contact));
    }
}