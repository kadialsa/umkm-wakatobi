<?php

namespace App\Http\Controllers\Store;

use App\Http\Controllers\Controller;
use App\Models\Order;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;

class OrderController extends Controller
{
    /**
     * Tampilkan daftar pesanan milik store.
     */
    public function index(Request $request)
    {
        $storeId = Auth::user()->store()->first()->id;

        // Opsional: filter pencarian via ?search=
        $query = Order::with(['user', 'orderItems.product'])
            ->where('store_id', $storeId)
            ->orderBy('created_at', 'desc');

        if ($search = $request->input('search')) {
            $query->where(function ($q) use ($search) {
                $q->where('id', $search)
                    ->orWhereHas('user', fn($u) => $u->where('name', 'like', '%' . $search . '%'));
            });
        }

        $orders = $query->paginate(10)
            ->appends($request->only('search'));

        return view('store.orders.index', compact('orders'));
    }

    /**
     * Tampilkan detail satu order.
     */
    public function show(Order $order)
    {
        // Pastikan order milik store ini
        if ($order->store_id !== Auth::user()->store()->first()->id) {
            abort(403);
        }

        $order->load(['user', 'orderItems.product']);

        return view('store.orders.show', compact('order'));
    }

    protected function guardOrder(Order $order)
    {
        if ($order->store_id !== Auth::user()->store()->first()->id) {
            abort(403);
        }
    }

    public function ship(Order $order)
    {
        // Pastikan order milik store
        if ($order->store_id !== Auth::user()->store()->first()->id) {
            abort(403);
        }

        if ($order->status !== 'ordered') {
            return back()->with('error', 'Hanya order dengan status "ordered" yang bisa dikirim.');
        }

        // Transaksi: kurangi stok, lalu ubah status
        DB::transaction(function () use ($order) {
            // 1) Kurangi stok tiap produk
            foreach ($order->orderItems as $item) {
                $product = $item->product;

                // Cek ketersediaan stok
                if ($product->quantity < $item->quantity) {
                    throw new \Exception("Stok produk {$product->name} tidak cukup.");
                }

                // Kurangi stok
                $product->decrement('quantity', $item->quantity);
            }

            // 2) Update order status dan timestamp
            $order->update([
                'status'     => 'shipped',
                'shipped_at' => now(),
            ]);
        });

        return back()->with('status', 'Order telah ditandai sebagai shipped dan stok sudah dikurangi.');
    }

    public function deliver(Order $order)
    {
        $this->guardOrder($order);
        if ($order->status !== 'shipped') {
            return back()->with('error', 'Cannot deliver an order not in "shipped" state.');
        }
        $order->update([
            'status'        => 'delivered',
            'delivered_at'  => now(),
        ]);
        return back()->with('status', 'Order marked as delivered.');
    }

    public function complete(Order $order)
    {
        $this->guardOrder($order);
        if ($order->status !== 'delivered') {
            return back()->with('error', 'Cannot complete an order not in "delivered" state.');
        }
        $order->update([
            'status'       => 'completed',
            'completed_at' => now(),
        ]);
        return back()->with('status', 'Order marked as completed.');
    }

    public function cancel(Order $order)
    {
        $this->guardOrder($order);
        if (in_array($order->status, ['delivered', 'completed', 'canceled'])) {
            return back()->with('error', 'Cannot cancel this order.');
        }
        $order->update([
            'status'       => 'canceled',
            'canceled_at'  => now(),
        ]);
        return back()->with('status', 'Order has been canceled.');
    }
}
