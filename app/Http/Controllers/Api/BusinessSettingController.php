<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Services\BusinessSettingServiceInterface;
use App\Http\Traits\ApiResponseTrait;
use App\Http\Resources\BusinessSettingResource;
use Illuminate\Http\JsonResponse;

/**
 * @tags Public
 */
class BusinessSettingController extends Controller
{
    use ApiResponseTrait;

    public function __construct(protected BusinessSettingServiceInterface $businessSettingService)
    {
    }

    /**
     * Get public business settings
     *
     * Returns business information including company details, opening hours, and contact information.
     * This endpoint provides all the information shown on the About Us page.
     *
     * @return JsonResponse Business settings with locale-filtered translations
     */
    public function index(): JsonResponse
    {
        try {
            $businessSettings = $this->businessSettingService->getBusinessSettings();

            if (!$businessSettings) {
                return $this->errorResponse('Business settings not found', 404);
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
     * Get business hours only
     *
     * Returns only the opening hours for quick access.
     *
     * @return JsonResponse Business hours data
     */
    public function getBusinessHours(): JsonResponse
    {
        try {
            $businessSettings = $this->businessSettingService->getBusinessSettings();

            if (!$businessSettings) {
                return $this->errorResponse('Business settings not found', 404);
            }

            $businessHours = $businessSettings->business_hours ?? [];

            return $this->successResponse([
                'business_hours' => $businessHours
            ], 'Business hours retrieved successfully');
        } catch (\Exception $e) {
            return $this->errorResponse('Failed to retrieve business hours: ' . $e->getMessage(), 500);
        }
    }

    /**
     * Get company information only
     *
     * Returns company details like name, address, phone, etc.
     *
     * @return JsonResponse Company information with locale-filtered translations
     */
    public function getCompanyInfo(): JsonResponse
    {
        try {
            $businessSettings = $this->businessSettingService->getBusinessSettings();

            if (!$businessSettings) {
                return $this->errorResponse('Business settings not found', 404);
            }

            $resource = new BusinessSettingResource($businessSettings);
            $data = $resource->toArray(request());

            // Extract only company-related information
            $companyInfo = [
                'shop_name' => $data['shop_name'],
                'address' => $data['address'],
                'phone_number' => $data['phone_number'],
                'shop_description' => $data['shop_description'],
                'access_information' => $data['access_information'],
                'website_url' => $data['website_url'],
                'meta' => $data['meta']
            ];

            return $this->successResponse(
                $companyInfo,
                'Company information retrieved successfully'
            );
        } catch (\Exception $e) {
            return $this->errorResponse('Failed to retrieve company information: ' . $e->getMessage(), 500);
        }
    }

    /**
     * Get terms and policies
     *
     * Returns terms of service and privacy policy.
     *
     * @return JsonResponse Terms and policies with locale-filtered translations
     */
    public function getTermsAndPolicies(): JsonResponse
    {
        try {
            $businessSettings = $this->businessSettingService->getBusinessSettings();

            if (!$businessSettings) {
                return $this->errorResponse('Business settings not found', 404);
            }

            $resource = new BusinessSettingResource($businessSettings);
            $data = $resource->toArray(request());

            // Extract only terms and policies
            $termsAndPolicies = [
                'terms_of_use' => $data['terms_of_use'],
                'privacy_policy' => $data['privacy_policy'],
                'meta' => $data['meta']
            ];

            return $this->successResponse(
                $termsAndPolicies,
                'Terms and policies retrieved successfully'
            );
        } catch (\Exception $e) {
            return $this->errorResponse('Failed to retrieve terms and policies: ' . $e->getMessage(), 500);
        }
    }
}
