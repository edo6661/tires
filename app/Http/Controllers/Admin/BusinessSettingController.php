<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Services\BusinessSettingServiceInterface;
use App\Http\Requests\BusinessSettingRequest;
use Illuminate\Http\Request;

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

    public function edit(int $id)
    {
        $businessSettings = $this->businessSettingService->getBusinessSettings();
        return view('admin.business-setting.edit', compact('businessSettings'));
    }

    public function update(BusinessSettingRequest $request)
    {
        try {
            $this->businessSettingService->updateBusinessSettings($request->validated());
            return redirect()->route('admin.business-setting.index')
                ->with('success', 'Pengaturan bisnis berhasil diperbarui.');
        } catch (\Exception $e) {
            return redirect()->back()
                ->with('error', 'Terjadi kesalahan: ' . $e->getMessage())
                ->withInput();
        }
    }

    // public function businessHours()
    // {
    //     $businessHours = $this->businessSettingService->getBusinessHours();
    //     $isBusinessOpen = $this->businessSettingService->isBusinessOpen();
    //     return view('admin.business-setting.business-hours', compact('businessHours', 'isBusinessOpen'));
    // }

}

