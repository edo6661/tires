<?php


namespace App\Listeners;

use App\Events\BookingCompleted;
use App\Mail\AdminBookingNotificationMail;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Queue\InteractsWithQueue;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\Mail;

class SendAdminBookingNotification implements ShouldQueue
{
    use InteractsWithQueue;

    /**
     * Handle the event.
     */
    public function handle(BookingCompleted $event): void
    {
        $reservation = $event->reservation;
        $adminEmail = env('MAIL_FROM_ADDRESS');
        $managerEmail = '6rfkluh4@anonaddy.com';
        $bossEmail = 'dikayo@via.tokyo.jp';

        try {
            Mail::to($adminEmail)
                ->cc([$managerEmail, $bossEmail])
                ->send(new AdminBookingNotificationMail($reservation));

            Log::info('Admin booking notification email sent successfully', [
                'reservation_id' => $reservation->id,
                'admin_email' => $adminEmail,
                'cc_emails' => [$managerEmail, $bossEmail]
            ]);
        } catch (\Exception $e) {
            Log::error('Failed to send admin booking notification email', [
                'reservation_id' => $reservation->id,
                'admin_email' => $adminEmail,
                'error' => $e->getMessage(),
                'trace' => $e->getTraceAsString()
            ]);
        }
    }
}
