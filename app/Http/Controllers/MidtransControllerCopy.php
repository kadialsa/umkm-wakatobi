<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Midtrans\Config;
use Midtrans\Notification as MidNotif;
use App\Models\Order;
use App\Models\OrderPayment;
use Illuminate\Support\Facades\Log;

class MidtransControllerCopy extends Controller
{
    public function notificationHandler(Request $request)
    {
        // 1) Konfig Midtrans
        Config::$serverKey    = config('midtrans.server_key');
        Config::$isProduction = config('midtrans.is_production');
        Config::$isSanitized  = config('midtrans.is_sanitized');
        Config::$is3ds        = config('midtrans.is_3ds');

        // 2) Ambil payload Notifikasi
        $notif = new MidNotif();
        Log::info('Midtrans Notification', (array) $notif);

        $midOrderId  = $notif->order_id;  // e.g. STORE7_ORD42_1651691524
        $trxStatus   = $notif->transaction_status;
        $fraudStatus = $notif->fraud_status ?? null;

        // Ambil orderId dengan regex:
        if (! preg_match('/_ORD(\d+)_/', $midOrderId, $m)) {
            Log::error("Invalid midOrderId format: $midOrderId");
            return response()->json(['error' => 'bad order id'], 400);
        }
        $orderId = (int)$m[1];

        $order = Order::findOrFail($orderId);
        $order->update(['payment_status' => $trxStatus]);

        OrderPayment::updateOrCreate(
            ['transaction_id' => $midOrderId],
            [
                'order_id'           => $order->id,
                'snap_token'         => $notif->token ?? null,
                'transaction_status' => $trxStatus,
                'fraud_status'       => $fraudStatus,
                'raw_response'       => json_encode($notif),
            ]
        );

        return response()->json(['code' => 200]);
    }
}
