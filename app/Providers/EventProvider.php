<?php

namespace App\Providers;

use App\Events\PasswordResetRequested;
use App\Listeners\SendPasswordResetNotification;
use Illuminate\Auth\Events\Registered;
use Illuminate\Auth\Listeners\SendEmailVerificationNotification;
use Illuminate\Foundation\Support\Providers\EventServiceProvider as ServiceProvider;
use Illuminate\Support\Facades\Event;

class EventProvider extends ServiceProvider
{
    protected $listen = [
  
        PasswordResetRequested::class => [
            SendPasswordResetNotification::class,
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