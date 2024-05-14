<?php

namespace App\Enum;

enum PaymentStatusEnum:string
{

    case PENDING = 'pending';
    case PAID = 'paid';
    case EXPIRED = 'expired';
    case FAILED = 'failed';
}
