<?php

namespace App\Services\PaymentMethod;

use App\Models\PaymentMethod;
use Illuminate\Support\Str;

abstract class BasePaymentMethodService
{
    private PaymentMethod $paymentMethodModel;

    public function __construct()
    {
        $class = Str::of(static::class)->classBasename()->kebab()->value();
        $this->paymentMethodModel = PaymentMethod::where('slug', $class)->first();
    }

    public function pay(float &$amount): array
    {
        $paymentValidationData = $this->validatePayment($amount);

        $fee = $this->calculateFee($amount);

        $amount -= $fee;

        return array_merge($paymentValidationData, ['fee' => $fee]);
    }

    public function validatePayment(float $amount): array
    {
        // return a boolean and do a random test that 70% of cases if true
        $risk = rand(0, 100);
        $status = match (true) {
            $risk >= 71 && $risk <= 80 => 'pending',
            $risk >= 81 && $risk <= 90 => 'expired',
            $risk >= 91 && $risk <= 100 => 'failed',
            default => 'paid',
        };

        return [
            'risk_percentage' => $risk,
            'status' => $status,
        ];
    }

    public function calculateFee(float $amount): float
    {
        return round($amount * $this->paymentMethodModel->fee, 2);
    }
}
