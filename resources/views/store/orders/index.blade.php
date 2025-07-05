@extends('layouts.store')

@push('styles')
  <style>
    .form-search {
      border: 1px solid #686c6c !important;
      border-radius: 1rem;
    }
  </style>
@endpush

@section('content')
  <div class="main-content-inner">
    {{-- Header --}}
    <div class="flex items-center justify-between mb-4">
      <h3>Semua Pesanan</h3>
      <ul class="breadcrumbs flex items-center gap-2">
        <li><a href="{{ route('store.index') }}">Dashboard</a></li>
        <li><i class="icon-chevron-right"></i></li>
        <li>All Orders</li>
      </ul>
    </div>

    <div class="wg-box p-4">

      {{-- Search bar --}}
      <form class="form-search mb-4" method="GET" action="{{ route('store.orders.index') }}">
        <fieldset class="name">
          <input type="text" name="search" placeholder="Cari pesanan..." value="{{ request('search') }}"
            class="form-control">
        </fieldset>
        <div class="button-submit">
          <button type="submit">
            <i class="icon-search"></i></button>
        </div>
      </form>

      {{-- Jika tidak ada hasil --}}
      @if ($orders->isEmpty())
        <div class="alert alert-danger text-center fs-3 p-4">
          Tidak ada pesanan yang sesuai dengan pencarian “<strong>{{ request('search') }}</strong>”.
        </div>
      @endif

      {{-- Tabel pesanan --}}
      <div class="table-responsive">
        <table class="table table-striped table-bordered mb-0">
          <thead class="table-light">
            <tr>
              <th>#</th>
              <th>Tanggal</th>
              <th>Pelanggan</th>
              <th>Total</th>
              <th>Status</th>
              <th>Aksi</th>
            </tr>
          </thead>
          <tbody>
            @foreach ($orders as $order)
              @php
                $statusClasses = [
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
                <td>{{ $order->user->name ?? $order->recipient_name }}</td>
                <td>@rupiahSymbol($order->total)</td>
                <td>
                  <span class="{{ $statusClasses[$order->status] ?? 'badge bg-secondary' }}">
                    {{ ucfirst($order->status) }}
                  </span>
                </td>
                <td>
                  <a href="{{ route('store.orders.show', $order) }}" class="btn btn-lg btn-primary fw-bold">
                    Detail
                  </a>
                </td>
              </tr>
            @endforeach
          </tbody>
        </table>
      </div>

      {{-- Pagination --}}
      <div class="mt-3">
        {{ $orders->links('pagination::bootstrap-5') }}
      </div>
    </div>
  </div>
@endsection
