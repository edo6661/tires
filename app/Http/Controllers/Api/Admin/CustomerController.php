<?php

namespace App\Http\Controllers\Api\Admin;

use App\Http\Controllers\Controller;
use App\Services\CustomerServiceInterface;
use App\Http\Traits\ApiResponseTrait;
use App\Http\Resources\CustomerResource;
use Illuminate\Http\Request;
use Illuminate\Http\JsonResponse;
use Illuminate\Support\Facades\App;

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
     */
    public function index(Request $request): JsonResponse
    {
        try {
            // Validate query parameters
            $request->validate([
                'per_page' => 'nullable|integer|min:1|max:100',
                'paginate' => 'sometimes|in:true,false',
                'cursor' => 'nullable|string',
                'customer_type' => 'nullable|in:first_time,repeat,dormant,all',
                'search' => 'nullable|string|max:255'
            ]);

            $perPage = min($request->get('per_page', 15), 100);
            $locale = App::getLocale();

            // Get filters from request
            $filters = [
                'customer_type' => $request->get('customer_type'),
                'search' => $request->get('search')
            ];

            // Remove empty filters
            $filters = array_filter($filters);

            if ($request->has('paginate') && $request->get('paginate') !== 'false') {
                // Paginated response with cursor and filters
                $cursor = $request->get('cursor');
                $customers = $this->customerService->getPaginatedCustomersWithCursor($perPage, $cursor, $filters);
                $collection = CustomerResource::collection($customers);

                $cursorInfo = $this->generateCursor($customers);

                return $this->successResponseWithCursor(
                    $collection->resolve(),
                    $cursorInfo,
                    'Customers retrieved successfully'
                );
            } else {
                // Simple response without pagination but with filters
                if (!empty($filters)) {
                    $customers = $this->customerService->getCustomers($filters, $perPage);
                } else {
                    // Use the standard getPaginated method even without filters to ensure all fields are included
                    $customers = $this->customerService->getCustomers([], $perPage);
                }
                $collection = CustomerResource::collection($customers);

                return $this->successResponse(
                    $collection->resolve(),
                    'Customers retrieved successfully'
                );
            }
        } catch (\Illuminate\Validation\ValidationException $e) {
            return $this->errorResponse(
                'Validation failed',
                422,
                collect($e->errors())->map(function ($messages, $field) {
                    return [
                        'field' => $field,
                        'tag' => 'validation_error',
                        'value' => request($field),
                        'message' => $messages[0]
                    ];
                })->values()->toArray()
            );
        } catch (\Exception $e) {
            return $this->errorResponse(
                'Failed to retrieve customers',
                500,
                [
                    [
                        'field' => 'general',
                        'tag' => 'server_error',
                        'value' => $e->getMessage(),
                        'message' => 'An unexpected error occurred'
                    ]
                ]
            );
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
                return $this->errorResponse('Customer not found', 404, [
                    [
                        'field' => 'general',
                        'tag' => 'not_found',
                        'value' => null,
                        'message' => 'Customer not found'
                    ]
                ]);
            }

            return $this->successResponse(
                $customerDetail,
                'Customer details retrieved successfully'
            );
        } catch (\Exception $e) {
            return $this->errorResponse(
                'Failed to retrieve customer',
                500,
                [[
                    'field' => 'general',
                    'tag' => 'server_error',
                    'value' => $e->getMessage(),
                    'message' => 'An unexpected error occurred'
                ]]
            );
        }
    }

    /**
     * Get first time customers
     */
    public function getFirstTimeCustomers(): JsonResponse
    {
        try {
            $customers = $this->customerService->getFirstTimeCustomers();
            $collection = CustomerResource::collection($customers);

            return $this->successResponse(
                $collection->resolve(),
                'First time customers retrieved successfully'
            );
        } catch (\Exception $e) {
            return $this->errorResponse(
                'Failed to retrieve first time customers',
                500,
                [[
                    'field' => 'general',
                    'tag' => 'server_error',
                    'value' => $e->getMessage(),
                    'message' => 'An unexpected error occurred'
                ]]
            );
        }
    }

    /**
     * Get repeat customers
     */
    public function getRepeatCustomers(): JsonResponse
    {
        try {
            $customers = $this->customerService->getRepeatCustomers();
            $collection = CustomerResource::collection($customers);

            return $this->successResponse(
                $collection->resolve(),
                'Repeat customers retrieved successfully'
            );
        } catch (\Exception $e) {
            return $this->errorResponse(
                'Failed to retrieve repeat customers',
                500,
                [[
                    'field' => 'general',
                    'tag' => 'server_error',
                    'value' => $e->getMessage(),
                    'message' => 'An unexpected error occurred'
                ]]
            );
        }
    }

    /**
     * Get dormant customers
     */
    public function getDormantCustomers(): JsonResponse
    {
        try {
            $customers = $this->customerService->getDormantCustomers();
            $collection = CustomerResource::collection($customers);

            return $this->successResponse(
                $collection->resolve(),
                'Dormant customers retrieved successfully'
            );
        } catch (\Exception $e) {
            return $this->errorResponse(
                'Failed to retrieve dormant customers',
                500,
                [[
                    'field' => 'general',
                    'tag' => 'server_error',
                    'value' => $e->getMessage(),
                    'message' => 'An unexpected error occurred'
                ]]
            );
        }
    }

    /**
     * Search customers by name, email, or phone number
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
            $collection = CustomerResource::collection($customers);

            return $this->successResponse(
                $collection->resolve(),
                'Customer search completed successfully'
            );
        } catch (\Illuminate\Validation\ValidationException $e) {
            return $this->errorResponse(
                'Validation failed',
                422,
                collect($e->errors())->map(function ($messages, $field) {
                    return [
                        'field' => $field,
                        'tag' => 'validation_error',
                        'value' => request($field),
                        'message' => $messages[0]
                    ];
                })->values()->toArray()
            );
        } catch (\Exception $e) {
            return $this->errorResponse(
                'Customer search failed',
                500,
                [[
                    'field' => 'general',
                    'tag' => 'server_error',
                    'value' => $e->getMessage(),
                    'message' => 'An unexpected error occurred'
                ]]
            );
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

            return $this->successResponse(
                [
                    'statistics' => $statistics,
                    'total_customers' => array_sum($statistics)
                ],
                'Customer statistics retrieved successfully'
            );
        } catch (\Exception $e) {
            return $this->errorResponse(
                'Failed to retrieve customer statistics',
                500,
                [[
                    'field' => 'general',
                    'tag' => 'server_error',
                    'value' => $e->getMessage(),
                    'message' => 'An unexpected error occurred'
                ]]
            );
        }
    }

    /**
     * Get customer type counts
     */
    public function getCustomerTypeCounts(): JsonResponse
    {
        try {
            $counts = $this->customerService->getCustomerTypeCounts();

            return $this->successResponse(
                $counts,
                'Customer type counts retrieved successfully'
            );
        } catch (\Exception $e) {
            return $this->errorResponse(
                'Failed to retrieve customer type counts',
                500,
                [[
                    'field' => 'general',
                    'tag' => 'server_error',
                    'value' => $e->getMessage(),
                    'message' => 'An unexpected error occurred'
                ]]
            );
        }
    }
}
