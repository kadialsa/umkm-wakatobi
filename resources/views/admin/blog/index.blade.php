@extends('layouts.admin')

@section('content')
  <div class="main-content-inner">
    <div class="main-content-wrap">
      <div class="flex items-center flex-wrap justify-between gap20 mb-27">
        <h3>All Blog Posts</h3>
        <ul class="breadcrumbs flex items-center flex-wrap justify-start gap10">
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
          <div class="wg-filter flex-grow">
            <form class="form-search" method="GET" action="{{ route('blog.index') }}">
              <fieldset class="name">
                <input type="text" name="search" placeholder="Search title..." class=""
                  value="{{ request('search') }}" required>
              </fieldset>
              <div class="button-submit">
                <button type="submit"><i class="icon-search"></i></button>
              </div>
            </form>
          </div>
          <a class="tf-button style-1 w208" href="{{ route('blog.create') }}">
            <i class="icon-plus"></i> Add New
          </a>
        </div>

        <div class="table-responsive mt-3">
          @if (Session::has('status'))
            <div class="alert alert-success">{{ Session::get('status') }}</div>
          @endif
          <table class="table table-striped table-bordered">
            <thead>
              <tr>
                <th>#</th>
                <th>Image</th>
                <th>Title</th>
                <th>Published</th>
                <th>Action</th>
              </tr>
            </thead>
            <tbody>
              @foreach ($posts as $post)
                <tr>
                  <td>{{ $post->id }}</td>
                  <td style="width:100px">
                    @if ($post->image)
                      <img src="{{ asset('storage/' . $post->image) }}" alt="" class="img-fluid rounded"
                        style="max-height:60px;">
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
                    <div class="list-icon-function">
                      <a href="{{ route('blog.show', $post) }}">
                        <div class="item edit">
                          <i class="icon-eye"></i>
                        </div>
                      </a>
                      <a href="{{ route('blog.edit', $post) }}">
                        <div class="item edit">
                          <i class="icon-edit-3"></i>
                        </div>
                      </a>
                      <form action="{{ route('blog.destroy', $post) }}" method="POST" class="d-inline">
                        @csrf @method('DELETE')
                        <div class="item text-danger delete" style="border:none;background:none;color:red;">
                          <i class="icon-trash-2"></i>
                        </div>
                      </form>
                    </div>
                  </td>
                </tr>
              @endforeach
            </tbody>
          </table>
        </div>

        <div class="divider"></div>
        <div class="flex items-center justify-between flex-wrap gap10 wgp-pagination">
          {{ $posts->links('pagination::bootstrap-5') }}
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
        let form = $(this).closest('form');
        swal({
          title: "Are you sure?",
          text: "This will delete the post permanently.",
          icon: "warning",
          buttons: ["No", "Yes"],
          dangerMode: true,
        }).then(res => res && form.submit());
      });
    });
  </script>
@endpush
