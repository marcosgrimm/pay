<?php

namespace App\Http\Middleware;

use App\Models\Merchant;
use Closure;
use Exception;
use Firebase\JWT\JWT;
use Firebase\JWT\Key;
use Illuminate\Support\Facades\Auth;


class JwtAuthMiddleware
{
    public function handle($request, Closure $next, $guard = null)
    {
        // get token from header
        $token = $request->header('Authorization');
        if(!$token) {
            // Unauthorized response if token not there
            return response()->json([
                'error' => 'Token not provided.'
            ], 401);
        }

        try {
            // remove Bearer from token
            $token = substr($token, 7);

            // decode token
            $decodedToken = JWT::decode($token, new Key(config('app.jwt_secret'), 'HS256'));

            // get credentials from token
            $publicKey = $decodedToken->public_key;

            $merchant = Merchant::where('public_key', $publicKey)->firstOrFail();

            //define Auth::user() to be the merchant
            Auth::setUser($merchant);
        } catch(Exception $e) {
            return response()->json([
                'error' => 'Unauthorized access.'
            ], 400);
        }


        return $next($request);
    }
}
