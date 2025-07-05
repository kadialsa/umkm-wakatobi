@extends('layouts.app')

@section('content')
  <style>
    :root {
      /* --tokopedia-green: #00ab55; */
      --tokopedia-light: #f0fdf6;
    }

    .page-title {
      color: var(--tokopedia-green);
      font-size: 1.75rem;
      font-weight: 700;
      margin-bottom: 1rem;
    }

    .order-card {
      border: 1px solid #e0e0e0;
      border-radius: .5rem;
      background: #fff;
      box-shadow: 0 1px 4px rgba(0, 0, 0, 0.05);
      margin-bottom: 1rem;
      transition: transform .15s;
    }

    .order-card:hover {
      transform: translateY(-1px);
      box-shadow: 0 2px 6px rgba(0, 0, 0, 0.1);
    }

    .order-header {
      background: #f7f7f7;
      border-bottom: 1px solid #e0e0e0;
      padding: .75rem 1rem;
      display: flex;
      justify-content: space-between;
      align-items: center;
      font-size: .95rem;
    }

    .order-id {
      font-weight: 600;
    }

    .badge-status {
      padding: .2rem .5rem;
      border-radius: .25rem;
      font-size: .7rem;
      font-weight: 500;
    }

    .badge-dipesan {
      background: #f5d700;
      color: #000;
    }

    .badge-dikirim {
      background: #17a2b8;
      color: #fff;
    }

    .badge-terkirim {
      background: var(--tokopedia-green);
      color: #fff;
    }

    .badge-selesai {
      background: #007bff;
      color: #fff;
    }

    .badge-dibatalkan {
      background: #f44032;
      color: #fff;
    }

    .order-body {
      padding: .8rem;
      font-size: .8rem;
    }

    .order-body h6 {
      font-size: .95rem;
      margin-bottom: .5rem;
      color: #333;
    }

    .order-body p,
    .order-body li {
      margin: .25rem 0;
      color: #555;
    }

    .order-body ul {
      padding-left: 1rem;
      margin: .5rem 0;
    }

    .order-actions {
      padding: .6rem 1rem;
      border-top: 1px solid #e0e0e0;
      display: flex;
      gap: .5rem;
      justify-content: flex-end;
    }

    .btn-tokopedia,
    .btn-detail {
      padding: .4rem .75rem;
      font-size: .875rem;
    }
  </style>

  <main class="pt-90">
    <div class="mb-4 pb-4"></div>
    <section class="my-account container">
      <h2 class="page-title">Pesanan Saya</h2>
      <div class="row">
        <div class="col-lg-3">
          @include('user.account-nav')
        </div>
        <div class="col-lg-9 ">
          <div class="page-content my-account__address">
            @forelse($orders as $order)
              @php
                $map = [
                    'ordered' => ['Dipesan', 'badge-dipesan'],
                    'shipped' => ['Dikirim', 'badge-dikirim'],
                    'delivered' => ['Terkirim', 'badge-terkirim'],
                    'completed' => ['Selesai', 'badge-selesai'],
                    'canceled' => ['Dibatalkan', 'badge-dibatalkan'],
                ];
                [$lbl, $cls] = $map[$order->status] ?? [ucfirst($order->status), 'badge-secondary'];
                $token = $snapTokens[$order->id] ?? null;
              @endphp

              <div class="order-card">
                <div class="order-header">
                  <div class="order-id">
                    #{{ $order->id }} <small class="text-muted">{{ $order->created_at->format('d M Y') }}</small>
                  </div>
                  <span class="badge-status {{ $cls }}">{{ $lbl }}</span>
                </div>

                <div class="order-body">
                  <div class="row gy-2">
                    <div class="col-4">
                      <h6>Penerima</h6>
                      <p>{{ $order->recipient_name }}<br><small>{{ $order->phone }}</small></p>
                    </div>
                    <div class="col-4">
                      <h6>Alamat</h6>
                      <p class="mb-0">{{ $order->full_address }}</p>
                      <small>{{ $order->subdistrict }}, {{ $order->city }}</small>
                    </div>
                    <div class="col-4">
                      <h6>Biaya</h6>
                      <p class="mb-0">Subtotal: Rp {{ number_format($order->subtotal, 0, ',', '.') }}</p>
                      <p class="mb-0">Total: Rp {{ number_format($order->total, 0, ',', '.') }}</p>
                    </div>
                  </div>
                  <hr class="my-2">
                  <h6>Produk</h6>
                  <ul class="mb-0">
                    @foreach ($order->orderItems as $item)
                      <li>
                        {{ $item->product->name }} (x{{ $item->quantity }}) —
                        Rp {{ number_format($item->price * $item->quantity, 0, ',', '.') }}
                      </li>
                    @endforeach
                  </ul>
                </div>

                <div class="order-actions">
                  @if ($token)
                    <button class="btn btn-success pay-now" data-token="{{ $token }}">
                      <i class="fa fa-credit-card"></i> Bayar
                    </button>
                  @endif
                  <a href="{{ route('user.order.details', $order->id) }}" class="btn btn-dark btn-sm">
                    <i class="fa fa-search me-1"></i> Detail
                  </a>
                </div>
              </div>
            @empty
              <p class="text-center text-muted">Belum ada pesanan.</p>
            @endforelse

            @if (method_exists($orders, 'links'))
              <div class="d-flex justify-content-center mt-3">
                {{ $orders->links('pagination::bootstrap-5') }}
              </div>
            @endif
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
      const opts = {
        onSuccess: handle,
        onPending: handle,
        onError: handle
      };
      document.querySelectorAll('.pay-now').forEach(btn =>
        btn.addEventListener('click', () => snap.pay(btn.dataset.token, opts))
      );

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
