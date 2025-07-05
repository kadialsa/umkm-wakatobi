@extends('layouts.app')

@section('content')
  <main class="pt-90">
    <div class="mb-4 pb-4"></div>
    <section class="my-account container">
      <h2 class="page-title">Ubah Profil</h2>
      <div class="row gy-4">
        {{-- Sidebar --}}
        <div class="col-lg-3">
          @include('user.account-nav')
        </div>
        {{-- Form --}}
        <div class="col-lg-9 page-content my-account__address">
          <form action="{{ route('user.details.update') }}" method="POST" enctype="multipart/form-data"
            class="card shadow-sm">
            @csrf @method('PUT')
            <div class="card-body">
              {{-- Nama --}}
              <div class="mb-3">
                <label class="form-label">Nama Lengkap <span class="text-danger">*</span></label>
                <input type="text" name="name" class="form-control @error('name') is-invalid @enderror"
                  value="{{ old('name', $user->name) }}" required>
                @error('name')
                  <div class="invalid-feedback">{{ $message }}</div>
                @enderror
              </div>

              {{-- Email --}}
              <div class="mb-3">
                <label class="form-label">Email <span class="text-danger">*</span></label>
                <input type="email" name="email" class="form-control @error('email') is-invalid @enderror"
                  value="{{ old('email', $user->email) }}" required>
                @error('email')
                  <div class="invalid-feedback">{{ $message }}</div>
                @enderror
              </div>

              {{-- Tanggal Lahir --}}
              <div class="mb-3">
                <label class="form-label">Tanggal Lahir</label>
                <input type="date" name="birthdate" class="form-control @error('birthdate') is-invalid @enderror"
                  value="{{ old('birthdate', optional($user->profile->birthdate)->format('Y-m-d')) }}">

                @error('birthdate')
                  <div class="invalid-feedback">{{ $message }}</div>
                @enderror
              </div>

              {{-- Jenis Kelamin --}}
              <div class="mb-3">
                <label class="form-label">Jenis Kelamin</label>
                <select name="gender" class="form-select @error('gender') is-invalid @enderror">
                  <option value="">— Pilih —</option>
                  <option value="male" {{ old('gender', $user->profile->gender ?? '') == 'male' ? 'selected' : '' }}>
                    Laki-laki</option>
                  <option value="female" {{ old('gender', $user->profile->gender ?? '') == 'female' ? 'selected' : '' }}>
                    Perempuan</option>
                  <option value="other" {{ old('gender', $user->profile->gender ?? '') == 'other' ? 'selected' : '' }}>
                    Lainnya</option>
                </select>
                @error('gender')
                  <div class="invalid-feedback">{{ $message }}</div>
                @enderror
              </div>

              {{-- Telepon --}}
              <div class="mb-3">
                <label class="form-label">Telepon</label>
                <input type="text" name="phone" class="form-control @error('phone') is-invalid @enderror"
                  value="{{ old('phone', $user->profile->phone ?? '') }}">
                @error('phone')
                  <div class="invalid-feedback">{{ $message }}</div>
                @enderror
              </div>

              {{-- Alamat --}}
              <div class="mb-3">
                <label class="form-label">Alamat</label>
                <textarea name="address" class="form-control @error('address') is-invalid @enderror" rows="2">{{ old('address', $user->profile->address ?? '') }}</textarea>
                @error('address')
                  <div class="invalid-feedback">{{ $message }}</div>
                @enderror
              </div>

              {{-- Avatar --}}
              <div class="mb-4">
                <label class="form-label">Avatar</label>
                <div class="d-flex align-items-center gap-3 mb-2">
                  @if ($user->profile->avatar)
                    <img id="currentAvatar" src="{{ asset('storage/' . $user->profile->avatar) }}" alt="Avatar"
                      class="rounded-circle" style="width:80px;height:80px;object-fit:cover;">
                  @endif
                  <div id="previewContainer" style="display:none;">
                    <img id="previewAvatar" src="#" alt="Preview" class="rounded-circle"
                      style="width:80px;height:80px;object-fit:cover;">
                  </div>
                </div>
                <input type="file" name="avatar" id="avatarUpload"
                  class="form-control @error('avatar') is-invalid @enderror" accept="image/*">
                @error('avatar')
                  <div class="invalid-feedback">{{ $message }}</div>
                @enderror
              </div>

              {{-- Tombol --}}
              <div class="d-flex gap-2">
                <button type="submit" class="btn btn-success btn-sm">
                  <i class="fa fa-save me-1"></i> Simpan
                </button>
                <a href="{{ route('user.details') }}" class="btn btn-danger btn-sm">
                  <i class="fa fa-arrow-left me-1"></i> Batal
                </a>
              </div>
            </div>
          </form>
        </div>
      </div>
    </section>
  </main>

  @push('scripts')
    <script>
      document.getElementById('avatarUpload').addEventListener('change', function(e) {
        const file = e.target.files[0];
        if (!file) return;
        const reader = new FileReader();
        reader.onload = evt => {
          document.getElementById('previewAvatar').src = evt.target.result;
          document.getElementById('previewContainer').style.display = 'block';
        };
        reader.readAsDataURL(file);
      });
    </script>
  @endpush
@endsection
