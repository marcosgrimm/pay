<?php

namespace App\Services\Auth;

use App\Models\Merchant;
use Firebase\JWT\JWT;

class AuthService
{
    public function getMerchantToken(Merchant $merchant): string
    {
        return JWT::encode(['merchant_id' => $merchant->uuid], config('app.jwt_secret'), 'HS256');
    }
}
