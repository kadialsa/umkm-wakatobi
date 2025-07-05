<?php

namespace App\Http\Controllers;

use App\Models\Address;
use App\Models\Order;
use App\Models\OrderItem;
use App\Models\OrderPayment;
use App\Models\Transaction;
use App\Models\User;
use Carbon\Carbon;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\Auth;
use Illuminate\Http\Request;
use Illuminate\Support\Arr;
use Illuminate\Support\Facades\Storage;

// midtrans
use Midtrans\Config;
use Midtrans\Snap;

class UserController extends Controller
{
    public function index()
    {
        return view('user.index');
    }


    /** Tampilkan detail profil */
    public function detailsIndex()
    {
        // cukup pass user, relasi profile sudah withDefault()
        $user = Auth::user();
        return view('user.details', compact('user'));
    }

    /** Tampilkan form edit profil */
    public function detailsEdit()
    {
        $user    = Auth::user();
        $profile = $user->profile;
        return view('user.details-edit', compact('user', 'profile'));
    }

    /** Proses update profil */
    public function detailsUpdate(Request $request)
    {
        $user = Auth::user();

        // validasi gabungan user + profile
        $validated = $request->validate([
            'name'        => 'required|string|max:255',
            'email'       => "required|email|max:255|unique:users,email,{$user->id}",
            'birthdate'   => 'nullable|date',
            'gender'      => 'nullable|in:male,female,other',
            'phone'       => 'nullable|string|max:20',
            'address'     => 'nullable|string|max:500',
            'avatar'      => 'nullable|image|max:10240',
        ]);

        // 1) Update tabel users
        $user->update(Arr::only($validated, ['name', 'email']));

        // 2) Siapkan data untuk profile
        $profileData = Arr::only($validated, ['birthdate', 'gender', 'phone', 'address']);

        // 3) Kalau ada upload avatar baru
        if ($request->hasFile('avatar')) {
            // hapus avatar lama jika ada
            if ($user->profile && $user->profile->avatar) {
                Storage::disk('public')->delete($user->profile->avatar);
            }
            // simpan file baru
            $profileData['avatar'] = $request->file('avatar')
                ->store('avatars', 'public');
        }

        // 4) Update or create di tabel user_profiles
        $user->profile()->updateOrCreate(
            ['user_id' => $user->id],
            $profileData
        );

        return redirect()
            ->route('user.details')
            ->with('status', 'Profil berhasil diperbarui.');
    }

    public function orders(Request $request)
    {
        $userId = Auth::id();

        // Ambil SEMUA order milik user
        $orders = Order::where('user_id', $userId)
            ->with('payments')   // eager load relation ke order_payments
            ->orderBy('created_at', 'DESC')
            ->paginate(10);

        // Konfig Midtrans untuk Snap JS
        Config::$serverKey    = config('midtrans.server_key');
        Config::$clientKey    = config('midtrans.client_key');
        Config::$isProduction = config('midtrans.is_production');
        Config::$isSanitized  = config('midtrans.is_sanitized');
        Config::$is3ds        = config('midtrans.is_3ds');

        // Kumpulkan snap_token dari payment terakhir jika statusnya 'pending'
        $snapTokens = [];
        foreach ($orders as $order) {
            $lastPayment = OrderPayment::where('order_id', $order->id)
                ->latest('created_at')
                ->first();

            if (
                $lastPayment
                && in_array($lastPayment->transaction_status, ['pending', 'unpaid'])
                && $lastPayment->snap_token
            ) {
                $snapTokens[$order->id] = $lastPayment->snap_token;
            }
        }

        return view('user.orders', compact('orders', 'snapTokens'));
    }

    public function orders_details($order_id)
    {
        $order = Order::where('user_id', Auth::user()->id)->where('id', $order_id)->first();
        if ($order) {
            $orderItems = OrderItem::where('order_id', $order->id)->orderBy('id')->paginate(12);
            $transaction = Transaction::where('order_id', $order->id)->first();
            return view('user.order-details', compact('order', 'orderItems', 'transaction'));
        } else {
            return redirect()->route('login');
        }
    }

    public function order_cancel(Request $request)
    {
        $order = Order::find($request->order_id);
        $order->status = "canceled";
        $order->canceled_date = Carbon::now();
        $order->save();
        return back()->with('status', "Order has been cancelled successfully!");
    }
}
