@extends('layouts.app')
@section('content')
  <style>
    .text-success {
      color: #278c04 !important;
    }

    .text-danger {
      color: #d61808 !important;
    }

    .store-header td {
      font-size: 1rem;
      background-color: #f1f1f1;
    }

    /* Baris coupon per toko */
    .store-coupon td {
      background-color: #ffffff;
      padding: 0.75rem;
    }

    /* Form coupon styling */
    .store-coupon form {
      display: flex;
      align-items: center;
      gap: 0.5rem;
    }

    /* Input coupon */
    .store-coupon input[name="coupon_code"] {
      flex: 1;
      max-width: 250px;
      padding: 0.5rem 0.75rem;
      border: 1px solid #ced4da;
      border-radius: 0.375rem;
      font-size: 0.875rem;
    }

    /* Tombol apply/remove */
    .store-coupon button {
      padding: 0.5rem 0.75rem;
      font-size: 0.875rem;
      border-radius: 0.375rem;
    }

    /* Tombol apply */
    .store-coupon button[type="submit"] {
      background-color: #0d6efd;
      border: 1px solid #0d6efd;
      color: #fff;
    }

    /* Tombol remove */
    .store-coupon button[type="submit"][value="Remove"],
    .store-coupon button[type="submit"][value="remove"] {
      background-color: #dc3545;
      border: 1px solid #dc3545;
      color: #fff;
    }

    /* Hover states */
    .store-coupon button[type="submit"]:hover {
      opacity: 0.9;
    }

    /* Responsiveness */
    @media (max-width: 576px) {
      .store-coupon form {
        flex-direction: column;
        align-items: stretch;
      }

      .store-coupon input[name="coupon_code"],
      .store-coupon button {
        width: 100%;
      }
    }
  </style>

  <main class="pt-90">
    <div class="mb-4 pb-4"></div>
    <section class="shop-checkout container">
      <h2 class="page-title">Keranjang Belanja</h2>
      <div class="checkout-steps">
        <a href="javascript:void(0)" class="checkout-steps__item active">
          <span class="checkout-steps__item-number">01</span>
          <span class="checkout-steps__item-title">
            <span>Keranjang</span><em>Kelola Daftar Barang</em>
          </span>
        </a>
        <a href="javascript:void(0)" class="checkout-steps__item">
          <span class="checkout-steps__item-number">02</span>
          <span class="checkout-steps__item-title">
            <span>Pengiriman & Pemesanan</span><em>Selesaikan Pesanan Anda</em>
          </span>
        </a>
        <a href="javascript:void(0)" class="checkout-steps__item">
          <span class="checkout-steps__item-number">03</span>
          <span class="checkout-steps__item-title">
            <span>Konfirmasi</span>
            <em>Periksa & Pembayaran</em>
          </span>
        </a>
      </div>
      <div class="shopping-cart">
        @if ($items->count() > 0)
          <div class="cart-table__wrapper">
            <table class="cart-table">
              <thead>
                <tr>
                  <th>Produk</th>
                  <th></th>
                  <th>Harga</th>
                  <th>Jumlah</th>
                  <th>Subtotal</th>
                  <th></th>
                </tr>
              </thead>
              <tbody>
                @foreach ($items->groupBy(fn($item) => $item->model->store->id) as $storeId => $group)
                  {{-- Header nama toko --}}
                  <tr class="store-header">
                    <td colspan="6" style="background: #f1f1f1; font-weight: 600; padding: .4rem;">
                      <div class="d-flex align-items-center">
                        @php $store = $group->first()->model->store; @endphp

                        @if ($store->logo)
                          <img src="{{ asset('storage/' . $store->logo) }}" alt="{{ $store->name }}"
                            class="rounded-circle me-2" style="width:32px; height:32px; object-fit:cover;">
                        @else
                          <div
                            class="rounded-circle bg-secondary text-white d-flex justify-content-center align-items-center me-2"
                            style="width:32px; height:32px; font-size:0.875rem;">
                            {{ Str::upper(Str::substr($store->name, 0, 1)) }}
                          </div>
                        @endif

                        <span>{{ $store->name }}</span>
                      </div>
                    </td>

                  </tr>

                  {{-- Coupon per toko --}}
                  {{-- <tr class="store-coupon">
                    <td colspan="6">
                      @php
                        $coupons = session('coupons', []);
                        $hasCoupon = isset($coupons[$storeId]);
                      @endphp

                      @if (!$hasCoupon)
                        <form action="{{ route('cart.coupon.apply') }}" method="POST" class="d-flex align-items-center">
                          @csrf
                          <input type="hidden" name="store_id" value="{{ $storeId }}">
                          <input class="form-control me-2" style="max-width: 200px;" type="text" name="coupon_code"
                            placeholder="Kode Kupon {{ $group->first()->model->store->name }}">
                          <button class="btn btn-sm btn-primary" type="submit">Terapkan</button>
                        </form>
                      @else
                        <div class="d-flex align-items-center">
                          <span class="me-3">Kupon “{{ $coupons[$storeId]['code'] }}” diterapkan</span>
                          <form action="{{ route('cart.coupon.remove') }}" method="POST">
                            @csrf @method('DELETE')
                            <input type="hidden" name="store_id" value="{{ $storeId }}">
                            <button class="btn btn-sm btn-danger" type="submit">Hapus</button>
                          </form>
                        </div>
                      @endif
                    </td>
                  </tr> --}}

                  {{-- Baris produk --}}
                  @foreach ($group as $item)
                    <tr>
                      <td>
                        <div class="shopping-cart__product-item">
                          <img loading="lazy"
                            src="{{ $item->model->image
                                ? asset('uploads/products/thumbnails/' . $item->model->image)
                                : 'https://www.svgrepo.com/show/508699/landscape-placeholder.svg' }}"
                            width="120" height="120" alt="{{ $item->name }}" class="pc__img py-2">

                        </div>
                      </td>
                      <td>
                        <div class="shopping-cart__product-item__detail">
                          <h4>{{ $item->name }}</h4>
                          <ul class="shopping-cart__product-item__options">
                            <li>Warna: Kuning</li>
                            <li>Ukuran: L</li>
                          </ul>
                        </div>
                      </td>
                      <td>
                        <span class="shopping-cart__product-price">Rp.{{ $item->price }}</span>
                      </td>
                      <td>
                        <div class="qty-control position-relative">
                          <input type="number" name="quantity" value="{{ $item->qty }}" min="1"
                            class="qty-control__number text-center">
                          <form method="POST" action="{{ route('cart.qty.decrease', ['rowId' => $item->rowId]) }}">
                            @csrf @method('PUT')
                            <div class="qty-control__reduce">-</div>
                          </form>
                          <form method="POST" action="{{ route('cart.qty.increase', ['rowId' => $item->rowId]) }}">
                            @csrf @method('PUT')
                            <div class="qty-control__increase">+</div>
                          </form>
                        </div>
                      </td>
                      <td>
                        <span class="shopping-cart__subtotal">Rp.{{ $item->subTotal() }}</span>
                      </td>
                      <td>
                        <form method="POST" action="{{ route('cart.item.remove', ['rowId' => $item->rowId]) }}">
                          @csrf @method('DELETE')
                          <a href="javascript:void(0)" class="remove-cart">
                            <svg width="10" height="10" viewBox="0 0 10 10" fill="#767676"
                              xmlns="http://www.w3.org/2000/svg">
                              <path d="M0.259435 8.85506L9.11449 0L10 0.885506L1.14494 9.74056L0.259435 8.85506Z" />
                              <path
                                d="M0.885506 0.0889838L9.74057 8.94404L8.85506 9.82955L0 0.97449L0.885506 0.0889838Z" />
                            </svg>
                          </a>
                        </form>
                      </td>
                    </tr>
                  @endforeach
                @endforeach
              </tbody>
            </table>

            <div class="cart-table-footer">
              @if (!Session::has('coupon'))
                <form action="{{ route('cart.coupon.apply') }}" method="POST" class="position-relative bg-body">
                  @csrf
                  <input class="form-control" type="text" name="coupon_code" placeholder="Kode Kupon">
                  <input class="btn-link fw-medium position-absolute top-0 end-0 h-100 px-4" type="submit"
                    value="Terapkan">
                </form>
              @else
                <form action="{{ route('cart.coupon.remove') }}" method="POST" class="position-relative bg-body">
                  @csrf
                  @method('DELETE')
                  <input class="form-control" type="text" name="coupon_code" placeholder="Kode Kupon"
                    value="{{ Session::get('coupon')['code'] }} diterapkan!">
                  <input class="btn-link fw-medium position-absolute top-0 end-0 h-100 px-4" type="submit"
                    value="Hapus Kupon">
                </form>
              @endif

              <form action="{{ route('cart.empty') }}" method="POST">
                @csrf
                @method('DELETE')
                <button class="btn btn-light" type="submit">Kosongkan</button>
              </form>
            </div>
            <div>
              @if (Session::has('success'))
                <p class="text-success">{{ Session::get('success') }}</p>
              @elseif(Session::has('error'))
                <p class="text-danger">{{ Session::get('error') }}</p>
              @endif
            </div>
          </div>
          <div class="shopping-cart__totals-wrapper">
            <div class="sticky-content">
              <div class="shopping-cart__totals">
                <h3>Total Keranjang</h3>

                @if (Session::has('discounts'))
                  <table class="cart-totals">
                    <tbody>
                      <tr>
                        <th>Subtotal</th>
                        <td>Rp.{{ Cart::instance('cart')->subTotal() }}</td>
                      </tr>
                      <tr>
                        <th>Diskon {{ Session::get('coupon')['code'] }}</th>
                        <td>Rp.{{ Session::get('discounts')['discount'] }}</td>
                      </tr>
                      <tr>
                        <th>Subtotal Setelah Diskon</th>
                        <td>Rp.{{ Session::get('discounts')['subtotal'] }}</td>
                      </tr>
                      {{-- <tr>
                        <th>Ongkos Kirim</th>
                        <td>Gratis</td>
                      </tr> --}}
                      <tr>
                        <th>PPN</th>
                        <td>Rp.{{ Session::get('discounts')['tax'] }}</td>
                      </tr>
                      <tr>
                        <th>Total</th>
                        <td>Rp.{{ Session::get('discounts')['total'] }}</td>
                      </tr>
                    </tbody>
                  </table>
                @else
                  <table class="cart-totals">
                    <tbody>
                      <tr>
                        <th>Subtotal</th>
                        <td>Rp.{{ Cart::instance('cart')->subTotal() }}</td>
                      </tr>
                      {{-- <tr>
                        <th>Ongkos Kirim</th>
                        <td>Gratis</td>
                      </tr> --}}
                      <tr>
                        <th>PPN</th>
                        <td>Rp.{{ Cart::instance('cart')->tax() }}</td>
                      </tr>
                      <tr>
                        <th>Total</th>
                        <td>Rp.{{ Cart::instance('cart')->total() }}</td>
                      </tr>
                    </tbody>
                  </table>
                @endif
              </div>
              <div class="mobile_fixed-btn_wrapper">
                <div class="button-wrapper container">
                  <a href="{{ route('cart.checkout') }}" class="btn btn-primary btn-checkout">Lanjutkan Pembayaran</a>
                </div>
              </div>
            </div>
          </div>
        @else
          <div class="row">
            <div class="col-md-12 text-center pt-5 bp-5">
              <p>Tidak ada item di keranjang</p>
              <a href="{{ route('shop.index') }}" class="btn btn-info">Belanja Sekarang</a>
            </div>
          </div>
        @endif
      </div>
    </section>
  </main>

@endsection

@push('scripts')
  <script>
    $(function() {
      $(".qty-control__increase").on("click", function() {
        $(this).closest('form').submit();
      });

      $(".qty-control__reduce").on("click", function() {
        $(this).closest('form').submit();
      });

      $(".remove-cart").on("click", function() {
        $(this).closest('form').submit();
      })
    })
  </script>
@endpush
