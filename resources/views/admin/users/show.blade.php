@extends('layouts.admin')

@section('content')
  <div class="main-content-inner">
    <div class="main-content-wrap">
      <div class="flex items-center flex-wrap justify-between gap20 mb-27">
        <h3>User Details</h3>
        <ul class="breadcrumbs flex items-center flex-wrap justify-start gap10">
          <li><a href="{{ route('admin.index') }}">
              <div class="text-tiny">Dashboard</div>
            </a></li>
          <li><i class="icon-chevron-right"></i></li>
          <li><a href="{{ route('admin.users.index') }}">
              <div class="text-tiny">Users</div>
            </a></li>
          <li><i class="icon-chevron-right"></i></li>
          <li>
            <div class="text-tiny">#{{ $user->id }}</div>
          </li>
        </ul>
      </div>

      <div class="wg-box p-5">
        <div class="row mb-4">
          <div class="col-md-6">
            <h6>Name</h6>
            <p>{{ $user->name }}</p>
          </div>
          <div class="col-md-6">
            <h6>Email</h6>
            <p>{{ $user->email }}</p>
          </div>
        </div>
        <div class="row mb-4">
          <div class="col-md-6">
            <h6>Mobile</h6>
            <p>{{ $user->mobile }}</p>
          </div>
          <div class="col-md-6">
            <h6>Type</h6>
            <p>{{ $user->utype }}</p>
          </div>
        </div>
        <div class="bot">
          <a href="{{ route('admin.users.edit', $user->id) }}" class="tf-button w208">Edit</a>
          <a href="{{ route('admin.users.index') }}" class="tf-button style-1 w208">Back to List</a>
        </div>
      </div>
    </div>
  </div>
@endsection
