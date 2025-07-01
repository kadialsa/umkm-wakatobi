@extends('layouts.admin')

@section('content')
  <div class="main-content-inner">
    <div class="main-content-wrap">

      {{-- Page Header --}}
      <div class="flex items-center flex-wrap justify-between gap20 mb-27">
        <h3>Edit Post</h3>
        <ul class="breadcrumbs flex items-center flex-wrap justify-start gap10">
          <li>
            <a href="{{ route('admin.index') }}">
              <div class="text-tiny">Dashboard</div>
            </a>
          </li>
          <li><i class="icon-chevron-right"></i></li>
          <li>
            <a href="{{ route('blog.index') }}">
              <div class="text-tiny">Blog</div>
            </a>
          </li>
          <li><i class="icon-chevron-right"></i></li>
          <li>
            <div class="text-tiny">Edit Post</div>
          </li>
        </ul>
      </div>

      <div class="wg-box p-4">
        <form action="{{ route('blog.update', $blog) }}" method="POST" enctype="multipart/form-data">
          @csrf
          @method('PUT')

          {{-- Title --}}
          <div class="mb-3">
            <label class="form-label">Title <span class="text-danger">*</span></label>
            <input type="text" name="title" class="form-control @error('title') is-invalid @enderror"
              value="{{ old('title', $blog->title) }}" required>
            @error('title')
              <div class="invalid-feedback">{{ $message }}</div>
            @enderror
          </div>

          {{-- Current Header Image --}}
          <div class="mb-3">
            <label class="form-label">Header Image</label>
            @if ($blog->image)
              <div class="mb-2">
                <img src="{{ asset('storage/' . $blog->image) }}" alt="Current Image" class="img-thumbnail"
                  style="max-height:120px;">
              </div>
            @endif
            <input type="file" name="image" class="form-control @error('image') is-invalid @enderror">
            @error('image')
              <div class="invalid-feedback">{{ $message }}</div>
            @enderror
          </div>

          {{-- Excerpt --}}
          <div class="mb-3">
            <label class="form-label">Excerpt</label>
            <textarea name="excerpt" rows="3" class="form-control @error('excerpt') is-invalid @enderror">{{ old('excerpt', $blog->excerpt) }}</textarea>
            @error('excerpt')
              <div class="invalid-feedback">{{ $message }}</div>
            @enderror
          </div>

          {{-- Body --}}
          <div class="mb-3">
            <label class="form-label">Body <span class="text-danger">*</span></label>
            <textarea name="body" rows="6" class="form-control @error('body') is-invalid @enderror" required>{{ old('body', $blog->body) }}</textarea>
            @error('body')
              <div class="invalid-feedback">{{ $message }}</div>
            @enderror
          </div>

          {{-- Published At --}}
          <div class="mb-4">
            <label class="form-label">Publish At</label>
            <input type="datetime-local" name="published_at"
              class="form-control @error('published_at') is-invalid @enderror"
              value="{{ old('published_at', optional($blog->published_at)->format('Y-m-d\TH:i')) }}">
            @error('published_at')
              <div class="invalid-feedback">{{ $message }}</div>
            @enderror
          </div>

          {{-- Buttons --}}
          <div class="flex items-center gap10">
            <button type="submit" class="tf-button style-1 w208">
              <i class="icon-save"></i> Update Post
            </button>
            <a href="{{ route('blog.index') }}" class="tf-button style-1-outline w208">
              <i class="icon-arrow-left"></i> Cancel
            </a>
          </div>
        </form>
      </div>

    </div>
  </div>
@endsection
