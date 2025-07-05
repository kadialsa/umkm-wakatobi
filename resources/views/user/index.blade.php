@extends('layouts.app')

@section('content')
  <main class="pt-90">
    <div class="mb-4 pb-4"></div>
    <section class="my-account container">
      <h2 class="page-title">Akun Saya</h2>
      <div class="row">
        {{-- Menu Samping --}}
        <div class="col-lg-3">
          @include('user.account-nav')
        </div>

        {{-- Konten Utama --}}
        <div class="col-lg-9">
          <div class="page-content my-account__dashboard">
            <p>Halo, <strong>{{ Auth::user()->name }}</strong></p>
            <p>
              Di halaman akun Anda, Anda dapat melihat <a href="{{ route('user.orders') }}" class="underline-link">pesanan
                terkini</a>,
              mengelola <a href="{{ route('user.address.index') }}" class="underline-link">alamat pengiriman</a>,
              dan <a href="{{ route('user.details') }}" class="underline-link">mengubah detail akun</a>.
            </p>
          </div>
        </div>
      </div>
    </section>
  </main>
@endsection
