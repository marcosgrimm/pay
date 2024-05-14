<?php

namespace Database\Seeders;

use App\Models\PaymentMethod;
use Illuminate\Database\Seeder;

class PaymentMethodsSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        PaymentMethod::create([
            'name' => 'Bank Transfer',
            'slug' => 'bank-transfer',
            'fee' => 0.04,
        ]);

        PaymentMethod::create([
            'name' => 'Boleto',
            'slug' => 'boleto',
            'fee' => 0.02,
        ]);

        PaymentMethod::create([
            'name' => 'pix',
            'slug' => 'pix',
            'fee' => 0.035,
        ]);
    }
}
