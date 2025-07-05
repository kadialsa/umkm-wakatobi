@extends('layouts.admin')

@section('content')
  <div class="main-content-inner">
    <div class="main-content-wrap">
      <div class="flex items-center flex-wrap justify-between gap20 mb-27">
        <h3>Edit User</h3>
        <ul class="breadcrumbs flex items-center flex-wrap justify-start gap10">
          <li>
            <a href="{{ route('admin.index') }}">
              <div class="text-tiny">Dashboard</div>
            </a>
          </li>
          <li><i class="icon-chevron-right"></i></li>
          <li>
            <a href="{{ route('admin.users.index') }}">
              <div class="text-tiny">Users</div>
            </a>
          </li>
          <li><i class="icon-chevron-right"></i></li>
          <li>
            <div class="text-tiny">Edit User</div>
          </li>
        </ul>
      </div>

      <div class="wg-box p-5">
        <form class="form-new-product form-style-1" action="{{ route('admin.users.update', $user->id) }}" method="POST">
          @csrf
          @method('PUT')

          {{-- Name --}}
          <fieldset class="name mb-3">
            <div class="body-title">Name <span class="tf-color-1">*</span></div>
            <input type="text" name="name" value="{{ old('name', $user->name) }}" required>
          </fieldset>
          @error('name')
            <span class="alert alert-danger">{{ $message }}</span>
          @enderror

          {{-- Email --}}
          <fieldset class="name mb-3">
            <div class="body-title">Email <span class="tf-color-1">*</span></div>
            <input type="email" name="email" value="{{ old('email', $user->email) }}" required>
          </fieldset>
          @error('email')
            <span class="alert alert-danger">{{ $message }}</span>
          @enderror

          {{-- Mobile --}}
          <fieldset class="name mb-3">
            <div class="body-title">Mobile <span class="tf-color-1">*</span></div>
            <input type="text" name="mobile" value="{{ old('mobile', $user->mobile) }}" required>
          </fieldset>
          @error('mobile')
            <span class="alert alert-danger">{{ $message }}</span>
          @enderror

          {{-- Password (leave blank to keep current) --}}
          <fieldset class="name mb-3">
            <div class="body-title">Password <small>(leave blank to keep)</small></div>
            <input type="password" name="password" placeholder="New password (optional)">
          </fieldset>
          @error('password')
            <span class="alert alert-danger">{{ $message }}</span>
          @enderror

          {{-- Utype --}}
          <fieldset class="name mb-4">
            <div class="body-title">Type <span class="tf-color-1">*</span></div>
            <select name="utype" required>
              <option value="USR" {{ old('utype', $user->utype) == 'USR' ? 'selected' : '' }}>Customer (USR)</option>
              <option value="STR" {{ old('utype', $user->utype) == 'STR' ? 'selected' : '' }}>Store Owner (STR)</option>
              <option value="ADM" {{ old('utype', $user->utype) == 'ADM' ? 'selected' : '' }}>Admin (ADM)</option>
            </select>
          </fieldset>
          @error('utype')
            <span class="alert alert-danger">{{ $message }}</span>
          @enderror

          <div class="bot">
            <div></div>
            <button class="tf-button w208" type="submit">Update</button>
          </div>
        </form>
      </div>
    </div>
  </div>
@endsection
