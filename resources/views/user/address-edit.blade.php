@extends('layouts.app')

@section('content')
    <main class="pt-90">
        <div class="mb-4 pb-4"></div>
        <section class="my-account container">
            <h2 class="page-title">Edit Address</h2>
            <div class="row">
                <div class="col-lg-3">
                    <div class="col-lg-2">
                        @include('user.account-nav')
                    </div>
                </div>
                <div class="col-lg-9">
                    <div class="page-content my-account__address">
                        <div class="row">
                            <div class="col-6">
                                <p class="notice">Update your address details below.</p>
                            </div>
                            <div class="col-6 text-right">
                                <a href="{{ route('user.address') }}" class="btn btn-sm btn-danger">Back</a>
                            </div>
                        </div>

                        <div class="row">
                            <div class="col-md-8">
                                <div class="card mb-5">
                                    <div class="card-header">
                                        <h5>Update Address</h5>
                                    </div>
                                    <div class="card-body">
                                        <form action="{{ route('user.address.update', $address->id) }}" method="POST">
                                            @csrf
                                            @method('PUT')

                                            <div class="row">
                                                <div class="col-md-6">
                                                    <div class="form-floating my-3">
                                                        <input type="text" class="form-control" name="name"
                                                            value="{{ old('name', $address->name) }}" required>
                                                        <label for="name">Full Name *</label>
                                                        @error('name')
                                                            <span class="text-danger"> {{ $message }} </span>
                                                        @enderror
                                                    </div>
                                                </div>

                                                <div class="col-md-6">
                                                    <div class="form-floating my-3">
                                                        <input type="text" class="form-control" name="phone"
                                                            value="{{ old('phone', $address->phone) }}" required>
                                                        <label for="phone">Phone Number *</label>
                                                        @error('phone')
                                                            <span class="text-danger"> {{ $message }} </span>
                                                        @enderror
                                                    </div>
                                                </div>

                                                <div class="col-md-4">
                                                    <div class="form-floating my-3">
                                                        <input type="text" class="form-control" name="zip"
                                                            value="{{ old('zip', $address->zip) }}" required>
                                                        <label for="zip">Pincode *</label>
                                                        @error('zip')
                                                            <span class="text-danger"> {{ $message }} </span>
                                                        @enderror
                                                    </div>
                                                </div>

                                                <div class="col-md-4">
                                                    <div class="form-floating mt-3 mb-3">
                                                        <input type="text" class="form-control" name="state"
                                                            value="{{ old('state', $address->state) }}" required>
                                                        <label for="state">State *</label>
                                                        @error('state')
                                                            <span class="text-danger"> {{ $message }} </span>
                                                        @enderror
                                                    </div>
                                                </div>

                                                <div class="col-md-4">
                                                    <div class="form-floating my-3">
                                                        <input type="text" class="form-control" name="city"
                                                            value="{{ old('city', $address->city) }}" required>
                                                        <label for="city">Town / City *</label>
                                                        @error('city')
                                                            <span class="text-danger"> {{ $message }} </span>
                                                        @enderror
                                                    </div>
                                                </div>

                                                <div class="col-md-6">
                                                    <div class="form-floating my-3">
                                                        <input type="text" class="form-control" name="address"
                                                            value="{{ old('address', $address->address) }}" required>
                                                        <label for="address">House no, Building Name *</label>
                                                        @error('address')
                                                            <span class="text-danger"> {{ $message }} </span>
                                                        @enderror
                                                    </div>
                                                </div>

                                                <div class="col-md-6">
                                                    <div class="form-floating my-3">
                                                        <input type="text" class="form-control" name="locality"
                                                            value="{{ old('locality', $address->locality) }}" required>
                                                        <label for="locality">Road Name, Area, Colony *</label>
                                                        @error('locality')
                                                            <span class="text-danger"> {{ $message }} </span>
                                                        @enderror
                                                    </div>
                                                </div>

                                                <div class="col-md-12">
                                                    <div class="form-floating my-3">
                                                        <input type="text" class="form-control" name="landmark"
                                                            value="{{ old('landmark', $address->landmark) }}" required>
                                                        <label for="landmark">Landmark *</label>
                                                        @error('landmark')
                                                            <span class="text-danger"> {{ $message }} </span>
                                                        @enderror
                                                    </div>
                                                </div>

                                                <div class="col-md-6">
                                                    <div class="form-floating my-3">
                                                        <input type="text" class="form-control" name="country"
                                                            value="{{ old('country', $address->country ?? '') }}" required>
                                                        <label for="country">Country *</label>
                                                        @error('country')
                                                            <span class="text-danger"> {{ $message }} </span>
                                                        @enderror
                                                    </div>
                                                </div>


                                                <div class="col-md-12 text-right">
                                                    <button type="submit" class="btn btn-success">Update</button>
                                                </div>
                                            </div>
                                        </form>
                                    </div>
                                </div>
                            </div>
                        </div>
                        <hr>
                    </div>
                </div>
            </div>
        </section>
    </main>
@endsection
