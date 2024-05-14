<?php

namespace Database\Factories;

use Firebase\JWT\JWT;
use Firebase\JWT\Key;
use Illuminate\Database\Eloquent\Factories\Factory;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Str;

/**
 * @extends \Illuminate\Database\Eloquent\Factories\Factory<\App\Models\Merchant>
 */
class MerchantFactory extends Factory
{
    /**
     * Define the model's default state.
     *
     * @return array<string, mixed>
     */
    public function definition(): array
    {
        // create public token
        $publicKey = Hash::make(Str::random(10));
        $token = JWT::encode(['public_key'=> $publicKey], config('app.jwt_secret'), 'HS256');

        return [
            'public_key' => $publicKey,
            'token' => $token,
        ];
    }
}
