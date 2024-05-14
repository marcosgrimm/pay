<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Concerns\HasUuids;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Payment extends Model
{
    use HasUuids;
    use HasFactory;

    protected $fillable = [
        'name_client',
        'cpf',
        'description',
        'amount',
        'fee',
        'status',
        'payment_method_slug',
        'paid_at',
        'merchant_id'
    ];
}
