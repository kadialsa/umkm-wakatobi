@extends('layouts.app')

@section('content')
  <main class="pt-90">
    <section class="shop-checkout container">
      <h2 class="page-title">Pesanan Diterima</h2>

      {{-- Langkah checkout --}}
      <div class="checkout-steps mb-4">
        <a class="checkout-steps__item active">
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
            <span>Konfirmasi</span><em>Pesanan Anda Berhasil</em>
          </span>
        </a>
      </div>

      @foreach ($orders as $order)
        <div class="card mb-4 shadow-sm">
          <div class="card-body">

            {{-- Header pesanan --}}
            <div class="d-flex justify-content-between align-items-center mb-3">
              <div>
                <h5 class="mb-1">#{{ $order->id }}</h5>
                <small class="text-muted">{{ $order->created_at->format('d M Y, H:i') }}</small>
              </div>

              @php
                // Ambil semua transaction_status dari order_payments, dan ambil yang terakhir
                $statuses = $order->payments->pluck('transaction_status');
                $latestStatus = $statuses->last() ?? 'unpaid';

                // Mapping ke label + badge class
                if (in_array($latestStatus, ['capture', 'settlement'])) {
                    $label = 'Lunas';
                    $badge = 'bg-success';
                } elseif ($latestStatus === 'pending') {
                    $label = 'Menunggu';
                    $badge = 'bg-warning text-dark';
                } else {
                    $label = 'Belum Dibayar';
                    $badge = 'bg-secondary';
                }
              @endphp

              <span class="badge {{ $badge }}">{{ $label }}</span>
            </div>


            {{-- Penjual --}}
            <div class="mb-3 small text-secondary">
              Penjual: <strong>{{ $order->store->name }}</strong>
            </div>

            {{-- Daftar produk --}}
            <ul class="list-unstyled mb-3">
              @foreach ($order->orderItems as $item)
                <li class="d-flex align-items-center mb-2">
                  <img src="{{ asset('storage/' . $item->product->image) }}" alt="{{ $item->product->name }}"
                    class="rounded me-3" style="width:48px; height:48px; object-fit:cover;">
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

            {{-- Ringkasan biaya --}}
            <div class="border-top pt-3 small">
              <div class="d-flex justify-content-between mb-1">
                <span>Subtotal</span>
                <span>@rupiahSymbol($order->subtotal)</span>
              </div>
              <div class="d-flex justify-content-between mb-1">
                <span>Ongkir</span>
                <span>Gratis</span>
              </div>
              <div class="d-flex justify-content-between mb-2">
                <span>PPN ({{ config('cart.tax') }}%)</span>
                <span>@rupiahSymbol($order->tax)</span>
              </div>
              <div class="d-flex justify-content-between fw-bold">
                <span>Total Pembayaran</span>
                <span>@rupiahSymbol($order->total)</span>
              </div>
            </div>

            @php
              // Ambil status transaksi terbaru, default 'unpaid' kalau belum ada
              $latestStatus = $order->payments->pluck('transaction_status')->last() ?? 'unpaid';
            @endphp

            {{-- Tombol Bayar atau Lihat Detail --}}
            @if (in_array($latestStatus, ['capture', 'settlement']))
              {{-- Sudah lunas --}}
              <a href="#" class="btn btn-dark w-100 mt-3">
                Lihat Detail Pesanan
              </a>
            @else
              {{-- Belum dibayar / pending --}}
              <button type="button" class="btn btn-success w-100 pay-button mt-3"
                data-token="{{ session('snap_tokens')[$order->id] }}"
                aria-label="Bayar pesanan {{ $order->id }} ke {{ $order->store->name }}">
                Bayar ke {{ $order->store->name }} &mdash; @rupiahSymbol($order->total)
              </button>
            @endif


          </div>
        </div>
      @endforeach

    </section>
  </main>

  {{-- Midtrans Snap JS --}}
  <script src="https://{{ config('midtrans.is_production') ? 'app' : 'app.sandbox' }}.midtrans.com/snap/snap.js"
    data-client-key="{{ config('midtrans.client_key') }}"></script>

  {{-- Tombol ganti metode --}}
  <button id="change-method" class="btn btn-link d-none">
    Ganti Metode Pembayaran
  </button>

  <script>
    let lastToken = null;

    const snapOptions = {
      onSuccess: res => handleMidtrans(res),
      onPending: res => handleMidtrans(res),
      onError: res => handleMidtrans(res),
      onClose: () => {
        // munculkan tombol ganti ketika modal ditutup
        document.getElementById('change-method').classList.remove('d-none');
      }
    };

    document.querySelectorAll('.pay-button').forEach(btn => {
      btn.addEventListener('click', () => {
        lastToken = btn.dataset.token;
        document.getElementById('change-method').classList.add('d-none');
        snap.pay(lastToken, snapOptions);
      });
    });

    document.getElementById('change-method').addEventListener('click', () => {
      // sembunyikan modal yang sedang aktif
      snap.hide();
      // buka ulang Snap dengan daftar metode pembayaran
      snap.pay(lastToken, snapOptions);
      document.getElementById('change-method').classList.add('d-none');
    });

    function handleMidtrans(result) {
      fetch("{{ route('midtrans.notification') }}", {
        method: 'POST',
        headers: {
          'X-CSRF-TOKEN': '{{ csrf_token() }}',
          'Content-Type': 'application/json'
        },
        body: JSON.stringify(result)
      }).then(() => location.reload());
    }
  </script>
@endsection
