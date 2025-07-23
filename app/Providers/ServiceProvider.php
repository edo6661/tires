<?php

namespace App\Providers;

use App\Services\AnnouncementService;
use App\Services\AnnouncementServiceInterface;
use App\Services\AuthService;
use App\Services\AuthServiceInterface;
use App\Services\BlockedPeriodService;
use App\Services\BlockedPeriodServiceInterface;
use App\Services\BusinessSettingService;
use App\Services\BusinessSettingServiceInterface;
use App\Services\ContactService;
use App\Services\ContactServiceInterface;
use App\Services\FaqService;
use App\Services\FaqServiceInterface;
use App\Services\MenuServiceInterface;
use App\Services\MenuService;
use App\Services\PaymentService;
use App\Services\PaymentServiceInterface;
use App\Services\QuestionnaireService;
use App\Services\QuestionnaireServiceInterface;
use App\Services\ReservationService;
use App\Services\ReservationServiceInterface;
use App\Services\TireStorageService;
use App\Services\TireStorageServiceInterface;
use App\Services\UserService;
use App\Services\UserServiceInterface;
use App\Services\CustomerService;
use App\Services\CustomerServiceInterface;
use Illuminate\Support\ServiceProvider as sv;

class ServiceProvider extends sv
{
    /**
     * Register services.
     */
    public function register(): void
    {
        $this->app->bind(AuthServiceInterface::class, AuthService::class);
        $this->app->bind(AnnouncementServiceInterface::class, AnnouncementService::class);
        $this->app->bind(BlockedPeriodServiceInterface::class, BlockedPeriodService::class);
        $this->app->bind(BusinessSettingServiceInterface::class, BusinessSettingService::class);
        $this->app->bind(ContactServiceInterface::class, ContactService::class);
        $this->app->bind(FaqServiceInterface::class, FaqService::class);
        $this->app->bind(MenuServiceInterface::class, MenuService::class);
        $this->app->bind(PaymentServiceInterface::class, PaymentService::class);
        $this->app->bind(QuestionnaireServiceInterface::class, QuestionnaireService::class);
        $this->app->bind(ReservationServiceInterface::class, ReservationService::class);
        $this->app->bind(TireStorageServiceInterface::class, TireStorageService::class);
        $this->app->bind(UserServiceInterface::class, UserService::class);
        $this->app->bind(CustomerServiceInterface::class, CustomerService::class);

    }

    /**
     * Bootstrap services.
     */
    public function boot(): void
    {
        //
    }
}
