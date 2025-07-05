@extends('layouts.app')

@section('content')
  <main class="pt-90">
    <div class="mb-4 pb-4"></div>
    <section class="my-account container">
      <h2 class="page-title">Daftar Alamat</h2>
      <div class="row">
        {{-- Sidebar akun --}}
        <div class="col-lg-3">
          @include('user.account-nav')
        </div>

        {{-- Konten alamat --}}
        <div class="col-lg-9">
          <div class="page-content my-account__address">
            {{-- Header dan tombol Tambah --}}
            <div class="row mb-3">
              {{-- <div class="col-6">
                <p class="notice">Alamat berikut akan digunakan sebagai alamat pengiriman default.</p>
              </div> --}}
              {{-- <div class="col-6 text-start">
                <a href="{{ route('user.address.create') }}" class="btn btn-sm btn-info">
                  <i class="fa fa-plus-circle me-1"></i>Tambah Alamat
                </a>
              </div> --}}
            </div>

            {{-- Flash message --}}
            @if (session('success'))
              <div class="alert alert-success">{{ session('success') }}</div>
            @endif

            <div class="row">
              @forelse($addresses as $addr)
                <div class="col-md-12 mb-4">
                  <div class="card shadow-sm h-100">
                    <div class="card-body d-flex flex-column">
                      <h5 class="card-title">{{ $addr->recipient_name }}</h5>
                      <p class="mb-1"><strong>Kode Pos:</strong> {{ $addr->zip_code }}</p>
                      <p class="mb-3"><strong>Telepon:</strong> {{ $addr->phone }}</p>
                      <p class="mb-1"><strong></strong> {{ $addr->full_address }}</p>

                      {{-- <p class="mb-1">{{ $addr->subdistrict }}, {{ $addr->district }}</p>
                      <p class="mb-1">{{ $addr->city }}, {{ $addr->province }}</p> --}}

                      {{-- Tombol Ubah & Hapus di bawah --}}
                      <div class="mt-3 d-flex justify-content-end gap-2">
                        <a href="{{ route('user.address.edit', $addr->id) }}" class="btn btn-sm btn-secondary">
                          <i class="fa fa-edit me-1"></i>Ubah
                        </a>
                        <form action="{{ route('user.address.destroy', $addr->id) }}" method="POST"
                          onsubmit="return confirm('Yakin ingin menghapus alamat ini?');">
                          @csrf @method('DELETE')
                          <button class="btn btn-sm btn-danger">
                            <i class="fa fa-trash me-1"></i>Hapus
                          </button>
                        </form>
                      </div>
                    </div>
                  </div>
                </div>
              @empty
                <div class="col-12">
                  <p class="text-center">Belum ada alamat tersimpan.</p>
                </div>
              @endforelse
            </div>

            {{-- Pagination --}}
            @if (method_exists($addresses, 'links'))
              <div class="d-flex justify-content-center mt-4">
                {{ $addresses->links('pagination::bootstrap-5') }}
              </div>
            @endif
          </div>
        </div>
      </div>
    </section>
  </main>
@endsection
