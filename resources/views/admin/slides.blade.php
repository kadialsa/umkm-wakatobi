@extends('layouts.admin')

@section('content')
  <div class="main-content-inner">
    <div class="main-content-wrap">

      {{-- Header & Breadcrumbs --}}
      <div class="flex items-center flex-wrap justify-between gap20 mb-27">
        <h3>Sliders</h3>
        <ul class="breadcrumbs flex items-center flex-wrap justify-start gap10">
          <li><a href="{{ route('admin.index') }}">
              <div class="text-tiny">Dashboard</div>
            </a></li>
          <li><i class="icon-chevron-right"></i></li>
          <li>
            <div class="text-tiny">Sliders</div>
          </li>
        </ul>
      </div>

      <div class="wg-box">
        {{-- Search & Add New --}}
        <div class="flex items-center justify-between gap10 flex-wrap mb-4">

          <form class="form-search mb-4" method="GET" action="{{ route('admin.slides') }}">
            <fieldset class="name">
              <input type="text" name="search" placeholder="Cari slide..." value="{{ request('search') }}"
                class="form-control">
            </fieldset>
            <div class="button-submit">
              <button type="submit">
                <i class="icon-search"></i>
              </button>
            </div>
          </form>

          <a class="tf-button style-1 w208" href="{{ route('admin.slide.add') }}">
            <i class="icon-plus"></i> Add New
          </a>
        </div>

        {{-- Flash message --}}
        @if (session('status'))
          <div class="alert alert-success">{{ session('status') }}</div>
        @endif

        {{-- Table --}}
        <div class="table-responsive">
          <table class="table table-striped table-bordered mb-0">
            <thead>
              <tr>
                <th>No.</th>
                <th>Gambar</th>
                <th>Tagline</th>
                <th>Judul</th>
                <th>Subjudul</th>
                <th>Link</th>
                <th>Aksi</th>
              </tr>
            </thead>
            <tbody>
              @if ($slides->isEmpty())
                <tr>
                  <td colspan="7" class="text-center text-muted py-4">
                    Tidak ada slide yang sesuai dengan pencarian “<strong>{{ request('search') }}</strong>”.
                  </td>
                </tr>
              @else
                @foreach ($slides as $slide)
                  <tr>
                    <td>{{ $loop->iteration }}</td>
                    <td>
                      <img src="{{ asset('uploads/slides/' . $slide->image) }}" alt="{{ $slide->title }}"
                        style="height:50px; object-fit:cover;">
                    </td>
                    <td>{{ $slide->tagline }}</td>
                    <td>{{ $slide->title }}</td>
                    <td>{{ $slide->subtitle }}</td>
                    <td>
                      <a href="{{ $slide->link }}" target="_blank">{{ Str::limit($slide->link, 30) }}</a>
                    </td>
                    <td>
                      <div class="list-icon-function">
                        <a href="{{ route('admin.slide.edit', $slide->id) }}">
                          <div class="item edit"><i class="icon-edit-3"></i></div>
                        </a>
                        <form action="{{ route('admin.slide.delete', $slide->id) }}" method="POST" class="d-inline">
                          @csrf @method('DELETE')
                          <button class="item text-danger delete" type="button">
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

        {{-- Pagination --}}
        <div class="divider my-3"></div>
        <div class="d-flex justify-content-center">
          {{ $slides->links('pagination::bootstrap-5') }}
        </div>
      </div>
    </div>
  </div>
@endsection

@push('scripts')
  <script>
    // Konfirmasi delete
    document.querySelectorAll('.delete').forEach(btn =>
      btn.addEventListener('click', function(e) {
        e.preventDefault();
        let form = this.closest('form');
        swal({
          title: "Yakin?",
          text: "Slide ini akan dihapus permanen.",
          icon: "warning",
          buttons: ["Batal", "Hapus"],
          dangerMode: true,
        }).then(ok => ok && form.submit());
      })
    );
  </script>
@endpush
