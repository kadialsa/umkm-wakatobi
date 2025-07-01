<?php

namespace App\Http\Controllers;

use App\Models\Coupon;
use App\Models\Address;
use App\Models\NewAddress;
use App\Models\Order;
use App\Models\OrderItem;
use App\Models\Transaction;
use Carbon\Carbon;
use Illuminate\Container\Attributes\Auth as AttributesAuth;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Session;
use Surfsidemedia\Shoppingcart\Facades\Cart;
use Illuminate\Support\Facades\Log;

// midtrans
use Illuminate\Http\Request;
use Midtrans\Config;
use Midtrans\Snap;



class CartController extends Controller
{
    public function index()
    {
        $items = Cart::instance('cart')->content();
        return view('cart', compact('items'));
    }

    public function add_to_cart(Request $request)
    {
        Cart::instance('cart')
            ->add($request->id, $request->name, $request->quantity, $request->price)
            ->associate('App\Models\Product');
        return redirect()->back();
    }

    public function increase_cart_quntity($rowId)
    {
        $product = Cart::instance('cart')->get($rowId);
        $qty = $product->qty + 1;
        Cart::instance('cart')->update($rowId, $qty);
        return redirect()->back();
    }

    public function decrease_cart_quantity($rowId)
    {
        $product = Cart::instance('cart')->get($rowId);
        $qty = $product->qty - 1;
        Cart::instance('cart')->update($rowId, $qty);
        return redirect()->back();
    }

    public function remove_item($rowId)
    {
        Cart::instance('cart')->remove($rowId);
        return redirect()->back();
    }

    public function empty_cart()
    {
        Cart::instance('cart')->destroy();
        return redirect()->back();
    }

    public function apply_coupon_code(Request $request)
    {
        $coupon_code = $request->coupon_code;
        if (isset($coupon_code)) {
            $coupon = Coupon::where('code', $coupon_code)->where('expiry_date', '>=', Carbon::today())
                ->where('cart_value', '<=', Cart::instance('cart')->subtotal())->first();
            if (!$coupon) {
                return redirect()->back()->with('error', 'Invalid coupon code!');
            } else {
                Session::put('coupon', [
                    'code' => $coupon->code,
                    'type' => $coupon->type,
                    'value' => $coupon->value,
                    'cart_value' => $coupon->cart_value
                ]);
                $this->calculateDiscount();
                return redirect()->back()->with('success', 'Coupon has been applied successfully!');
            }
        } else {
            return redirect()->back()->with('error', 'Invalid coupon code!');
        }
    }

    public function calculateDiscount()
    {
        $discount = 0;
        if (Session::has('coupon')) {
            if (Session::get('coupon')['type'] == 'fixed') {
                $discount = Session::get('coupon')['value'];
            } else {
                $discount = (Cart::instance('cart')->subtotal() * Session::get('coupon')['value']) / 100;
            }
            $subtotalAfterDiscount = Cart::instance('cart')->subtotal() - $discount;
            $taxAfterDicount = ($subtotalAfterDiscount * config('cart.tax')) / 100;
            $totalAfterDiscount = $subtotalAfterDiscount + $taxAfterDicount;

            Session::put('discounts', [
                'discount' => number_format(floatval($discount), 2, '.', ''),
                'subtotal' => number_format(floatval($subtotalAfterDiscount), 2, '.', ''),
                'tax' => number_format(floatval($taxAfterDicount), 2, '.', ''),
                'total' => number_format(floatval($totalAfterDiscount), 2, '.', ''),
            ]);
        }
    }

    public function remove_coupon_code()
    {
        Session::forget('coupon');
        Session::forget('discounts');
        return back()->with('success', 'Coupon has been removed!');
    }

    public function Checkout()
    {
        if (!Auth::check()) {
            return redirect()->route('login');
        }

        // dd(Auth::user()->id);

        // $address = Address::where('user_id', Auth::user()->id)->where('isdefault', 1)->first();
        $address = NewAddress::where('user_id', Auth::user()->id)
            ->first();

        // dd($address);

        return view('checkout', compact('address'));
    }

    public function place_an_order(Request $request)
    {
        $userId   = Auth::id();
        $orderIds = [];
        $snapTokens = [];

        // --- Validasi dasar untuk kurir & ongkir ---
        $request->validate([
            'shipping_service' => 'required|string',
            'shipping_cost'    => 'required|numeric',
        ]);

        // 1) Tentukan alamat (existing atau baru)
        if ($request->filled('address_id')) {
            $address = NewAddress::where('user_id', $userId)
                ->findOrFail($request->input('address_id'));
            $isDifferent = false;
        } else {
            $validated = $request->validate([
                'destination_id'   => 'required|integer',
                'province_name'    => 'required|string|max:100',
                'city_name'        => 'required|string|max:100',
                'district_name'    => 'required|string|max:100',
                'subdistrict_name' => 'required|string|max:100',
                'full_address'     => 'required|string|max:500',
                'zip_code'         => 'required|string|digits_between:5,6',
                'phone_number'     => 'required|string|digits_between:10,15',
                'recipient_name'   => 'required|string|max:100',
            ]);

            $address = NewAddress::create([
                'user_id'        => $userId,
                'destination_id' => $validated['destination_id'],
                'province'       => $validated['province_name'],
                'city'           => $validated['city_name'],
                'district'       => $validated['district_name'],
                'subdistrict'    => $validated['subdistrict_name'],
                'full_address'   => $validated['full_address'],
                'zip_code'       => $validated['zip_code'],
                'phone'          => $validated['phone_number'],
                'recipient_name' => $validated['recipient_name'],
            ]);

            // Cek lagi
            $isDifferent = true;
        }

        // 2) Buat Order per toko
        $groups = Cart::instance('cart')
            ->content()
            ->groupBy(fn($i) => $i->model->store_id);

        foreach ($groups as $storeId => $items) {
            $sub     = $items->sum(fn($i) => $i->price * $i->qty);
            $taxRate = config('cart.tax') / 100;
            $tax     = round($sub * $taxRate, 2);
            $shippingCost = $request->input('shipping_cost');
            $total = round($sub + $tax + $shippingCost, 2);

            $order = Order::create([
                'user_id'                => $userId,
                'store_id'               => $storeId,
                'subtotal'               => $sub,
                'discount'               => 0,
                'tax'                    => $tax,
                'total'                  => $total,
                // kurir & ongkir
                'shipping_service'       => $request->input('shipping_service'),
                'shipping_cost'          => $request->input('shipping_cost'),
                // alamat pengiriman
                'destination_id'         => $address->destination_id,
                'province'               => $address->province,
                'city'                   => $address->city,
                'district'               => $address->district,
                'subdistrict'            => $address->subdistrict,
                'full_address'           => $address->full_address,
                'zip_code'               => $address->zip_code,
                'phone'                  => $address->phone,
                'recipient_name'         => $address->recipient_name,
                // flag alamat berbeda
                'is_shipping_different'  => $isDifferent,
            ]);

            // detail item
            foreach ($items as $item) {
                OrderItem::create([
                    'order_id'   => $order->id,
                    'product_id' => $item->id,
                    'price'      => $item->price,
                    'quantity'   => $item->qty,
                ]);
            }

            // COD transaction
            if ($request->mode === 'cod') {
                Transaction::create([
                    'user_id'  => $userId,
                    'order_id' => $order->id,
                    'status'   => 'pending',
                ]);
            }

            $orderIds[] = $order->id;
        }

        // 3) Konfigurasi Midtrans
        Config::$serverKey    = config('midtrans.server_key');
        Config::$isProduction = config('midtrans.is_production');
        Config::$isSanitized  = config('midtrans.is_sanitized');
        Config::$is3ds        = config('midtrans.is_3ds');

        // 4) Generate Snap Token
        foreach ($orderIds as $id) {
            $order = Order::findOrFail($id);
            $params = [
                'transaction_details' => [
                    'order_id'     => 'STORE' . $order->store_id . '_' . $order->id,
                    'gross_amount' => (int) $order->total,
                ],
                'customer_details' => [
                    'first_name' => $request->user()->name,
                    'email'      => $request->user()->email,
                    'phone'      => $order->phone,
                ],
                'expiry' => [
                    'start_time' => Carbon::now()->format('Y-m-d H:i:s O'),
                    'unit'       => 'hour',
                    'duration'   => 1,
                ],
            ];

            Log::info("Generating Snap Token for Order {$order->id}", $params);

            $snapTokens[$order->id] = Snap::getSnapToken($params);
        }

        // 5) Simpan session & bersihkan cart
        Session::put('order_ids',   $orderIds);
        Session::put('snap_tokens', $snapTokens);
        Cart::instance('cart')->destroy();
        Session::forget(['checkout', 'coupon', 'discounts']);

        return redirect()->route('cart.order.confirmation');
    }

    protected function setAmountforCheckout()
    {
        // Jika keranjang kosong, batalkan checkout
        if (Cart::instance('cart')->count() === 0) {
            Session::forget('checkout');
            return;
        }

        if (Session::has('coupon')) {
            // Ambil nilai dari session discounts, hapus koma ribuan, cast ke float
            $discounts = Session::get('discounts');

            Session::put('checkout', [
                'discount' => (float) str_replace(',', '', $discounts['discount']),
                'subtotal' => (float) str_replace(',', '', $discounts['subtotal']),
                'tax'      => (float) str_replace(',', '', $discounts['tax']),
                'total'    => (float) str_replace(',', '', $discounts['total']),
            ]);
        } else {
            // Ambil langsung angka murni dari Cart tanpa format ribuan
            $subtotal = Cart::instance('cart')->subtotal(2, '.', '');
            $tax      = Cart::instance('cart')->tax(2, '.', '');
            $total    = Cart::instance('cart')->total(2, '.', '');

            Session::put('checkout', [
                'discount' => 0.00,
                'subtotal' => (float) $subtotal,
                'tax'      => (float) $tax,
                'total'    => (float) $total,
            ]);
        }
    }


    public function order_confirmation()
    {
        if (! Session::has('order_ids')) {
            return redirect()->route('cart.index');
        }

        $orders = Order::with([
            'store',
            'orderItems.product',
            'payments'         // panggil relasi payments, bukan transaction
        ])
            ->whereIn('id', Session::get('order_ids'))
            ->get();

        return view('order_confirmation', compact('orders'));
    }
}
