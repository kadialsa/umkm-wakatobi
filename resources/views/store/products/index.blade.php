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
    <div class="main-content-wrap">
      <div class="flex items-center flex-wrap justify-between gap20 mb-27">
        <h3>All Products</h3>
        <ul class="breadcrumbs flex items-center flex-wrap justify-start gap10">
          <li>
            <a href="#">
              <div class="text-tiny">Dashboard</div>
            </a>
          </li>
          <li>
            <i class="icon-chevron-right"></i>
          </li>
          <li>
            <div class="text-tiny">All Products</div>
          </li>
        </ul>
      </div>

      <div class="wg-box">
        <div class="flex items-center justify-between gap10 flex-wrap">
          <div class="wg-filter flex-grow">
            <form class="form-search mb-4" method="GET" action="{{ route('store.products.index') }}">
              <fieldset class="name">
                <input type="text" name="name" placeholder="Cari produk..." value="{{ request('name') }}"
                  class="form-control">
              </fieldset>
              <div class="button-submit">
                <button type="submit"><i class="icon-search"></i></button>
              </div>
            </form>
          </div>
          <a class="tf-button style-1 w208" href="{{ route('store.products.create') }}"><i class="icon-plus"></i>Add
            new</a>
        </div>
        <div class="table-responsive">
          @if (Session::has('status'))
            <div class="alert alert-success">
              <p class="alert alert-success">{{ Session::get('status') }}</p>
            </div>
          @endif

          @if ($products->isEmpty())
            <div class="alert alert-danger text-center fs-3 p-4">
              Tidak ada produk yang sesuai dengan pencarian “<strong>{{ request('name') }}</strong>”.
            </div>
          @else
            <table class="table table-striped table-bordered">
              <thead>
                <tr>
                  <th>No.</th>
                  <th>Nama</th>
                  <th>Harga Asli</th>
                  <th>Harga Discount</th>
                  <th>SKU</th>
                  <th>Category</th>
                  <th>Produk Unggulan</th>
                  <th>Quantity</th>
                  <th>Action</th>
                </tr>
              </thead>
              <tbody>
                @foreach ($products as $product)
                  <tr>
                    <td>{{ $loop->iteration }}</td>
                    <td class="pname">
                      <div class="image">
                        <img src="{{ asset('uploads/products/thumbnails') }}/{{ $product->image }}"
                          alt="{{ $product->name }}" class="image">
                      </div>
                      <div class="name">
                        <a href="#" class="body-title-2">{{ $product->name }}</a>
                        <div class="text-tiny mt-3">{{ $product->slug }}</div>
                      </div>
                    </td>
                    <td>Rp. {{ $product->regular_price }}</td>
                    <td>Rp. {{ $product->sale_price }}</td>
                    <td>{{ $product->SKU }}</td>
                    <td>{{ $product->category->name }}</td>
                    {{-- <td>{{ $product->brand->name }}</td> --}}
                    <td>{{ $product->featured == 0 ? 'No' : 'Yes' }}</td>
                    <td>{{ $product->quantity }}</td>
                    <td>
                      <div class="list-icon-function">
                        <a href="{{ route('store.products.edit', ['product' => $product->id]) }}">
                          <div class="item edit">
                            <i class="icon-edit-3"></i>
                          </div>
                        </a>
                        <form action="{{ route('store.products.destroy', ['product' => $product->id]) }}" method="POST">
                          @csrf
                          @method('DELETE')
                          <div class="item text-danger delete" style="border:none; background:none; color:red;">
                            <i class="icon-trash-2"></i>
                          </div>
                        </form>
                      </div>
                    </td>
                  </tr>
                @endforeach
              </tbody>
            </table>
          @endif
        </div>

        <div class="divider"></div>
        <div class="flex items-center justify-between flex-wrap gap10 wgp-pagination">
          {{ $products->links('pagination::bootstrap-5') }}

        </div>
      </div>
    </div>
  </div>
@endsection

@push('scripts')
  <script>
    $(function() {
      $('.delete').on('click', function(e) {
        e.preventDefault();
        var form = $(this).closest('form');
        swal({
          title: "Are you sure?",
          text: "You want to delete this record?",
          icon: "warning",
          buttons: ["No", "Yes"],
          dangerMode: true,
        }).then(function(result) {
          if (result) {
            form.submit();
          }
        });
      });
    });
  </script>
@endpush
