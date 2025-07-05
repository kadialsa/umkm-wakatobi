@extends('layouts.admin')

@section('content')
  <div class="main-content-inner">
    <div class="main-content-wrap">

      <div class="flex items-center justify-between gap20 mb-27">
        <h3>Stores</h3>
        <ul class="breadcrumbs flex items-center gap10">
          <li><a href="{{ route('admin.index') }}">
              <div class="text-tiny">Dashboard</div>
            </a></li>
          <li><i class="icon-chevron-right"></i></li>
          <li>
            <div class="text-tiny">Stores</div>
          </li>
        </ul>
      </div>

      <div class="wg-box p-5">
        <div class="flex items-center justify-between gap10 flex-wrap">
          {{-- search form --}}
          <form class="form-search" method="GET" action="{{ route('admin.stores') }}">
            <fieldset class="name">
              <input type="text" name="name" placeholder="Search store name..." value="{{ $q }}"
                required>
            </fieldset>
            <div class="button-submit">
              <button type="submit"><i class="icon-search"></i></button>
            </div>
          </form>

          <a class="tf-button style-1 w208" href="{{ route('admin.store.add') }}">
            <i class="icon-plus"></i> Add new
          </a>
        </div>

        @if (session('status'))
          <div class="alert alert-success mt-3">
            {{ session('status') }}
          </div>
        @endif

        <div class="wg-table table-all-user mt-3">
          <div class="table-responsive">
            <table class="table table-striped table-bordered">
              <thead>
                <tr>
                  <th>No.</th>
                  <th>Name</th>
                  <th>Slug</th>
                  <th>Description</th>
                  <th>Action</th>
                </tr>
              </thead>
              <tbody>
                @if ($stores->isEmpty())
                  <tr>
                    <td colspan="5" class="text-center text-dark p-4 fs-4">
                      Tidak ada toko ditemukan untuk kata kunci “{{ $q }}”.
                    </td>
                  </tr>
                @else
                  @foreach ($stores as $store)
                    <tr>
                      <td>{{ $loop->iteration + ($stores->currentPage() - 1) * $stores->perPage() }}</td>
                      <td class="pname d-flex align-items-center">
                        <img src="{{ asset('uploads/stores/' . $store->image) }}" alt="{{ $store->name }}" class="me-2"
                          style="width:32px;height:32px;object-fit:cover;border-radius:4px;">
                        <span>{{ $store->name }}</span>
                      </td>
                      <td>{{ $store->slug }}</td>
                      <td>{{ Str::limit($store->description, 50) }}</td>
                      <td>
                        <div class="list-icon-function d-flex gap-2">
                          <a href="{{ route('admin.store.edit', $store->id) }}">
                            <i class="icon-edit-3 text-primary"></i>
                          </a>
                          <form action="{{ route('admin.store.delete', $store->id) }}" method="POST"
                            class="d-inline delete-form">
                            @csrf @method('DELETE')
                            <button type="submit" class="border-0 bg-transparent text-danger">
                              <i class="icon-trash-2"></i>
                            </button>
                          </form>
                        </div>
                      </td>
                    </tr>
                  @endforeach
                @endif
              </tbody>
            </table>
          </div>

          <div class="divider"></div>
          <div class="flex items-center justify-center mt-3">
            {{ $stores->links('pagination::bootstrap-5') }}
          </div>
        </div>
      </div>
    </div>
  </div>
@endsection

@push('scripts')
  <script>
    document.addEventListener('DOMContentLoaded', () => {
      document.querySelectorAll('.delete-form').forEach(form => {
        form.addEventListener('submit', e => {
          e.preventDefault();
          if (confirm('Yakin ingin menghapus toko ini?')) {
            form.submit();
          }
        });
      });
    });
  </script>
@endpush
