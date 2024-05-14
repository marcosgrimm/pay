<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use OpenApi\Annotations as OA;

/**
 * @OA\Schema(
 *     schema="PaymentMethod",
 *     required={"name", "slug", "fee"},
 *
 *     @OA\Property(
 *         property="name",
 *         type="string",
 *         description="The name of the payment method",
 *     ),
 *     @OA\Property(
 *         property="slug",
 *         type="string",
 *         description="The slug of the payment method",
 *         enum="App\Enum\PaymentMethodSlugEnum"
 *     ),
 *     @OA\Property(
 *         property="fee",
 *         type="number",
 *         format="float",
 *         description="The fee of the payment method that will be retained by SFP Pay",
 *     ),
 * )
 */
class PaymentMethod extends Model
{
}
