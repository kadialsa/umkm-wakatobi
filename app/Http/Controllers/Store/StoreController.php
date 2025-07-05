<?php

namespace App\Http\Controllers\Store;

use App\Http\Controllers\Controller;
use App\Models\Order;
use App\Models\Product;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Storage;

class StoreController extends Controller
{
    public function index()
    {
        // 1. Ambil store milik user
        $store = Auth::user()->store()->first();
        $storeId = $store ? $store->id : 0;

        // 2. 10 order terbaru, eager‐load orderItems untuk count()
        $orders = Order::with('orderItems')
            ->where('store_id', $storeId)
            ->orderBy('created_at', 'desc')
            ->limit(10)
            ->get();

        // 3. Ringkasan totals per status (sesuai struktur tabel orders)
        $dashboardDatas = DB::table('orders')
            ->selectRaw(<<<SQL
                SUM(total) AS TotalAmount,
                SUM(CASE WHEN status = 'ordered'   THEN total ELSE 0 END) AS TotalOrderedAmount,
                SUM(CASE WHEN status = 'delivered' THEN total ELSE 0 END) AS TotalDeliveredAmount,
                SUM(CASE WHEN status = 'canceled'  THEN total ELSE 0 END) AS TotalCanceledAmount,
                COUNT(*) AS Total,
                SUM(CASE WHEN status = 'ordered'   THEN 1 ELSE 0 END) AS TotalOrdered,
                SUM(CASE WHEN status = 'delivered' THEN 1 ELSE 0 END) AS TotalDelivered,
                SUM(CASE WHEN status = 'canceled'  THEN 1 ELSE 0 END) AS TotalCanceled
            SQL)
            ->where('store_id', $storeId)
            ->first();

        // 4. Data bulanan YTD (sama seperti sebelumnya)
        $monthlySub = DB::table('orders')
            ->selectRaw(<<<SQL
                MONTH(created_at) AS MonthNo,
                DATE_FORMAT(created_at, '%b') AS MonthName,
                SUM(total) AS TotalAmount,
                SUM(CASE WHEN status = 'ordered'   THEN total ELSE 0 END) AS TotalOrderedAmount,
                SUM(CASE WHEN status = 'delivered' THEN total ELSE 0 END) AS TotalDeliveredAmount,
                SUM(CASE WHEN status = 'canceled'  THEN total ELSE 0 END) AS TotalCanceledAmount
            SQL)
            ->where('store_id', $storeId)
            ->whereYear('created_at', now()->year)
            ->groupBy('MonthNo', 'MonthName');

        $monthlyDatas = DB::table('month_names AS M')
            ->leftJoinSub($monthlySub, 'D', fn($j) => $j->on('D.MonthNo', '=', 'M.id'))
            ->selectRaw(<<<SQL
                M.id   AS MonthNo,
                M.name AS MonthName,
                COALESCE(D.TotalAmount, 0)          AS TotalAmount,
                COALESCE(D.TotalOrderedAmount, 0)   AS TotalOrderedAmount,
                COALESCE(D.TotalDeliveredAmount, 0) AS TotalDeliveredAmount,
                COALESCE(D.TotalCanceledAmount, 0)  AS TotalCanceledAmount
            SQL)
            ->get();

        // 5. Siapkan string untuk chart
        $AmountM          = $monthlyDatas->pluck('TotalAmount')->implode(',');
        $OrderedAmountM   = $monthlyDatas->pluck('TotalOrderedAmount')->implode(',');
        $DeliveredAmountM = $monthlyDatas->pluck('TotalDeliveredAmount')->implode(',');
        $CanceledAmountM  = $monthlyDatas->pluck('TotalCanceledAmount')->implode(',');

        // 6. Totals YTD
        $TotalAmount          = $monthlyDatas->sum('TotalAmount');
        $TotalOrderedAmount   = $monthlyDatas->sum('TotalOrderedAmount');
        $TotalDeliveredAmount = $monthlyDatas->sum('TotalDeliveredAmount');
        $TotalCanceledAmount  = $monthlyDatas->sum('TotalCanceledAmount');

        return view('store.index', compact(
            'orders',
            'dashboardDatas',
            'AmountM',
            'OrderedAmountM',
            'DeliveredAmountM',
            'CanceledAmountM',
            'TotalAmount',
            'TotalOrderedAmount',
            'TotalDeliveredAmount',
            'TotalCanceledAmount'
        ));
    }

    public function profile()
    {
        $store = Auth::user()->store;
        return view('store.profile.index', compact('store'));
    }

    public function updateProfile(Request $request)
    {
        $user  = Auth::user();
        $store = $user->store;

        // 1) validate store & owner fields
        $v = $request->validate([
            // store
            'name'        => 'required|string|max:255',
            'description' => 'nullable|string',
            'image'       => 'nullable|image|max:2048',
            // owner
            'owner_name'      => 'required|string|max:255',
            'owner_email'     => 'required|email|unique:users,email,' . $user->id,
            'owner_mobile'    => 'nullable|string|max:15',
            'current_password' => 'nullable|string',
            'new_password'    => 'nullable|string|min:6|confirmed',
        ]);

        // 2) handle store image
        if ($request->hasFile('image')) {
            if ($store->image) {
                Storage::disk('public')->delete($store->image);
            }
            $v['image'] = $request->file('image')->store('stores', 'public');
        }
        // update store
        $store->update([
            'name'        => $v['name'],
            'description' => $v['description'] ?? null,
            'image'       => $v['image'] ?? $store->image,
        ]);

        // 3) update user profile
        $user->update([
            'name'   => $v['owner_name'],
            'email'  => $v['owner_email'],
            'mobile' => $v['owner_mobile'],
        ]);

        // 4) if requested password change, verify old password first
        if ($v['new_password']) {
            if (! Hash::check($v['current_password'], $user->password)) {
                return back()
                    ->withErrors(['current_password' => 'Current password is incorrect'])
                    ->withInput();
            }
            $user->update(['password' => Hash::make($v['new_password'])]);
        }

        return redirect()
            ->route('store.profile')
            ->with('status', 'Profile updated successfully.');
    }
}
