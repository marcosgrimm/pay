<?php

namespace App\Services\Payments;

use App\Enum\PaymentStatusEnum;
use App\Models\Merchant;
use App\Models\Payment;
use App\Services\PaymentMethod\BankTransfer;
use App\Services\PaymentMethod\Boleto;
use App\Services\PaymentMethod\Pix;

class PaymentsService
{

    public function processPayment(Merchant $merchant, $paymentMethodSlug, $nameClient, $clientCpf, $description, $amount  ) : array
    {
        $paymentMethodService = match ($paymentMethodSlug) {
            'pix' => new Pix(),
            'bank-transfer' => new BankTransfer(),
            'boleto' => new Boleto(),
            default => throw new \Exception('Payment method not found'),
        };

        $paymentResult = $paymentMethodService->pay($merchant, $nameClient, $clientCpf, $description, $amount);

        try{
            Payment::create([
                'merchant_id' => $merchant->id,
                'payment_method_slug' => $paymentMethodSlug,
                'description' => $description,
                'amount' => $amount,
                'fee' => $paymentResult['fee'],
                'name_client' => $nameClient,
                'cpf' => $clientCpf,
                'status' => $paymentResult['status'],
                'paid_at' => now(),
            ]);
        } catch (\Exception $e) {
            return [
                'status' => (PaymentStatusEnum::FAILED)->value,
                'risk_percentage' => $paymentResult['risk_percentage'],
            ];
        }

        if ($paymentResult['status'] === (PaymentStatusEnum::PAID)->value) {
            $merchant->saldo += $amount;
            $merchant->save();
        }

        return [
            'status' => $paymentResult['status'],
            'risk_percentage' => $paymentResult['risk_percentage'],
        ];
    }
}
