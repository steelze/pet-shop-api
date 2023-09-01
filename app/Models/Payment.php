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

    /**
     * The attributes that are not mass assignable.
     *
     * @phpcsSuppress SlevomatCodingStandard.TypeHints.PropertyTypeHint.MissingNativeTypeHint
     * @var array<string>
     */
    protected $guarded = ['id'];

    /**
     * The attributes that should be cast.
     *
     * @phpcsSuppress SlevomatCodingStandard.TypeHints.PropertyTypeHint.MissingNativeTypeHint
     * @var array<string, string>
     */
    protected $casts = [
        'type' => PaymentTypeEnum::class,
        'details' => 'array',
    ];

    /**
     *
     * @return array<string>
     */
    public function uniqueIds(): array
    {
        return ['uuid'];
    }

    public function order(): HasOne
    {
        return $this->hasOne(Order::class, 'payment_uuid');
    }
}
