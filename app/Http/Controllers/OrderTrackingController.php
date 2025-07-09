<?php

namespace App\Http\Controllers;

use App\Models\Order;
use App\Models\OrderTracking;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class OrderTrackingController extends Controller
{
    /**
     * Store a new tracking location.
     */
    public function store(Request $request, Order $order)
    {

        $this->authorize('view', $order);

        // 1) Authorize store
        // if ($order->store_id !== Auth::user()->store()->first()->id) {
        //     abort(403);
        // }

        // 2) Validate
        $data = $request->validate([
            'location' => 'required|string|max:255',
            'note'     => 'nullable|string|max:1000',
        ]);

        // 3) Create
        $order->trackings()->create([
            'store_id' => Auth::user()->store()->first()->id,
            'location' => $data['location'],
            'note'     => $data['note'] ?? null,
        ]);

        return back()->with('status', 'Tracking location added.');
    }

    /**
     * Update an existing tracking.
     */
    public function update(Request $request, Order $order, OrderTracking $tracking)
    {
        // 1) Authorize store & belonging
        // if (
        //     $order->store_id !== Auth::user()->store()->first()->id
        //     || $tracking->order_id !== $order->id
        // ) {
        //     abort(403);
        // }

        $this->authorize('view', $order);

        // 2) Validate
        $data = $request->validate([
            'location' => 'required|string|max:255',
            'note'     => 'nullable|string|max:1000',
        ]);

        // 3) Update
        $tracking->update($data);

        return back()->with('status', 'Tracking location updated.');
    }
}
