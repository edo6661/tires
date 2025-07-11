<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Services\PaymentServiceInterface;
use App\Http\Requests\PaymentRequest;
use Illuminate\Http\Request;

class PaymentController extends Controller
{
    public function __construct(protected PaymentServiceInterface $paymentService)
    {
        
    }

    public function index()
    {
        $payments = $this->paymentService->getPaginatedPayments(15);
        return view('admin.payment.index', compact('payments'));
    }

    public function create()
    {
        return view('admin.payment.create');
    }

    public function store(PaymentRequest $request)
    {
        try {
            $this->paymentService->createPayment($request->validated());
            return redirect()->route('admin.payment.index')
                ->with('success', 'Pembayaran berhasil dibuat.');
        } catch (\Exception $e) {
            return redirect()->back()
                ->with('error', 'Terjadi kesalahan: ' . $e->getMessage())
                ->withInput();
        }
    }

    public function show(int $id)
    {
        $payment = $this->paymentService->findPayment($id);
        if (!$payment) {
            return redirect()->route('admin.payment.index')
                ->with('error', 'Pembayaran tidak ditemukan.');
        }
        return view('admin.payment.show', compact('payment'));
    }

    public function edit(int $id)
    {
        $payment = $this->paymentService->findPayment($id);
        if (!$payment) {
            return redirect()->route('admin.payment.index')
                ->with('error', 'Pembayaran tidak ditemukan.');
        }
        return view('admin.payment.edit', compact('payment'));
    }

    public function update(PaymentRequest $request, int $id)
    {
        try {
            $payment = $this->paymentService->updatePayment($id, $request->validated());
            if (!$payment) {
                return redirect()->route('admin.payment.index')
                    ->with('error', 'Pembayaran tidak ditemukan.');
            }
            return redirect()->route('admin.payment.index')
                ->with('success', 'Pembayaran berhasil diperbarui.');
        } catch (\Exception $e) {
            return redirect()->back()
                ->with('error', 'Terjadi kesalahan: ' . $e->getMessage())
                ->withInput();
        }
    }

    public function destroy(int $id)
    {
        try {
            $deleted = $this->paymentService->deletePayment($id);
            if (!$deleted) {
                return redirect()->route('admin.payment.index')
                    ->with('error', 'Pembayaran tidak ditemukan.');
            }
            return redirect()->route('admin.payment.index')
                ->with('success', 'Pembayaran berhasil dihapus.');
        } catch (\Exception $e) {
            return redirect()->back()
                ->with('error', 'Terjadi kesalahan: ' . $e->getMessage());
        }
    }

    public function getByStatus(Request $request)
    {
        try {
            $request->validate([
                'status' => 'required|in:pending,completed,failed,refunded',
            ]);

            $payments = $this->paymentService->getPaymentsByStatus($request->input('status'));
            return response()->json(['payments' => $payments]);
        } catch (\Exception $e) {
            return response()->json(['error' => 'Terjadi kesalahan: ' . $e->getMessage()], 500);
        }
    }

    public function getByUser(Request $request)
    {
        try {
            $request->validate([
                'user_id' => 'required|integer|exists:users,id',
            ]);

            $payments = $this->paymentService->getPaymentsByUser($request->input('user_id'));
            return response()->json(['payments' => $payments]);
        } catch (\Exception $e) {
            return response()->json(['error' => 'Terjadi kesalahan: ' . $e->getMessage()], 500);
        }
    }

    public function getByReservation(Request $request)
    {
        try {
            $request->validate([
                'reservation_id' => 'required|integer|exists:reservations,id',
            ]);

            $payments = $this->paymentService->getPaymentsByReservation($request->input('reservation_id'));
            return response()->json(['payments' => $payments]);
        } catch (\Exception $e) {
            return response()->json(['error' => 'Terjadi kesalahan: ' . $e->getMessage()], 500);
        }
    }

    public function getTotalRevenue()
    {
        try {
            $revenue = $this->paymentService->getTotalRevenue();
            return response()->json(['total_revenue' => $revenue]);
        } catch (\Exception $e) {
            return response()->json(['error' => 'Terjadi kesalahan: ' . $e->getMessage()], 500);
        }
    }

    public function processPayment(Request $request)
    {
        try {
            $request->validate([
                'payment_id' => 'required|integer|exists:payments,id',
                'payment_data' => 'required|array',
                'payment_data.transaction_id' => 'nullable|string',
            ]);

            $processed = $this->paymentService->processPayment(
                $request->input('payment_id'),
                $request->input('payment_data')
            );

            if (!$processed) {
                return response()->json(['error' => 'Gagal memproses pembayaran.'], 500);
            }

            return response()->json(['success' => 'Pembayaran berhasil diproses.']);
        } catch (\Exception $e) {
            return response()->json(['error' => 'Terjadi kesalahan: ' . $e->getMessage()], 500);
        }
    }
}
