<?php

namespace App\Http\Controllers\Auth;

use App\Http\Controllers\Controller;
use App\Services\Auth\AuthService;
use Firebase\JWT\JWT;
use OpenApi\Annotations as OA;

class AuthController extends Controller
{
    /**
     * @OA\Get(
     *     path="/demo-random-token",
     *     tags={"Demo"},
     *     summary="Generate a random JWT token",
     *     description="This endpoint generates a random JWT token for a randomly selected merchant.",
     *
     *     @OA\Response(
     *         response=200,
     *         description="Successful operation",
     *
     *         @OA\JsonContent(
     *             type="object",
     *
     *             @OA\Property(
     *                 property="token",
     *                 type="string",
     *                 description="The generated JWT token",
     *             ),
     *         )
     *     ),
     *
     *     @OA\Response(
     *         response=404,
     *         description="No merchants found",
     *     ),
     * )
     */
    public function demoRandomToken(AuthService $authService)
    {
        if (config('app.env') !== 'local') {
            return response()->json([], 400);
        }

        $merchant = \App\Models\Merchant::inRandomOrder()->first();
        if (! $merchant) {
            return response()->json([], 404);
        }

        return response()->json([
            'token' => $authService->getMerchantToken($merchant),
        ]);
    }
}
