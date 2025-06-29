@extends('layouts.admin')

@section('content')
  <div class="main-content-inner">
    <div class="main-content-wrap">
      <div class="flex items-center flex-wrap justify-between gap20 mb-27">
        <h3>Add Store</h3>
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
            <div class="text-tiny">New Store</div>
          </li>
        </ul>
      </div>

      <div class="wg-box p-5">
        <form class="form-new-product form-style-1" action="{{ route('admin.store.store') }}" method="POST"
          enctype="multipart/form-data">
          @csrf

          <fieldset class="name">
            <div class="body-title">Owner Name <span class="tf-color-1">*</span></div>
            <input class="flex-grow" type="text" name="owner_name" placeholder="Owner full name"
              value="{{ old('owner_name') }}" required>
          </fieldset>
          @error('owner_name')
            <span class="alert alert-danger text-center">{{ $message }}</span>
          @enderror

          <fieldset class="name">
            <div class="body-title">Owner Email <span class="tf-color-1">*</span></div>
            <input class="flex-grow" type="email" name="owner_email" placeholder="email@example.com"
              value="{{ old('owner_email') }}" required>
          </fieldset>
          @error('owner_email')
            <span class="alert alert-danger text-center">{{ $message }}</span>
          @enderror

          <fieldset class="name">
            <div class="body-title">Owner Mobile <span class="tf-color-1">*</span></div>
            <input class="flex-grow" type="text" name="owner_mobile" placeholder="08xxxxxxxxxx"
              value="{{ old('owner_mobile') }}" required>
          </fieldset>
          @error('owner_mobile')
            <span class="alert alert-danger text-center">{{ $message }}</span>
          @enderror

          <fieldset class="name">
            <div class="body-title">Password <span class="tf-color-1">*</span></div>
            <input class="flex-grow" type="password" name="owner_password" placeholder="Password" required>
          </fieldset>
          @error('owner_password')
            <span class="alert alert-danger text-center">{{ $message }}</span>
          @enderror

          <fieldset class="name">
            <div class="body-title">Store Name <span class="tf-color-1">*</span></div>
            <input class="flex-grow" type="text" placeholder="Store name" name="name" value="{{ old('name') }}"
              required>
          </fieldset>
          @error('name')
            <span class="alert alert-danger text-center">{{ $message }}</span>
          @enderror

          <fieldset class="name">
            <div class="body-title">Store Slug <span class="tf-color-1">*</span></div>
            <input class="flex-grow" type="text" placeholder="store-slug" name="slug" value="{{ old('slug') }}"
              required>
          </fieldset>
          @error('slug')
            <span class="alert alert-danger text-center">{{ $message }}</span>
          @enderror

          <fieldset class="name">
            <div class="body-title">Description</div>
            <textarea class="flex-grow" rows="4" placeholder="Store description (optional)" name="description">{{ old('description') }}</textarea>
          </fieldset>
          @error('description')
            <span class="alert alert-danger text-center">{{ $message }}</span>
          @enderror

          <fieldset>
            <div class="body-title">Upload Image</div>
            <div class="upload-image flex-grow">
              <div class="item" id="imgpreview" style="display:none">
                <img src="#" class="effect8" alt="">
              </div>
              <div id="upload-file" class="item up-load">
                <label class="uploadfile" for="myFile">
                  <span class="icon"><i class="icon-upload-cloud"></i></span>
                  <span class="body-text">Drop your image here or select <span class="tf-color">click to
                      browse</span></span>
                  <input type="file" id="myFile" name="image" accept="image/*">
                </label>
              </div>
            </div>
          </fieldset>
          @error('image')
            <span class="alert alert-danger text-center">{{ $message }}</span>
          @enderror

          <div class="bot">
            <div></div>
            <button class="tf-button w208" type="submit">Save</button>
          </div>
        </form>
      </div>
    </div>
  </div>
@endsection

@push('scripts')
  <script>
    $(function() {
      $("#myFile").on("change", function() {
        const [file] = this.files;
        if (file) {
          $("#imgpreview img").attr("src", URL.createObjectURL(file));
          $("#imgpreview").show();
        }
      });

      $("input[name='name']").on("input", function() {
        $("input[name='slug']").val(StringToSlug($(this).val()));
      });
    });

    function StringToSlug(Text) {
      return Text.toLowerCase()
        .replace(/\s+/g, "-")
        .replace(/[^\w-]+/g, "")
        .replace(/-+/g, "-")
        .replace(/^-+|-+$/g, "");
    }
  </script>
@endpush
