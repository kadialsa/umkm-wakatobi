@extends('layouts.app')
@section('content')
  <style>
    .filled-heart {
      color: orange;
    }

    /* Make product images rounded */
    .product-single__image-item img {
      border-radius: 0.75rem;
    }

    /* Wishlist & Share alignment */
    .product-single__addtolinks {
      display: flex;
      align-items: center;
      gap: 1rem;
      margin-top: 1rem;
    }

    .product-single__addtolinks form {
      margin: 0;
    }

    .product-single__addtolinks button.menu-link {
      display: flex;
      align-items: center;
      gap: 0.5rem;
      font-weight: 500;
      background: none;
      border: none;
      padding: 0.25rem;
      cursor: pointer;
    }

    .product-single__addtolinks svg {
      width: 16px;
      height: 16px;
      display: inline-block;
      vertical-align: middle;
    }

    /* Store info card */
    .store-info-card {
      border: 1px solid #e2e8f0;
      border-radius: 0.75rem;
      padding: 0.75rem;
      margin-top: 2rem;
      background-color: #fafafa;
    }

    .store-info-card h5 {
      margin-bottom: 0.5rem;
      font-weight: 600;
    }

    .store-info-card p {
      margin: 0;
      color: #4a5568;
    }
  </style>


  <main class="pt-90">
    <div class="mb-md-1 pb-md-3"></div>
    <section class="product-single container mt-5">
      <div class="row">
        <div class="col-lg-7">
          <div class="product-single__media" data-media-type="vertical-thumbnail">
            <div class="product-single__image">
              <div class="swiper-container">
                <div class="swiper-wrapper">

                  <div class="swiper-slide product-single__image-item">
                    <img loading="lazy" class="h-auto" src="{{ asset('uploads/products') }}/{{ $product->image }}"
                      width="674" height="674" alt="" />
                    <a data-fancybox="gallery" href="{{ asset('uploads/products') }}/{{ $product->image }}"
                      data-bs-toggle="tooltip" data-bs-placement="left" title="Zoom">
                      <svg width="16" height="16" viewBox="0 0 16 16" fill="none"
                        xmlns="http://www.w3.org/2000/svg">
                        <use href="#icon_zoom" />
                      </svg>
                    </a>
                  </div>

                  @foreach (explode(',', $product->images) as $gimg)
                    <div class="swiper-slide product-single__image-item">
                      <img loading="lazy" class="h-auto" src="{{ asset('uploads/products') }}/{{ $gimg }}"
                        width="674" height="674" alt="" />
                      <a data-fancybox="gallery" href="{{ asset('uploads/products') }}/{{ $gimg }}"
                        data-bs-toggle="tooltip" data-bs-placement="left" title="Zoom">
                        <svg width="16" height="16" viewBox="0 0 16 16" fill="none"
                          xmlns="http://www.w3.org/2000/svg">
                          <use href="#icon_zoom" />
                        </svg>
                      </a>
                    </div>
                  @endforeach

                </div>
                <div class="swiper-button-prev"><svg width="7" height="11" viewBox="0 0 7 11"
                    xmlns="http://www.w3.org/2000/svg">
                    <use href="#icon_prev_sm" />
                  </svg></div>
                <div class="swiper-button-next"><svg width="7" height="11" viewBox="0 0 7 11"
                    xmlns="http://www.w3.org/2000/svg">
                    <use href="#icon_next_sm" />
                  </svg></div>
              </div>
            </div>
            <div class="product-single__thumbnail">
              <div class="swiper-container">
                <div class="swiper-wrapper">
                  <div class="swiper-slide product-single__image-item"><img loading="lazy" class="h-auto"
                      src="{{ asset('uploads/products/thumbnails') }}/{{ $product->image }}" width="104" height="104"
                      alt="" /></div>
                  @foreach (explode(',', $product->images) as $gimg)
                    <div class="swiper-slide product-single__image-item"><img loading="lazy" class="h-auto"
                        src="{{ asset('uploads/products/thumbnails') }}/{{ $gimg }}" width="104"
                        height="104" alt="" /></div>
                  @endforeach
                </div>
              </div>
            </div>
          </div>
        </div>
        <div class="col-lg-5">
          <div class="d-flex justify-content-between mb-4 pb-md-2">

            {{-- Breadcrumb --}}

            {{-- End Breadcrumb --}}

            {{-- Next & Prev --}}

            {{-- End Next & Prev --}}

          </div>
          <h1 class="product-single__name">{{ $product->name }}</h1>

          {{-- <div class="product-single__rating">
            <div class="reviews-group d-flex">
              <svg class="review-star" viewBox="0 0 9 9" xmlns="http://www.w3.org/2000/svg">
                <use href="#icon_star" />
              </svg>
              <svg class="review-star" viewBox="0 0 9 9" xmlns="http://www.w3.org/2000/svg">
                <use href="#icon_star" />
              </svg>
              <svg class="review-star" viewBox="0 0 9 9" xmlns="http://www.w3.org/2000/svg">
                <use href="#icon_star" />
              </svg>
              <svg class="review-star" viewBox="0 0 9 9" xmlns="http://www.w3.org/2000/svg">
                <use href="#icon_star" />
              </svg>
              <svg class="review-star" viewBox="0 0 9 9" xmlns="http://www.w3.org/2000/svg">
                <use href="#icon_star" />
              </svg>
            </div>
            <span class="reviews-note text-lowercase text-secondary ms-1">8k+ reviews</span>
          </div> --}}

          <div class="product-single__price">
            <span class="current-price">
              @if ($product->sale_price)
                <s class="fw-light">@rupiahSymbol($product->regular_price)</s>
                @rupiahSymbol($product->sale_price)
              @else
                @rupiahSymbol($product->regular_price)
              @endif
            </span>
          </div>

          <div class="product-single__short-desc">
            <p>{{ $product->short_description }}</p>
          </div>
          @if (Cart::instance('cart')->content()->where('id', $product->id)->count() > 0)
            <a href="{{ route('cart.index') }}" class="btn btn-warning mb-3">Go to Cart</a>
          @else
            <form name="addtocart-form" method="post" action="{{ route('cart.add') }}">
              @csrf
              <div class="product-single__addtocart">

                <div class="qty-control position-relative d-flex align-items-center">
                  <div class="qty-control__reduce" role="button" aria-label="Kurangi jumlah">
                    <!-- SVG ikon “kurang” -->
                    <svg class="unf-icon" viewBox="0 0 24 24" width="16px" height="16px" fill="var(--NN300, #B3BBC9)"
                      style="display: inline-block; vertical-align: middle;">
                      <path d="M20 12.75H4a.75.75 0 1 1 0-1.5h16a.75.75 0 1 1 0 1.5Z"></path>
                    </svg>
                  </div>

                  <input type="number" name="quantity" value="1" min="1"
                    class="qty-control__number text-center mx-2" style="width: 60px;" />

                  <div class="qty-control__increase" role="button" aria-label="Tambah jumlah">
                    <!-- SVG ikon “tambah” -->
                    <svg class="unf-icon" viewBox="0 0 24 24" width="16px" height="16px"
                      fill="var(--NN300, #B3BBC9)" style="display: inline-block; vertical-align: middle;">
                      <path
                        d="M20 11.25h-7.25V4a.75.75 0 1 0-1.5 0v7.25H4a.75.75 0 1 0 0 1.5h7.25V20a.75.75 0 1 0 1.5 0v-7.25H20a.75.75 0 1 0 0-1.5Z">
                      </path>
                    </svg>
                  </div>
                </div>

                <input type="hidden" name="id" value="{{ $product->id }}">
                <input type="hidden" name="name" value="{{ $product->name }}">
                <input type="hidden" name="price"
                  value="{{ $product->sale_price == '' ? $product->regular_price : $product->sale_price }}">
                <button type="submit" class="btn btn-primary btn-addtocart" data-aside="cartDrawer">+
                  Keranjang</button>
              </div>
            </form>
          @endif

          {{-- wishlist & share --}}
          <div class="product-single__addtolinks">
            @if (Cart::instance('wishlist')->content()->where('id', $product->id)->count() > 0)
              <form method="POST"
                action="{{ route('wishlis.item.remove', ['rowId' => Cart::instance('wishlist')->content()->where('id', $product->id)->first()->rowId]) }}"
                id="frm-remove-item">
                @csrf @method('DELETE')
                <button type="submit" class="menu-link add-to-wishlist filled-heart">
                  <svg viewBox="0 0 20 20">
                    <use href="#icon_heart" />
                  </svg>
                  <span>Remove from Wishlist</span>
                </button>
              </form>
            @else
              <form method="POST" action="{{ route('wishlist.add') }}" id="wishlist-form">
                @csrf
                <input type="hidden" name="id" value="{{ $product->id }}">
                <input type="hidden" name="name" value="{{ $product->name }}">
                <input type="hidden" name="price"
                  value="{{ $product->sale_price == '' ? $product->regular_price : $product->sale_price }}">
                <input type="hidden" name="quantity" value="1">
                <button type="submit" class="menu-link add-to-wishlist">
                  <svg viewBox="0 0 20 20">
                    <use href="#icon_heart" />
                  </svg>
                  <span>Add to Wishlist</span>
                </button>
              </form>
            @endif
            <share-button class="share-button">
              <button class="menu-link to-share">
                <svg viewBox="0 0 16 19">
                  <use href="#icon_sharing" />
                </svg>
                <span>Share</span>
              </button>
            </share-button>
          </div>

          <div class="product-single__meta-info">
            <div class="meta-item">
              <label>SKU:</label>
              <span>{{ $product->SKU }}</span>
            </div>
            <div class="meta-item">
              <label>Kategori:</label>
              <span>{{ $product->category->name }}</span>
            </div>
            {{-- <div class="meta-item">
              <label>Tags:</label>
              <span>NA</span>
            </div> --}}
          </div>

          <!-- Store Information -->
          <div class="store-info-card d-flex align-items-center mt-0">
            @if ($product->store->logo)
              <img src="{{ asset('storage/' . $product->store->logo) }}" alt="{{ $product->store->name }}"
                class="rounded-circle me-3" style="width:32px; height:32px; object-fit:cover;">
            @else
              <!-- fallback: inisial nama toko -->
              <div class="rounded-circle bg-secondary text-white d-flex justify-content-center align-items-center me-3"
                style="width:32px; height:32px; font-weight:bold;">
                {{ Str::upper(Str::substr($product->store->name, 0, 1)) }}
              </div>
            @endif

            <h6 class="mb-0">Toko {{ $product->store->name }}</h6>
          </div>

          {{-- <div class="store-info-card">
            <h5>Informasi Toko</h5>
            <p><strong>Nama Toko:</strong> {{ $product->store->name }}</p>
            @if ($product->store->description)
              <p><strong>Deskripsi:</strong> {{ Str::limit($product->store->description, 100) }}</p>
            @endif
            @if ($product->store->owner)
              <p><strong>Pemilik:</strong> {{ $product->store->owner->name }}</p>
            @endif
            <a href="#" class="btn btn-outline-primary btn-sm mt-2">
              Lihat Toko
            </a>
          </div> --}}


        </div>
      </div>
      <div class="product-single__details-tab">
        <ul class="nav nav-tabs" id="myTab" role="tablist">
          <li class="nav-item" role="presentation">
            <a class="nav-link nav-link_underscore active" id="tab-description-tab" data-bs-toggle="tab"
              href="#tab-description" role="tab" aria-controls="tab-description" aria-selected="true">Detail
              Product</a>
          </li>
          {{-- <li class="nav-item" role="presentation">
            <a class="nav-link nav-link_underscore" id="tab-additional-info-tab" data-bs-toggle="tab"
              href="#tab-additional-info" role="tab" aria-controls="tab-additional-info"
              aria-selected="false">Additional Information</a>
          </li>
          <li class="nav-item" role="presentation">
            <a class="nav-link nav-link_underscore" id="tab-reviews-tab" data-bs-toggle="tab" href="#tab-reviews"
              role="tab" aria-controls="tab-reviews" aria-selected="false">Reviews (2)</a>
          </li> --}}
        </ul>
        <div class="tab-content">
          <div class="tab-pane fade show active" id="tab-description" role="tabpanel"
            aria-labelledby="tab-description-tab">
            <div class="product-single__description">
              {{ $product->description }}
            </div>
          </div>



        </div>
      </div>
    </section>
    <section class="products-carousel container">
      <h2 class="h3 text-uppercase mb-4 pb-xl-2 mb-xl-4">Produk <strong>Terkait</strong></h2>

      <div id="related_products" class="position-relative">
        <div class="swiper-container js-swiper-slider"
          data-settings='{
            "autoplay": false,
            "slidesPerView": 4,
            "slidesPerGroup": 4,
            "effect": "none",
            "loop": true,
            "pagination": {
              "el": "#related_products .products-pagination",
              "type": "bullets",
              "clickable": true
            },
            "navigation": {
              "nextEl": "#related_products .products-carousel__next",
              "prevEl": "#related_products .products-carousel__prev"
            },
            "breakpoints": {
              "320": {
                "slidesPerView": 2,
                "slidesPerGroup": 2,
                "spaceBetween": 14
              },
              "768": {
                "slidesPerView": 3,
                "slidesPerGroup": 3,
                "spaceBetween": 24
              },
              "992": {
                "slidesPerView": 4,
                "slidesPerGroup": 4,
                "spaceBetween": 30
              }
            }
          }'>
          <div class="swiper-wrapper">
            @foreach ($rproducts as $rproduct)
              <div class="swiper-slide product-card">
                <div class="pc__img-wrapper">
                  <a href="{{ route('shop.product.details', ['product_slug' => $rproduct->slug]) }}">
                    <img loading="lazy" src="{{ asset('uploads/products') }}/{{ $rproduct->image }}" width="330"
                      height="400" alt="{{ $rproduct->name }}" class="pc__img">
                    @foreach (explode(',', $rproduct->images) as $gimg)
                      <img loading="lazy" src="{{ asset('uploads/products') }}/{{ $gimg }}" width="330"
                        height="400" alt="{{ $rproduct->name }}" class="pc__img pc__img-second">
                    @endforeach
                  </a>
                  @if (Cart::instance('cart')->content()->where('id', $rproduct->id)->count() > 0)
                    <a href="{{ route('cart.index') }}"
                      class="pc__atc btn anim_appear-bottom btn position-absolute border-0 text-uppercase fw-medium btn-warning mb-3">Go
                      to Cart</a>
                  @else
                    <form name="addtocart-form" method="post" action="{{ route('cart.add') }}">
                      @csrf
                      <input type="hidden" name="id" value="{{ $rproduct->id }}">
                      <input type="hidden" name="quantity" value="1">
                      <input type="hidden" name="name" value="{{ $rproduct->name }}">
                      <input type="hidden" name="price"
                        value="{{ $rproduct->sale_price == '' ? $rproduct->regular_price : $rproduct->sale_price }}">
                      <button type="submit"
                        class="pc__atc btn anim_appear-bottom btn position-absolute border-0 text-uppercase fw-medium"
                        data-aside="cartDrawer" title="Add To Cart">+ Keranjang</button>
                    </form>
                  @endif
                </div>

                <div class="pc__info position-relative">
                  <p class="pc__category">{{ $rproduct->category->name }}</p>
                  <h6 class="pc__title"><a
                      href="{{ route('shop.product.details', ['product_slug' => $rproduct->slug]) }}">{{ $rproduct->name }}</a>
                  </h6>
                  <div class="product-card__price d-flex">

                    <span class="money price">
                      @if ($product->sale_price)
                        <s>@rupiahSymbol($product->regular_price)</s>
                        <span class="fw-bold">@rupiahSymbol($product->sale_price)</span>
                      @else
                        <span class="fw-bold">@rupiahSymbol($product->regular_price)</span>
                      @endif
                    </span>

                  </div>

                  <button class="pc__btn-wl position-absolute top-0 end-0 bg-transparent border-0 js-add-wishlist"
                    title="Add To Wishlist">
                    <svg width="16" height="16" viewBox="0 0 20 20" fill="none"
                      xmlns="http://www.w3.org/2000/svg">
                      <use href="#icon_heart" />
                    </svg>
                  </button>
                </div>
              </div>
            @endforeach
          </div> {{-- swiper-wrapper --}}
        </div><!-- /.swiper-container js-swiper-slider -->

        <div class="products-carousel__prev position-absolute top-50 d-flex align-items-center justify-content-center">
          <svg width="25" height="25" viewBox="0 0 25 25" xmlns="http://www.w3.org/2000/svg">
            <use href="#icon_prev_md" />
          </svg>
        </div><!-- /.products-carousel__prev -->
        <div class="products-carousel__next position-absolute top-50 d-flex align-items-center justify-content-center">
          <svg width="25" height="25" viewBox="0 0 25 25" xmlns="http://www.w3.org/2000/svg">
            <use href="#icon_next_md" />
          </svg>
        </div><!-- /.products-carousel__next -->

        <div class="products-pagination mt-4 mb-5 d-flex align-items-center justify-content-center"></div>
        <!-- /.products-pagination -->
      </div><!-- /.position-relative -->

    </section><!-- /.products-carousel container -->
  </main>
@endsection
