<?php

namespace Feature\Payments;

// use Illuminate\Foundation\Testing\RefreshDatabase;
use App\Enum\PaymentMethodSlugEnum;
use App\Models\Merchant;
use App\Models\Payment;
use App\Models\PaymentMethod;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Faker\Provider\pt_BR\Person as FakerPerson;
use Illuminate\Foundation\Testing\WithFaker;
use Tests\TestCase;

class PaymentsTest extends TestCase
{
    use RefreshDatabase;
    use WithFaker;

    private Merchant $merchant;

    public function setUp(): void
    {
        parent::setUp();
        $this->seed();
    }

    private function setUpMerchantAndMockJwtMiddleware(): void
    {
        Merchant::factory(5)->create();

        $this->merchant = Merchant::inRandomOrder()->first();

        $this->faker->addProvider(new FakerPerson($this->faker));

        // mock JWTMiddleware
        $this->mock(\App\Http\Middleware\JwtAuthMiddleware::class, function ($mock) {
            $mock->shouldReceive('handle')->andReturnUsing(function ($request, $next) {
                $request->merge(['merchant_id' => $this->merchant->id]);
                $this->be($this->merchant);
                return $next($request);
            });
        });
    }

    /**
     * A basic test example.
     */
    public function test_it_returns_unauthorized_response_on_payment_creation_when_no_token(): void
    {
        $response = $this->post('/api/v1/payments');

        $response->assertStatus(401);
    }

    public function test_it_list_all_payments(): void
    {
        $this->setUpMerchantAndMockJwtMiddleware();

        Payment::factory(3)->pix()->create(['merchant_id' => $this->merchant->id]);
        Payment::factory(3)->boleto()->create(['merchant_id' => $this->merchant->id]);
        Payment::factory(3)->bank_transfer()->create(['merchant_id' => $this->merchant->id]);

        $response = $this->get('/api/v1/payments');

        $response->assertJsonStructure([
            'payments' =>
                [
                    '*' =>
                        [
                            'id',
                            'name_client',
                            'cpf',
                            'description',
                            'amount',
                            'fee',
                            'status',
                            'payment_method_slug',
                            'paid_at',
                            'merchant_id'
                        ]
                ],
            'meta'     => [
                'total',
                'sum'
            ]
        ]);
        $response->assertJsonCount(9, 'payments');
        $response->assertJsonFragment(['total' => 9]);
        $response->assertStatus(200);

        foreach (PaymentMethodSlugEnum::cases() as $slug) {
            $response = $this->get('/api/v1/payments?payment_method_slug=' . $slug->value);
            $response->assertJsonCount(3, 'payments');
            $response->assertJsonFragment(['total' => 3]);
            $response->assertStatus(200);
        }
    }

    public function test_it_list_a_payments(): void
    {
        $this->setUpMerchantAndMockJwtMiddleware();
        $payment = Payment::factory()->create(['merchant_id' => $this->merchant->id]);
        Payment::factory(2)->create(['merchant_id' => $this->merchant->id]);

        $response = $this->get("/api/v1/payments/{$payment->id}");

        $response->assertJsonStructure([
            'payment' => [
                'id',
                'name_client',
                'cpf',
                'description',
                'amount',
                'fee',
                'status',
                'payment_method_slug',
                'paid_at',
                'merchant_id'
            ]
        ]);
        $response->assertJsonFragment(['id' => $payment->id]);

        $response->assertStatus(200);
    }


    public function test_it_returns_empty_when_payment_not_found(): void
    {
        $this->setUpMerchantAndMockJwtMiddleware();
        $payment = Payment::factory()->create(['merchant_id' => $this->merchant->id]);
        $invalidId = $payment->id . '123';
        $response = $this->get("/api/v1/payments/{$invalidId}");
        $response->assertNotFound();
    }

    /**
     * A basic test example.
     */
    private function test_it_creates_a_payment_record($slug): void
    {
        $this->setUpMerchantAndMockJwtMiddleware();

        $payment = Payment::factory()->make(['merchant_id' => $this->merchant->id, 'payment_method_slug' => $slug]);

        $paymentMethod = PaymentMethod::where('slug', $payment->payment_method_slug)->first();
        $feeAmount = round($payment->amount * $paymentMethod->fee, 2);

        $response = $this->post('/api/v1/payments', $payment->toArray());

        $this->assertContains($response->getStatusCode(), array(201, 400));

        $this->assertDatabaseHas('payments', [
            'merchant_id'         => $payment->merchant_id,
            'cpf'                 => $payment->cpf,
            'fee'                 => $feeAmount,
            'name_client'         => $payment->name_client,
            'description'         => $payment->description,
            'payment_method_slug' => $payment->payment_method_slug,
        ]);
    }

    public function test_creates_a_pix_payment_record(): void
    {
        $this->test_it_creates_a_payment_record((PaymentMethodSlugEnum::PIX)->value);
    }

    public function test_creates_a_boleto_payment_record(): void
    {
        $this->test_it_creates_a_payment_record((PaymentMethodSlugEnum::BOLETO)->value);
    }

    public function test_creates_a_bank_transfer_payment_record(): void
    {
        $this->test_it_creates_a_payment_record((PaymentMethodSlugEnum::BANK_TRANSFER)->value);
    }

    public function test_it_fails_to_create_a_payment_record_because_of_validation(): void
    {
        $this->setUpMerchantAndMockJwtMiddleware();

        // invalid payment method slug
        $payment = Payment::factory()->make([
            'merchant_id'         => $this->merchant->id,
            'name_client'         => 123,
            'cpf'                 => '1234567890',
            'payment_method_slug' => 'invalid',
            'amount'              => 'abc',
            'description'         => 456,
        ]);

        $response = $this->post('/api/v1/payments', $payment->toArray());

        $response->assertStatus(422);

        $response->assertJsonValidationErrors(['cpf', 'payment_method_slug', 'amount', 'name_client', 'description']);

        // test with invalid cpf format and invalid amount
        $payment = Payment::factory()->make([
            'merchant_id' => $this->merchant->id,
            'cpf'         => '31368407048',
            'amount'      => 40.1234,
        ]);

        $response = $this->post('/api/v1/payments', $payment->toArray());

        $response->assertStatus(422);

        $response->assertJsonValidationErrors(['cpf', 'amount']);
    }

    public function test_it_fails_to_create_a_payment_record_because_of_a_critical_error(): void
    {
        $this->setUpMerchantAndMockJwtMiddleware();

        // invalid payment method slug
        $payment = Payment::factory()->make([
            'merchant_id'         => $this->merchant->id,
            'name_client'         => 123,
            'cpf'                 => '1234567890',
            'payment_method_slug' => 'invalid',
            'amount'              => 'abc',
            'description'         => 456,
        ]);

        $response = $this->post('/api/v1/payments', $payment->toArray());

        $response->assertStatus(422);

        $response->assertJsonValidationErrors(['cpf', 'payment_method_slug', 'amount', 'name_client', 'description']);

        // test with invalid cpf format and invalid amount
        $payment = Payment::factory()->make([
            'merchant_id' => $this->merchant->id,
            'cpf'         => '31368407048',
            'amount'      => 40.1234,
        ]);

        $response = $this->post('/api/v1/payments', $payment->toArray());

        $response->assertStatus(422);

        $response->assertJsonValidationErrors(['cpf', 'amount']);
    }
}
