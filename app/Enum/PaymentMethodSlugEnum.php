<?php

namespace App\Enum;

use OpenApi\Annotations as OA;

/**
 * @OA\Schema(
 *     schema="PaymentMethodSlugEnum",
 *     type="string",
 *     description="The payment method slugs of the payment",
 *     enum={"boleto", "pix", "bank-transfer"}
 * )
 *
 * Class representing the status of a payment.
 */
enum PaymentMethodSlugEnum: string
{
    /**
     * @OA\Enum(value="boleto", description="Payment method slug is boleto")
     */
    case BOLETO = 'boleto';

    /**
     * @OA\Enum(value="pix", description="Payment method slug is pix")
     */
    case PIX = 'pix';

    /**
     * @OA\Enum(value="bank-transfer", description="Payment method slug is bank-transfer")
     */
    case BANK_TRANSFER = 'bank-transfer';

}
