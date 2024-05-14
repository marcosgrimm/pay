<?php

namespace App\Enum;

use OpenApi\Annotations as OA;

/**
 * @OA\Schema(
 *     schema="PaymentStatusEnum",
 *     type="string",
 *     description="The status of the payment",
 *     enum={"pending", "paid", "expired", "failed"}
 * )
 *
 * Class representing the status of a payment.
 */
enum PaymentStatusEnum: string
{
    /**
     * @OA\Enum(value="pending", description="Payment is pending")
     */
    case PENDING = 'pending';

    /**
     * @OA\Enum(value="paid", description="Payment is paid")
     */
    case PAID = 'paid';

    /**
     * @OA\Enum(value="expired", description="Payment is expired")
     */
    case EXPIRED = 'expired';

    /**
     * @OA\Enum(value="failed", description="Payment has failed")
     */
    case FAILED = 'failed';
}
