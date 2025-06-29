<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Transaction extends Model
{
    /** @use HasFactory<\Database\Factories\TransactionFactory> */
    use HasFactory;

    protected $fillable = [
        'order_id',
        'mode',
        'status',
    ];


    public function order()
    {
        return $this->belongsTo(Order::class);
    }

    // Mendapatkan store via order:
    public function store()
    {
        return $this->order->store();
    }
}
