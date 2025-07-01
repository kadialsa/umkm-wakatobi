<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Order extends Model
{
    use HasFactory;

    /**
     * Semua kolom selain primary key boleh di‐mass‐assign.
     */
    protected $guarded = ['id'];

    /**
     * Casting untuk field boolean dan timestamp custom.
     */
    protected $casts = [
        'is_shipping_different' => 'boolean',
        'shipped_at'            => 'datetime',
        'delivered_at'          => 'datetime',
        'completed_at'          => 'datetime',
        'canceled_at'           => 'datetime',
    ];

    /**
     * Shortcuts untuk date fields agar otomatis di‐set
     * when setting status via update([...])
     */
    public function markShipped()
    {
        $this->update([
            'status'     => 'shipped',
            'shipped_at' => now(),
        ]);
    }

    public function markDelivered()
    {
        $this->update([
            'status'       => 'delivered',
            'delivered_at' => now(),
        ]);
    }

    public function markCompleted()
    {
        $this->update([
            'status'       => 'completed',
            'completed_at' => now(),
        ]);
    }

    public function markCanceled()
    {
        $this->update([
            'status'      => 'canceled',
            'canceled_at' => now(),
        ]);
    }

    /**
     * Apply global scope to only show orders for current store.
     */
    protected static function booted()
    {
        static::addGlobalScope('store', function (Builder $builder) {
            if ($storeId = session('current_store_id')) {
                $builder->where('store_id', $storeId);
            }
        });
    }

    /* -----------------------------------------------------------------
     |  Relationships
     |-----------------------------------------------------------------
     */

    public function user()
    {
        return $this->belongsTo(User::class);
    }

    public function store()
    {
        return $this->belongsTo(Store::class);
    }

    public function orderItems()
    {
        return $this->hasMany(OrderItem::class);
    }

    public function payments()
    {
        return $this->hasMany(OrderPayment::class);
    }

    public function transaction()
    {
        return $this->hasOne(Transaction::class);
    }

    public function trackings()
    {
        return $this->hasMany(OrderTracking::class);
    }
}
