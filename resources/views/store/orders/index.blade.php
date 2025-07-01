@extends('layouts.store')

@section('content')
  <div class="main-content-inner">
    <div class="main-content-wrap">
      {{-- Header --}}
      <div class="flex items-center flex-wrap justify-between gap20 mb-27">
        <h3>All Orders</h3>
        <ul class="breadcrumbs flex items-center gap10">
          <li><a href="{{ route('store.index') }}">
              <div class="text-tiny">Dashboard</div>
            </a></li>
          <li><i class="icon-chevron-right"></i></li>
          <li>
            <div class="text-tiny">All Orders</div>
          </li>
        </ul>
      </div>

      <div class="wg-box">
        {{-- Search & Filter --}}
        <div class="flex items-center justify-between gap10 flex-wrap mb-3">
          <form class="form-search" method="GET" action="{{ route('store.orders.index') }}">
            <fieldset class="name">
              <input type="text" name="search" value="{{ request('search') }}"
                placeholder="Search by order ID or customer..." required>
            </fieldset>
            <button type="submit" class="button-submit"><i class="icon-search"></i></button>
          </form>
        </div>

        {{-- Flash --}}
        @if (session('status'))
          <div class="alert alert-success">{{ session('status') }}</div>
        @endif

        {{-- Tabel --}}
        <div class="table-responsive">
          <table class="table table-striped table-bordered">
            <thead>
              <tr>
                <th>#</th>
                <th>Tanggal</th>
                <th>Customer</th>
                <th>Total</th>
                <th>Status</th>
                <th>Aksi</th>
              </tr>
            </thead>
            <tbody>
              @forelse($orders as $order)
                @php
                  $classes = [
                      'ordered' => 'badge bg-secondary',
                      'shipped' => 'badge bg-info text-white',
                      'delivered' => 'badge bg-success',
                      'completed' => 'badge bg-primary',
                      'canceled' => 'badge bg-danger',
                  ];
                @endphp
                <tr>
                  <td>{{ $order->id }}</td>
                  <td>{{ $order->created_at->format('d M Y, H:i') }}</td>
                  <td>{{ $order->user->name ?? '-' }}</td>
                  <td>@rupiahSymbol($order->total)</td>
                  <td>
                    <span class="{{ $classes[$order->status] ?? 'badge bg-secondary' }}">
                      {{ ucfirst($order->status) }}
                    </span>
                  </td>
                  <td>
                    <a href="{{ route('store.orders.show', $order) }}" class="btn btn-lg btn-primary">Detail</a>
                  </td>
                </tr>
              @empty
                <tr>
                  <td colspan="6" class="text-center">Belum ada pesanan.</td>
                </tr>
              @endforelse
            </tbody>
          </table>
        </div>

        {{-- Pagination --}}
        <div class="divider"></div>
        <div class="flex items-center justify-between wgp-pagination">
          {{ $orders->links('pagination::bootstrap-5') }}
        </div>
      </div>
    </div>
  </div>
@endsection
