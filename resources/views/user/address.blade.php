@extends('layouts.app')
@section('content')
    <style>
        .table> :not(caption)>tr>th {
            padding: 0.625rem 1.5rem .625rem !important;
            background-color: #6a6e51 !important;
        }

        .table>tr>td {
            padding: 0.625rem 1.5rem .625rem !important;
        }

        .table-bordered> :not(caption)>tr>th,
        .table-bordered> :not(caption)>tr>td {
            border-width: 1px 1px;
            border-color: #6a6e51;
        }

        .table> :not(caption)>tr>td {
            padding: .8rem 1rem !important;
        }

        .bg-success {
            background-color: #40c710 !important;
        }

        .bg-danger {
            background-color: #f44032 !important;
        }

        .bg-warning {
            background-color: #f5d700 !important;
            color: #000;
        }
    </style>
    <main class="pt-90">
        <div class="mb-4 pb-4"></div>
        <section class="my-account container">
            <h2 class="page-title">Addresses</h2>
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
                                <p class="notice">The following addresses will be used on the checkout page by default.</p>
                            </div>

                            @if (session('success'))
                                <div class="alert alert-success">
                                    {{ session('success') }}
                                </div>
                            @endif

                            <div class="col-6 text-right">
                                <a href="{{ route('user.address.add') }}" class="btn btn-sm btn-info">Add New</a>
                            </div>
                        </div>

                        <div class="my-account__address-list row">
                            <h5>Shipping Address</h5>

                            @foreach ($addresses as $address)
                                <div class="my-account__address-item col-md-6">
                                    <div class="my-account__address-item__title">
                                        <h5>
                                            {{ $address->name }}
                                            @if ($address->isdefault)
                                                <i class="fa fa-check-circle text-success"></i>
                                            @endif
                                        </h5>
                                        <div style="display: flex; gap: 8px;">
                                            <a href="{{ route('user.address.edit', $address->id) }}">Edit</a>

                                            <form action="{{ route('user.address.destroy', $address->id) }}" method="POST"
                                                style="margin: 0;"
                                                onsubmit="return confirm('Are you sure want to delete this address?');">
                                                @csrf
                                                @method('DELETE')
                                                <button type="submit" class="btn btn-sm btn-danger"
                                                    style="padding: 4px 10px;">Delete</button>
                                            </form>
                                        </div>
                                    </div>
                                    <div class="my-account__address-item__detail">
                                        <p>{{ $address->address }}</p>
                                        <p>{{ $address->locality }}, {{ $address->city }}</p>
                                        <p>{{ $address->state }}, {{ $address->country }}</p>
                                        @if ($address->landmark)
                                            <p>{{ $address->landmark }}</p>
                                        @endif
                                        <p>{{ $address->zip }}</p>
                                        <br>
                                        <p>Mobile : {{ $address->phone }}</p>
                                    </div>
                                </div>
                                <hr>
                            @endforeach

                        </div>
                    </div>
                </div>
            </div>
        </section>
    </main>
@endsection
