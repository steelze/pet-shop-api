<?php

namespace App\Models;

use App\Enums\PaymentTypeEnum;
use Illuminate\Database\Eloquent\Concerns\HasUuids;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\HasOne;

class Payment extends Model
{
    use HasFactory, HasUuids;

    protected $guarded = ['id'];

    /**
     * The attributes that should be cast.
     *
     * @var array
     */
    protected $casts = [
        'type' => PaymentTypeEnum::class,
        'details' => 'array',
    ];

    public function uniqueIds()
    {
        return ['uuid'];
    }

    public function order(): HasOne
    {
        return $this->hasOne(Order::class, 'payment_uuid');
    }
}