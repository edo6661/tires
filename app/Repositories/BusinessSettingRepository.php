<?php
namespace App\Repositories;
use App\Models\BusinessSetting;
class BusinessSettingRepository implements BusinessSettingRepositoryInterface
{
    protected $model;
    public function __construct(BusinessSetting $model)
    {
        $this->model = $model;
    }
    public function getSettings(): ?BusinessSetting
    {
        return $this->model->withTranslations()->first();
    }

    public function updateSettings(array $data): BusinessSetting
    {
        $settings = $this->getSettings();
        if ($settings) {
            $translations = $data['translations'] ?? [];
            unset($data['translations']);
            $settings->update($data);
            $settings->setTranslations($translations);
            return $settings->fresh(['translations']);
        }
        return $this->createSettings($data);
    }
    public function createSettings(array $data): BusinessSetting
    {
        $translations = $data['translations'] ?? [];
        unset($data['translations']);
        $settings = $this->model->create($data);
        $settings->setTranslations($translations);
        return $settings->fresh(['translations']);
    }
}
