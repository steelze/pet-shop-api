<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Concerns\HasUuids;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\HasMany;

class Brand extends Model
{
    use HasFactory, HasUuids;

    protected $guarded = ['id'];

    public function uniqueIds()
    {
        return ['uuid'];
    }

    public function products(): HasMany
    {
        return $this->hasMany(Product::class);
    }
}
