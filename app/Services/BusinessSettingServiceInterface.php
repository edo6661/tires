<?php

namespace App\Services;

use App\Models\BusinessSetting;

interface BusinessSettingServiceInterface
{
    public function getBusinessSettings(): ?BusinessSetting;
    public function updateBusinessSettings(array $data): BusinessSetting;
    public function createBusinessSettings(array $data): BusinessSetting;
    public function isBusinessOpen(): bool;
    public function getBusinessHours(): array;
}