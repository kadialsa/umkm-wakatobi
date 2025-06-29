<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Midtrans\Config;
use Midtrans\Notification as MidNotif;
use App\Models\Order;
use App\Models\OrderPayment;
use Illuminate\Support\Facades\Log;

class MidtransController extends Controller
{
    public function notificationHandler(Request $request)
    {
        // 1) Set config Midtrans
        Config::$serverKey    = config('midtrans.server_key');
        Config::$isProduction = config('midtrans.is_production');
        Config::$isSanitized  = config('midtrans.is_sanitized');
        Config::$is3ds        = config('midtrans.is_3ds');

        // 2) Ambil notifikasi
        $notif = new MidNotif();

        // 3) (Debug) Log payload
        Log::info('Midtrans Notification', (array) $notif);

        // 4) Parse order ID
        $midOrderId  = $notif->order_id;
        $trxStatus   = $notif->transaction_status;
        $fraudStatus = $notif->fraud_status ?? null;
        $id          = (int) substr($midOrderId, strrpos($midOrderId, '_') + 1);

        // 5) Update Order & simpan payment record
        $order = Order::findOrFail($id);
        $order->update(['payment_status' => $trxStatus]);

        OrderPayment::create([
            'order_id'           => $order->id,
            'transaction_id'     => $midOrderId,
            'transaction_status' => $trxStatus,
            'fraud_status'       => $fraudStatus,
            'raw_response'       => json_encode($notif),
        ]);

        // 6) Return success ke Midtrans
        return response()->json(['code' => 200]);
    }
}
