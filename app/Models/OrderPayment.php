<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class OrderPayment extends Model
{
    protected $fillable = [
        'order_id',
        'transaction_id',
        'transaction_status',
        'fraud_status',
        'raw_response',
    ];

    public function order()
    {
        return $this->belongsTo(Order::class);
    }
}
