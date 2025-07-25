@extends('layouts.store')

@push('styles')
  <style>
    .form-control {
      padding: .75rem;
      font-size: 1.4rem;
      border: 1px solid #686c6c !important;
    }
  </style>
@endpush

@section('content')
  <div class="main-content-inner">
    <!-- main-content-wrap -->
    <div class="main-content-wrap">
      <div class="flex items-center flex-wrap justify-between gap20 mb-27">
        <h3>Add Product</h3>
        <ul class="breadcrumbs flex items-center flex-wrap justify-start gap10">
          <li>
            <a href="{{ route('admin.index') }}">
              <div class="text-tiny">Dashboard</div>
            </a>
          </li>
          <li>
            <i class="icon-chevron-right"></i>
          </li>
          <li>
            <a href="{{ route('store.products.index') }}">
              <div class="text-tiny">Products</div>
            </a>
          </li>
          <li>
            <i class="icon-chevron-right"></i>
          </li>
          <li>
            <div class="text-tiny">Add product</div>
          </li>
        </ul>
      </div>
      <!-- form-add-product -->
      <form class="tf-section-2 form-add-product" method="post" enctype="multipart/form-data"
        action="{{ route('store.products.store') }}">
        @csrf
        <div class="wg-box p-5">
          <fieldset class="name">
            <div class="body-title mb-10">Product name <span class="tf-color-1">*</span>
            </div>
            <input class="mb-10 form-control" type="text" placeholder="Enter product name" name="name"
              tabindex="0" value="{{ old('name') }}" aria-required="true" required="">
            <div class="text-tiny">Do not exceed 100 characters when entering the
              product name.</div>
          </fieldset>
          @error('name')
            <span class="alert alert-danger text-center">{{ $message }}</span>
          @enderror

          <fieldset class="name">
            <div class="body-title mb-10">Slug <span class="tf-color-1">*</span></div>
            <input class="mb-10 form-control" type="text" placeholder="Enter product slug" name="slug"
              tabindex="0" value="{{ old('slug') }}" aria-required="true" required="">
            <div class="text-tiny">Do not exceed 100 characters when entering the
              product name.</div>
          </fieldset>
          @error('slug')
            <span class="alert alert-danger text-center">{{ $message }}</span>
          @enderror

          {{-- Category --}}
          <div class="gap22 cols">
            <fieldset class="category">
              <div class="body-title mb-10">Category <span class="tf-color-1">*</span>
              </div>
              <div class="select">
                <select class="" name="category_id">
                  <option>Choose category</option>

                  @foreach ($categories as $category)
                    <option value="{{ $category->id }}">{{ $category->name }}</option>
                  @endforeach

                </select>
              </div>
            </fieldset>
            @error('category_id')
              <span class="alert alert-danger text-center">{{ $message }}</span>
            @enderror
            {{-- endCategory --}}

            {{-- bramd --}}
            {{-- <fieldset class="brand">
              <div class="body-title mb-10">Brand <span class="tf-color-1">*</span>
              </div>
              <div class="select">
                <select class="" name="brand_id">
                  <option>Choose Brand</option>

                  @foreach ($brands as $brand)
                    <option value="{{ $brand->id }}">{{ $brand->name }}</option>
                  @endforeach

                </select>
              </div>
            </fieldset>
            @error('brand_id')
              <span class="alert alert-danger text-center">{{ $message }}</span>
            @enderror --}}
          </div>
          {{-- end Brand --}}

          <fieldset class="shortdescription">
            <div class="body-title mb-10">Short Description <span class="tf-color-1">*</span></div>
            <textarea class="mb-10 ht-150" name="short_description" placeholder="Short Description" tabindex="0"
              aria-required="true" required="">{{ old('short_description') }}</textarea>
            <div class="text-tiny">Do not exceed 100 characters when entering the
              product name.</div>
          </fieldset>
          @error('short_description')
            <span class="alert alert-danger text-center">{{ $message }}</span>
          @enderror

          <fieldset class="description">
            <div class="body-title mb-10">Description <span class="tf-color-1">*</span>
            </div>
            <textarea class="mb-10" name="description" placeholder="Description" tabindex="0" aria-required="true"
              required="">{{ old('description') }}</textarea>
            <div class="text-tiny">Do not exceed 100 characters when entering the
              product name.</div>
          </fieldset>
          @error('description')
            <span class="alert alert-danger text-center">{{ $message }}</span>
          @enderror
        </div>
        <div class="wg-box p-5">
          <fieldset>
            <div class="body-title">Upload images <span class="tf-color-1">*</span>
            </div>
            <div class="upload-image flex-grow mt-2">
              <div class="item" id="imgpreview" style="display:none">
                <img src="../../../localhost_8000/images/upload/upload-1.png" class="effect8" alt="">
              </div>
              <div id="upload-file" class="item up-load">
                <label class="uploadfile" for="myFile">
                  <span class="icon">
                    <i class="icon-upload-cloud"></i>
                  </span>
                  <span class="body-text">Drop your images here or select <span class="tf-color">click to
                      browse</span></span>
                  <input type="file" id="myFile" name="image" accept="image/*">
                </label>
              </div>
            </div>
          </fieldset>
          @error('image')
            <span class="alert alert-danger text-center">{{ $message }}</span>
          @enderror

          <fieldset>
            <div class="body-title mb-10">Upload Gallery Images</div>
            <div class="upload-image mb-16">
              <div id="galUpload" class="item up-load">
                <label class="uploadfile" for="gFile">
                  <span class="icon">
                    <i class="icon-upload-cloud"></i>
                  </span>
                  <span class="text-tiny">Drop your images here or select <span class="tf-color">click
                      to browse</span></span>
                  <input type="file" id="gFile" name="images[]" accept="image/*" multiple="">
                </label>
              </div>
            </div>
          </fieldset>
          @error('images')
            <span class="alert alert-danger text-center">{{ $message }}</span>
          @enderror

          <div class="cols gap22">
            <fieldset class="name">
              <div class="body-title mb-10">Harga Asli <span class="tf-color-1">*</span></div>
              <input class="mb-10 form-control" type="text" placeholder="Enter regular price" name="regular_price"
                tabindex="0" value="{{ old('regular_price') }}" aria-required="true" required="">
            </fieldset>
            @error('regular_price')
              <span class="alert alert-danger text-center">{{ $message }}</span>
            @enderror
            <fieldset class="name">
              <div class="body-title mb-10">Harga Discount <span class="tf-color-1">*</span></div>
              <input class="mb-10 form-control" type="text" placeholder="Enter sale price" name="sale_price"
                tabindex="0" value="{{ old('sale_price') }}" aria-required="true" required="">
            </fieldset>
            @error('sale_price')
              <span class="alert alert-danger text-center">{{ $message }}</span>
            @enderror
          </div>

          <div class="cols gap22">
            <fieldset class="name">
              <div class="body-title mb-10">SKU <span class="tf-color-1">*</span>
              </div>
              <input class="mb-10 form-control" type="text" placeholder="Enter SKU" name="SKU" tabindex="0"
                value="{{ old('SKU') }}" aria-required="true" required="">
            </fieldset>
            @error('SKU')
              <span class="alert alert-danger text-center">{{ $message }}</span>
            @enderror

            <fieldset class="name">
              <div class="body-title mb-10">Quantity <span class="tf-color-1">*</span>
              </div>
              <input class="mb-10 form-control" type="text" placeholder="Enter quantity" name="quantity"
                tabindex="0" value="{{ old('quantity') }}" aria-required="true" required="">
            </fieldset>
            @error('quantity')
              <span class="alert alert-danger text-center">{{ $message }}</span>
            @enderror

          </div>

          <div class="cols gap22">

            <fieldset class="name">
              <div class="body-title mb-10">Produk Unggulan</div>
              <div class="select mb-10">
                <select class="" name="featured">
                  <option value="0">No</option>
                  <option value="1">Yes</option>
                </select>
              </div>
            </fieldset>
            @error('featured')
              <span class="alert alert-danger text-center">{{ $message }}</span>
            @enderror
          </div>
          <div class="cols gap10">
            <button class="tf-button w-full" type="submit">Add product</button>
          </div>
        </div>
      </form>
      <!-- /form-add-product -->
    </div>
    <!-- /main-content-wrap -->
  </div>
@endsection

@push('scripts')
  <script>
    $(function() {
      // Event ketika input file berubah
      $("#myFile").on("change", function(e) {
        const photoInp = $("myFile");
        const [file] = this.files; // Mengambil file pertama
        if (file) {
          // Menampilkan preview gambar
          $("#imgpreview img").attr("src", URL.createObjectURL(file));
          $("#imgpreview").show();
        }
      });

      $("#gFile").on("change", function(e) {
        const photoInp = $("#gFile");
        const gphotos = this.files; // Mengambil file pertama
        $.each(gphotos, function(key, val) {
          $("#galUpload").prepend(`<div class="item gitems"><img src="${URL.createObjectURL(val)}"/></div>`);
        });
      });

      // Event ketika input 'name' berubah
      $("input[name='name']").on("change", function() {
        $("input[name='slug']").val(StringToSlug($(this).val()));
      });

      // Fungsi untuk mengubah teks menjadi slug
      function StringToSlug(Text) {
        return Text.toLowerCase()
          .replace(/\s+/g, "-") // Mengganti spasi dengan tanda hubung
          .replace(/[^\w-]+/g, "") // Menghapus karakter non-alfanumerik kecuali tanda hubung
          .replace(/-+/g, "-") // Mengganti tanda hubung ganda dengan satu
          .replace(/^-+|-+$/g, ""); // Menghapus tanda hubung di awal/akhir
      }
    });
  </script>
@endpush
