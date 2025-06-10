@extends('layouts.app')

@section('content')
<main class="pt-90">
    <section class="my-account container">
        <h2 class="page-title">Add Address</h2>
        <div class="row">
            <div class="col-lg-3">
                @include('user.account-nav')
            </div>
            <div class="col-lg-9">
                <div class="card mb-5">
                    <div class="card-header">
                        <h5>New Address</h5>
                    </div>
                    <div class="card-body">
                        <form action="{{ route('user.address.store') }}" method="POST">
                            @csrf
                            <div class="row">
                                <div class="col-md-6">
                                    <div class="form-floating my-3">
                                        <input type="text" class="form-control" name="name" required value="{{ old('name') }}">
                                        <label for="name">Full Name *</label>
                                        @error('name') <span class="text-danger">{{ $message }}</span> @enderror
                                    </div>
                                </div>
                                <div class="col-md-6">
                                    <div class="form-floating my-3">
                                        <input type="text" class="form-control" name="phone" required value="{{ old('phone') }}">
                                        <label for="phone">Phone Number *</label>
                                        @error('phone') <span class="text-danger">{{ $message }}</span> @enderror
                                    </div>
                                </div>
                                <div class="col-md-4">
                                    <div class="form-floating my-3">
                                        <input type="text" class="form-control" name="zip" required value="{{ old('zip') }}">
                                        <label for="zip">Pincode *</label>
                                        @error('zip') <span class="text-danger">{{ $message }}</span> @enderror
                                    </div>
                                </div>
                                <div class="col-md-4">
                                    <div class="form-floating my-3">
                                        <input type="text" class="form-control" name="state" required value="{{ old('state') }}">
                                        <label for="state">State *</label>
                                        @error('state') <span class="text-danger">{{ $message }}</span> @enderror
                                    </div>
                                </div>
                                <div class="col-md-4">
                                    <div class="form-floating my-3">
                                        <input type="text" class="form-control" name="city" required value="{{ old('city') }}">
                                        <label for="city">City *</label>
                                        @error('city') <span class="text-danger">{{ $message }}</span> @enderror
                                    </div>
                                </div>
                                <div class="col-md-6">
                                    <div class="form-floating my-3">
                                        <input type="text" class="form-control" name="address" required value="{{ old('address') }}">
                                        <label for="address">House no, Building *</label>
                                        @error('address') <span class="text-danger">{{ $message }}</span> @enderror
                                    </div>
                                </div>
                                <div class="col-md-6">
                                    <div class="form-floating my-3">
                                        <input type="text" class="form-control" name="locality" required value="{{ old('locality') }}">
                                        <label for="locality">Area, Road Name *</label>
                                        @error('locality') <span class="text-danger">{{ $message }}</span> @enderror
                                    </div>
                                </div>
                                <div class="col-md-6">
                                    <div class="form-floating my-3">
                                        <input type="text" class="form-control" name="landmark" required value="{{ old('landmark') }}">
                                        <label for="landmark">Landmark *</label>
                                        @error('landmark') <span class="text-danger">{{ $message }}</span> @enderror
                                    </div>
                                </div>
                                <div class="col-md-6">
                                    <div class="form-floating my-3">
                                        <input type="text" class="form-control" name="country" required value="{{ old('country', 'Indonesia') }}">
                                        <label for="country">Country *</label>
                                        @error('country') <span class="text-danger">{{ $message }}</span> @enderror
                                    </div>
                                </div>
                                <div class="col-md-12 text-end">
                                    <button type="submit" class="btn btn-success">Add Address</button>
                                    <a href="{{ route('user.address') }}" class="btn btn-secondary">Cancel</a>
                                </div>
                            </div>
                        </form>
                    </div>
                </div>
            </div>
        </div>
    </section>
</main>
@endsection
