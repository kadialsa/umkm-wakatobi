@extends('layouts.admin')

@section('content')
  <div class="main-content-inner">
    <div class="main-content-wrap">
      <div class="flex items-center flex-wrap justify-between gap20 mb-27">
        <h3>Add Address</h3>
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
            <div class="text-tiny">New Address</div>
          </li>
        </ul>
      </div>

      <div class="wg-box max-w-screen-md mx-auto p-5">
        <form class="form-new-product form-style-1" action="{{ route('admin.address.store') }}" method="POST"
          enctype="multipart/form-data">
          @csrf
          <fieldset class="name mb-5">
            <div class="body-title">Full Name <span class="tf-color-1">*</span></div>
            <input class="flex-grow" type="text" name="name" placeholder="Full Name" tabindex="0"
              value="{{ old('name') }}" aria-required="true" required>
            @error('name')
              <span class="text-danger text-tiny">{{ $message }}</span>
            @enderror
          </fieldset>

          <fieldset class="name mb-5">
            <div class="body-title">Phone Number <span class="tf-color-1">*</span></div>
            <input class="flex-grow" type="text" name="phone" placeholder="Phone Number" tabindex="0"
              value="{{ old('phone') }}" aria-required="true" required>
            @error('phone')
              <span class="text-danger text-tiny">{{ $message }}</span>
            @enderror
          </fieldset>

          <fieldset class="name mb-5">
            <div class="body-title">Pincode <span class="tf-color-1">*</span></div>
            <input class="flex-grow" type="text" name="zip" placeholder="Pincode" tabindex="0"
              value="{{ old('zip') }}" aria-required="true" required>
            @error('zip')
              <span class="text-danger text-tiny">{{ $message }}</span>
            @enderror
          </fieldset>

          <fieldset class="name mb-5">
            <div class="body-title">State <span class="tf-color-1">*</span></div>
            <input class="flex-grow" type="text" name="state" placeholder="State" tabindex="0"
              value="{{ old('state') }}" aria-required="true" required>
            @error('state')
              <span class="text-danger text-tiny">{{ $message }}</span>
            @enderror
          </fieldset>

          <fieldset class="name mb-5">
            <div class="body-title">City <span class="tf-color-1">*</span></div>
            <input class="flex-grow" type="text" name="city" placeholder="City" tabindex="0"
              value="{{ old('city') }}" aria-required="true" required>
            @error('city')
              <span class="text-danger text-tiny">{{ $message }}</span>
            @enderror
          </fieldset>

          <fieldset class="name mb-5">
            <div class="body-title">House no, Building <span class="tf-color-1">*</span></div>
            <input class="flex-grow" type="text" name="address" placeholder="House no, Building" tabindex="0"
              value="{{ old('address') }}" aria-required="true" required>
            @error('address')
              <span class="text-danger text-tiny">{{ $message }}</span>
            @enderror
          </fieldset>

          <fieldset class="name mb-5">
            <div class="body-title">Area, Road Name <span class="tf-color-1">*</span></div>
            <input class="flex-grow" type="text" name="locality" placeholder="Area, Road Name" tabindex="0"
              value="{{ old('locality') }}" aria-required="true" required>
            @error('locality')
              <span class="text-danger text-tiny">{{ $message }}</span>
            @enderror
          </fieldset>

          <fieldset class="name mb-5">
            <div class="body-title">Landmark <span class="tf-color-1">*</span></div>
            <input class="flex-grow" type="text" name="landmark" placeholder="Landmark" tabindex="0"
              value="{{ old('landmark') }}" aria-required="true" required>
            @error('landmark')
              <span class="text-danger text-tiny">{{ $message }}</span>
            @enderror
          </fieldset>

          <fieldset class="name mb-5">
            <div class="body-title">Country <span class="tf-color-1">*</span></div>
            <input class="flex-grow" type="text" name="country" placeholder="Country" tabindex="0"
              value="{{ old('country', 'Indonesia') }}" aria-required="true" required>
            @error('country')
              <span class="text-danger text-tiny">{{ $message }}</span>
            @enderror
          </fieldset>

          <div class="bot flex justify-end gap-4 pt-4">
            <a href="{{ route('user.address') }}" class="tf-button bg-gray-400">Cancel</a>
            <button class="tf-button w208" type="submit">Add Address</button>
          </div>
        </form>
      </div>

    </div>
  </div>
@endsection
