@extends('layouts.admin')
@section('content')
  <h2>Semua Pesanan</h2>
  <table class="table table-striped">
    <thead>
      <tr>
        <th>ID</th>
        <th>Toko</th>
        <th>Total</th>
        <th>Status Bayar</th>
        <th>Tgl Pesan</th>
        <th>Aksi</th>
      </tr>
    </thead>
    <tbody>
      @foreach ($orders as $o)
        <tr>
          <td>{{ $o->id }}</td>
          <td>{{ $o->store->name }}</td>
          <td>@rupiahSymbol($o->total)</td>
          <td>{{ $o->payment_status }}</td>
          <td>{{ $o->created_at->format('d M Y H:i') }}</td>
          <td>
            <a href="{{ route('admin.orders.show', $o->id) }}" class="btn btn-sm btn-primary">Lihat</a>
          </td>
        </tr>
      @endforeach
    </tbody>
  </table>
  {{ $orders->links() }}
@endsection
