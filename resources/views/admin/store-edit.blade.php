@extends('layouts.admin')

@section('content')
  <div class="main-content-inner">
    <div class="main-content-wrap">
      <div class="flex items-center flex-wrap justify-between gap20 mb-27">
        <h3>Edit Store</h3>
        <ul class="breadcrumbs flex items-center flex-wrap justify-start gap10">
          <li>
            <a href="{{ route('admin.index') }}">
              <div class="text-tiny">Dashboard</div>
            </a>
          </li>
          <li><i class="icon-chevron-right"></i></li>
          <li>
            <a href="{{ route('admin.stores') }}">
              <div class="text-tiny">Stores</div>
            </a>
          </li>
          <li><i class="icon-chevron-right"></i></li>
          <li>
            <div class="text-tiny">Edit Store</div>
          </li>
        </ul>
      </div>

      <div class="wg-box p-5">
        <form class="form-new-product form-style-1" action="{{ route('admin.store.update') }}" method="POST"
          enctype="multipart/form-data">
          @csrf
          @method('PUT')

          <input type="hidden" name="id" value="{{ $store->id }}">
          <input type="hidden" name="owner_id" value="{{ $owner->id }}">

          <fieldset class="name">
            <div class="body-title">Store Name <span class="tf-color-1">*</span></div>
            <input class="flex-grow" type="text" name="name" value="{{ old('name', $store->name) }}" required>
          </fieldset>
          @error('name')
            <span class="alert alert-danger text-center">{{ $message }}</span>
          @enderror

          <fieldset class="name">
            <div class="body-title">Slug <span class="tf-color-1">*</span></div>
            <input class="flex-grow" type="text" name="slug" value="{{ old('slug', $store->slug) }}" required>
          </fieldset>
          @error('slug')
            <span class="alert alert-danger text-center">{{ $message }}</span>
          @enderror

          <fieldset class="name">
            <div class="body-title">Description</div>
            <textarea class="flex-grow" rows="4" name="description">{{ old('description', $store->description) }}</textarea>
          </fieldset>

          <fieldset>
            <div class="body-title">Store Image</div>
            <div class="upload-image flex-grow">
              {{-- Preview gambar awal --}}
              <div class="item" id="imgpreview">
                @if ($store->image)
                  <img src="{{ asset('uploads/stores/' . $store->image) }}" class="effect8" alt="{{ $store->name }}"
                    style="max-height: 124px;">
                @endif
              </div>

              {{-- Upload baru --}}
              <div id="upload-file" class="item up-load">
                <label class="uploadfile" for="myFile">
                  <span class="icon"><i class="icon-upload-cloud"></i></span>
                  <span class="body-text">Upload new image</span>
                  <input type="file" id="myFile" name="image" accept="image/*">
                </label>
              </div>
            </div>
          </fieldset>
          @error('image')
            <span class="alert alert-danger text-center">{{ $message }}</span>
          @enderror


          <hr class="my-4">

          <h4 class="mb-3">Store Owner Information</h4>

          <fieldset class="name">
            <div class="body-title">Owner Name <span class="tf-color-1">*</span></div>
            <input class="flex-grow" type="text" name="owner_name" value="{{ old('owner_name', $owner->name) }}"
              required>
          </fieldset>
          @error('owner_name')
            <span class="alert alert-danger text-center">{{ $message }}</span>
          @enderror

          <fieldset class="name">
            <div class="body-title">Owner Email <span class="tf-color-1">*</span></div>
            <input class="flex-grow" type="email" name="owner_email" value="{{ old('owner_email', $owner->email) }}"
              required>
          </fieldset>
          @error('owner_email')
            <span class="alert alert-danger text-center">{{ $message }}</span>
          @enderror

          <fieldset class="name">
            <div class="body-title">Owner Mobile <span class="tf-color-1">*</span></div>
            <input class="flex-grow" type="text" name="owner_mobile" value="{{ old('owner_mobile', $owner->mobile) }}"
              required>
          </fieldset>
          @error('owner_mobile')
            <span class="alert alert-danger text-center">{{ $message }}</span>
          @enderror

          <fieldset class="name">
            <div class="body-title">New Password <span class="text-muted">(leave blank to keep existing)</span></div>
            <input class="flex-grow" type="password" name="owner_password" placeholder="Enter new password if needed">
          </fieldset>
          @error('owner_password')
            <span class="alert alert-danger text-center">{{ $message }}</span>
          @enderror

          <div class="bot mt-4">
            <div></div>
            <button class="tf-button w208" type="submit">Update Store</button>
          </div>
        </form>
      </div>
    </div>
  </div>
@endsection

@push('scripts')
  <script>
    $(function() {
      // Auto generate slug dari nama store
      $("input[name='name']").on("input", function() {
        $("input[name='slug']").val(StringToSlug($(this).val()));
      });

      // Preview gambar saat file dipilih
      $("#myFile").on("change", function() {
        const [file] = this.files;
        if (file) {
          const previewHtml = `
          <div class="item" id="imgpreview">
            <img src="${URL.createObjectURL(file)}" class="effect8" alt="Preview" style="max-height: 124px;">
          </div>`;
          $("#imgpreview").remove(); // hapus preview lama
          $("#upload-file").before(previewHtml); // insert baru sebelum upload box
        }
      });
    });

    // Fungsi konversi teks ke slug
    function StringToSlug(Text) {
      return Text.toLowerCase()
        .replace(/\s+/g, "-")
        .replace(/[^\w-]+/g, "")
        .replace(/-+/g, "-")
        .replace(/^-+|-+$/g, "");
    }
  </script>
@endpush
