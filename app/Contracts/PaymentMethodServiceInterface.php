<?php

namespace App\Contracts;

interface PaymentMethodServiceInterface
{
    /**
     * @param float $amount
     * @return array
     */
    public function pay(float &$amount): array;
}
