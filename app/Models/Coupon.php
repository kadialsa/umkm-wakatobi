<?php

namespace App\Models;

use Illuminate\Contracts\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Coupon extends Model
{
    /** @use HasFactory<\Database\Factories\CouponFactory> */
    use HasFactory;

    protected $fillable = [
        'store_id',
        'code',
        'type',
        'value',
        'cart_value',
        'expiry_date',
    ];

    protected static function booted()
    {
        static::addGlobalScope('store', function (Builder $builder) {
            if ($storeId = session('current_store_id')) {
                $builder->where('store_id', $storeId);
            }
        });
    }

    public function store()
    {
        return $this->belongsTo(Store::class);
    }
}
