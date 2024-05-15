<?php

namespace App\Services\PaymentMethod;

use App\Contracts\PaymentMethodServiceInterface;

class BankTransfer extends BasePaymentMethodService implements PaymentMethodServiceInterface
{
    protected function validatePayment(float $amount): array
    {
        // return a boolean and do a random test that 70% of cases if true
        $risk = rand(0, 100);
        $status = match (true) {
            $risk >= 71 && $risk <= 75 => 'pending',
            $risk >= 81 && $risk <= 90 => 'expired',
            $risk >= 91 && $risk <= 100 => 'failed',
            default => 'paid',
        };

        return [
            'risk_percentage' => $risk,
            'status' => $status,
        ];
    }
}
