<?php

namespace App\Http\Controllers\Api\Admin;

use App\Http\Controllers\Controller;
use App\Services\CustomerServiceInterface;
use App\Http\Traits\ApiResponseTrait;
use Illuminate\Http\Request;
use Illuminate\Http\JsonResponse;

class CustomerController extends Controller
{
    use ApiResponseTrait;

    public function __construct(protected CustomerServiceInterface $customerService)
    {
    }

    /**
     * Display a listing of customers
     */
    public function index(Request $request): JsonResponse
    {
        try {
            $filters = [];

            if ($request->filled('search')) {
                $filters['search'] = $request->input('search');
            }

            if ($request->filled('customer_type')) {
                $filters['customer_type'] = $request->input('customer_type');
            }

            $perPage = $request->get('per_page', 15);
            $customers = $this->customerService->getCustomers($filters, $perPage);
            $customerTypeCounts = $this->customerService->getCustomerTypeCounts();

            return $this->successResponse([
                'customers' => $customers,
                'customer_type_counts' => $customerTypeCounts,
                'filters' => $filters
            ], 'Customers retrieved successfully');
        } catch (\Exception $e) {
            return $this->errorResponse('Failed to retrieve customers: ' . $e->getMessage(), 500);
        }
    }

    /**
     * Display the specified customer
     */
    public function show(int $id): JsonResponse
    {
        try {
            $customerDetail = $this->customerService->getCustomerDetail($id);

            if (!$customerDetail) {
                return $this->errorResponse('Customer not found', 404);
            }

            return $this->successResponse($customerDetail, 'Customer details retrieved successfully');
        } catch (\Exception $e) {
            return $this->errorResponse('Failed to retrieve customer: ' . $e->getMessage(), 500);
        }
    }

    /**
     * Get first time customers
     */
    public function getFirstTimeCustomers(): JsonResponse
    {
        try {
            $customers = $this->customerService->getFirstTimeCustomers();

            return $this->successResponse($customers, 'First time customers retrieved successfully');
        } catch (\Exception $e) {
            return $this->errorResponse('Failed to retrieve first time customers: ' . $e->getMessage(), 500);
        }
    }

    /**
     * Get repeat customers
     */
    public function getRepeatCustomers(): JsonResponse
    {
        try {
            $customers = $this->customerService->getRepeatCustomers();

            return $this->successResponse($customers, 'Repeat customers retrieved successfully');
        } catch (\Exception $e) {
            return $this->errorResponse('Failed to retrieve repeat customers: ' . $e->getMessage(), 500);
        }
    }

    /**
     * Get dormant customers
     */
    public function getDormantCustomers(): JsonResponse
    {
        try {
            $customers = $this->customerService->getDormantCustomers();

            return $this->successResponse($customers, 'Dormant customers retrieved successfully');
        } catch (\Exception $e) {
            return $this->errorResponse('Failed to retrieve dormant customers: ' . $e->getMessage(), 500);
        }
    }

    /**
     * Search customers
     */
    public function search(Request $request): JsonResponse
    {
        try {
            $request->validate([
                'search' => 'required|string|min:1'
            ]);

            $customers = $this->customerService->searchCustomers($request->input('search'));

            return $this->successResponse($customers, 'Customer search completed successfully');
        } catch (\Illuminate\Validation\ValidationException $e) {
            return $this->validationErrorResponse($e->errors());
        } catch (\Exception $e) {
            return $this->errorResponse('Customer search failed: ' . $e->getMessage(), 500);
        }
    }

    /**
     * Get customer type counts
     */
    public function getCustomerTypeCounts(): JsonResponse
    {
        try {
            $counts = $this->customerService->getCustomerTypeCounts();

            return $this->successResponse($counts, 'Customer type counts retrieved successfully');
        } catch (\Exception $e) {
            return $this->errorResponse('Failed to retrieve customer type counts: ' . $e->getMessage(), 500);
        }
    }
}
