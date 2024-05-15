<?php

namespace Tests\Unit;

use App\Enum\PaymentStatusEnum;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;
use App\Models\Merchant;
use App\Models\Payment;
use App\Services\Payments\PaymentsService;

class PaymentServiceTest extends TestCase
{
    use RefreshDatabase;

    public function setUp(): void
    {
        parent::setUp();
        $this->seed('Database\\Seeders\\PaymentMethodsSeeder');
    }


    public function test_it_fails_to_create_a_payment_with_invalid_payment_method()
    {
        $paymentService = new PaymentsService();
        $merchant = Merchant::factory()->create();
        $payment = Payment::factory()->make([
            'merchant_id' => $merchant->id,
        ]);

        $return = $paymentService->processPayment($merchant, 'invalid', $payment->name_client, $payment->cpf, $payment->description, $payment->amount);

        $this->assertEquals([
            'status' => (PaymentStatusEnum::FAILED)->value,
            'risk_percentage' => -1,
        ], $return);

    }

    public function test_it_fails_to_create_a_payment_with_null_cpf()
    {
        $paymentService = new PaymentsService();
        $merchant = Merchant::factory()->create();
        $payment = Payment::factory()->make([
            'merchant_id' => $merchant->id,
        ]);

        $return = $paymentService->processPayment(
            $merchant,
            $payment->payment_method_slug,
            $payment->name_client,
            null,
            $payment->description,
            $payment->amount
        );

        $this->assertEquals([
            'status' => (PaymentStatusEnum::FAILED)->value,
            'risk_percentage' => -1,
        ], $return);

    }

}
