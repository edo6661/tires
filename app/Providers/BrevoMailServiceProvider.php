<?php

namespace App\Providers;

use App\Mail\Transport\BrevoTransport;
use Illuminate\Mail\MailManager;
use Illuminate\Support\ServiceProvider;

class BrevoMailServiceProvider extends ServiceProvider
{
  /**
   * Register services.
   */
  public function register(): void
  {
    //
  }

  /**
   * Bootstrap services.
   */
  public function boot(): void
  {
    $this->app->afterResolving(MailManager::class, function (MailManager $mailManager) {
      $mailManager->extend('brevo', function (array $config) {
        return new BrevoTransport($config['api_key'] ?? config('services.brevo.key'));
      });
    });
  }
}
