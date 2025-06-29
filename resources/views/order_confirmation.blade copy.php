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
        <div class="order-complete mb-5">
          <div class="order-complete__message text-start mb-0">
            {{-- <svg width="80" height="80" viewBox="0 0 80 80" fill="none">
              <circle cx="40" cy="40" r="40" fill="#B9A16B" />
              <path d="M52.97 35.76...Z" fill="white" />
            </svg> --}}
            <h5>Pesanan dari <strong>{{ $order->store->name }}</strong> Berhasil!</h5>
            <p>Terima kasih. Pesanan Anda telah diterima.</p>
          </div>

          <div class="order-info mb-3">
            <div class="order-info__item">
              <label>Nomor Pesanan</label>
              <span>{{ $order->id }}</span>
            </div>
            <div class="order-info__item">
              <label>Tanggal</label>
              <span>{{ $order->created_at->format('d M Y H:i') }}</span>
            </div>
            <div class="order-info__item">
              <label>Total</label>
              <span>@rupiahSymbol($order->total)</span>
            </div>
            <div class="order-info__item">
              <label>Metode Pembayaran</label>
              <span>{{ strtoupper($order->transaction->status === 'pending' ? 'COD' : $order->transaction->mode) }}</span>
            </div>
          </div>

          <div class="checkout__totals-wrapper">
            <h4>Detail Pesanan</h4>
            <table class="checkout-cart-items table mb-3">
              <thead>
                <tr>
                  <th>Produk</th>
                  <th class="text-end">Subtotal</th>
                </tr>
              </thead>
              <tbody>
                @foreach ($order->orderItems as $item)
                  <tr>
                    <td>{{ $item->product->name }} x {{ $item->quantity }}</td>
                    <td class="text-end">@rupiahSymbol($item->price * $item->quantity)</td>
                  </tr>
                @endforeach
              </tbody>
            </table>

            <table class="checkout-totals table">
              <tbody>
                <tr>
                  <th>Subtotal</th>
                  <td class="text-end">@rupiahSymbol($order->subtotal)</td>
                </tr>
                <tr>
                  <th>Diskon</th>
                  <td class="text-end">@rupiahSymbol($order->discount)</td>
                </tr>
                <tr>
                  <th>Free Shipping</th>
                  <td class="text-end">Gratis</td>
                </tr>
                <tr>
                  <th>PPN ({{ config('cart.tax') }}%)</th>
                  <td class="text-end">@rupiahSymbol($order->tax)</td>
                </tr>
                <tr>
                  <th>Total</th>
                  <td class="text-end">@rupiahSymbol($order->total)</td>
                </tr>
              </tbody>
            </table>
          </div>
        </div>
      @endforeach

    </section>
  </main>
@endsection
