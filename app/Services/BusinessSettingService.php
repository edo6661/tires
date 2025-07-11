<?php


namespace App\Services;

use App\Models\BusinessSetting;
use App\Repositories\BusinessSettingRepositoryInterface;
use Carbon\Carbon;

class BusinessSettingService implements BusinessSettingServiceInterface
{
    protected $businessSettingRepository;

    public function __construct(BusinessSettingRepositoryInterface $businessSettingRepository)
    {
        $this->businessSettingRepository = $businessSettingRepository;
    }

    public function getBusinessSettings(): ?BusinessSetting
    {
        return $this->businessSettingRepository->getSettings();
    }

    public function updateBusinessSettings(array $data): BusinessSetting
    {
        return $this->businessSettingRepository->updateSettings($data);
    }

    public function createBusinessSettings(array $data): BusinessSetting
    {
        return $this->businessSettingRepository->createSettings($data);
    }

    public function isBusinessOpen(): bool
    {
        $settings = $this->getBusinessSettings();
        if (!$settings || !$settings->business_hours) {
            return false;
        }

        $now = Carbon::now();
        $dayOfWeek = strtolower($now->format('l'));
        $currentTime = $now->format('H:i');

        $businessHours = $settings->business_hours;
        if (!isset($businessHours[$dayOfWeek])) {
            return false;
        }

        $todayHours = $businessHours[$dayOfWeek];
        if (!$todayHours['open'] || !isset($todayHours['start']) || !isset($todayHours['end'])) {
            return false;
        }

        return $currentTime >= $todayHours['start'] && $currentTime <= $todayHours['end'];
    }

    public function getBusinessHours(): array
    {
        $settings = $this->getBusinessSettings();
        return $settings ? $settings->business_hours ?? [] : [];
    }
}