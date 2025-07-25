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
        // 1) Konfig Midtrans
        Config::$serverKey    = config('midtrans.server_key');
        Config::$isProduction = config('midtrans.is_production');
        Config::$isSanitized  = config('midtrans.is_sanitized');
        Config::$is3ds        = config('midtrans.is_3ds');

        // 2) Ambil payload
        $notif = new MidNotif();
        Log::info('Midtrans Notification', (array) $notif);

        $midOrderId  = $notif->order_id;
        $trxStatus   = $notif->transaction_status;
        $fraudStatus = $notif->fraud_status ?? null;

        // 3) Extract Order ID
        if (! preg_match('/_ORD(\d+)_/', $midOrderId, $m)) {
            Log::error("Invalid midOrderId format: $midOrderId");
            return response()->json(['error' => 'bad order id'], 400);
        }
        $orderId = (int)$m[1];

        // 4) Update Order & simpan payment
        $order = Order::findOrFail($orderId);
        $order->update(['payment_status' => $trxStatus]);

        // OrderPayment::updateOrCreate(
        //     ['transaction_id' => $midOrderId],
        //     [
        //         'order_id'           => $order->id,
        //         'snap_token'         => $notif->token ?? null,
        //         'transaction_status' => $trxStatus,
        //         'fraud_status'       => $fraudStatus,
        //         'raw_response'       => json_encode($notif),
        //     ]
        // );

        $payload = [
            'order_id'           => $order->id,
            'transaction_status' => $trxStatus,
            'fraud_status'       => $fraudStatus,
            'raw_response'       => json_encode($notif),
        ];
        // simpan token hanya jika ada (di kebanyakan webhook, token = null)
        if (! empty($notif->token)) {
            $payload['snap_token'] = $notif->token;
        }
        OrderPayment::updateOrCreate(
            ['transaction_id' => $midOrderId],
            $payload
        );

        // 5) Jika sukses, kurangi stok produk lewat orderItems
        if (in_array($trxStatus, ['settlement', 'capture'])) {
            if (! $order->stock_reduced) {
                foreach ($order->orderItems as $item) {
                    if ($item->product) {
                        $item->product->decrement('quantity', $item->quantity);
                    }
                }
                $order->update(['stock_reduced' => true]);
            }
        }

        return response()->json(['code' => 200]);
    }
}
