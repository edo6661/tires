<?php

namespace App\Repositories;

use App\Models\BusinessSetting;

interface BusinessSettingRepositoryInterface
{
    public function getSettings(): ?BusinessSetting;
    public function updateSettings(array $data): BusinessSetting;
    public function createSettings(array $data): BusinessSetting;
}