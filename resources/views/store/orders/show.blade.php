@extends('layouts.store')

@section('content')
  @if (session('status'))
    <div class="alert alert-success">{{ session('status') }}</div>
  @endif
  @if (session('error'))
    <div class="alert alert-danger">{{ session('error') }}</div>
  @endif


  <div class="container my-5 p-4">

    {{-- Page Title --}}
    <div class="d-flex justify-content-between align-items-center mb-5">
      <h1 class="display-5">Order #{{ $order->id }} Details</h1>
      <nav aria-label="breadcrumb">
        <ol class="breadcrumb mb-0">
          <li class="breadcrumb-item"><a href="{{ route('store.index') }}">Dashboard</a></li>
          <li class="breadcrumb-item"><a href="{{ route('store.orders.index') }}">Orders</a></li>
          <li class="breadcrumb-item active" aria-current="page">#{{ $order->id }}</li>
        </ol>
      </nav>
    </div>

    {{-- Order & Shipping --}}
    <div class="row gy-4 mb-30">
      <div class="col-lg-4">
        <div class="card h-100 shadow-sm">
          <div class="card-header bg-white border-bottom">
            <h5 class="mb-0">Order Info</h5>
          </div>
          <div class="card-body">
            <p class="fs-5 mb-3"><strong>ID:</strong> {{ $order->id }}</p>
            <p class="fs-5 mb-3"><strong>Date:</strong> {{ $order->created_at->format('d M Y, H:i') }}</p>
            <p class="fs-5"><strong>Customer:</strong><br>
              <span class="fw-medium">{{ $order->user->name }}</span><br>
              <small class="fw-bold">{{ $order->user->email }}</small>
            </p>
          </div>
        </div>
      </div>
      <div class="col-lg-8">
        <div class="card h-100 shadow-sm">
          <div class="card-header bg-white border-bottom">
            <h5 class="mb-0">Shipping Address</h5>
          </div>
          <div class="card-body">
            <address class="fs-5 mb-3">
              <strong class="fw-bold">{{ $order->recipient_name }}</strong><br>
              {{ $order->full_address }}<br>
              {{ $order->subdistrict }}, {{ $order->district }}<br>
              {{ $order->city }}, {{ $order->province }}<br>
              <abbr title="ZIP">ZIP</abbr>: {{ $order->zip_code }}<br>
              <abbr title="Phone">Telp</abbr>: {{ $order->phone }}
            </address>
            @if ($order->is_shipping_different)
              <span class="badge bg-info fs-6">Alamat Berbeda</span>
            @endif
          </div>
        </div>
      </div>
    </div>

    {{-- Items Table --}}
    <div class="card mb-30 shadow-sm">
      <div class="card-header bg-white border-bottom">
        <h5 class="mb-0">Order Items</h5>
      </div>
      <div class="table-responsive">
        <table class="table align-middle mb-0">
          <thead class="table-light">
            <tr class="fs-6">
              <th>Product</th>
              <th class="text-center">Qty</th>
              <th class="text-end">Price</th>
              <th class="text-end">Subtotal</th>
            </tr>
          </thead>
          <tbody>
            @foreach ($order->orderItems as $item)
              <tr class="fs-6">
                <td class="py-3">{{ $item->product->name }}</td>
                <td class="text-center py-3">{{ $item->quantity }}</td>
                <td class="text-end py-3">@rupiahSymbol($item->price)</td>
                <td class="text-end py-3">@rupiahSymbol($item->price * $item->quantity)</td>
              </tr>
            @endforeach
          </tbody>
        </table>
      </div>
    </div>

    {{-- Cost Breakdown & Status --}}
    <div class="row gy-4 mb-30">
      <div class="col-md-6">
        <div class="card h-100 shadow-sm">
          <div class="card-header bg-white border-bottom">
            <h5 class="mb-0">Cost Breakdown</h5>
          </div>
          <ul class="list-group list-group-flush fs-6">
            <li class="list-group-item d-flex justify-content-between py-3">
              <span>Subtotal</span>
              <span>@rupiahSymbol($order->subtotal)</span>
            </li>
            <li class="list-group-item d-flex justify-content-between py-3">
              <span>Ongkir ({{ strtoupper($order->shipping_service) }})</span>
              <span>@rupiahSymbol($order->shipping_cost)</span>
            </li>
            <li class="list-group-item d-flex justify-content-between py-3">
              <span>PPN ({{ config('cart.tax') }}%)</span>
              <span>@rupiahSymbol($order->tax)</span>
            </li>
            <li class="list-group-item d-flex justify-content-between py-3 fw-bold">
              <span>Total</span>
              <span>@rupiahSymbol($order->total)</span>
            </li>
          </ul>
        </div>
      </div>
      <div class="col-md-6 d-flex align-items-center justify-content-center">
        @php
          $statusClasses = [
              'ordered' => 'bg-secondary',
              'shipped' => 'bg-info text-white',
              'delivered' => 'bg-success',
              'completed' => 'bg-primary',
              'canceled' => 'bg-danger',
          ];
          $badgeClass = $statusClasses[$order->status] ?? 'bg-secondary';
        @endphp
        <div class="text-center my-4">
          <h5 class="mb-3">Order Status</h5>
          <span class="badge {{ $badgeClass }} shadow-sm fs-4 py-2 px-4 mb-4">
            {{ ucfirst($order->status) }}
          </span>

          {{-- Aksi Order --}}
          <div class="d-grid gap-2 d-md-flex justify-content-md-center">
            @if ($order->status === 'ordered')
              <form action="{{ route('store.orders.ship', $order) }}" method="POST" class="me-md-2">
                @csrf
                <button type="submit" class="btn btn-primary btn-lg">
                  <i class="bi bi-truck-flatbed me-2"></i>
                  Mark as Shipped
                </button>
              </form>
              <form action="{{ route('store.orders.cancel', $order) }}" method="POST">
                @csrf
                <button type="submit" class="btn btn-outline-danger btn-lg">
                  <i class="bi bi-x-circle me-2"></i>
                  Cancel Order
                </button>
              </form>
            @elseif($order->status === 'shipped')
              <form action="{{ route('store.orders.deliver', $order) }}" method="POST" class="me-md-2">
                @csrf
                <button type="submit" class="btn btn-success btn-lg">
                  <i class="bi bi-box-seam me-2"></i>
                  Mark as Delivered
                </button>
              </form>
              <form action="{{ route('store.orders.cancel', $order) }}" method="POST">
                @csrf
                <button type="submit" class="btn btn-outline-danger btn-lg">
                  <i class="bi bi-x-circle me-2"></i>
                  Cancel Order
                </button>
              </form>
            @elseif($order->status === 'delivered')
              <form action="{{ route('store.orders.complete', $order) }}" method="POST">
                @csrf
                <button type="submit" class="btn btn-warning btn-lg">
                  <i class="bi bi-check2-circle me-2"></i>
                  Complete Order
                </button>
              </form>
            @endif
          </div>
        </div>

      </div>
    </div>

    {{-- Back Button --}}
    <div class="mb-5">
      <a href="{{ route('store.orders.index') }}" class="btn btn-lg btn-outline-secondary">
        <i class="bi bi-arrow-left me-2"></i> Back to Orders
      </a>
    </div>

  </div>
@endsection
