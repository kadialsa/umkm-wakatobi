{{-- resources/views/user/orders.blade.php --}}
@extends('layouts.app')

@section('content')
  <style>
    :root {
      --tokopedia-green: #00ab55;
      --tokopedia-light: #f0fdf6;
    }

    .page-title {
      color: var(--tokopedia-green);
      font-size: 2rem;
      font-weight: 700;
      margin-bottom: 1.5rem;
    }

    .order-card {
      border: 1px solid #e0e0e0;
      border-radius: .5rem;
      background-color: #fff;
      box-shadow: 0 2px 6px rgba(0, 0, 0, 0.05);
      margin-bottom: 1.5rem;
      transition: transform .15s;
    }

    .order-card:hover {
      transform: translateY(-2px);
      box-shadow: 0 4px 12px rgba(0, 0, 0, 0.1);
    }

    .order-header {
      background-color: var(--tokopedia-light);
      border-bottom: 1px solid #e0e0e0;
      padding: 1rem 1.5rem;
      display: flex;
      justify-content: space-between;
      align-items: center;
      font-size: 1rem;
    }

    .order-header .order-id {
      font-weight: 600;
    }

    .badge-status {
      padding: .25rem .75rem;
      border-radius: 1rem;
      font-size: .875rem;
      font-weight: 600;
    }

    .badge-dipesan {
      background-color: #f5d700;
      color: #000;
    }

    .badge-dikirim {
      background-color: #17a2b8;
      color: #fff;
    }

    .badge-terkirim {
      background-color: var(--tokopedia-green);
      color: #fff;
    }

    .badge-selesai {
      background-color: #007bff;
      color: #fff;
    }

    .badge-dibatalkan {
      background-color: #f44032;
      color: #fff;
    }

    .order-body {
      padding: 1.5rem;
    }

    .order-body h6 {
      font-size: 1rem;
      font-weight: 600;
      margin-bottom: .75rem;
      color: #333;
    }

    .order-body p,
    .order-body li {
      font-size: .95rem;
      color: #555;
    }

    .order-body ul {
      padding-left: 1rem;
    }

    .order-actions {
      padding: 1.5rem;
      border-top: 1px solid #e0e0e0;
      display: flex;
      gap: .75rem;
      justify-content: flex-end;
    }

    .btn-tokopedia {
      background-color: var(--tokopedia-green);
      color: #fff;
      border: none;
      padding: .5rem 1.25rem;
      font-weight: 600;
      border-radius: .375rem;
      display: flex;
      align-items: center;
      gap: .5rem;
    }

    .btn-detail {
      background-color: #fff;
      color: var(--tokopedia-green);
      border: 1px solid var(--tokopedia-green);
      padding: .5rem 1.25rem;
      font-weight: 600;
      border-radius: .375rem;
      display: flex;
      align-items: center;
      gap: .5rem;
    }

    .btn-tokopedia:hover,
    .btn-detail:hover {
      opacity: .9;
    }
  </style>

  <main class="pt-90" style="padding-top:0;">
    <section class="my-account container">
      <h2 class="page-title">Pesanan Saya</h2>
      <div class="row">
        {{-- Navbar akun --}}
        <div class="col-lg-2">
          @include('user.account-nav')
        </div>

        {{-- Konten pesanan --}}
        <div class="col-lg-10">
          @foreach ($orders as $order)
            @php
              // map status order
              switch ($order->status) {
                  case 'ordered':
                      $cls = 'badge-dipesan';
                      $lbl = 'Dipesan';
                      break;
                  case 'shipped':
                      $cls = 'badge-dikirim';
                      $lbl = 'Dikirim';
                      break;
                  case 'delivered':
                      $cls = 'badge-terkirim';
                      $lbl = 'Terkirim';
                      break;
                  case 'completed':
                      $cls = 'badge-selesai';
                      $lbl = 'Selesai';
                      break;
                  case 'canceled':
                      $cls = 'badge-dibatalkan';
                      $lbl = 'Dibatalkan';
                      break;
                  default:
                      $cls = 'badge-dipesan';
                      $lbl = ucfirst($order->status);
              }
              $latestPayment = $order->payments->pluck('transaction_status')->last() ?? 'unpaid';
              $needsPayment = in_array($latestPayment, ['pending', 'unpaid']);
            @endphp

            <div class="order-card">
              {{-- Header --}}
              <div class="order-header">
                <div class="order-id">
                  <i class="fa fa-hashtag"></i> #{{ $order->id }}
                  <small class="text-muted">— {{ $order->created_at->format('d M Y') }}</small>
                </div>
                <span class="badge-status {{ $cls }}">{{ $lbl }}</span>
              </div>

              {{-- Body --}}
              <div class="order-body">
                <div class="row gy-4">
                  <div class="col-md-4">
                    <h6>Penerima & Kontak</h6>
                    <p>{{ $order->recipient_name }}<br>{{ $order->phone }}</p>
                  </div>
                  <div class="col-md-4">
                    <h6>Alamat</h6>
                    <p>
                      {{ $order->full_address }}<br>
                      {{ $order->subdistrict }}, {{ $order->district }}<br>
                      {{ $order->city }}, {{ $order->province }}<br>
                      ZIP: {{ $order->zip_code }}
                    </p>
                  </div>
                  <div class="col-md-4">
                    <h6>Ringkasan Biaya</h6>
                    <p>Subtotal: <strong>Rp {{ number_format($order->subtotal, 2, ',', '.') }}</strong></p>
                    <p>PPN: <strong>Rp {{ number_format($order->tax, 2, ',', '.') }}</strong></p>
                    <p>Ongkir: <strong>Rp {{ number_format($order->shipping_cost, 2, ',', '.') }}</strong></p>
                    <p>Total: <strong>Rp {{ number_format($order->total, 2, ',', '.') }}</strong></p>
                  </div>
                </div>

                <hr>

                <div class="row gy-3">
                  <div class="col-md-6">
                    <h6>Detail Produk</h6>
                    <ul>
                      @foreach ($order->orderItems as $item)
                        <li>
                          {{ $item->product->name }} (x{{ $item->quantity }}) —
                          Rp {{ number_format($item->price * $item->quantity, 2, ',', '.') }}
                        </li>
                      @endforeach
                    </ul>
                  </div>
                  <div class="col-md-6">
                    <h6>Statistik</h6>
                    <p>Jumlah Item: {{ $order->orderItems->count() }}</p>
                    <p>Dikirim: {{ $order->delivered_at ? $order->delivered_at->format('d M Y') : '-' }}</p>
                  </div>
                </div>
              </div>

              {{-- Aksi --}}
              <div class="order-actions">
                @if ($needsPayment && session('snap_tokens')[$order->id] ?? false)
                  <button type="button" class="btn-tokopedia" data-token="{{ session('snap_tokens')[$order->id] }}">
                    <i class="fa fa-credit-card"></i> Bayar Sekarang
                  </button>
                @endif
                <a href="{{ route('user.order.details', ['order_id' => $order->id]) }}" class="btn-detail">
                  <i class="fa fa-eye"></i> Detail
                </a>
              </div>
            </div>
          @endforeach

          {{-- Pagination --}}
          <div class="d-flex justify-content-center mt-4">
            {{ $orders->links('pagination::bootstrap-5') }}
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
      const snapOptions = {
        onSuccess: handle,
        onPending: handle,
        onError: handle
      };
      document.querySelectorAll('.btn-tokopedia').forEach(btn => {
        btn.addEventListener('click', () => {
          snap.pay(btn.dataset.token, snapOptions);
        });
      });

      function handle(res) {
        fetch("{{ route('midtrans.notification') }}", {
          method: 'POST',
          headers: {
            'X-CSRF-TOKEN': '{{ csrf_token() }}',
            'Content-Type': 'application/json'
          },
          body: JSON.stringify(res)
        }).then(() => location.reload());
      }
    });
  </script>
@endpush
