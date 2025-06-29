@extends('layouts.app')

@section('content')
  <main class="pt-90">
    <section class="shop-checkout container">
      <h2 class="page-title">Pesanan Diterima</h2>

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
              <span class="badge bg-dark">Selesai</span>
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
                  <div class="fw-bold">@rupiahSymbol($item->price * $item->quantity)</div>
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

            {{-- Tombol detail --}}
            <a href="{{ route('user.orders') }}" class="btn btn-dark w-100 mt-3">
              Lihat Detail Pesanan
            </a>

          </div>
        </div>
      @endforeach

    </section>
  </main>
@endsection
