<?php

namespace App\Providers;

use App\Repositories\AnnouncementRepository;
use App\Repositories\AnnouncementRepositoryInterface;
use App\Repositories\AuthRepository;
use App\Repositories\AuthRepositoryInterface;
use App\Repositories\BlockedPeriodRepository;
use App\Repositories\BlockedPeriodRepositoryInterface;
use App\Repositories\BusinessSettingRepository;
use App\Repositories\BusinessSettingRepositoryInterface;
use App\Repositories\ContactRepository;
use App\Repositories\ContactRepositoryInterface;
use App\Repositories\FaqRepository;
use App\Repositories\FaqRepositoryInterface;
use App\Repositories\MenuRepositoryInterface;
use App\Repositories\MenuRepository;
use App\Repositories\PaymentRepository;
use App\Repositories\PaymentRepositoryInterface;
use App\Repositories\QuestionnaireRepository;
use App\Repositories\QuestionnaireRepositoryInterface;
use App\Repositories\ReservationRepository;
use App\Repositories\ReservationRepositoryInterface;
use App\Repositories\TireStorageRepository;
use App\Repositories\TireStorageRepositoryInterface;
use App\Repositories\UserRepository;
use App\Repositories\UserRepositoryInterface;
use Illuminate\Support\ServiceProvider;

class RepositoryProvider extends ServiceProvider
{
    /**
     * Register services.
     */
    public function register(): void
    {
        $this->app->bind(AuthRepositoryInterface::class, AuthRepository::class);
        $this->app->bind(AnnouncementRepositoryInterface::class, AnnouncementRepository::class);
        $this->app->bind(BlockedPeriodRepositoryInterface::class, BlockedPeriodRepository::class);
        $this->app->bind(BusinessSettingRepositoryInterface::class, BusinessSettingRepository::class);
        $this->app->bind(ContactRepositoryInterface::class, ContactRepository::class);
        $this->app->bind(FaqRepositoryInterface::class, FaqRepository::class);
        $this->app->bind(MenuRepositoryInterface::class, MenuRepository::class);
        $this->app->bind(PaymentRepositoryInterface::class, PaymentRepository::class);
        $this->app->bind(QuestionnaireRepositoryInterface::class, QuestionnaireRepository::class);
        $this->app->bind(ReservationRepositoryInterface::class, ReservationRepository::class);
        $this->app->bind(TireStorageRepositoryInterface::class, TireStorageRepository::class);
        $this->app->bind(UserRepositoryInterface::class, UserRepository::class);
    }

    /**
     * Bootstrap services.
     */
    public function boot(): void
    {
        //
    }
}
