@extends('layouts.admin')

@push('styles')
  {{--
  <style>
    .input-group .form-control {
      border-right: 0;
    }
    .input-group .btn {
      border-left: 0;
    }
    .input-group .btn i {
      font-size: 1rem;
    }
  </style>
  --}}
@endpush

@section('content')
  <div class="main-content-inner">
    <div class="main-content-wrap">

      {{-- Header & Breadcrumbs --}}
      <div class="flex items-center flex-wrap justify-between gap20 mb-27">
        <h3>Users</h3>
        <ul class="breadcrumbs flex items-center flex-wrap justify-start gap10">
          <li>
            <a href="{{ route('admin.index') }}">
              <div class="text-tiny">Dashboard</div>
            </a>
          </li>
          <li><i class="icon-chevron-right"></i></li>
          <li>
            <div class="text-tiny">Users</div>
          </li>
        </ul>
      </div>

      <div class="wg-box p-5">
        {{-- Search & Add New --}}
        <div class="flex items-center justify-between gap10 flex-wrap">
          <div class="wg-filter flex-grow">
            <form class="form-search mb-4" method="GET" action="{{ route('admin.users.index') }}">
              <fieldset class="name">
                <input type="text" name="search" placeholder="Search user name..." value="{{ request('search') }}"
                  required>
              </fieldset>
              <div class="button-submit">
                <button type="submit"><i class="icon-search"></i></button>
              </div>
            </form>
          </div>
          <a class="tf-button style-1 w208" href="{{ route('admin.users.create') }}">
            <i class="icon-plus"></i> Add new
          </a>
        </div>

        <div class="wg-table table-all-user">
          @if (session('status'))
            <div class="alert alert-success">
              {{ session('status') }}
            </div>
          @endif

          <div class="table-responsive">
            <table class="table table-striped table-bordered">
              <thead>
                <tr>
                  <th>No.</th>
                  <th>Name</th>
                  <th>Email</th>
                  <th>Role</th>
                  <th>Phone</th>
                  <th>Action</th>
                </tr>
              </thead>
              <tbody>
                @if ($users->isEmpty())
                  <tr>
                    <td colspan="4" class="text-center text-dark p-4 fs-4">
                      Tidak ada user yang sesuai dengan pencarian “{{ request('search') }}”.
                    </td>
                  </tr>
                @else
                  @foreach ($users as $user)
                    <tr>
                      <td>{{ $loop->iteration + ($users->currentPage() - 1) * $users->perPage() }}</td>
                      <td>{{ $user->name }}</td>
                      <td>{{ $user->email }}</td>
                      <td>
                        @switch($user->utype)
                          @case('ADM')
                            Administrator
                          @break

                          @case('STR')
                            Pemilik Toko
                          @break

                          @case('USR')
                            Pelanggan
                          @break

                          @default
                            —
                        @endswitch
                      </td>

                      <td>{{ $user->mobile ?? '-' }}</td>
                      <td>
                        <div class="list-icon-function">
                          <a href="{{ route('admin.users.edit', $user->id) }}">
                            <div class="item edit"><i class="icon-edit-3"></i></div>
                          </a>
                          <form action="{{ route('admin.users.destroy', $user->id) }}" method="POST" class="d-inline">
                            @csrf
                            @method('DELETE')
                            <button type="button" class="item text-danger delete" style="border:none; background:none;">
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
            {{ $users->links('pagination::bootstrap-5') }}
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
        const form = $(this).closest('form');
        swal({
          title: "Are you sure?",
          text: "This will delete the user permanently.",
          icon: "warning",
          buttons: ["No", "Yes"],
          dangerMode: true,
        }).then(confirmed => confirmed && form.submit());
      });
    });
  </script>
@endpush
