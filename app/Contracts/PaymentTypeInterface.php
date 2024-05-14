<?php

namespace App\Contracts;

interface PaymentInterface
{
    public function pay(float $amount, string $description, string $clientName, string $clientCpf): bool;
}
