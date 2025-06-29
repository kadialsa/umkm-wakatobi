<?php

namespace App\Models;

use Illuminate\Contracts\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Brand extends Model
{
    /** @use HasFactory<\Database\Factories\BrandFactory> */
    use HasFactory;

    protected $fillable = [
        'store_id',
        'name',
        'slug',
        'image',
    ];

    protected static function booted()
    {
        static::addGlobalScope('store', function (Builder $builder) {
            if ($storeId = session('current_store_id')) {
                $builder->where('store_id', $storeId);
            }
        });
    }

    public function products()
    {
        return $this->hasMany(Product::class);
    }

    public function store()
    {
        return $this->belongsTo(Store::class);
    }
}
