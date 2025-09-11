<?php

namespace App\Http\Controllers\Api\Admin;

use App\Http\Controllers\Controller;
use App\Services\BusinessSettingServiceInterface;
use App\Http\Traits\ApiResponseTrait;
use App\Http\Resources\BusinessSettingResource;
use App\Http\Requests\BusinessSettingRequest;
use Illuminate\Http\Request;
use Illuminate\Http\JsonResponse;
use Illuminate\Support\Facades\Storage;

/**
 * @tags Admin
 */
class BusinessSettingController extends Controller
{
    use ApiResponseTrait;

    public function __construct(protected BusinessSettingServiceInterface $businessSettingService)
    {
    }

    /**
     * Display business settings
     */
    public function index(): JsonResponse
    {
        try {
            $businessSettings = $this->businessSettingService->getBusinessSettings();

            if (!$businessSettings) {
                return $this->successResponse(null, 'No business settings found');
            }

            return $this->successResponse(
                new BusinessSettingResource($businessSettings),
                'Business settings retrieved successfully'
            );
        } catch (\Exception $e) {
            return $this->errorResponse('Failed to retrieve business settings: ' . $e->getMessage(), 500);
        }
    }

    /**
     * Get business settings for editing
     */
    public function edit(int $id): JsonResponse
    {
        try {
            $businessSettings = $this->businessSettingService->getBusinessSettings();

            if (!$businessSettings) {
                return $this->successResponse(null, 'No business settings found for editing');
            }

            return $this->successResponse(
                new BusinessSettingResource($businessSettings),
                'Business settings retrieved for editing'
            );
        } catch (\Exception $e) {
            return $this->errorResponse('Failed to retrieve business settings: ' . $e->getMessage(), 500);
        }
    }

    /**
     * Update business settings
     */
    public function update(BusinessSettingRequest $request): JsonResponse
    {
        try {
            $data = $request->validated();

            // Handle file upload if present
            if ($request->hasFile('top_image')) {
                $businessSettings = $this->businessSettingService->getBusinessSettings();

                // Delete old image if exists
                if ($businessSettings && $businessSettings->top_image_path) {
                    Storage::disk('s3')->delete($businessSettings->top_image_path);
                }

                // Store new image
                $imagePath = $request->file('top_image')->store('business-images', 's3');
                $data['top_image_path'] = $imagePath;
            }

            // Process business hours
            if (isset($data['business_hours'])) {
                $data['business_hours'] = $this->processBusinessHours($data['business_hours']);
            }

            $updatedSettings = $this->businessSettingService->updateBusinessSettings($data);

            return $this->successResponse(
                new BusinessSettingResource($updatedSettings),
                'Business settings updated successfully'
            );
        } catch (\Illuminate\Validation\ValidationException $e) {
            return $this->validationErrorResponse($e->errors());
        } catch (\Exception $e) {
            return $this->errorResponse('Failed to update business settings: ' . $e->getMessage(), 500);
        }
    }

    /**
     * Get business hours
     */
    public function getBusinessHours(): JsonResponse
    {
        try {
            $businessSettings = $this->businessSettingService->getBusinessSettings();
            $businessHours = $businessSettings ? $businessSettings->business_hours : null;

            return $this->successResponse([
                'business_hours' => $businessHours
            ], 'Business hours retrieved successfully');
        } catch (\Exception $e) {
            return $this->errorResponse('Failed to retrieve business hours: ' . $e->getMessage(), 500);
        }
    }

    /**
     * Update business hours only
     */
    public function updateBusinessHours(Request $request): JsonResponse
    {
        try {
            $request->validate([
                'business_hours' => 'required|array',
            ]);

            $data = [
                'business_hours' => $this->processBusinessHours($request->input('business_hours'))
            ];

            $updatedSettings = $this->businessSettingService->updateBusinessSettings($data);

            return $this->successResponse(
                new BusinessSettingResource($updatedSettings),
                'Business hours updated successfully'
            );
        } catch (\Illuminate\Validation\ValidationException $e) {
            return $this->validationErrorResponse($e->errors());
        } catch (\Exception $e) {
            return $this->errorResponse('Failed to update business hours: ' . $e->getMessage(), 500);
        }
    }

    /**
     * Get top image URL
     */
    public function getTopImage(): JsonResponse
    {
        try {
            $businessSettings = $this->businessSettingService->getBusinessSettings();
            $topImageUrl = null;

            if ($businessSettings && $businessSettings->top_image_path) {
                // Return the S3 path - frontend can construct full URL if needed
                $topImageUrl = $businessSettings->top_image_path;
            }

            return $this->successResponse([
                'top_image_url' => $topImageUrl,
                'top_image_path' => $businessSettings ? $businessSettings->top_image_path : null
            ], 'Top image URL retrieved successfully');
        } catch (\Exception $e) {
            return $this->errorResponse('Failed to retrieve top image: ' . $e->getMessage(), 500);
        }
    }

    /**
     * Process business hours data
     */
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
