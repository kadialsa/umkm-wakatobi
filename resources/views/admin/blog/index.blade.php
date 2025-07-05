@extends('layouts.admin')

@section('content')
  <div class="main-content-inner">
    <div class="main-content-wrap">

      {{-- Header --}}
      <div class="flex items-center justify-between gap20 mb-27">
        <h3>All Blog Posts</h3>
        <ul class="breadcrumbs flex items-center gap10">
          <li><a href="{{ route('admin.index') }}">
              <div class="text-tiny">Dashboard</div>
            </a></li>
          <li><i class="icon-chevron-right"></i></li>
          <li>
            <div class="text-tiny">Blog</div>
          </li>
        </ul>
      </div>

      <div class="wg-box">
        <div class="flex items-center justify-between gap10 flex-wrap">
          {{-- Search Form --}}
          <form class="form-search" method="GET" action="{{ route('blog.index') }}">
            <fieldset class="name">
              <input type="text" name="search" placeholder="Search title..." value="{{ $search }}" required>
            </fieldset>
            <div class="button-submit">
              <button type="submit"><i class="icon-search"></i></button>
            </div>
          </form>

          {{-- Add New --}}
          <a class="tf-button style-1 w208" href="{{ route('blog.create') }}">
            <i class="icon-plus"></i> Add New
          </a>
        </div>

        {{-- Status Message --}}
        @if (session('status'))
          <div class="alert alert-success mt-3">{{ session('status') }}</div>
        @endif

        {{-- Table --}}
        <div class="table-responsive mt-3">
          <table class="table table-striped table-bordered">
            <thead>
              <tr>
                <th style="width: 50px">#</th>
                <th style="width:100px">Image</th>
                <th>Title</th>
                <th style="width:120px">Published</th>
                <th style="width:120px">Action</th>
              </tr>
            </thead>
            <tbody>
              @if ($posts->isEmpty())
                <tr>
                  <td colspan="5" class="text-center text-dark p-4 fs-4">
                    Tidak ada post ditemukan untuk “{{ $search }}”.
                  </td>
                </tr>
              @else
                @foreach ($posts as $post)
                  <tr>
                    <td>{{ $post->id }}</td>
                    <td>
                      @if ($post->image)
                        <img src="{{ asset('storage/' . $post->image) }}" class="img-fluid rounded"
                          style="max-height:60px;" alt="cover">
                      @else
                        <div class="bg-secondary text-white text-center" style="height:60px;line-height:60px;">
                          No Img
                        </div>
                      @endif
                    </td>
                    <td>{{ $post->title }}</td>
                    <td>
                      {{ $post->published_at ? $post->published_at->format('d M Y') : 'Draft' }}
                    </td>
                    <td>
                      <div class="list-icon-function d-flex gap-2">
                        {{-- View --}}
                        <a href="{{ route('blog.show', $post) }}">
                          <i class="icon-eye text-primary"></i>
                        </a>
                        {{-- Edit --}}
                        <a href="{{ route('blog.edit', $post) }}">
                          <i class="icon-edit-3 text-success"></i>
                        </a>
                        {{-- Delete --}}
                        <form action="{{ route('blog.destroy', $post) }}" method="POST" class="d-inline delete-form">
                          @csrf @method('DELETE')
                          <button type="button" class="border-0 bg-transparent text-danger">
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
        <div class="divider"></div>
        <div class="flex items-center justify-center mt-3">
          {{ $posts->links('pagination::bootstrap-5') }}
        </div>
      </div>

    </div>
  </div>
@endsection

@push('scripts')
  <script>
    // konfirmasi delete
    document.addEventListener('DOMContentLoaded', () => {
      document.querySelectorAll('.delete-form').forEach(form => {
        form.querySelector('button').addEventListener('click', () => {
          if (confirm('Yakin ingin menghapus post ini?')) {
            form.submit();
          }
        });
      });
    });
  </script>
@endpush
