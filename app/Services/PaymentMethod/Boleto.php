<?php

namespace App\Services\PaymentMethod;

use App\Contracts\PaymentMethodServiceInterface;

class Boleto extends BasePaymentMethodService implements PaymentMethodServiceInterface
{
    protected function validatePayment(float $amount): array
    {
        // return a boolean and do a random test that 70% of cases if true
        $risk = rand(0, 100);
        $status = match (true) {
            $risk >= 71 && $risk <= 90 => 'pending',
            $risk >= 91 && $risk <= 95 => 'expired',
            $risk >= 96 && $risk <= 100 => 'failed',
            default => 'paid',
        };

        return [
            'risk_percentage' => $risk,
            'status' => $status,
        ];
    }
}
