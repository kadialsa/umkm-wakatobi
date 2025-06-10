@extends('layouts.admin')

@section('content')
    <div class="main-content-inner">
        <div class="main-content-wrap">
            <div class="flex items-center flex-wrap justify-between gap20 mb-27">
                <h3>Update Address</h3>
                <ul class="breadcrumbs flex items-center flex-wrap justify-start gap10">
                    <li>
                        <a href="{{ route('admin.index') }}">
                            <div class="text-tiny">Dashboard</div>
                        </a>
                    </li>
                    <li><i class="icon-chevron-right"></i></li>
                    <li>
                        <a href="{{ route('admin.address') }}">
                            <div class="text-tiny">Addresses</div>
                        </a>
                    </li>
                    <li><i class="icon-chevron-right"></i></li>
                    <li>
                        <div class="text-tiny">Update Address</div>
                    </li>
                </ul>
            </div>
            <!-- update-address -->
            <div class="wg-box">
                <form class="form-new-product form-style-1" action="{{ route('admin.address.update', $address->id) }}" method="POST" enctype="multipart/form-data">
                    @csrf
                    @method('PUT')
                    <input type="hidden" name="id" value="{{ $address->id }}">

                    <fieldset class="name">
                        <div class="body-title">Full Name <span class="tf-color-1">*</span></div>
                        <input class="flex-grow" type="text" placeholder="Full Name" name="name" tabindex="0"
                            value="{{ old('name', $address->name) }}" aria-required="true" required>
                        @error('name')
                            <span class="alert alert-danger text-center">{{ $message }}</span>
                        @enderror
                    </fieldset>

                    <fieldset class="name">
                        <div class="body-title">Phone Number <span class="tf-color-1">*</span></div>
                        <input class="flex-grow" type="text" placeholder="Phone Number" name="phone" tabindex="0"
                            value="{{ old('phone', $address->phone) }}" aria-required="true" required>
                        @error('phone')
                            <span class="alert alert-danger text-center">{{ $message }}</span>
                        @enderror
                    </fieldset>

                    <fieldset class="name">
                        <div class="body-title">Pincode <span class="tf-color-1">*</span></div>
                        <input class="flex-grow" type="text" placeholder="Pincode" name="zip" tabindex="0"
                            value="{{ old('zip', $address->zip) }}" aria-required="true" required>
                        @error('zip')
                            <span class="alert alert-danger text-center">{{ $message }}</span>
                        @enderror
                    </fieldset>

                    <fieldset class="name">
                        <div class="body-title">State <span class="tf-color-1">*</span></div>
                        <input class="flex-grow" type="text" placeholder="State" name="state" tabindex="0"
                            value="{{ old('state', $address->state) }}" aria-required="true" required>
                        @error('state')
                            <span class="alert alert-danger text-center">{{ $message }}</span>
                        @enderror
                    </fieldset>

                    <fieldset class="name">
                        <div class="body-title">City <span class="tf-color-1">*</span></div>
                        <input class="flex-grow" type="text" placeholder="City" name="city" tabindex="0"
                            value="{{ old('city', $address->city) }}" aria-required="true" required>
                        @error('city')
                            <span class="alert alert-danger text-center">{{ $message }}</span>
                        @enderror
                    </fieldset>

                    <fieldset class="name">
                        <div class="body-title">House no, Building <span class="tf-color-1">*</span></div>
                        <input class="flex-grow" type="text" placeholder="House no, Building" name="address" tabindex="0"
                            value="{{ old('address', $address->address) }}" aria-required="true" required>
                        @error('address')
                            <span class="alert alert-danger text-center">{{ $message }}</span>
                        @enderror
                    </fieldset>

                    <fieldset class="name">
                        <div class="body-title">Area, Road Name <span class="tf-color-1">*</span></div>
                        <input class="flex-grow" type="text" placeholder="Area, Road Name" name="locality" tabindex="0"
                            value="{{ old('locality', $address->locality) }}" aria-required="true" required>
                        @error('locality')
                            <span class="alert alert-danger text-center">{{ $message }}</span>
                        @enderror
                    </fieldset>

                    <fieldset class="name">
                        <div class="body-title">Landmark <span class="tf-color-1">*</span></div>
                        <input class="flex-grow" type="text" placeholder="Landmark" name="landmark" tabindex="0"
                            value="{{ old('landmark', $address->landmark) }}" aria-required="true" required>
                        @error('landmark')
                            <span class="alert alert-danger text-center">{{ $message }}</span>
                        @enderror
                    </fieldset>

                    <fieldset class="name">
                        <div class="body-title">Country <span class="tf-color-1">*</span></div>
                        <input class="flex-grow" type="text" placeholder="Country" name="country" tabindex="0"
                            value="{{ old('country', $address->country ?? '') }}" aria-required="true" required>
                        @error('country')
                            <span class="alert alert-danger text-center">{{ $message }}</span>
                        @enderror
                    </fieldset>

                    <div class="bot">
                        <div></div>
                        <button class="tf-button w208" type="submit">Update</button>
                    </div>
                </form>
            </div>
        </div>
    </div>
@endsection

@push('scripts')
<script>
    $(function () {
        // Jika kamu ingin preview gambar upload (kalau alamat ada file gambar)
        // Kamu bisa sesuaikan sesuai kebutuhan alamat kamu.

        // Contoh preview gambar untuk image upload jika ada input file (opsional)
        $("#myFile").on("change", function (e) {
            const [file] = this.files;
            if (file) {
                $("#imgpreview img").attr("src", URL.createObjectURL(file));
                $("#imgpreview").show();
            }
        });

        // Contoh: jika ingin buat auto slug dari name (kalau perlu)
        // $("input[name='name']").on("change", function () {
        //     $("input[name='slug']").val(StringToSlug($(this).val()));
        // });

    });

    // Fungsi untuk mengubah teks menjadi slug (opsional)
    function StringToSlug(Text) {
        return Text.toLowerCase()
            .replace(/\s+/g, "-")
            .replace(/[^\w-]+/g, "")
            .replace(/-+/g, "-")
            .replace(/^-+|-+$/g, "");
    }
</script>
@endpush
