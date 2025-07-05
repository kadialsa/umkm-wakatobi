@extends('layouts.app')

@section('content')
  <main class="pt-90">
    <div class="mb-4 pb-4"></div>
    <section class="container my-account">
      <div class="row">
        {{-- Sidebar --}}
        <div class="col-lg-3">
          @include('user.account-nav')
        </div>

        {{-- Konten Utama --}}
        <div class="col-lg-9 page-content my-account__address">
          {{-- Flash Message --}}
          @if (session('status'))
            <div class="alert alert-success alert-dismissible fade show" role="alert">
              {{ session('status') }}
              <button type="button" class="btn-close" data-bs-dismiss="alert"></button>
            </div>
          @endif

          <div class="card mb-4 shadow-sm">
            <div class="card-body">
              <div class="row">
                {{-- Avatar --}}
                <div class="col-md-3 text-center">
                  <img src="{{ $user->avatar_url ?: asset('images/default-avatar.png') }}" alt="Avatar"
                    class="img-fluid rounded-circle mb-3" style="width:150px; height:150px; object-fit:cover;">
                </div>

                {{-- Biodata & Kontak --}}
                <div class="col-md-9">
                  <h5 class="mb-3">Biodata Diri</h5>
                  <dl class="row mb-4">
                    <dt class="col-sm-4">Nama Lengkap</dt>
                    <dd class="col-sm-8">{{ $user->name }}</dd>

                    <dt class="col-sm-4">Tanggal Lahir</dt>
                    <dd class="col-sm-8">
                      {{ optional($user->profile->birthdate)->format('j F Y') ?? '-' }}
                    </dd>

                    <dt class="col-sm-4">Jenis Kelamin</dt>
                    <dd class="col-sm-8">{{ $user->profile->gender_label ?? '-' }}</dd>
                  </dl>

                  <h5 class="mb-3">Kontak</h5>
                  <dl class="row mb-4">
                    <dt class="col-sm-4">Email</dt>
                    <dd class="col-sm-8">{{ $user->email }}</dd>

                    <dt class="col-sm-4">Nomor HP</dt>
                    <dd class="col-sm-8">
                      {{ $user->profile->phone ?? '-' }}
                      @if ($user->profile->phone && $user->phone_verified_at)
                        <span class="badge bg-success ms-2">Terverifikasi</span>
                      @endif
                    </dd>
                  </dl>

                  <div class="text-start">
                    <a href="{{ route('user.details.edit') }}" class="btn btn-primary btn-sm">
                      <i class="fa fa-edit me-1"></i> Ubah
                    </a>
                  </div>
                </div>
              </div>
            </div>
          </div> {{-- /.card --}}
        </div>
      </div>
    </section>
  </main>
@endsection
