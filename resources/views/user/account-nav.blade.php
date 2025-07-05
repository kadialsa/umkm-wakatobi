<ul class="account-nav">
  <li><a href="{{ route('user.index') }}" class="menu-link menu-link_us-s">Beranda</a></li>
  <li><a href="{{ route('user.orders') }}" class="menu-link menu-link_us-s">Pesanan</a></li>
  <li><a href="{{ route('user.address.index') }}" class="menu-link menu-link_us-s">Alamat</a></li>
  <li><a href="{{ route('user.details') }}" class="menu-link menu-link_us-s">Detail Akun</a></li>
  <li><a href="{{ route('wishlist.index') }}" class="menu-link menu-link_us-s">Daftar Keinginan</a></li>
  <li>
    <form method="POST" action="{{ route('logout') }}" id="logout-form">@csrf</form>
    <a href="{{ route('logout') }}" class="menu-link menu-link_us-s btn btn-danger px-5"
      onclick="event.preventDefault();document.getElementById('logout-form').submit();">
      Keluar
    </a>
  </li>
</ul>
