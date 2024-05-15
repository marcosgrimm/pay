<?php

namespace App\Http\Controllers\Api\V1;

use App\Enum\PaymentStatusEnum;
use App\Http\Requests\StorePaymentRequest;
use App\Models\Merchant;
use App\Models\Payment;
use App\Services\Payments\PaymentsService;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Log;
use OpenApi\Annotations as OA;

class PaymentsController
{
    /**
     * @OA\Get(
     *     path="/api/v1/payments",
     *     tags={"Payments"},
     *     summary="Get list of payments",
     *     description="Returns list of payments",
     *
     *     @OA\Parameter(
     *          name="payment_method_slug",
     *          in="query",
     *          description="Filter payments by payment method slug",
     *          required=false,
     *
     *          @OA\Schema(
     *              type="string"
     *          )
     *      ),
     *
     *      @OA\Parameter(
     *          name="status",
     *          in="query",
     *          description="Filter payments by status",
     *          required=false,
     *
     *          @OA\Schema(
     *              type="string"
     *          )
     *      ),
     *
     *     @OA\Response(
     *          response=200,
     *          description="Successful operation",
     *
     *          @OA\JsonContent(
     *             type="object",
     *
     *             @OA\Property(
     *                 property="payments",
     *                 type="array",
     *
     *                 @OA\Items(ref="#/components/schemas/Payment")
     *             ),
     *
     *             @OA\Property(
     *                 property="meta",
     *                 type="object",
     *                 @OA\Property(
     *                     property="total",
     *                     type="integer",
     *                     description="Total count of payments"
     *                 ),
     *                 @OA\Property(
     *                     property="sum",
     *                     type="number",
     *                     format="float",
     *                     description="Sum of payment amounts"
     *                 )
     *             )
     *          ),
     *      ),
     *
     *     @OA\Response(
     *         response=401,
     *         description="Unauthenticated",
     *     ),
     *     @OA\Response(
     *         response=403,
     *         description="Forbidden"
     *     )
     * )
     */
    public function index(): \Illuminate\Http\JsonResponse
    {
        $paymentMethodSlug = request('payment_method_slug');
        $status = request('status');

        // get all payments. Filters can be used
        $payments = Payment::where('merchant_id', Auth::id())
            ->when($paymentMethodSlug, fn ($query) => $query->where('payment_method_slug', $paymentMethodSlug))
            ->when($status, fn ($query) => $query->where('status', $status))
            ->get();

        return response()->json([
            'payments' => $payments,
            'meta'     => [
                'total' => $payments->count(),
                'sum'   => $payments->sum('amount'),
            ],
        ]);
    }

    /**
     * @OA\Get(
     *     path="/api/v1/payments/{id}",
     *     tags={"Payments"},
     *     summary="Get a specific payment",
     *     description="Returns a specific payment by its id",
     *
     *     @OA\Parameter(
     *         name="id",
     *         in="path",
     *         description="ID of the payment to return",
     *         required=true,
     *
     *         @OA\Schema(
     *             type="integer"
     *         )
     *     ),
     *
     *     @OA\Response(
     *         response=200,
     *         description="Successful operation",
     *
     *         @OA\JsonContent(
     *             type="object",
     *
     *             @OA\Property(
     *                 property="payment",
     *                 ref="#/components/schemas/Payment"
     *             )
     *         ),
     *     ),
     *
     *     @OA\Response(
     *         response=404,
     *         description="Payment not found"
     *     ),
     *     @OA\Response(
     *         response=401,
     *         description="Unauthenticated",
     *     ),
     *     @OA\Response(
     *         response=403,
     *         description="Forbidden"
     *     )
     * )
     */
    public function show($id): \Illuminate\Http\JsonResponse
    {
        return response()->json(['payment' => Payment::findOrFail($id)]);
    }

    /**
     * @OA\Post(
     *     path="/api/v1/payments",
     *     tags={"Payments"},
     *     summary="Creates a new payment",
     *     description="Creates and validates a new payment, charge the fee correspondent to the payment method used and return its status.",
     *
     *     @OA\RequestBody(
     *         description="Payment request data",
     *         required=true,
     *
     *         @OA\JsonContent(
     *             type="object",
     *
     *             @OA\Property(
     *                 property="payment_method_slug",
     *                 type="string",
     *                 description="The slug of the payment method",
     *                 enum="App\Enum\PaymentMethodSlugEnum"
     *             ),
     *             @OA\Property(
     *                 property="name_client",
     *                 type="string",
     *                 description="The name of the client"
     *             ),
     *             @OA\Property(
     *                 property="cpf",
     *                 type="string",
     *                 format="XXX.XXX.XXX-XX",
     *                 description="The CPF of the client"
     *             ),
     *             @OA\Property(
     *                 property="description",
     *                 type="string",
     *                 description="The description of the payment"
     *             ),
     *             @OA\Property(
     *                 property="amount",
     *                 type="number",
     *                 format="float",
     *                 description="The amount of the payment, digits with 0 to 2 decimal places"
     *             ),
     *
     *             @OA\Examples(
     *                 example="boleto",
     *                 value={
     *                   "name_client": "Pedro Paulo",
     *                   "cpf": "759.251.010-30",
     *                   "description": "Bet Grêmio 3 X 0 Palmeiras",
     *                   "amount": 10000000000000,
     *                   "payment_method_slug": "boleto"
     *                 },
     *                 summary="Boleto Payment"),
     *             @OA\Examples(
     *                 example="pix",
     *                 value={
     *                   "name_client": "Paulo Pedro",
     *                   "cpf": "207.645.680-51",
     *                   "description": "Bet Grêmio 7 X 1 Inter",
     *                   "amount": 123.5,
     *                   "payment_method_slug": "pix"
     *                 },
     *                 summary="Pix Payment"),
     *              @OA\Examples(
     *                 example="bank-transfer",
     *                 value={
     *                   "name_client": "João do Pulo",
     *                   "cpf": "561.999.390-69",
     *                   "description": "Bet Grêmio 3 X 0   Flamengo",
     *                   "amount": 100.22,
     *                   "payment_method_slug": "bank-transfer"
     *                 },
     *                 summary="Bank Transfer Payment"),
     *
     *         )
     *     ),
     *
     *     @OA\Response(
     *         response=201,
     *         description="Payment created successfully",
     *
     *         @OA\JsonContent(
     *             type="object",
     *
     *             @OA\Property(
     *                 property="status",
     *                 type="string",
     *                 description="The status of the payment"
     *             ),
     *             @OA\Property(
     *                 property="risk_percentage",
     *                 type="number",
     *                 format="float",
     *                 description="The risk percentage of the payment"
     *             ),
     *             @OA\Property(
     *                 property="message",
     *                 type="string",
     *                 description="The message related to the payment status"
     *             ),
     *             @OA\Property(
     *                 property="status_code",
     *                 type="integer",
     *                 description="The HTTP status code"
     *             ),
     *         ),
     *     ),
     *
     *     @OA\Response(
     *         response=400,
     *         description="Payment creation failed",
     *     ),
     *     @OA\Response(
     *         response=401,
     *         description="Unauthenticated",
     *     ),
     *     @OA\Response(
     *         response=403,
     *         description="Forbidden"
     *     )
     * )
     */
    public function store(StorePaymentRequest $request, PaymentsService $paymentsService): \Illuminate\Http\JsonResponse
    {
        // get all request data
        $attributes = $request->safe();

        /** @var Merchant $merchant */
        $merchant = Auth::user();

        try {
            $result = $paymentsService->processPayment($merchant, $attributes['payment_method_slug'], $attributes['name_client'], $attributes['cpf'], $attributes['description'], $attributes['amount']);
        } catch (\Exception $e) {
            Log::critical('UNEXPECTED_ERROR_PAYMENT_NOT_PROCESSED', ['error' => $e->getMessage(), 'e' => $e]);
            return response()->json(['message' => 'Payment could not be processed'], 500);
        }

        $response = [
            'status'          => $result['status'],
            'risk_percentage' => $result['risk_percentage'] ?? null,
            'message'         => $this->getStoreErrorMessage($result['status']),
            'status_code'     => $result['status'] === (PaymentStatusEnum::PAID)->value ? 201 : 400,
        ];

        return response()->json($response, $response['status_code']);
    }

    /**
     * @param string $status
     * @return string
     */
    private function getStoreErrorMessage(string $status): string
    {
        return match ($status) {
            (PaymentStatusEnum::PENDING)->value => 'Payment is pending.',
            (PaymentStatusEnum::EXPIRED)->value => 'Payment is expired.',
            (PaymentStatusEnum::FAILED)->value => 'Payment failed.',
            default => 'Payment Succeeded.',
        };
    }
}
