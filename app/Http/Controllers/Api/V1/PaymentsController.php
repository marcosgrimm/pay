<?php

namespace App\Http\Controllers\Api\V1;

use App\Enum\PaymentStatusEnum;
use App\Http\Controllers\Controller;
use App\Http\Requests\StorePaymentRequest;
use App\Models\Merchant;
use App\Models\Payment;
use App\Services\Payments\PaymentsService;
use Illuminate\Support\Facades\Auth;

class PaymentsController extends Controller
{
    public function index()
    {
        // get all payments filter by date
        $payments = Payment::where('merchant_id', Auth::id())
            ->when(request('payment_method_slug'), fn($query) => $query->where('payment_method_slug', request('payment_method_slug')))
            ->when(request('status'), fn($query) => $query->where('status', request('status')))
            ->get();

        // filter payment_method_slug
        if ($paymentMethodSlug = request('payment_method_slug')) {
            $payments = $payments->where('payment_method_slug', $paymentMethodSlug);
        }

        return response()->json([
            'payments' => $payments,
            'meta'     => [
                'total' => $payments->count(),
                'sum'   => $payments->sum('amount'),
            ],
        ]);
    }

    public function show($id)
    {
        return response()->json(['payment' => Payment::findOrFail($id)]);
    }

    public function store(StorePaymentRequest $request, PaymentsService $paymentsService)
    {
        $attributes = $request->safe();

        /** @var Merchant $merchant */
        $merchant = Auth::user();

        // pass each attribute to processPayment method
        $result = $paymentsService->processPayment($merchant, $attributes['payment_method_slug'], $attributes['name_client'], $attributes['cpf'], $attributes['description'], $attributes['amount']);

        $response = [
            'status'          => $result['status'],
            'risk_percentage' => $result['risk_percentage'] ?? null,
            'message'         => $this->getStoreErrorMessage($result['status']),
            'status_code'     => $result['status'] === (PaymentStatusEnum::PAID)->value ? 201 : 400,
        ];

        return response()->json($response, $response['status_code']);
    }

    private function getStoreErrorMessage($status)
    {
        return match ($status) {
            (PaymentStatusEnum::PENDING)->value => 'Payment is pending.',
            (PaymentStatusEnum::EXPIRED)->value => 'Payment is expired.',
            (PaymentStatusEnum::FAILED)->value => 'Payment failed.',
            default => 'Payment Succeeded.',
        };
    }
}
