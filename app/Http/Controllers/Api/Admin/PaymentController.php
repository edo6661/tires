<?php

namespace App\Http\Controllers\Api\Admin;

use App\Http\Controllers\Controller;
use App\Services\PaymentServiceInterface;
use App\Http\Traits\ApiResponseTrait;
use App\Http\Requests\PaymentRequest;
use Illuminate\Http\Request;
use Illuminate\Http\JsonResponse;

/**
 * @tags Admin - Payment Settings
 */
class PaymentController extends Controller
{
    use ApiResponseTrait;

    public function __construct(protected PaymentServiceInterface $paymentService)
    {
    }

    /**
     * Display a listing of payments
     */
    public function index(Request $request): JsonResponse
    {
        try {
            $perPage = $request->get('per_page', 15);
            $payments = $this->paymentService->getPaginatedPayments($perPage);

            return $this->successResponse($payments, 'Payments retrieved successfully');
        } catch (\Exception $e) {
            return $this->errorResponse('Failed to retrieve payments: ' . $e->getMessage(), 500);
        }
    }

    /**
     * Store a newly created payment
     */
    public function store(PaymentRequest $request): JsonResponse
    {
        try {
            $payment = $this->paymentService->createPayment($request->validated());

            return $this->successResponse($payment, 'Payment created successfully', 201);
        } catch (\Exception $e) {
            return $this->errorResponse('Failed to create payment: ' . $e->getMessage(), 500);
        }
    }

    /**
     * Display the specified payment
     */
    public function show(int $id): JsonResponse
    {
        try {
            $payment = $this->paymentService->findPayment($id);

            if (!$payment) {
                return $this->errorResponse('Payment not found', 404);
            }

            return $this->successResponse($payment, 'Payment retrieved successfully');
        } catch (\Exception $e) {
            return $this->errorResponse('Failed to retrieve payment: ' . $e->getMessage(), 500);
        }
    }

    /**
     * Update the specified payment
     */
    public function update(PaymentRequest $request, int $id): JsonResponse
    {
        try {
            $payment = $this->paymentService->updatePayment($id, $request->validated());

            if (!$payment) {
                return $this->errorResponse('Payment not found', 404);
            }

            return $this->successResponse($payment, 'Payment updated successfully');
        } catch (\Exception $e) {
            return $this->errorResponse('Failed to update payment: ' . $e->getMessage(), 500);
        }
    }

    /**
     * Remove the specified payment
     */
    public function destroy(int $id): JsonResponse
    {
        try {
            $deleted = $this->paymentService->deletePayment($id);

            if (!$deleted) {
                return $this->errorResponse('Payment not found', 404);
            }

            return $this->successResponse(null, 'Payment deleted successfully');
        } catch (\Exception $e) {
            return $this->errorResponse('Failed to delete payment: ' . $e->getMessage(), 500);
        }
    }

    /**
     * Get payments by status
     */
    public function getByStatus(string $status): JsonResponse
    {
        try {
            $validStatuses = ['pending', 'completed', 'failed', 'refunded'];

            if (!in_array($status, $validStatuses)) {
                return $this->errorResponse('Invalid status. Must be one of: ' . implode(', ', $validStatuses), 422);
            }

            $payments = $this->paymentService->getPaymentsByStatus($status);

            return $this->successResponse($payments, "Payments with status '{$status}' retrieved successfully");
        } catch (\Exception $e) {
            return $this->errorResponse('Failed to retrieve payments by status: ' . $e->getMessage(), 500);
        }
    }

    /**
     * Get payments by user
     */
    public function getByUser(int $userId): JsonResponse
    {
        try {
            $payments = $this->paymentService->getPaymentsByUser($userId);

            return $this->successResponse($payments, "Payments for user {$userId} retrieved successfully");
        } catch (\Exception $e) {
            return $this->errorResponse('Failed to retrieve payments by user: ' . $e->getMessage(), 500);
        }
    }

    /**
     * Get payments by reservation
     */
    public function getByReservation(int $reservationId): JsonResponse
    {
        try {
            $payments = $this->paymentService->getPaymentsByReservation($reservationId);

            return $this->successResponse($payments, "Payments for reservation {$reservationId} retrieved successfully");
        } catch (\Exception $e) {
            return $this->errorResponse('Failed to retrieve payments by reservation: ' . $e->getMessage(), 500);
        }
    }

    /**
     * Get total revenue
     */
    public function getTotalRevenue(): JsonResponse
    {
        try {
            $revenue = $this->paymentService->getTotalRevenue();

            return $this->successResponse([
                'total_revenue' => $revenue,
                'currency' => 'JPY' // Assuming Japanese Yen based on the project
            ], 'Total revenue retrieved successfully');
        } catch (\Exception $e) {
            return $this->errorResponse('Failed to retrieve total revenue: ' . $e->getMessage(), 500);
        }
    }

    /**
     * Process payment
     */
    public function processPayment(Request $request, int $id): JsonResponse
    {
        try {
            $request->validate([
                'payment_data' => 'required|array',
                'payment_data.transaction_id' => 'nullable|string',
            ]);

            $processed = $this->paymentService->processPayment(
                $id,
                $request->input('payment_data')
            );

            if (!$processed) {
                return $this->errorResponse('Failed to process payment', 500);
            }

            // Get the updated payment to return
            $payment = $this->paymentService->findPayment($id);

            return $this->successResponse($payment, 'Payment processed successfully');
        } catch (\Illuminate\Validation\ValidationException $e) {
            return $this->validationErrorResponse($e->errors());
        } catch (\Exception $e) {
            return $this->errorResponse('Failed to process payment: ' . $e->getMessage(), 500);
        }
    }

    /**
     * Get payment statistics
     */
    public function getStatistics(): JsonResponse
    {
        try {
            $totalRevenue = $this->paymentService->getTotalRevenue();
            $pendingPayments = $this->paymentService->getPaymentsByStatus('pending');
            $completedPayments = $this->paymentService->getPaymentsByStatus('completed');
            $failedPayments = $this->paymentService->getPaymentsByStatus('failed');

            $statistics = [
                'total_revenue' => $totalRevenue,
                'pending_count' => $pendingPayments->count(),
                'completed_count' => $completedPayments->count(),
                'failed_count' => $failedPayments->count(),
                'success_rate' => $completedPayments->count() > 0 ?
                    round(($completedPayments->count() / ($completedPayments->count() + $failedPayments->count())) * 100, 2) : 0
            ];

            return $this->successResponse($statistics, 'Payment statistics retrieved successfully');
        } catch (\Exception $e) {
            return $this->errorResponse('Failed to retrieve payment statistics: ' . $e->getMessage(), 500);
        }
    }

    /**
     * Bulk update payment status
     */
    public function bulkUpdateStatus(Request $request): JsonResponse
    {
        try {
            $request->validate([
                'ids' => 'required|array',
                'ids.*' => 'integer|exists:payments,id',
                'status' => 'required|in:pending,completed,failed,refunded',
            ]);

            $updatedCount = 0;
            foreach ($request->ids as $id) {
                $payment = $this->paymentService->updatePayment($id, ['status' => $request->status]);
                if ($payment) {
                    $updatedCount++;
                }
            }

            return $this->successResponse([
                'updated_count' => $updatedCount,
                'total_requested' => count($request->ids),
                'new_status' => $request->status
            ], "Successfully updated {$updatedCount} payments");
        } catch (\Illuminate\Validation\ValidationException $e) {
            return $this->validationErrorResponse($e->errors());
        } catch (\Exception $e) {
            return $this->errorResponse('Failed to bulk update payment status: ' . $e->getMessage(), 500);
        }
    }
}
