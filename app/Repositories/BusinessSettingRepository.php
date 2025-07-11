<?php
namespace App\Repositories;

use App\Models\BusinessSetting;
use App\Repositories\BusinessSettingRepositoryInterface;

class BusinessSettingRepository implements BusinessSettingRepositoryInterface
{
    protected $model;

    public function __construct(BusinessSetting $model)
    {
        $this->model = $model;
    }

    public function getSettings(): ?BusinessSetting
    {
        return $this->model->first();
    }
    

    public function updateSettings(array $data): BusinessSetting
    {
        $settings = $this->getSettings();
        if ($settings) {
            $settings->update($data);
            return $settings;
        }
        return $this->createSettings($data);
    }

    public function createSettings(array $data): BusinessSetting
    {
        return $this->model->create($data);
    }
}
