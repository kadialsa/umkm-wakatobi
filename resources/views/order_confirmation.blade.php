@extends('layouts.app')

@section('content')
  <main class="pt-90">
    <section class="shop-checkout container pt-90">
      <h2 class="page-title">Konfirmasi Pembayaran</h2>

      {{-- Langkah checkout --}}
      <div class="checkout-steps mb-4">
        <a href="{{ route('cart.index') }}" class="checkout-steps__item active">
          <span class="checkout-steps__item-number">01</span>
          <span class="checkout-steps__item-title">
            <span>Keranjang</span><em>Kelola Daftar Barang</em>
          </span>
        </a>
        <a class="checkout-steps__item active">
          <span class="checkout-steps__item-number">02</span>
          <span class="checkout-steps__item-title">
            <span>Pengiriman & Pembayaran</span><em>Selesaikan Pesanan Anda</em>
          </span>
        </a>
        <a class="checkout-steps__item active">
          <span class="checkout-steps__item-number">03</span>
          <span class="checkout-steps__item-title">
            <span>Konfirmasi</span><em>Periksa & Pembayaran</em>
          </span>
        </a>
      </div>

      {{-- 1) Alamat Pengiriman --}}
      @php $first = $orders->first(); @endphp
      <div class="card mb-4 shadow-sm">
        <div class="card-body">
          <h5 class="mb-1">#{{ $first->id }}</h5>
          <small class="text-muted">{{ $first->created_at->format('d M Y, H:i') }}</small>

          <hr class="my-3">

          <h6 class="mb-1">Alamat Pengiriman</h6>
          <p class="small">
            <strong>{{ $first->recipient_name }}</strong><br>
            {{ $first->full_address }}<br>
            {{ $first->subdistrict }}, {{ $first->district }}<br>
            {{ $first->city }}, {{ $first->province }}<br>
            Kode Pos: {{ $first->zip_code }}<br>
            Telp: {{ $first->phone }}
          </p>
        </div>
      </div>

      {{-- 2) Detail per Store --}}
      @foreach ($orders as $order)
        <div class="card mb-4 shadow-sm">
          <div class="card-body">
            @php
              // Hitung status terakhir
              $latest = $order->payments->pluck('transaction_status')->last() ?? 'unpaid';
              if (in_array($latest, ['capture', 'settlement'])) {
                  $statusLabel = 'Lunas';
                  $statusClass = 'badge bg-success';
              } elseif ($latest === 'pending') {
                  $statusLabel = 'Menunggu';
                  $statusClass = 'badge bg-warning text-dark';
              } else {
                  $statusLabel = 'Belum Dibayar';
                  $statusClass = 'badge bg-secondary';
              }
            @endphp

            <div class="mb-3 d-flex justify-content-between align-items-center small text-secondary">
              <div>
                Penjual: <strong>{{ $order->store->name }}</strong>
              </div>
              <div>
                <span class="{{ $statusClass }}">{{ $statusLabel }}</span>
              </div>
            </div>


            {{-- Daftar Produk --}}
            <ul class="list-unstyled mb-3">
              @foreach ($order->orderItems as $item)
                <li class="d-flex align-items-center mb-2">

                  {{-- @dd($item->product->image) --}}

                  <img loading="lazy"
                    src="{{ $item->product->image
                        ? asset('uploads/products/' . $item->product->image)
                        : 'https://www.svgrepo.com/show/508699/landscape-placeholder.svg' }}"
                    alt="{{ $item->product->name ?? 'No Image' }}" class="rounded me-3"
                    style="width:48px;height:48px;object-fit:cover;">
                  <div class="flex-fill">
                    {{ $item->product->name }}<br>
                    <small class="text-muted">x{{ $item->quantity }}</small>
                  </div>
                  <div class="fw-bold">
                    @rupiahSymbol($item->price * $item->quantity)
                  </div>
                </li>
              @endforeach
            </ul>

            {{-- Ringkasan Biaya --}}
            <div class="border-top pt-3 small">
              <div class="d-flex justify-content-between mb-1">
                <span>Subtotal</span>
                <span>@rupiahSymbol($order->subtotal)</span>
              </div>
              <div class="d-flex justify-content-between mb-1">
                <span>Ongkir ({{ strtoupper($order->shipping_service) }})</span>
                <span>@rupiahSymbol($order->shipping_cost)</span>
              </div>
              <div class="d-flex justify-content-between mb-1">
                <span>PPN ({{ config('cart.tax') }}%)</span>
                <span>@rupiahSymbol($order->tax)</span>
              </div>
              <div class="d-flex justify-content-between fw-bold">
                <span>Total Pembayaran</span>
                <span>@rupiahSymbol($order->total)</span>
              </div>
            </div>

            {{-- Tombol Bayar atau Lihat Detail --}}
            @if (in_array($latest, ['capture', 'settlement']))
              <a href="{{ route('user.orders') }}" class="btn btn-dark w-100 mt-3">Lihat Detail Pesanan</a>
            @else
              <button type="button" class="btn btn-success w-100 pay-button mt-3"
                data-token="{{ session('snap_tokens')[$order->id] }}"
                aria-label="Bayar pesanan {{ $order->id }} ke {{ $order->store->name }}">
                Bayar ke {{ $order->store->name }} &mdash; @rupiahSymbol($order->total)
              </button>
            @endif
          </div>
        </div>
      @endforeach


      {{-- Ganti Metode Pembayaran --}}
      <button id="change-method" class="btn btn-link d-none mb-3">
        Ganti Metode Pembayaran
      </button>
    </section>
  </main>
@endsection

@push('scripts')
  {{-- Midtrans Snap JS --}}
  <script src="https://{{ config('midtrans.is_production') ? 'app' : 'app.sandbox' }}.midtrans.com/snap/snap.js"
    data-client-key="{{ config('midtrans.client_key') }}"></script>

  {{-- Payment Handler --}}
  <script>
    document.addEventListener('DOMContentLoaded', function() {
      let lastToken = null;
      const opts = {
        onSuccess: handle,
        onPending: handle,
        onError: handle,
        onClose: () => document.getElementById('change-method').classList.remove('d-none')
      };

      document.querySelectorAll('.pay-button').forEach(btn => {
        btn.addEventListener('click', () => {
          lastToken = btn.dataset.token;
          document.getElementById('change-method').classList.add('d-none');
          snap.pay(lastToken, opts);
        });
      });

      document.getElementById('change-method').addEventListener('click', () => {
        snap.hide();
        snap.pay(lastToken, opts);
        document.getElementById('change-method').classList.add('d-none');
      });

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
