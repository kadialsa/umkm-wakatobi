@extends('layouts.store')

@section('content')
  {{-- Flash Messages --}}
  @if (session('status'))
    <div class="alert alert-success alert-dismissible fade show" role="alert">
      <i class="bi bi-check-circle-fill me-2"></i>{{ session('status') }}
      <button type="button" class="btn-close" data-bs-dismiss="alert"></button>
    </div>
  @endif
  @if (session('error'))
    <div class="alert alert-danger alert-dismissible fade show" role="alert">
      <i class="bi bi-exclamation-triangle-fill me-2"></i>{{ session('error') }}
      <button type="button" class="btn-close" data-bs-dismiss="alert"></button>
    </div>
  @endif

  <div class="container-fluid px-4 py-5">

    {{-- Page Header --}}
    <div class="row mb-4">
      <div class="col-12">
        <div class="d-flex flex-column flex-lg-row justify-content-between align-items-start align-items-lg-center gap-3">
          <div>
            <h1 class="display-4 fw-bold text-primary mb-2">Order #{{ $order->id }}</h1>
            <p class="text-muted mb-0 fs-5">Order details and tracking information</p>
          </div>
          <nav aria-label="breadcrumb">
            <ol class="breadcrumb mb-0 bg-light rounded-pill px-3 py-2">
              <li class="breadcrumb-item">
                <a href="{{ route('store.index') }}" class="text-decoration-none fs-6">
                  <i class="bi bi-house-door me-1"></i>Dashboard
                </a>
              </li>
              <li class="breadcrumb-item">
                <a href="{{ route('store.orders.index') }}" class="text-decoration-none fs-6">Orders</a>
              </li>
              <li class="breadcrumb-item active text-primary fw-medium fs-6">#{{ $order->id }}</li>
            </ol>
          </nav>
        </div>
      </div>
    </div>

    {{-- Status Overview Cards --}}
    @php
      $latest = $order->payments->pluck('transaction_status')->last() ?? 'unpaid';
      if (in_array($latest, ['capture', 'settlement'])) {
          $paymentLabel = 'Lunas';
          $paymentClass = 'text-success';
          $paymentIcon = 'bi-check-circle-fill';
      } elseif ($latest === 'pending') {
          $paymentLabel = 'Menunggu Pembayaran';
          $paymentClass = 'text-warning';
          $paymentIcon = 'bi-clock-fill';
      } else {
          $paymentLabel = 'Belum Dibayar';
          $paymentClass = 'text-secondary';
          $paymentIcon = 'bi-x-circle-fill';
      }

      $statusConfig = [
          'ordered' => [
              'label' => 'Ordered',
              'class' => 'text-info',
              'icon' => 'bi-cart-check-fill',
              'bg' => 'bg-info',
          ],
          'shipped' => ['label' => 'Shipped', 'class' => 'text-primary', 'icon' => 'bi-truck', 'bg' => 'bg-primary'],
          'delivered' => [
              'label' => 'Delivered',
              'class' => 'text-success',
              'icon' => 'bi-box-seam-fill',
              'bg' => 'bg-success',
          ],
          'completed' => [
              'label' => 'Completed',
              'class' => 'text-success',
              'icon' => 'bi-check-circle-fill',
              'bg' => 'bg-success',
          ],
          'canceled' => [
              'label' => 'Canceled',
              'class' => 'text-danger',
              'icon' => 'bi-x-circle-fill',
              'bg' => 'bg-danger',
          ],
      ];
      $orderConfig = $statusConfig[$order->status] ?? $statusConfig['ordered'];
    @endphp

    <div class="row g-4 mb-5 mt-4">
      <div class="col-md-6">
        <div class="card border-0 shadow-sm h-100">
          <div class="card-body text-center p-4">
            <div class="mb-3">
              <i class="bi {{ $paymentIcon }} fs-1 {{ $paymentClass }}"></i>
            </div>
            <h5 class="card-title mb-2 fs-4">Payment Status</h5>
            <span class="badge rounded-pill px-3 py-2 fs-5 {{ str_replace('text-', 'bg-', $paymentClass) }} text-white">
              {{ $paymentLabel }}
            </span>
          </div>
        </div>
      </div>
      <div class="col-md-6">
        <div class="card border-0 shadow-sm h-100">
          <div class="card-body text-center p-4">
            <div class="mb-3">
              <i class="bi {{ $orderConfig['icon'] }} fs-1 {{ $orderConfig['class'] }}"></i>
            </div>
            <h5 class="card-title mb-2 fs-4">Order Status</h5>
            <span class="badge rounded-pill px-3 py-2 fs-5 {{ $orderConfig['bg'] }} text-white">
              {{ $orderConfig['label'] }}
            </span>
          </div>
        </div>
      </div>
    </div>

    {{-- Order Info & Shipping Address --}}
    <div class="row g-4 mb-5 mt-5">
      <div class="col-lg-4">
        <div class="card border-0 shadow-sm h-100">
          <div class="card-header bg-gradient bg-primary text-white border-0">
            <h5 class="mb-0 fw-semibold">
              <i class="bi bi-info-circle me-2"></i>Order Information
            </h5>
          </div>
          <div class="card-body p-4">
            <div class="row g-3">
              <div class="col-12">
                <div class="d-flex align-items-center">
                  <i class="bi bi-hash text-primary me-2"></i>
                  <div>
                    <small class="text-muted d-block fs-6">Order ID</small>
                    <strong class="fs-5">{{ $order->id }}</strong>
                  </div>
                </div>
              </div>
              <div class="col-12">
                <div class="d-flex align-items-center">
                  <i class="bi bi-calendar-event text-primary me-2"></i>
                  <div>
                    <small class="text-muted d-block fs-6">Order Date</small>
                    <strong class="fs-5">{{ $order->created_at->format('d M Y, H:i') }}</strong>
                  </div>
                </div>
              </div>
              <div class="col-12">
                <div class="d-flex align-items-center">
                  <i class="bi bi-person-circle text-primary me-2"></i>
                  <div>
                    <small class="text-muted d-block fs-6">Customer</small>
                    <strong class="fs-5">{{ $order->user->name }}</strong>
                    <small class="text-muted d-block fs-6">{{ $order->user->email }}</small>
                  </div>
                </div>
              </div>
            </div>
          </div>
        </div>
      </div>
      <div class="col-lg-8">
        <div class="card border-0 shadow-sm h-100">
          <div class="card-header bg-gradient bg-success text-white border-0">
            <h5 class="mb-0 fw-semibold">
              <i class="bi bi-geo-alt me-2"></i>Shipping Address
            </h5>
          </div>
          <div class="card-body p-4">
            <div class="row">
              <div class="col-md-8">
                <address class="fs-5 mb-0 lh-lg">
                  <strong class="fw-bold text-dark fs-4">{{ $order->recipient_name }}</strong><br>
                  <span class="text-muted fs-6">{{ $order->full_address }}</span><br>
                  <span class="text-muted fs-6">{{ $order->subdistrict }}, {{ $order->district }}</span><br>
                  <span class="text-muted fs-6">{{ $order->city }}, {{ $order->province }}</span><br>
                  <span class="text-muted fs-6"><i class="bi bi-mailbox me-1"></i>{{ $order->zip_code }}</span><br>
                  <span class="text-muted fs-6"><i class="bi bi-telephone me-1"></i>{{ $order->phone }}</span>
                </address>
              </div>
              <div class="col-md-4 text-end">
                @if ($order->is_shipping_different)
                  <span class="badge bg-info rounded-pill px-3 py-2">
                    <i class="bi bi-truck me-1"></i>Alamat Berbeda
                  </span>
                @endif
              </div>
            </div>
          </div>
        </div>
      </div>
    </div>

    {{-- Order Items --}}
    <div class="card border-0 shadow-sm mb-5 mt-5">
      <div class="card-header bg-gradient bg-warning text-dark border-0">
        <h5 class="mb-0 fw-semibold">
          <i class="bi bi-bag-check me-2"></i>Order Items
        </h5>
      </div>
      <div class="table-responsive">
        <table class="table table-hover align-middle mb-0">
          <thead class="table-light">
            <tr>
              <th class="py-3 fw-semibold fs-5">Product</th>
              <th class="text-center py-3 fw-semibold fs-5">Qty</th>
              <th class="text-end py-3 fw-semibold fs-5">Price</th>
              <th class="text-end py-3 fw-semibold fs-5">Subtotal</th>
            </tr>
          </thead>
          <tbody>
            @foreach ($order->orderItems as $item)
              <tr class="border-bottom">
                <td class="py-4">
                  <div class="fw-medium fs-5">{{ $item->product->name }}</div>
                </td>
                <td class="text-center py-4">
                  <span class="badge bg-light text-dark rounded-pill px-3 py-2 fs-6">{{ $item->quantity }}</span>
                </td>
                <td class="text-end py-4 fw-medium fs-6">@rupiahSymbol($item->price)</td>
                <td class="text-end py-4 fw-bold text-primary fs-5">@rupiahSymbol($item->price * $item->quantity)</td>
              </tr>
            @endforeach
          </tbody>
        </table>
      </div>
    </div>

    {{-- Cost Breakdown & Actions --}}
    <div class="row g-4 mb-5 mt-5">
      <div class="col-lg-6">
        <div class="card border-0 shadow-sm">
          <div class="card-header bg-gradient bg-secondary text-white border-0">
            <h5 class="mb-0 fw-semibold">
              <i class="bi bi-calculator me-2"></i>Cost Breakdown
            </h5>
          </div>
          <div class="card-body p-0">
            <div class="list-group list-group-flush">
              <div class="list-group-item d-flex justify-content-between align-items-center py-3">
                <span class="fw-medium fs-5">Subtotal</span>
                <span class="fw-medium fs-5">@rupiahSymbol($order->subtotal)</span>
              </div>
              <div class="list-group-item d-flex justify-content-between align-items-center py-3">
                <span class="fw-medium fs-5">
                  <i class="bi bi-truck me-2 text-primary"></i>
                  Ongkir ({{ strtoupper($order->shipping_service) }})
                </span>
                <span class="fw-medium fs-5">@rupiahSymbol($order->shipping_cost)</span>
              </div>
              <div class="list-group-item d-flex justify-content-between align-items-center py-3">
                <span class="fw-medium fs-5">
                  <i class="bi bi-percent me-2 text-success"></i>
                  PPN ({{ config('cart.tax') }}%)
                </span>
                <span class="fw-medium fs-5">@rupiahSymbol($order->tax)</span>
              </div>
              <div class="list-group-item d-flex justify-content-between align-items-center py-3 bg-light">
                <span class="fw-bold fs-4">Total</span>
                <span class="fw-bold fs-4 text-primary">@rupiahSymbol($order->total)</span>
              </div>
            </div>
          </div>
        </div>
      </div>

      <div class="col-lg-6">
        <div class="card border-0 shadow-sm h-100">
          <div class="card-header bg-gradient bg-secondary text-white border-0">
            <h5 class="mb-0 fw-semibold">
              <i class="bi bi-gear me-2"></i>Order Actions
            </h5>
          </div>
          <div class="card-body d-flex flex-column justify-content-center p-4">
            <div class="d-grid gap-3">
              @if ($order->status === 'ordered')
                <form action="{{ route('store.orders.ship', $order) }}" method="POST">
                  @csrf
                  <button type="submit" class="btn btn-primary btn-lg w-100 rounded-pill">
                    <i class="bi bi-truck-flatbed me-2"></i>Mark as Shipped
                  </button>
                </form>
                <form action="{{ route('store.orders.cancel', $order) }}" method="POST">
                  @csrf
                  <button type="submit" class="btn btn-outline-danger btn-lg w-100 rounded-pill">
                    <i class="bi bi-x-circle me-2"></i>Cancel Order
                  </button>
                </form>
              @elseif($order->status === 'shipped')
                <form action="{{ route('store.orders.deliver', $order) }}" method="POST">
                  @csrf
                  <button type="submit" class="btn btn-success btn-lg w-100 rounded-pill">
                    <i class="bi bi-box-seam me-2"></i>Mark as Delivered
                  </button>
                </form>
                <form action="{{ route('store.orders.cancel', $order) }}" method="POST">
                  @csrf
                  <button type="submit" class="btn btn-outline-danger btn-lg w-100 rounded-pill">
                    <i class="bi bi-x-circle me-2"></i>Cancel Order
                  </button>
                </form>
              @elseif($order->status === 'delivered')
                <form action="{{ route('store.orders.complete', $order) }}" method="POST">
                  @csrf
                  <button type="submit" class="btn btn-warning btn-lg w-100 rounded-pill">
                    <i class="bi bi-check2-circle me-2"></i>Complete Order
                  </button>
                </form>
              @else
                <div class="text-center text-muted">
                  <i class="bi bi-check-circle fs-1 mb-2"></i>
                  <p class="mb-0 fs-5">No actions available for this order status.</p>
                </div>
              @endif
            </div>
          </div>
        </div>
      </div>
    </div>

    {{-- Tracking History --}}
    <div class="card border-0 shadow-sm mb-5 mt-5">
      <div class="card-header bg-gradient bg-info text-white border-0">
        <h5 class="mb-0 fw-semibold">
          <i class="bi bi-geo-alt-fill me-2"></i>Tracking History
        </h5>
      </div>
      <div class="card-body p-4">
        @if ($order->trackings->isEmpty())
          <div class="text-center py-5">
            <i class="bi bi-geo text-muted mb-3" style="font-size: 3rem;"></i>
            <p class="text-muted fs-4 mb-0">Belum ada update lokasi.</p>
          </div>
        @else
          <div class="row mb-4">
            <div class="col-12">
              @foreach ($order->trackings as $index => $t)
                <div class="d-flex mb-4 position-relative">
                  @if (!$loop->last)
                    <div class="position-absolute start-0 top-0 h-100 border-start border-2 border-primary ms-2"
                      style="margin-top: 2rem;"></div>
                  @endif
                  <div class="flex-shrink-0 me-3">
                    <div class="bg-primary rounded-circle d-flex align-items-center justify-content-center"
                      style="width: 2.5rem; height: 2.5rem;">
                      <i class="bi bi-geo-alt-fill text-white"></i>
                    </div>
                  </div>
                  <div class="flex-grow-1">
                    <div class="card border-0 bg-light">
                      <div class="card-body p-3">
                        <div class="d-flex justify-content-between align-items-start mb-2">
                          <div>
                            <h6 class="mb-1 fw-bold fs-5">{{ $t->location }}</h6>
                            <small class="text-muted fs-6">
                              <i class="bi bi-clock me-1"></i>{{ $t->created_at->format('d M Y, H:i') }}
                            </small>
                          </div>
                          <button class="btn btn-lg btn-outline-primary rounded-pill" type="button"
                            data-bs-toggle="collapse" data-bs-target="#edit-tracking-{{ $t->id }}">
                            <i class="bi bi-pencil me-1"></i>Edit
                          </button>
                        </div>
                        @if ($t->note)
                          <p class="mb-0 text-muted fs-6">{{ $t->note }}</p>
                        @endif
                        <div class="collapse mt-3" id="edit-tracking-{{ $t->id }}">
                          <form method="POST" action="{{ route('store.orders.trackings.update', [$order, $t]) }}"
                            class="border-top pt-3">
                            @csrf @method('PUT')
                            <div class="row g-2">
                              <div class="col-md-5">
                                <input type="text" name="location" class="form-control"
                                  value="{{ old('location', $t->location) }}" placeholder="Location" required>
                              </div>
                              <div class="col-md-5">
                                <input type="text" name="note" class="form-control"
                                  value="{{ old('note', $t->note) }}" placeholder="Note (optional)">
                              </div>
                              <div class="col-md-2">
                                <button class="btn btn-lg btn-primary w-100">
                                  <i class="bi bi-check2"></i>
                                </button>
                              </div>
                            </div>
                          </form>
                        </div>
                      </div>
                    </div>
                  </div>
                </div>
              @endforeach
            </div>
          </div>
        @endif

        <div class="border-top pt-4">
          <h6 class="mb-3 fw-semibold fs-5">
            <i class="bi bi-plus-circle me-2"></i>Add New Tracking
          </h6>
          <form method="POST" action="{{ route('store.orders.trackings.store', $order) }}">
            @csrf
            <div class="row g-3">
              <div class="col-md-5">
                <div class="form-floating">
                  <input type="text" name="location" class="form-control @error('location') is-invalid @enderror"
                    value="{{ old('location') }}" placeholder="Location" required>
                  <label>Location *</label>
                  @error('location')
                    <div class="invalid-feedback">{{ $message }}</div>
                  @enderror
                </div>
              </div>
              <div class="col-md-5">
                <div class="form-floating">
                  <input type="text" name="note" class="form-control @error('note') is-invalid @enderror"
                    value="{{ old('note') }}" placeholder="Note">
                  <label>Note (optional)</label>
                  @error('note')
                    <div class="invalid-feedback">{{ $message }}</div>
                  @enderror
                </div>
              </div>
              <div class="col-md-2">
                <button type="submit" class="btn btn-primary h-100 w-100 rounded-pill">
                  <i class="bi bi-plus-lg me-1"></i>Add
                </button>
              </div>
            </div>
          </form>
        </div>
      </div>
    </div>

    {{-- Back Button --}}
    <div class="row">
      <div class="col-12">
        <a href="{{ route('store.orders.index') }}" class="btn btn-lg btn-outline-secondary rounded-pill px-4">
          <i class="bi bi-arrow-left me-2"></i>Back to Orders
        </a>
      </div>
    </div>

  </div>
@endsection
