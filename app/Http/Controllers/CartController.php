<?php

namespace App\Http\Controllers;

use App\Models\Coupon;
use App\Models\Address;
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
        Cart::instance('cart')->add($request->id, $request->name, $request->quantity, $request->price)->associate('App\Models\Product');
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

        $address = Address::where('user_id', Auth::user()->id)->where('isdefault', 1)->first();
        // $address = Address::where('user_id', Auth::user()->id)->where('isdefault', 0)->first();

        return view('checkout', compact('address'));
    }

    // public function place_an_order(Request $request)
    // {
    //     $user_id = Auth::user()->id;
    //     $address = Address::where('user_id', $user_id)->where('isdefault', true)->first();

    //     if (!$address) {
    //         $request->validate([
    //             'name' => 'required|max:100',
    //             'phone' => 'required|numeric|digits:12',
    //             'zip' => 'required|numeric|digits:6',
    //             'state' => 'required',
    //             'city' => 'required',
    //             'address' => 'required',
    //             'locality' => 'required',
    //             'landmark' => 'required',
    //         ]);

    //         $address = new Address();
    //         $address->name = $request->name;
    //         $address->phone = $request->phone;
    //         $address->zip = $request->zip;
    //         $address->state = $request->state;
    //         $address->city = $request->city;
    //         $address->address = $request->address;
    //         $address->locality = $request->locality;
    //         $address->landmark = $request->landmark;
    //         $address->country = 'Indonesia';
    //         $address->user_id = $user_id;
    //         $address->isdefault = true;
    //         $address->save();
    //     }
    //     $this->setAmountforCheckout();

    //     $order = new Order();
    //     $order->user_id = $user_id;
    //     $order->subtotal = Session::get('checkout')['subtotal'];
    //     $order->discount = Session::get('checkout')['discount'];
    //     $order->tax = Session::get('checkout')['tax'];
    //     $order->total = Session::get('checkout')['total'];
    //     $order->name = $address->name;
    //     $order->phone = $address->phone;
    //     $order->locality = $address->locality;
    //     $order->address = $address->address;
    //     $order->city = $address->city;
    //     $order->state = $address->state;
    //     $order->country = $address->country;
    //     $order->landmark = $address->landmark;
    //     $order->zip = $address->zip;
    //     $order->save();

    //     foreach (Cart::instance('cart')->content() as $item) {
    //         $orderItem = new OrderItem();
    //         $orderItem->product_id = $item->id;
    //         $orderItem->order_id = $order->id;
    //         $orderItem->price = $item->price;
    //         $orderItem->quantity = $item->qty;
    //         $orderItem->save();
    //     }

    //     if ($request->mode == "card") {
    //         //
    //     } elseif ($request->mode == "pypal") {
    //         //
    //     } elseif ($request->mode == "cod") {
    //         $transection = new Transaction();
    //         $transection->user_id = $user_id;
    //         $transection->order_id = $order->id;
    //         $transection->status = "pending";
    //         $transection->save();
    //     }

    //     Cart::instance('cart')->destroy();
    //     Session::forget('checkout');
    //     Session::forget('coupon');
    //     Session::forget('discounts');
    //     Session::put('order_id', $order->id);
    //     return redirect()->route('cart.order.comfirmation');
    // }

    public function place_an_order(Request $request)
    {
        $userId     = Auth::id();
        $orderIds   = [];
        $snapTokens = [];

        // 1) Tentukan alamat
        if ($request->filled('address_id')) {
            $address = Address::where('user_id', $userId)
                ->findOrFail($request->input('address_id'));
        } else {
            $validated = $request->validate([
                'name'     => 'required|max:100',
                'phone'    => 'required|numeric|digits_between:10,15',
                'zip'      => 'required|numeric|digits_between:5,6',
                'state'    => 'required',
                'city'     => 'required',
                'address'  => 'required',
                'locality' => 'required',
                'landmark' => 'required',
            ]);
            $validated['user_id']   = $userId;
            $validated['country']   = 'Indonesia';
            $validated['isdefault'] = true;
            $address = Address::create($validated);
        }

        // 2) Buat Order per toko
        $groups = Cart::instance('cart')
            ->content()
            ->groupBy(fn($i) => $i->model->store_id);

        foreach ($groups as $storeId => $items) {
            $sub     = $items->sum(fn($i) => $i->price * $i->qty);
            $taxRate = config('cart.tax') / 100;
            $tax     = round($sub * $taxRate, 2);
            $total   = round($sub + $tax, 2);

            $order = Order::create([
                'user_id'   => $userId,
                'store_id'  => $storeId,
                'subtotal'  => round($sub, 2),
                'discount'  => 0,
                'tax'       => $tax,
                'total'     => $total,
                'name'      => $address->name,
                'phone'     => $address->phone,
                'locality'  => $address->locality,
                'address'   => $address->address,
                'city'      => $address->city,
                'state'     => $address->state,
                'country'   => $address->country,
                'landmark'  => $address->landmark,
                'zip'       => $address->zip,
            ]);

            foreach ($items as $item) {
                OrderItem::create([
                    'order_id'   => $order->id,
                    'product_id' => $item->id,
                    'price'      => $item->price,
                    'quantity'   => $item->qty,
                ]);
            }

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

        // 4) Generate Snap Token untuk tiap order dengan expiry 1 jam
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
                    // Mulai sekarang, durasi 1 jam
                    'start_time' => Carbon::now()->format('Y-m-d H:i:s O'),
                    'unit'       => 'hour',
                    'duration'   => 1,
                ],
            ];

            Log::info("Generating Snap Token for Order {$order->id}", $params);

            $snapTokens[$order->id] = Snap::getSnapToken($params);
        }

        // 5) Simpan untuk view konfirmasi
        Session::put('order_ids',   $orderIds);
        Session::put('snap_tokens', $snapTokens);

        // 6) Bersihkan cart & session lama lainnya
        Cart::instance('cart')->destroy();
        Session::forget(['checkout', 'coupon', 'discounts']);

        return redirect()->route('cart.order.comfirmation');
    }


    // public function setAmountforCheckout()
    // {
    //     if (!Cart::instance('cart')->content()->count() > 0) {
    //         Session::forget('checkout');
    //         return;
    //     }
    //     if (Session::has('coupon')) {
    //         Session::put('checkout', [
    //             'discount' => Session::get('discounts')['discount'],
    //             'subtotal' => Session::get('discounts')['subtotal'],
    //             'tax' => Session::get('discounts')['tax'],
    //             'total' => Session::get('discounts')['total'],
    //         ]);
    //     } else {
    //         Session::put('checkout', [
    //             'discount' => 0,
    //             'subtotal' => Cart::instance('cart')->subtotal(),
    //             'tax' => Cart::instance('cart')->tax(),
    //             'total' => Cart::instance('cart')->total(),
    //         ]);
    //     }
    // }

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


    public function order_comfirmation()
    {
        if (! Session::has('order_ids')) {
            return redirect()->route('cart.index');
        }

        $orders = Order::with(['store', 'orderItems.product', 'transaction'])
            ->whereIn('id', Session::get('order_ids'))
            ->get();

        return view('order_confirmation', compact('orders'));
    }
}
