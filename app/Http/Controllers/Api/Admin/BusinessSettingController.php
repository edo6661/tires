<?php

namespace App\Http\Controllers\Api\Admin;

use Illuminate\Support\Facades\Validator;
use Illuminate\Http\Request;
use Illuminate\Http\JsonResponse;
use App\Http\Controllers\Controller;
use App\Http\Traits\ApiResponseTrait;
use Illuminate\Support\Facades\Storage;
use App\Http\Requests\BusinessSettingRequest;
use App\Http\Resources\BusinessSettingResource;
use App\Services\BusinessSettingServiceInterface;

/**
 * @tags Admin - Business Setting Management
 */
class BusinessSettingController extends Controller
{
    use ApiResponseTrait;

    public function __construct(protected BusinessSettingServiceInterface $businessSettingService) {}

    /**
     * Display business settings
     */
    public function index(Request $request): JsonResponse
    {
        try {
            // Validate query parameters
            $request->validate([
                'locale' => 'sometimes|string|in:en,ja',
            ]);
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

            // Map translatable fields into translations payload for current locale
            $locale = $request->input('locale') ?? $request->header('X-Locale') ?? app()->getLocale();
            $translatableFields = [
                'shop_name',
                'address',
                'access_information',
                'site_name',
                'shop_description',
                'terms_of_use',
                'privacy_policy',
            ];

            $translationPayload = [];
            foreach ($translatableFields as $field) {
                if (array_key_exists($field, $data)) {
                    $translationPayload[$field] = $data[$field];
                    unset($data[$field]); // Remove from base payload to avoid base table update
                }
            }

            if (!empty($translationPayload)) {
                $data['translations'] = [
                    $locale => $translationPayload
                ];
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
     * Update only top image
     */
    public function updateTopImage(Request $request): JsonResponse
    {
        try {

            // Validasi dengan pesan error yang lebih spesifik
            $validator = Validator::make($request->all(), [
                'top_image' => 'required|file|image|mimes:jpeg,png,jpg,webp|max:2048',
            ], [
                'top_image.required' => 'Top image file is required',
                'top_image.file' => 'Top image must be a valid file',
                'top_image.image' => 'Top image must be an image file',
                'top_image.mimes' => 'Top image must be a file of type: jpeg, png, jpg, webp',
                'top_image.max' => 'Top image size must not exceed 2MB',
            ]);

            if ($validator->fails()) {
                return $this->errorResponse(
                    'Validation failed',
                    422,
                    collect($validator->errors())->map(function ($messages, $field) {
                        return [
                            'field' => $field,
                            'tag' => 'validation_error',
                            'value' => null,
                            'message' => $messages[0]
                        ];
                    })->values()->toArray()
                );
            }

            $businessSettings = $this->businessSettingService->getBusinessSettings();

            // Hapus gambar lama jika ada
            if ($businessSettings && $businessSettings->top_image_path) {
                Storage::disk('s3')->delete($businessSettings->top_image_path);
            }

            // Upload gambar baru
            $imagePath = $request->file('top_image')->store('business-images', 's3');

            $updatedSettings = $this->businessSettingService->updateBusinessSettings([
                'top_image_path' => $imagePath,
            ]);

            return $this->successResponse(
                new BusinessSettingResource($updatedSettings),
                'Top image updated successfully'
            );
        } catch (\Exception $e) {


            return $this->errorResponse(
                'Failed to update top image: ' . $e->getMessage(),
                500,
                [
                    [
                        'field' => 'general',
                        'tag' => 'upload_error',
                        'value' => null,
                        'message' => 'Image upload failed'
                    ]
                ]
            );
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
