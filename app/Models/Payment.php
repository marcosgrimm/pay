<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Concerns\HasUuids;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use OpenApi\Annotations as OA;

/**
 * @OA\Schema(
 *     schema="Payment",
 *     required={"name_client", "cpf", "description", "amount", "fee", "status", "payment_method_slug", "paid_at", "merchant_id"},
 *
 *     @OA\Property(
 *         property="name_client",
 *         type="string",
 *         description="The name of the client",
 *     ),
 *     @OA\Property(
 *         property="cpf",
 *         type="string",
 *         description="The CPF of the client",
 *     ),
 *     @OA\Property(
 *         property="description",
 *         type="string",
 *         description="The description of the payment",
 *     ),
 *     @OA\Property(
 *         property="amount",
 *         type="number",
 *         format="float",
 *         description="The amount of the payment",
 *     ),
 *     @OA\Property(
 *         property="fee",
 *         type="float",
 *         format="float,0:2",
 *         description="The fee of the payment",
 *     ),
 *     @OA\Property(
 *         property="status",
 *         type="string",
 *         description="The status of the payment",
 *         enum="App\Enum\PaymentStatusEnum"
 *     ),
 *     @OA\Property(
 *         property="payment_method_slug",
 *         type="string",
 *         description="The slug of the payment method",
 *         enum="App\Enum\PaymentMethodSlugEnum"
 *     ),
 *     @OA\Property(
 *         property="paid_at",
 *         type="string",
 *         format="date-time",
 *         description="The date and time when the payment was made",
 *     ),
 *     @OA\Property(
 *         property="merchant_id",
 *         type="integer",
 *         description="The ID of the merchant",
 *     ),
 * )
 */
class Payment extends Model
{
    use HasFactory;
    use HasUuids;

    protected $fillable = [
        'name_client',
        'cpf',
        'description',
        'amount',
        'fee',
        'status',
        'payment_method_slug',
        'paid_at',
        'merchant_id',
    ];
}
