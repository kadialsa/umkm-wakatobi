<header id="header" class="header header-fullwidth header-transparent-bg">
  <div class="container">
    <div class="header-desk header-desk_type_1">
      <div class="logo">
        <a href="{{ route('home.index') }}">
          <img src="{{ asset('assets/images/logo-UMKM.png') }}" alt="Uomo" class="logo__image d-block" />`
        </a>
      </div>

      <nav class="navigation">
        <ul class="navigation__list list-unstyled d-flex">

          <li class="navigation__item">
            <a href="{{ route('home.index') }}" class="navigation__link">BERANDA</a>
          </li>

          <li class="navigation__item">
            <a href="{{ route('shop.index') }}" class="navigation__link">BELANJA</a>
          </li>

          <li class="navigation__item">
            <a href="{{ route('cart.index') }}" class="navigation__link">KERANJANG</a>
          </li>

          <li class="navigation__item">
            <a href="{{ route('home.articles') }}" class="navigation__link">BLOG</a>
          </li>

        </ul>
      </nav>

      {{-- Search --}}
      <div class="header-tools d-flex align-items-center">
        <div class="header-tools__item hover-container">
          <div class="js-hover__open position-relative">
            <a class="js-search-popup search-field__actor" href="{{ route('search') }}">
              <svg class="d-block" width="20" height="20" viewBox="0 0 20 20" fill="none"
                xmlns="http://www.w3.org/2000/svg">
                <use href="#icon_search" />
              </svg>
              <i class="btn-icon btn-close-lg"></i>
            </a>
          </div>

          <div class="search-popup js-hidden-content">
            <form action="{{ route('search') }}" method="GET" class="search-field container">
              <p class="text-uppercase text-secondary fw-medium mb-4">Apa yang Anda cari?</p>
              <div class="position-relative">
                <input class="search-field__input search-popup__input w-100 fw-medium" type="text" name="q"
                  id="search-input-product" placeholder="Cari produk…" />
                <button class="btn-icon search-popup__submit" type="submit">
                  <svg class="d-block" width="20" height="20" viewBox="0 0 20 20" fill="none"
                    xmlns="http://www.w3.org/2000/svg">
                    <use href="#icon_search" />
                  </svg>
                </button>
                <button class="btn-icon btn-close-lg search-popup__reset" type="reset"></button>
              </div>

              <div class="search-popup__results">
                <ul id="box-content-search"></ul>
              </div>
            </form>
          </div>
        </div>

        @guest
          <div class="header-tools__item hover-container">
            <a href="{{ route('login') }}" class="header-tools__item">
              <svg class="d-block" width="20" height="20" viewBox="0 0 20 20" fill="none"
                xmlns="http://www.w3.org/2000/svg">
                <use href="#icon_user" />
              </svg>
            </a>
          </div>
        @else
          <div class="header-tools__item hover-container">
            <a href="{{ Auth::user()->utype === 'ADM'
                ? route('admin.index')
                : (Auth::user()->utype === 'STR'
                    ? route('store.index')
                    : route('user.index')) }}"
              class="header-tools__item">
              <span class="pr-6px">{{ Auth::user()->name }}</span>
              <svg class="d-block" width="20" height="20" viewBox="0 0 20 20" fill="none"
                xmlns="http://www.w3.org/2000/svg">
                <use href="#icon_user" />
              </svg>
            </a>
          </div>

        @endguest

        {{-- wishlist --}}
        <a href="{{ route('wishlist.index') }}" class="header-tools__item header-tools__cart">
          <svg width="20" height="20" viewBox="0 0 20 20" fill="none" xmlns="http://www.w3.org/2000/svg">
            <use href="#icon_heart" />
          </svg>
          @if (Cart::instance('wishlist')->content()->count() > 0)
            <span
              class="cart-amount d-block position-absolute js-cart-items-count">{{ Cart::instance('wishlist')->content()->count() }}</span>
          @endif
        </a>

        <a href="{{ route('cart.index') }}" class="header-tools__item header-tools__cart">
          <svg class="d-block" width="20" height="20" viewBox="0 0 20 20" fill="none"
            xmlns="http://www.w3.org/2000/svg">
            <use href="#icon_cart" />
          </svg>
          @if (Cart::instance('cart')->content()->count() > 0)
            <span
              class="cart-amount d-block position-absolute js-cart-items-count">{{ Cart::instance('cart')->content()->count() }}</span>
          @endif
        </a>
      </div>

    </div>
  </div>
</header>
