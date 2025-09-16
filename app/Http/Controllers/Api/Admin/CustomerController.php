<?php

namespace App\Http\Controllers\Api\Admin;

use App\Http\Controllers\Controller;
use App\Services\CustomerServiceInterface;
use App\Http\Traits\ApiResponseTrait;
use Illuminate\Http\Request;
use Illuminate\Http\JsonResponse;

/**
 * @tags Admin - Customer Management
 */
class CustomerController extends Controller
{
    use ApiResponseTrait;

    public function __construct(protected CustomerServiceInterface $customerService)
    {
    }

    /**
     * Display a listing of customers with search and filter support
     *
     * @param Request $request
     * @return JsonResponse
     *
     * Query Parameters:
     * - search: string (search by name, email, or phone)
     * - customer_type: string (first_time|repeat|dormant|all)
     * - per_page: int (pagination limit, default: 15)
     * - page: int (current page, default: 1)
     */
    public function index(Request $request): JsonResponse
    {
        try {
            $request->validate([
                'search' => 'nullable|string|max:255',
                'customer_type' => 'nullable|string|in:first_time,repeat,dormant,all',
                'per_page' => 'nullable|integer|min:1|max:100',
                'page' => 'nullable|integer|min:1'
            ]);

            $filters = [];

            if ($request->filled('search')) {
                $filters['search'] = $request->input('search');
            }

            if ($request->filled('customer_type') && $request->input('customer_type') !== 'all') {
                $filters['customer_type'] = $request->input('customer_type');
            }

            $perPage = $request->get('per_page', 15);
            $customers = $this->customerService->getCustomers($filters, $perPage);
            $customerTypeCounts = $this->customerService->getCustomerTypeCounts();

            return $this->successResponse([
                'customers' => $customers,
                'customer_type_counts' => $customerTypeCounts,
                'filters' => $filters,
                'pagination_info' => [
                    'current_page' => $customers->currentPage(),
                    'per_page' => $customers->perPage(),
                    'total' => $customers->total(),
                    'last_page' => $customers->lastPage()
                ]
            ], 'Customers retrieved successfully');
        } catch (\Illuminate\Validation\ValidationException $e) {
            return $this->validationErrorResponse($e->errors());
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
     * Search customers by name, email, or phone number
     *
     * @param Request $request
     * @return JsonResponse
     */
    public function search(Request $request): JsonResponse
    {
        try {
            $request->validate([
                'search' => 'required|string|min:1|max:255',
                'customer_type' => 'nullable|string|in:first_time,repeat,dormant,all',
                'per_page' => 'nullable|integer|min:1|max:100'
            ]);

            $searchTerm = $request->input('search');
            $customerType = $request->input('customer_type');
            $perPage = $request->get('per_page', 15);

            $filters = [
                'search' => $searchTerm
            ];

            if ($customerType && $customerType !== 'all') {
                $filters['customer_type'] = $customerType;
            }

            $customers = $this->customerService->getCustomers($filters, $perPage);

            return $this->successResponse([
                'customers' => $customers,
                'search_term' => $searchTerm,
                'customer_type' => $customerType,
                'results_count' => $customers->total()
            ], 'Customer search completed successfully');
        } catch (\Illuminate\Validation\ValidationException $e) {
            return $this->validationErrorResponse($e->errors());
        } catch (\Exception $e) {
            return $this->errorResponse('Customer search failed: ' . $e->getMessage(), 500);
        }
    }

    /**
     * Get customer statistics overview
     *
     * Returns counts for different customer types matching the web interface
     *
     * @return JsonResponse
     */
    public function getStatistics(): JsonResponse
    {
        try {
            $statistics = $this->customerService->getCustomerTypeCounts();

            return $this->successResponse([
                'statistics' => $statistics,
                'total_customers' => array_sum($statistics)
            ], 'Customer statistics retrieved successfully');
        } catch (\Exception $e) {
            return $this->errorResponse('Failed to retrieve customer statistics: ' . $e->getMessage(), 500);
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
