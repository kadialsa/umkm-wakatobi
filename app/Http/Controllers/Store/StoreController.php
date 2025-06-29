<?php

namespace App\Http\Controllers\Store;

use App\Http\Controllers\Controller;
use App\Models\Order;
use App\Models\Product;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;

class StoreController extends Controller
{
    public function index()
    {
        // 1. Ambil store_id dari user yang login
        $store = Auth::user()->store()->first();
        $storeId = $store ? $store->id : 0;  // fallback 0 jika belum punya store

        // 2. 10 order terbaru untuk toko ini
        $orders = Order::where('store_id', $storeId)
            ->orderBy('created_at', 'desc')
            ->limit(10)
            ->get();

        // 3. Ringkasan totals per status
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

        // 4. Sub-query bulanan YTD
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
            ->leftJoinSub($monthlySub, 'D', function ($join) {
                $join->on('D.MonthNo', '=', 'M.id');
            })
            ->selectRaw(<<<SQL
                M.id   AS MonthNo,
                M.name AS MonthName,
                COALESCE(D.TotalAmount, 0)          AS TotalAmount,
                COALESCE(D.TotalOrderedAmount, 0)   AS TotalOrderedAmount,
                COALESCE(D.TotalDeliveredAmount, 0) AS TotalDeliveredAmount,
                COALESCE(D.TotalCanceledAmount, 0)  AS TotalCanceledAmount
            SQL)
            ->get();

        // 5. Siapkan string untuk chart JS
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
}
