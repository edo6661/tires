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
        if (isset($todayHours['closed']) && $todayHours['closed']) {
            return false;
        }
        if (!isset($todayHours['open']) || !isset($todayHours['close'])) {
            return false;
        }
        return $currentTime >= $todayHours['open'] && $currentTime <= $todayHours['close'];
    }
    public function getBusinessHours(): array
    {
        $settings = $this->getBusinessSettings();
        return $settings ? $settings->business_hours ?? [] : [];
    }
    public function getTodayBusinessHours(): ?array
    {
        $businessHours = $this->getBusinessHours();
        $dayOfWeek = strtolower(Carbon::now()->format('l'));
        return $businessHours[$dayOfWeek] ?? null;
    }
    public function getBusinessHoursForDisplay(): array
    {
        $businessHours = $this->getBusinessHours();
        $displayHours = [];
        $days = [
            'monday' => 'Senin',
            'tuesday' => 'Selasa', 
            'wednesday' => 'Rabu',
            'thursday' => 'Kamis',
            'friday' => 'Jumat',
            'saturday' => 'Sabtu',
            'sunday' => 'Minggu'
        ];
        foreach ($days as $day => $dayName) {
            $hours = $businessHours[$day] ?? null;
            if ($hours && isset($hours['closed']) && $hours['closed']) {
                $displayHours[$dayName] = 'Tutup';
            } elseif ($hours && isset($hours['open']) && isset($hours['close'])) {
                $displayHours[$dayName] = $hours['open'] . ' - ' . $hours['close'];
            } else {
                $displayHours[$dayName] = 'Tutup';
            }
        }
        return $displayHours;
    }
}