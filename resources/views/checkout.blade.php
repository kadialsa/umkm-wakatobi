@extends('layouts.app')

@section('content')
  <main class="pt-90">
    <div class="mb-4 pb-4"></div>
    <section class="shop-checkout container">
      <h2 class="page-title">Pengiriman & Pemesanan</h2>

      <div class="checkout-steps mb-4">
        <a href="{{ route('cart.index') }}" class="checkout-steps__item active">
          <span class="checkout-steps__item-number">01</span>
          <span class="checkout-steps__item-title">
            <span>Keranjang</span>
            <em>Kelola Daftar Barang</em>
          </span>
        </a>
        <a href="javascript:void(0)" class="checkout-steps__item active">
          <span class="checkout-steps__item-number">02</span>
          <span class="checkout-steps__item-title">
            <span>Pengiriman & Pembayaran</span>
            <em>Selesaikan Pesanan Anda</em>
          </span>
        </a>
        <a href="javascript:void(0)" class="checkout-steps__item">
          <span class="checkout-steps__item-number">03</span>
          <span class="checkout-steps__item-title">
            <span>Konfirmasi</span>
            <em>Periksa & Kirim Pesanan</em>
          </span>
        </a>
      </div>

      <form action="{{ route('cart.place.an.order') }}" method="POST">
        @csrf

        {{-- 1) Alamat Pengiriman --}}
        <div class="mb-4 p-3 border rounded">
          <h4>Detail Pengiriman</h4>

          @if ($address)
            {{-- Kirim address_id sebagai hidden field --}}
            <input type="hidden" name="address_id" value="{{ $address->id }}">

            <p>
              <strong>{{ $address->name }}</strong><br>
              {{ $address->address }}, {{ $address->locality }}<br>
              {{ $address->city }}, {{ $address->state }}, {{ $address->country }}<br>
              Kode Pos: {{ $address->zip }}<br>
              Telp: {{ $address->phone }}
            </p>
          @else
            <div class="row mt-4">
              <div class="col-md-6">
                <div class="form-floating mb-2">
                  <input type="text" name="name" class="form-control" required value="{{ old('name') }}">
                  <label>Nama Lengkap *</label>
                  @error('name')
                    <span class="text-danger">{{ $message }}</span>
                  @enderror
                </div>
              </div>
              {{-- … field phone, zip, state, city, address, locality, landmark … --}}
            </div>
          @endif
        </div>

        {{-- 2) Ringkasan Produk per Toko --}}
        @foreach (Cart::instance('cart')->content()->groupBy(fn($i) => $i->model->store_id) as $storeId => $group)
          @php $store = $group->first()->model->store; @endphp
          <div class="mb-3 p-3 border rounded">
            <h5 class="d-flex align-items-center">
              @if ($store->logo)
                <img src="{{ asset('storage/' . $store->logo) }}" alt="{{ $store->name }}" class="rounded-circle me-2"
                  style="width:32px;height:32px;object-fit:cover;">
              @else
                <div class="rounded-circle bg-secondary text-white d-flex justify-content-center align-items-center me-2"
                  style="width:32px;height:32px;font-size:.875rem;">
                  {{ Str::upper(Str::substr($store->name, 0, 1)) }}
                </div>
              @endif
              {{ $store->name }}
            </h5>
            <table class="table mb-2">
              <thead>
                <tr>
                  <th>Produk</th>
                  <th class="text-end">Subtotal</th>
                </tr>
              </thead>
              <tbody>
                @foreach ($group as $item)
                  <tr>
                    <td>{{ $item->name }} x {{ $item->qty }}</td>
                    <td class="text-end">
                      @rupiahSymbol($item->price * $item->qty)
                    </td>
                  </tr>
                @endforeach
              </tbody>
            </table>
          </div>
        @endforeach

        {{-- 3) Total Keseluruhan --}}
        @php
          $subAll = Cart::instance('cart')->content()->sum(fn($i) => $i->price * $i->qty);
          $taxRate = config('cart.tax') / 100;
          $taxAll = $subAll * $taxRate;
          $totalAll = $subAll + $taxAll;
        @endphp
        <div class="mb-4 p-3 border rounded">
          <p><strong>Subtotal:</strong> @rupiahSymbol($subAll)</p>
          <p><strong>PPN ({{ config('cart.tax') }}%):</strong> @rupiahSymbol($taxAll)</p>
          <p><strong>Total:</strong> @rupiahSymbol($totalAll)</p>
        </div>

        {{-- 4) Metode Pembayaran --}}
        <div class="mb-4 p-3 border rounded">
          <h5>Metode Pembayaran</h5>
          <div class="form-check mb-2">
            <input class="form-check-input" type="radio" name="mode" id="cod" value="cod" checked>
            <label class="form-check-label" for="cod">COD (Bayar di Tempat)</label>
          </div>
          <div class="form-check mb-2">
            <input class="form-check-input" type="radio" name="mode" id="card" value="card">
            <label class="form-check-label" for="card">BRI Virtual Account</label>
          </div>
          <div class="form-check">
            <input class="form-check-input" type="radio" name="mode" id="paypal" value="paypal">
            <label class="form-check-label" for="paypal">BCA Virtual Account</label>
          </div>
        </div>

        <button type="submit" class="btn btn-primary btn-checkout w-100">Buat Pesanan</button>
      </form>
    </section>
  </main>
@endsection
