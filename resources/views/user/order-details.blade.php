@extends('layouts.app')

@section('content')
  <style>
    .page-title {
      font-size: 1.75rem;
      font-weight: 700;
      margin-bottom: 1.5rem;
      color: #333;
    }

    .badge-status {
      font-size: .9rem;
      font-weight: 600;
      padding: .4rem .75rem;
      border-radius: .35rem;
      margin-left: .5rem;
    }

    .badge-ordered {
      background-color: #f5d700;
      color: #000;
    }

    .badge-shipped {
      background-color: #17a2b8;
      color: #fff;
    }

    .badge-delivered {
      background-color: #40c710;
      color: #fff;
    }

    .badge-completed {
      background-color: #007bff;
      color: #fff;
    }

    .badge-canceled {
      background-color: #f44032;
      color: #fff;
    }

    .badge-paid {
      background-color: #40c710;
      color: #fff;
    }

    .badge-pending {
      background-color: #ffc107;
      color: #333;
    }

    .card-section {
      margin-bottom: 1.5rem;
      box-shadow: 0 2px 6px rgba(0, 0, 0, 0.05);
    }

    .card-header {
      background-color: #f8f9fa;
      font-weight: 600;
    }

    .table-summary th,
    .table-summary td {
      vertical-align: middle;
      padding: .75rem 1rem;
    }

    /* Tracking History styles */
    .tracking-card .card-header {
      background: #000;
      color: #fff;
    }

    .tracking-timeline {
      position: relative;
      padding-left: 2rem;
    }

    .tracking-timeline::before {
      content: '';
      position: absolute;
      left: 1rem;
      top: 1.25rem;
      bottom: 1rem;
      width: 2px;
      background: #17a2b8;
    }

    .tracking-item {
      position: relative;
      margin-bottom: 2rem;
    }

    .tracking-item:last-child {
      margin-bottom: 0;
    }

    .tracking-item::before {
      content: '';
      position: absolute;
      left: 0;
      top: 0;
      width: 1rem;
      height: 1rem;
      background: #000;
      border-radius: 50%;
    }
  </style>

  <main class="pt-90">
    <section class="my-account container">
      <div class="mb-4 pb-4"></div>
      <h2 class="page-title">Detail Pesanan</h2>
      <div class="row">
        {{-- Sidebar akun --}}
        <div class="col-lg-3">
          @include('user.account-nav')
        </div>

        {{-- Konten utama --}}
        <div class="col-lg-9 page-content my-account__address">

          {{-- Pesanan tidak ditemukan --}}
          @if (!$order)
            <div class="alert alert-warning">
              <i class="fa fa-exclamation-triangle me-2"></i>
              Pesanan tidak ditemukan atau sudah dihapus.
            </div>
            <a href="{{ route('user.orders') }}" class="btn btn-secondary">
              <i class="fa fa-arrow-left me-1"></i>Kembali ke Daftar Pesanan
            </a>
            @return;
          @endif

          {{-- Ringkasan Pesanan --}}
          @php
            $statusMap = [
                'ordered' => ['Dipesan', 'badge-ordered'],
                'shipped' => ['Dikirim', 'badge-shipped'],
                'delivered' => ['Terkirim', 'badge-delivered'],
                'completed' => ['Selesai', 'badge-completed'],
                'canceled' => ['Dibatalkan', 'badge-canceled'],
            ];
            $st = $statusMap[$order->status] ?? [ucfirst($order->status), 'badge-secondary'];

            $latestPayment = $order->payments->pluck('transaction_status')->last() ?? 'unpaid';
            if (in_array($latestPayment, ['capture', 'settlement'])) {
                $payLabel = 'Lunas';
                $payClass = 'badge-paid';
            } elseif ($latestPayment === 'pending') {
                $payLabel = 'Menunggu';
                $payClass = 'badge-pending';
            } else {
                $payLabel = 'Belum Dibayar';
                $payClass = 'badge-pending';
            }
            $needsPayment = in_array($latestPayment, ['pending', 'unpaid']);
          @endphp

          <div class="card card-section">
            <div class="card-header d-flex justify-content-between align-items-center">
              <div>
                Pesanan #<strong>{{ $order->id }}</strong>
                <small class="text-muted">— {{ $order->created_at->format('d M Y, H:i') }}</small>
              </div>
              <div>
                <span class="badge-status {{ $st[1] }}">{{ $st[0] }}</span>
                <span class="badge-status {{ $payClass }}">{{ $payLabel }}</span>
              </div>
            </div>
          </div>

          {{-- Info & Alamat --}}
          <div class="row">
            <div class="col-md-6">
              <div class="card card-section">
                <div class="card-header">Info Penerima</div>
                <div class="card-body">
                  <p><strong>Nama:</strong> {{ $order->recipient_name }}</p>
                  <p><strong>Telepon:</strong> {{ $order->phone }}</p>
                </div>
              </div>
            </div>
            <div class="col-md-6">
              <div class="card card-section">
                <div class="card-header">Alamat Pengiriman</div>
                <div class="card-body">
                  <p>{{ $order->full_address }}</p>
                  <p>{{ $order->subdistrict }}, {{ $order->district }}</p>
                  <p>{{ $order->city }}, {{ $order->province }}</p>
                  <p><strong>Kode Pos:</strong> {{ $order->zip_code }}</p>
                </div>
              </div>
            </div>
          </div>

          {{-- Daftar Produk --}}
          <div class="card card-section">
            <div class="card-header">Detail Produk</div>
            <div class="table-responsive">
              <table class="table table-striped mb-0">
                <thead>
                  <tr>
                    <th>Produk</th>
                    <th class="text-center">Qty</th>
                    <th class="text-end">Harga</th>
                    <th class="text-end">Subtotal</th>
                  </tr>
                </thead>
                <tbody>
                  @foreach ($order->orderItems as $item)
                    <tr>
                      <td>{{ $item->product->name }}</td>
                      <td class="text-center">{{ $item->quantity }}</td>
                      <td class="text-end">Rp {{ number_format($item->price, 2, ',', '.') }}</td>
                      <td class="text-end">Rp {{ number_format($item->price * $item->quantity, 2, ',', '.') }}</td>
                    </tr>
                  @endforeach
                </tbody>
              </table>
            </div>
          </div>

          {{-- Ringkasan Biaya --}}
          <div class="card card-section">
            <div class="card-header">Ringkasan Biaya</div>
            <div class="table-responsive">
              <table class="table table-summary mb-0">
                <tbody>
                  <tr>
                    <th>Subtotal</th>
                    <td>Rp {{ number_format($order->subtotal, 2, ',', '.') }}</td>
                  </tr>
                  <tr>
                    <th>PPN ({{ config('cart.tax') }}%)</th>
                    <td>Rp {{ number_format($order->tax, 2, ',', '.') }}</td>
                  </tr>
                  <tr>
                    <th>Ongkir</th>
                    <td>Rp
                      {{ number_format($order->shipping_cost, 2, ',', '.') }}<br><small>{{ strtoupper($order->shipping_service) }}</small>
                    </td>
                  </tr>
                  <tr>
                    <th>Total</th>
                    <td><strong>Rp {{ number_format($order->total, 2, ',', '.') }}</strong></td>
                  </tr>
                </tbody>
              </table>
            </div>
          </div>

          {{-- Tombol Aksi --}}
          <div class="d-flex gap-2 mb-5">
            @if ($needsPayment && session('snap_tokens')[$order->id] ?? false)
              <button id="pay-now" type="button" class="btn btn-success"
                data-token="{{ session('snap_tokens')[$order->id] }}">
                <i class="fa fa-credit-card me-1"></i>Bayar Sekarang
              </button>
            @endif
            <a href="{{ route('user.orders') }}" class="btn btn-secondary">
              <i class="fa fa-arrow-left me-1"></i>Kembali
            </a>
          </div>

          {{-- Tracking History Header --}}
          <div class="card card-section tracking-card mb-5 mt-5">
            <div class="card-header">
              <i class="fa fa-map me-2"></i>Tracking History
            </div>
            <div class="card-body p-4">
              @if ($order->trackings->isEmpty())
                <div class="text-center py-5 text-muted">
                  <i class="fa fa-map-marker fa-3x mb-3"></i>
                  <p class="fs-5">Belum ada update lokasi.</p>
                </div>
              @else
                <div class="tracking-timeline">
                  @foreach ($order->trackings as $t)
                    <div class="tracking-item">
                      <h6 class="ps-4 fw-bold">{{ $t->location }}</h6>
                      <small class="text-muted">
                        <i class="fa fa-clock me-1"></i>{{ $t->created_at->format('d M Y, H:i') }}
                      </small>
                      @if ($t->note)
                        <p class="mt-1 mb-0 text-muted">{{ $t->note }}</p>
                      @endif
                    </div>
                  @endforeach
                </div>
              @endif
            </div>
          </div>


        </div>
      </div>
    </section>
  </main>
@endsection

@push('scripts')
  {{-- Midtrans Snap JS --}}
  <script src="https://{{ config('midtrans.is_production') ? 'app' : 'app.sandbox' }}.midtrans.com/snap/snap.js"
    data-client-key="{{ config('midtrans.client_key') }}"></script>
  <script>
    document.addEventListener('DOMContentLoaded', () => {
      const payBtn = document.getElementById('pay-now');
      if (payBtn) {
        payBtn.addEventListener('click', () => {
          snap.pay(payBtn.dataset.token, {
            onSuccess: handle,
            onPending: handle,
            onError: handle,
          });
        });
      }

      function handle(result) {
        fetch("{{ route('midtrans.notification') }}", {
          method: 'POST',
          headers: {
            'X-CSRF-TOKEN': '{{ csrf_token() }}',
            'Content-Type': 'application/json'
          },
          body: JSON.stringify(result)
        }).then(() => location.reload());
      }
    });
  </script>
@endpush
