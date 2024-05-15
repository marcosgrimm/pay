<?php

namespace Database\Factories;

use App\Enum\PaymentMethodSlugEnum;
use App\Enum\PaymentStatusEnum;
use App\Models\Merchant;
use Faker\Provider\pt_BR\Person as FakerPerson;
use Illuminate\Database\Eloquent\Factories\Factory;

;
/**
 * @extends \Illuminate\Database\Eloquent\Factories\Factory<\App\Models\Merchant>
 */
class PaymentFactory extends Factory
{
    /**
     * Define the model's default state.
     *
     * @return array<string, mixed>
     */
    public function definition(): array
    {
        $this->faker->addProvider(new FakerPerson($this->faker));

        return [
            'merchant_id' => Merchant::factory()->create()->id,
            'cpf' => $this->faker->cpf(true),
            'name_client' => $this->faker->firstName() . ' ' . $this->faker->lastName,
            'amount' => $this->faker->randomFloat(2, 1, 1000),
            'description' => $this->faker->text(30),
            'payment_method_slug' => PaymentMethodSlugEnum::cases()[rand(0, count(PaymentMethodSlugEnum::cases()) - 1)]->value,
            'fee' => $this->faker->randomFloat(2, 0, 10),
            'status' => PaymentStatusEnum::cases()[rand(0, count(PaymentStatusEnum::cases()) - 1)]->value,
        ];
    }

    public function pix(): Factory
    {
        return $this->state(function (array $attributes) {
            return [
                'payment_method_slug' => 'pix',
            ];
        });
    }
    public function boleto(): Factory
    {
        return $this->state(function (array $attributes) {
            return [
                'payment_method_slug' => 'boleto',
            ];
        });
    }
    public function bank_transfer(): Factory
    {
        return $this->state(function (array $attributes) {
            return [
                'payment_method_slug' => 'bank-transfer',
            ];
        });
    }
}
