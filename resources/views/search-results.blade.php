{{-- resources/views/search/results.blade.php --}}
@extends('layouts.app')

@section('content')
  <style>
    .filled-heart {
      color: orange;
    }

    /* Tambahkan styling khusus search page di sini jika perlu */
  </style>

  <main class="pt-90">
    <section class="shop-main container d-flex pt-4 pt-xl-5">
      {{-- Kosongkan sidebar sepenuhnya --}}
      <div class="d-none d-lg-block" style="width:0;"></div>

      {{-- Main content --}}
      <div class="flex-grow-1">

        {{-- (2) Breadcrumb --}}
        <nav aria-label="breadcrumb" class="mb-4 d-none d-md-block">
          <ol class="breadcrumb bg-transparent px-0">
            {{-- <li class="breadcrumb-item">
              <a href="{{ route('home.index') }}">Beranda</a>
            </li> --}}
            <li class="breadcrumb-item active text-dark" aria-current="page">Hasil Pencarian</li>
          </ol>
        </nav>

        {{-- (3) Kontrol Tampilkan & Urutkan --}}
        {{-- <div class="d-flex justify-content-between align-items-center mb-3">
          <div>
            <span class="me-2">Tampilkan:</span>
            <select id="pagesize" class="form-select d-inline-block w-auto">
              <option value="12" {{ $products->perPage() == 12 ? 'selected' : '' }}>12</option>
              <option value="24" {{ $products->perPage() == 24 ? 'selected' : '' }}>24</option>
              <option value="48" {{ $products->perPage() == 48 ? 'selected' : '' }}>48</option>
            </select>
          </div>
          <div>
            <span class="me-2">Urutkan:</span>
            <select id="orderby" class="form-select d-inline-block w-auto">
              <option value="default"{{ request('sort') == 'default' ? ' selected' : '' }}>Default</option>
              <option value="new_desc"{{ request('sort') == 'new_desc' ? ' selected' : '' }}>Terbaru</option>
              <option value="price_asc"{{ request('sort') == 'price_asc' ? ' selected' : '' }}>Harga: Rendah → Tinggi
              </option>
              <option value="price_desc"{{ request('sort') == 'price_desc' ? ' selected' : '' }}>Harga: Tinggi → Rendah
              </option>
            </select>
          </div>
        </div> --}}

        {{-- (4) Grid Produk --}}
        @if ($products->isEmpty())
          <p class="text-center text-muted">Tidak ada produk ditemukan untuk kata kunci “{{ $q }}”.</p>
        @else
          <div class="row row-cols-2 row-cols-md-3 row-cols-lg-4 g-4 pt-4" id="products-grid">
            @foreach ($products as $product)
              <div class="product-card-wrapper">
                <div class="product-card mb-3 mb-md-4 mb-xxl-5">
                  <div class="pc__img-wrapper">
                    <div class="swiper-container background-img js-swiper-slider"
                      data-settings='{"resizeObserver": true}'>
                      <div class="swiper-wrapper">

                        <div class="swiper-slide">
                          <a href="{{ route('shop.product.details', ['product_slug' => $product->slug]) }}">
                            <img loading="lazy"
                              src="{{ $product->image
                                  ? asset('uploads/products/' . $product->image)
                                  : 'https://www.svgrepo.com/show/508699/landscape-placeholder.svg' }}"
                              width="330" height="400" alt="{{ $product->name }}" class="pc__img"
                              style="border-radius: 12px;">

                          </a>
                        </div>
                        <div class="swiper-slide">
                          @foreach (explode(',', $product->images) as $gimg)
                            <a href="{{ route('shop.product.details', ['product_slug' => $product->slug]) }}">
                              <img loading="lazy"
                                src="{{ $gimg ? asset('uploads/products/' . $gimg) : 'https://www.svgrepo.com/show/508699/landscape-placeholder.svg' }}"
                                width="330" height="400" alt="{{ $product->name }}" class="pc__img"
                                style="border-radius: 12px;">
                            </a>
                          @endforeach

                        </div>

                      </div>
                      <span class="pc__img-prev"><svg width="7" height="11" viewBox="0 0 7 11"
                          xmlns="http://www.w3.org/2000/svg">
                          <use href="#icon_prev_sm" />
                        </svg></span>
                      <span class="pc__img-next"><svg width="7" height="11" viewBox="0 0 7 11"
                          xmlns="http://www.w3.org/2000/svg">
                          <use href="#icon_next_sm" />
                        </svg></span>
                    </div>
                    @if (Cart::instance('cart')->content()->where('id', $product->id)->count() > 0)
                      <a href="{{ route('cart.index') }}"
                        class="pc__atc btn anim_appear-bottom btn position-absolute border-0 text-uppercase fw-medium btn-warning mb-3">Go
                        to Cart</a>
                    @else
                      <form name="addtocart-form" method="post" action="{{ route('cart.add') }}">
                        @csrf
                        <input type="hidden" name="id" value="{{ $product->id }}">
                        <input type="hidden" name="quantity" value="1">
                        <input type="hidden" name="name" value="{{ $product->name }}">
                        <input type="hidden" name="price"
                          value="{{ $product->sale_price == '' ? $product->regular_price : $product->sale_price }}">
                        <button type="submit"
                          class="pc__atc btn anim_appear-bottom btn position-absolute border-0 text-uppercase fw-medium"
                          data-aside="cartDrawer" title="Add To Cart">+ Keranjang</button>
                      </form>
                    @endif
                  </div>

                  <div class="pc__info position-relative">
                    <p class="pc__category">{{ $product->category->name }}</p>
                    <h6 class="pc__title"><a
                        href="{{ route('shop.product.details', ['product_slug' => $product->slug]) }}">{{ $product->name }}</a>
                    </h6>
                    <div class="product-card__price d-flex">
                      <span class="money price">
                        @if ($product->sale_price)
                          <s>@rupiahSymbol($product->regular_price)</s>
                          @rupiahSymbol($product->sale_price)
                        @else
                          @rupiahSymbol($product->regular_price)
                        @endif
                      </span>

                    </div>

                    {{-- review --}}
                    {{-- end review --}}

                    {{-- wishlist --}}
                    @if (Cart::instance('wishlist')->content()->where('id', $product->id)->count() > 0)
                      <form method="POST"
                        action="{{ route('wishlis.item.remove', ['rowId' => Cart::instance('wishlist')->content()->where('id', $product->id)->first()->rowId]) }}">
                        @csrf
                        @method('DELETE')
                        <button
                          class="pc__btn-wl position-absolute top-0 end-0 bg-transparent border-0 js-add-wishlist filled-heart"
                          title="Remove from Wishlist">
                          <svg width="16" height="16" viewBox="0 0 20 20" fill="none"
                            xmlns="http://www.w3.org/2000/svg">
                            <use href="#icon_heart" />
                          </svg>
                        </button>
                      </form>
                    @else
                      <form method="POST" action="{{ route('wishlist.add') }}">
                        @csrf
                        <input type="hidden" name="id" value="{{ $product->id }}">
                        <input type="hidden" name="name" value="{{ $product->name }}">
                        <input type="hidden" name="price"
                          value="{{ $product->sale_price == '' ? $product->regular_price : $product->sale_price }}">
                        <input type="hidden" name="quantity" value="1">
                        <button class="pc__btn-wl position-absolute top-0 end-0 bg-transparent border-0 js-add-wishlist"
                          title="Add To Wishlist">
                          <svg width="16" height="16" viewBox="0 0 20 20" fill="none"
                            xmlns="http://www.w3.org/2000/svg">
                            <use href="#icon_heart" />
                          </svg>
                        </button>
                      </form>
                    @endif
                  </div>
                </div>
              </div>
            @endforeach
          </div>

          {{-- (5) Pagination --}}
          <div class="d-flex justify-content-center mt-4">
            {{ $products->appends(['q' => $q, 'perPage' => $products->perPage(), 'sort' => request('sort')])->links('pagination::bootstrap-5') }}
          </div>
        @endif

      </div>
    </section>
  </main>
@endsection

{{-- @push('scripts')
  <script>
    $(function() {
      // rubah jumlah per halaman
      $('#pagesize').on('change', () => {
        const perPage = $('#pagesize').val();
        const url = new URL(window.location.href);
        url.searchParams.set('perPage', perPage);
        window.location.href = url;
      });
      // rubah urutan
      $('#orderby').on('change', () => {
        const sort = $('#orderby').val();
        const url = new URL(window.location.href);
        url.searchParams.set('sort', sort);
        window.location.href = url;
      });
      // inisialisasi swiper (jika dipakai)
      new Swiper('.js-swiper-slider-features', {
        loop: true,
        autoplay: {
          delay: 5000
        },
        effect: 'fade',
        pagination: {
          el: '.slideshow-pagination',
          clickable: true
        }
      });
    });
  </script>
@endpush --}}

@push('scripts')
  <script>
    $(function() {
      $("#pagesize").on("change", function() {
        $("#size").val($("#pagesize option:selected").val());
        $("#frmfilter").submit();
      });

      $("#orderby").on("change", function() {
        $("#order").val($("#orderby option:selected").val());
        $("#frmfilter").submit();
      });
      // brand
      $("input[name='brands']").on("change", function() {
        var brands = "";
        $("input[name='brands']:checked").each(function() {
          if (brands == "") {
            brands = $(this).val();
          } else {
            brands += "," + $(this).val();
          }
        });
        $("#hdnBrands").val(brands);
        $("#frmfilter").submit();
      });
      // category
      $("input[name='categories']").on("change", function() {
        var categories = "";
        $("input[name='categories']:checked").each(function() {
          if (categories == "") {
            categories = $(this).val();
          } else {
            categories += "," + $(this).val();
          }
        });
        $("#hdnCategories").val(categories);
        $("#frmfilter").submit();
      });

      // Price
      $("[name='price_range']").on("change", function() {
        var min = $(this).val().split(',')[0];
        var max = $(this).val().split(',')[1];
        $("#hdMinPrice").val(min);
        $("#hdMaxPrice").val(max);
        setTimeout(() => {
          $("#frmfilter").submit();
        }, 2000);
      });
    });

    // slider
    const swiper = new Swiper('.js-swiper-slider-features', {
      loop: true,
      autoplay: {
        delay: 3000, // 3 detik
        disableOnInteraction: false,
      },
      speed: 600,
      spaceBetween: 30,
      pagination: {
        el: '.swiper-pagination',
        clickable: true
      },
      navigation: {
        nextEl: '.swiper-button-next',
        prevEl: '.swiper-button-prev'
      },
    });
  </script>
@endpush
