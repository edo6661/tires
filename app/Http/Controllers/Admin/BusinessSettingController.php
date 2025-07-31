<?php
namespace App\Http\Controllers\Admin;
use App\Http\Controllers\Controller;
use App\Services\BusinessSettingServiceInterface;
use App\Http\Requests\BusinessSettingRequest;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Storage;
class BusinessSettingController extends Controller
{
    public function __construct(protected BusinessSettingServiceInterface $businessSettingService)
    {
    }
    public function index()
    {
        $businessSettings = $this->businessSettingService->getBusinessSettings();
        return view('admin.business-setting.index', compact('businessSettings'));
    }
    public function edit($locale, int $id)
    {
        $businessSettings = $this->businessSettingService->getBusinessSettings();
        return view('admin.business-setting.edit', compact('businessSettings'));
    }
    public function update(BusinessSettingRequest $request)
    {
        try {
            $data = $request->validated();
            if ($request->hasFile('top_image')) {
                $businessSettings = $this->businessSettingService->getBusinessSettings();
                if ($businessSettings && $businessSettings->top_image_path) {
                    Storage::disk('s3')->delete($businessSettings->top_image_path);
                }
                $imagePath = $request->file('top_image')->store('business-images', 's3');
                $data['top_image_path'] = $imagePath;
            }
            $data['business_hours'] = $this->processBusinessHours($data['business_hours']);
            $this->businessSettingService->updateBusinessSettings($data);
            return redirect()->route('admin.business-setting.index')
                ->with('success', __('admin/business-setting/general.notifications.success_update'));
        } catch (\Exception $e) {
            return redirect()->back()
                ->with('error', __('admin/business-setting/general.notifications.error_update') . $e->getMessage())
                ->withInput();
        }
    }
    private function processBusinessHours(array $businessHours): array
    {
        $processedHours = [];
        foreach ($businessHours as $day => $hours) {
            if (isset($hours['closed']) && $hours['closed']) {
                $processedHours[$day] = ['closed' => true];
            } else {
                $processedHours[$day] = [
                    'open' => $hours['open'],
                    'close' => $hours['close']
                ];
            }
        }
        return $processedHours;
    }
}
