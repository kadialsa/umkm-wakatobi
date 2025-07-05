@extends('layouts.store')

@push('styles')
  <style>
    .form-label {
      font-size: 1.4rem;
      font-weight: 600;
    }

    .form-control {
      padding: .75rem;
      font-size: 1.4rem;
      border: 1px solid #ced4da !important;
    }

    textarea.form-control {
      border-radius: 12px !important;
      padding: 1rem 2rem;
    }

    #currentImage,
    #previewImage {
      width: 80px;
      height: 80px;
      object-fit: cover;
      border-radius: 50%;
      border: 1px solid #dee2e6;
    }

    .password-toggle {
      cursor: pointer;
      position: absolute;
      right: 1rem;
      top: 50%;
      transform: translateY(-50%);
      color: #6c757d;
    }

    .position-relative {
      position: relative;
    }

    .form-control {
      padding: .75rem;
      font-size: 1.4rem;
      border: 1px solid #686c6c !important;
    }
  </style>
@endpush

@section('content')
  <div class="container p-4">

    {{-- Header --}}
    <div class="d-flex justify-content-between align-items-center mb-15">
      <h2 class="h4">
        <i class="fas fa-store text-primary me-2"></i>Profil Toko & Akun Saya
      </h2>
    </div>

    {{-- Notifikasi sukses --}}
    @if (session('status'))
      <div class="alert alert-success">{{ session('status') }}</div>
    @endif

    <div class="card mb-4 shadow-sm p-3">
      <div class="card-body">
        <form action="{{ route('store.profile.update') }}" method="POST" enctype="multipart/form-data">
          @csrf

          {{-- INFO TOKO --}}
          <h5 class="mb-20 text-primary">Informasi Toko</h5>
          <div class="row">
            <div class="col-12 mb-15">
              <label class="form-label">Nama Toko <span class="text-danger">*</span></label>
              <input name="name" type="text" class="form-control @error('name') is-invalid @enderror"
                value="{{ old('name', $store->name) }}" required>
              @error('name')
                <div class="invalid-feedback">{{ $message }}</div>
              @enderror
            </div>
            <div class="col-12 mb-15">
              <label class="form-label">Deskripsi</label>
              <textarea name="description" class="form-control @error('description') is-invalid @enderror" rows="2">{{ old('description', $store->description) }}</textarea>
              @error('description')
                <div class="invalid-feedback">{{ $message }}</div>
              @enderror
            </div>
            <div class="col-12 mb-15">
              <label class="form-label mb-10">Logo Toko</label>
              <div class="d-flex align-items-center gap-3 mb-10">
                @if ($store->image)
                  <img id="currentImage" src="{{ asset('storage/' . $store->image) }}" alt="Logo Saat Ini">
                @endif
                <div id="previewContainer" style="display:none">
                  <img id="previewImage" src="#" alt="Preview Baru">
                </div>
              </div>
              <input name="image" id="imageUpload" type="file"
                class="form-control @error('image') is-invalid @enderror" accept="image/*">
              @error('image')
                <div class="invalid-feedback">{{ $message }}</div>
              @enderror
            </div>
          </div>

          <hr>

          {{-- INFO PEMILIK/USER --}}
          <h5 class="mt-4 mb-20 text-primary">Informasi Akun</h5>
          <div class="row">
            <div class="col-md-4 mb-15">
              <label class="form-label">Nama Anda <span class="text-danger">*</span></label>
              <input name="owner_name" type="text" class="form-control @error('owner_name') is-invalid @enderror"
                value="{{ old('owner_name', auth()->user()->name) }}" required>
              @error('owner_name')
                <div class="invalid-feedback">{{ $message }}</div>
              @enderror
            </div>
            <div class="col-md-4 mb-15">
              <label class="form-label">Email <span class="text-danger">*</span></label>
              <input name="owner_email" type="email" class="form-control @error('owner_email') is-invalid @enderror"
                value="{{ old('owner_email', auth()->user()->email) }}" required>
              @error('owner_email')
                <div class="invalid-feedback">{{ $message }}</div>
              @enderror
            </div>
            <div class="col-md-4 mb-15">
              <label class="form-label">Telepon</label>
              <input name="owner_mobile" type="text" class="form-control @error('owner_mobile') is-invalid @enderror"
                value="{{ old('owner_mobile', auth()->user()->mobile) }}">
              @error('owner_mobile')
                <div class="invalid-feedback">{{ $message }}</div>
              @enderror
            </div>
          </div>

          {{-- GANTI PASSWORD --}}
          <div class="row">
            <div class="col-md-4 mb-15 position-relative">
              <label class="form-label">Password Saat Ini</label>
              <input name="current_password" id="current_password" type="password"
                class="form-control @error('current_password') is-invalid @enderror" placeholder="">
              <i class="fas fa-eye fs-4 password-toggle pe-4 pt-4" data-target="current_password"></i>
              @error('current_password')
                <div class="invalid-feedback">{{ $message }}</div>
              @enderror
            </div>
            <div class="col-md-4 mb-15 position-relative">
              <label class="form-label">Password Baru</label>
              <input name="new_password" id="new_password" type="password"
                class="form-control @error('new_password') is-invalid @enderror" placeholder="">
              <i class="fas fa-eye fs-4 password-toggle pe-4 pt-4" data-target="new_password"></i>
              @error('new_password')
                <div class="invalid-feedback">{{ $message }}</div>
              @enderror
            </div>
            <div class="col-md-4 mb-15 position-relative">
              <label class="form-label">Konfirmasi Password</label>
              <input name="new_password_confirmation" id="new_password_confirmation" type="password" class="form-control"
                placeholder="">
              <i class="fas fa-eye fs-4 password-toggle pe-4 pt-4" data-target="new_password_confirmation"></i>
            </div>
          </div>


          <div class="mt-4">
            <button type="submit" class="btn btn-primary px-5 py-3 btn-lg">
              <i class="fas fa-save me-1"></i>
              Simpan
            </button>
          </div>
        </form>
      </div>
    </div>
  </div>
@endsection

@push('scripts')
  <script>
    document.addEventListener('DOMContentLoaded', () => {
      // Preview gambar baru
      const upload = document.getElementById('imageUpload'),
        previewImg = document.getElementById('previewImage'),
        previewCt = document.getElementById('previewContainer');
      upload.onchange = e => {
        const file = e.target.files[0];
        if (!file) return previewCt.style.display = 'none';
        const reader = new FileReader();
        reader.onload = ev => {
          previewImg.src = ev.target.result;
          previewCt.style.display = 'block';
        };
        reader.readAsDataURL(file);
      };

      // Toggle show/hide password
      document.querySelectorAll('.password-toggle').forEach(icon => {
        icon.onclick = () => {
          const name = icon.getAttribute('data-target'),
            input = document.querySelector(`input[name="${name}"]`);
          if (input.type === 'password') {
            input.type = 'text';
            icon.classList.replace('fa-eye', 'fa-eye-slash');
          } else {
            input.type = 'password';
            icon.classList.replace('fa-eye-slash', 'fa-eye');
          }
        };
      });
    });
  </script>
@endpush
