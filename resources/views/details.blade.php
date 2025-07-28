@extends('layouts.app')
@section('content')
  <style>
    .filled-heart {
      color: orange;
    }

    .swiper-slide {
      height: auto !important;
    }

    /* Make product images rounded */
    .product-single__image-item img {
      border-radius: 0.5rem;
      /* height: 100% !important; */
      height: auto !important;


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

    #thumbnailSwiper {
      height: 420px;
      /* atau lebih tinggi jika gambar thumb besar */
      overflow-y: auto !important;
    }

    #thumbnailSwiper .swiper-wrapper {
      flex-direction: column !important;
    }

    .swiper-container.thumbnail-swiper {
      min-height: 220px;
      max-height: 100%;
    }
  </style>


  {{-- Debug, tampilkan list gambar --}}

  <main class="pt-90">
    <div class="mb-md-1 pb-md-3"></div>
    <section class="product-single container mt-5">
      <div class="row">

        <div class="col-lg-7">
          <div class="product-single__media vertical-thumbnail" data-media-type="vertical-thumbnail">
            <div class="product-single__image">
              <div class="swiper-container main-image-swiper" id="mainImageSwiper">
                <div class="swiper-wrapper">

                  @php
                    // Konversi images ke array
                    $imagesArray = !empty($product->images) ? explode(',', $product->images) : [];

                    // Ambil image utama (string)
                    $mainImage = !empty($product->image) ? $product->image : null;

                    // Merge jadi satu array, image utama di depan (jika belum ada)
                    $mergedImages = $imagesArray;
                    if ($mainImage && !in_array($mainImage, $mergedImages)) {
                        array_unshift($mergedImages, $mainImage);
                    }
                    // Hilangkan duplikat (opsional, untuk jaga-jaga)
                    $productImages = array_unique($mergedImages);

                    // Hitung total images hasil merge
                    $totalImages = count($mergedImages);
                  @endphp


                  @if ($totalImages > 0)
                    @foreach ($productImages as $index => $image)
                      <div class="swiper-slide product-single__image-item">
                        <img loading="lazy" class="h-auto" src="{{ asset('uploads/products/' . trim($image)) }}"
                          width="674" height="674" alt="{{ $product->name }}">
                        <a data-fancybox="gallery" href="{{ asset('uploads/products/' . trim($image)) }}"
                          data-bs-toggle="tooltip" data-bs-placement="left" title="" data-bs-original-title="Zoom">
                          <svg width="16" height="16" viewBox="0 0 16 16" fill="none"
                            xmlns="http://www.w3.org/2000/svg">
                            <use href="#icon_zoom"></use>
                          </svg>
                        </a>
                      </div>
                    @endforeach
                  @else
                    {{-- Fallback if no images --}}
                    <div class="swiper-slide product-single__image-item">
                      <img loading="lazy" class="h-auto" src="{{ asset('assets/images/products/no-image.jpg') }}"
                        width="674" height="674" alt="{{ $product->name }}">
                      <a data-fancybox="gallery" href="{{ asset('assets/images/products/no-image.jpg') }}"
                        data-bs-toggle="tooltip" data-bs-placement="left" title="" data-bs-original-title="Zoom">
                        <svg width="16" height="16" viewBox="0 0 16 16" fill="none"
                          xmlns="http://www.w3.org/2000/svg">
                          <use href="#icon_zoom"></use>
                        </svg>
                      </a>
                    </div>
                  @endif

                </div>
                <div class="swiper-button-prev" id="mainImagePrev">
                  <svg width="7" height="11" viewBox="0 0 7 11" xmlns="http://www.w3.org/2000/svg">
                    <use href="#icon_prev_sm"></use>
                  </svg>
                </div>
                <div class="swiper-button-next" id="mainImageNext">
                  <svg width="7" height="11" viewBox="0 0 7 11" xmlns="http://www.w3.org/2000/svg">
                    <use href="#icon_next_sm"></use>
                  </svg>
                </div>
              </div>
            </div>
            <div class="product-single__thumbnail">
              <div class="swiper-container thumbnail-swiper" id="thumbnailSwiper">
                <div class="swiper-wrapper">

                  @if ($totalImages > 0)
                    @foreach ($productImages as $index => $image)
                      <div class="swiper-slide product-single__image-item">
                        <img loading="lazy" class="h-auto" src="{{ asset('uploads/products/' . trim($image)) }}"
                          width="120" height="120" alt="{{ $product->name }}">
                      </div>
                    @endforeach
                  @else
                    {{-- Fallback thumbnail if no images --}}
                    <div class="swiper-slide product-single__image-item">
                      <img loading="lazy" class="h-auto" src="{{ asset('assets/images/products/no-image.jpg') }}"
                        width="120" height="120" alt="{{ $product->name }}">
                    </div>
                  @endif

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
                    <svg class="unf-icon" viewBox="0 0 24 24" width="16px" height="16px"
                      fill="var(--NN300, #B3BBC9)" style="display: inline-block; vertical-align: middle;">
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


        </div>
      </div>
      <div class="product-single__details-tab">
        <ul class="nav nav-tabs" id="myTab" role="tablist">
          <li class="nav-item" role="presentation">
            <a class="nav-link nav-link_underscore active" id="tab-description-tab" data-bs-toggle="tab"
              href="#tab-description" role="tab" aria-controls="tab-description" aria-selected="true">Detail
              Product</a>
          </li>

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

  @push('scripts')
    <script>
      document.addEventListener('DOMContentLoaded', function() {
        // Make sure container for thumbnail has fixed height for vertical
        document.querySelector('#thumbnailSwiper').style.height = '400px';

        // Initialize thumbnail Swiper - always vertical
        var thumbnailSwiper = new Swiper('#thumbnailSwiper', {
          direction: 'vertical',
          slidesPerView: 4,
          spaceBetween: 10,
          freeMode: true,
          watchSlidesProgress: true,
          allowTouchMove: true,
          // Responsive: At <768px, make it scrollable vertically (not horizontal)
          breakpoints: {
            0: {
              direction: 'vertical',
              slidesPerView: 3,
              spaceBetween: 6,
            },
            576: {
              direction: 'vertical',
              slidesPerView: 4,
              spaceBetween: 8,
            },
            992: {
              direction: 'vertical',
              slidesPerView: 5,
              spaceBetween: 10,
            }
          }
        });

        // Main image Swiper
        var mainSwiper = new Swiper('#mainImageSwiper', {
          spaceBetween: 10,
          loop: {{ $totalImages > 1 ? 'true' : 'false' }},
          navigation: {
            nextEl: '#mainImageNext',
            prevEl: '#mainImagePrev',
          },
          thumbs: {
            swiper: thumbnailSwiper,
          },
          on: {
            slideChange: function() {
              // Make sure thumbnail follows active slide
              thumbnailSwiper.slideTo(this.activeIndex);
            }
          }
        });

        // Click event: klik thumbnail pindah slide utama + highlight
        document.querySelectorAll('#thumbnailSwiper .swiper-slide').forEach((slide, index) => {
          slide.addEventListener('click', function() {
            mainSwiper.slideTo(index);

            document.querySelectorAll('#thumbnailSwiper .swiper-slide').forEach(s => {
              s.classList.remove('swiper-slide-thumb-active');
            });
            this.classList.add('swiper-slide-thumb-active');
          });
        });
      });
    </script>
  @endpush
@endsection
