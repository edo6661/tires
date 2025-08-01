<?php

namespace App\Providers;

use App\Events\BookingCompleted;
use App\Events\InquirySubmitted;
use App\Events\PasswordResetRequested;
use App\Listeners\SendAdminBookingNotification;
use App\Listeners\SendBookingConfirmationEmail;
use App\Listeners\SendInquiryNotification;
use App\Listeners\SendPasswordResetNotification;
use Illuminate\Auth\Events\Registered;
use Illuminate\Auth\Listeners\SendEmailVerificationNotification;
use Illuminate\Foundation\Support\Providers\EventServiceProvider as ServiceProvider;
use Illuminate\Support\Facades\Event;
use App\Events\ContactReplied; 
use App\Listeners\SendAdminReplyNotification; 


class EventProvider extends ServiceProvider
{
    protected $listen = [
        PasswordResetRequested::class => [
            SendPasswordResetNotification::class,
        ],
        
        InquirySubmitted::class => [
            SendInquiryNotification::class,
        ],
        BookingCompleted::class => [
            SendBookingConfirmationEmail::class,
            SendAdminBookingNotification::class,
        ],
        ContactReplied::class => [ 
            SendAdminReplyNotification::class,
        ],
    ];

    public function boot(): void
    {
        //
    }

    public function shouldDiscoverEvents(): bool
    {
        return false;
    }
}