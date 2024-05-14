<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Foundation\Auth\User as Authenticatable;
use OpenApi\Annotations as OA;

/**
 * @OA\Schema(
 *     schema="Merchant",
 *     required={"saldo"},
 *
 *     @OA\Property(
 *         property="saldo",
 *         type="number",
 *         format="dec",
 *         description="The balance of the merchant",
 *     ),
 * )
 */
class Merchant extends Authenticatable
{
    use HasFactory;

    protected $casts = [
        'saldo' => 'decimal:2',
    ];

    protected $fillable = [
        'saldo',
    ];
}
