<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Services\CustomerServiceInterface;
use Illuminate\Http\Request;
use Illuminate\Http\JsonResponse;

class CustomerController extends Controller
{
    public function __construct(protected CustomerServiceInterface $customerService)
    {
    }

    public function index(Request $request)
    {
        $filters = [];
        
        if ($request->filled('search')) {
            $filters['search'] = $request->input('search');
        }
        
        if ($request->filled('customer_type')) {
            $filters['customer_type'] = $request->input('customer_type');
        }
        
        $customers = $this->customerService->getCustomers($filters, 15);
        $customerTypeCounts = $this->customerService->getCustomerTypeCounts();
        
        return view('admin.customer.index', compact('customers', 'customerTypeCounts'));
    }

    public function show($locale, int $id)
    {
        $customerDetail = $this->customerService->getCustomerDetail($id);
        
        if (!$customerDetail) {
            return redirect()->route('admin.customer.index')
                ->with('error', 'Customer tidak ditemukan.');
        }
        
        return view('admin.customer.show', compact('customerDetail'));
    }

    public function getFirstTimeCustomers(): JsonResponse
    {
        try {
            $customers = $this->customerService->getFirstTimeCustomers();
            return response()->json([
                'success' => true,
                'data' => $customers
            ]);
        } catch (\Exception $e) {
            return response()->json([
                'success' => false,
                'message' => 'Terjadi kesalahan: ' . $e->getMessage()
            ], 500);
        }
    }

    public function getRepeatCustomers(): JsonResponse
    {
        try {
            $customers = $this->customerService->getRepeatCustomers();
            return response()->json([
                'success' => true,
                'data' => $customers
            ]);
        } catch (\Exception $e) {
            return response()->json([
                'success' => false,
                'message' => 'Terjadi kesalahan: ' . $e->getMessage()
            ], 500);
        }
    }

    public function getDormantCustomers(): JsonResponse
    {
        try {
            $customers = $this->customerService->getDormantCustomers();
            return response()->json([
                'success' => true,
                'data' => $customers
            ]);
        } catch (\Exception $e) {
            return response()->json([
                'success' => false,
                'message' => 'Terjadi kesalahan: ' . $e->getMessage()
            ], 500);
        }
    }

    public function search(Request $request): JsonResponse
    {
        try {
            $request->validate([
                'search' => 'required|string|min:1'
            ]);

            $customers = $this->customerService->searchCustomers($request->input('search'));
            
            return response()->json([
                'success' => true,
                'data' => $customers
            ]);
        } catch (\Exception $e) {
            return response()->json([
                'success' => false,
                'message' => 'Terjadi kesalahan: ' . $e->getMessage()
            ], 500);
        }
    }

    public function getCustomerTypeCounts(): JsonResponse
    {
        try {
            $counts = $this->customerService->getCustomerTypeCounts();
            return response()->json([
                'success' => true,
                'data' => $counts
            ]);
        } catch (\Exception $e) {
            return response()->json([
                'success' => false,
                'message' => 'Terjadi kesalahan: ' . $e->getMessage()
            ], 500);
        }
    }
}