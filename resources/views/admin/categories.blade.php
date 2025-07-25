@extends('layouts.admin')

@push('styles')
  {{-- <style>
    .input-group .form-control {
      border-right: 0;
    }

    .input-group .btn {
      border-left: 0;
    }

    .input-group .btn i {
      font-size: 1rem;
    }
  </style> --}}
@endpush

@section('content')
  <div class="main-content-inner">
    <div class="main-content-wrap">
      <div class="flex items-center flex-wrap justify-between gap20 mb-27">
        <h3>Categories</h3>
        <ul class="breadcrumbs flex items-center flex-wrap justify-start gap10">
          <li>
            <a href="{{ route('admin.index') }}">
              <div class="text-tiny">Dashboard</div>
            </a>
          </li>
          <li>
            <i class="icon-chevron-right"></i>
          </li>
          <li>
            <div class="text-tiny">Categories</div>
          </li>
        </ul>
      </div>

      <div class="wg-box p-5">
        <div class="flex items-center justify-between gap10 flex-wrap">
          <div class="wg-filter flex-grow">

            <form class="form-search mb-4" method="GET" action="{{ route('admin.categories') }}">
              <fieldset class="name">
                <input type="text" name="name" placeholder="Search category name..." value="{{ request('name') }}"
                  required>
              </fieldset>
              <div class="button-submit">
                <button type="submit"><i class="icon-search"></i></button>
              </div>
            </form>

          </div>
          <a class="tf-button style-1 w208" href="{{ route('admin.category.add') }}"><i class="icon-plus"></i>Add
            new</a>
        </div>


        <div class="wg-table table-all-user">
          @if (Session::has('status'))
            <div class="alert alert-success">
              <p class="alert alert-success">{{ Session::get('status') }}</p>
            </div>
          @endif

          <div class="table-responsive">
            <table class="table table-striped table-bordered">
              <thead>
                <tr>
                  <th>No.</th>
                  <th>Name</th>
                  <th>Slug</th>
                  <th>Action</th>
                </tr>
              </thead>
              <tbody>
                @if ($categories->isEmpty())
                  <tr>
                    <td colspan="4" class="text-center text-dark p-4 fs-4">
                      Tidak ada kategori yang sesuai dengan pencarian “{{ request('name') }}”.
                    </td>
                  </tr>
                @else
                  @foreach ($categories as $category)
                    <tr>
                      <td>{{ $loop->iteration }}</td>
                      <td class="pname">
                        <div class="image">
                          <img src="{{ asset('uploads/categories/' . $category->image) }}" alt="{{ $category->name }}"
                            class="image">
                        </div>
                        <div class="name">
                          <a href="#" class="body-title-2">{{ $category->name }}</a>
                        </div>
                      </td>
                      <td>{{ $category->slug }}</td>
                      <td>
                        <div class="list-icon-function">
                          <a href="{{ route('admin.category.edit', $category->id) }}">
                            <div class="item edit"><i class="icon-edit-3"></i></div>
                          </a>
                          <form action="{{ route('admin.category.delete', $category->id) }}" method="POST"
                            class="d-inline">
                            @csrf @method('DELETE')
                            <button type="submit" class="item text-danger delete" style="border:none; background:none;">
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
          <div class="flex items-center justify-between flex-wrap gap10 wgp-pagination">
            {{ $categories->links('pagination::bootstrap-5') }}
          </div>
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
