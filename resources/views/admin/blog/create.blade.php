@extends('layouts.admin')

@push('styles')
  <style>
    .form-label {
      font-size: 1.6rem;
      font-weight: 600;
      margin-bottom: 1rem;
    }

    .form-control {
      padding: .75rem;
      font-size: 1.4rem;
      border: 1px solid #686c6c !important;
    }
  </style>
@endpush

@section('content')
  <div class="main-content-inner">
    <div class="main-content-wrap">
      <div class="flex items-center justify-between gap20 mb-27">
        <h3>{{ isset($blog) ? 'Edit Post' : 'Add New Post' }}</h3>
        <ul class="breadcrumbs flex items-center flex-wrap justify-start gap10">
          <li><a href="{{ route('admin.index') }}">
              <div class="text-tiny">Dashboard</div>
            </a></li>
          <li><i class="icon-chevron-right"></i></li>
          <li><a href="{{ route('blog.index') }}">
              <div class="text-tiny">Blog</div>
            </a></li>
          <li><i class="icon-chevron-right"></i></li>
          <li>
            <div class="text-tiny">{{ isset($blog) ? 'Edit' : 'Create' }}</div>
          </li>
        </ul>
      </div>

      <div class="wg-box p-4">
        <form action="{{ isset($blog) ? route('blog.update', $blog) : route('blog.store') }}" method="POST"
          enctype="multipart/form-data">
          @csrf
          @if (isset($blog))
            @method('PUT')
          @endif

          <div class="mb-20">
            <label class="form-label">Title <span class="text-danger">*</span></label>
            <input type="text" name="title" class="form-control @error('title') is-invalid @enderror"
              value="{{ old('title', $blog->title ?? '') }}" required>
            @error('title')
              <div class="invalid-feedback">{{ $message }}</div>
            @enderror
          </div>

          <div class="mb-20">
            <label class="form-label">Image</label>
            @if (isset($blog) && $blog->image)
              <div class="mb-2">
                <img src="{{ asset('storage/' . $blog->image) }}" class="img-thumbnail" style="max-height:150px;">
              </div>
            @endif
            <input type="file" name="image" class="form-control @error('image') is-invalid @enderror">
            @error('image')
              <div class="invalid-feedback">{{ $message }}</div>
            @enderror
          </div>

          <div class="mb-20">
            <label class="form-label">Excerpt</label>
            <textarea name="excerpt" rows="3" class="form-control @error('excerpt') is-invalid @enderror">{{ old('excerpt', $blog->excerpt ?? '') }}</textarea>
            @error('excerpt')
              <div class="invalid-feedback">{{ $message }}</div>
            @enderror
          </div>

          <div class="mb-20">
            <label class="form-label">Body <span class="text-danger">*</span></label>
            <textarea name="body" rows="6" class="form-control @error('body') is-invalid @enderror" required>{{ old('body', $blog->body ?? '') }}</textarea>
            @error('body')
              <div class="invalid-feedback">{{ $message }}</div>
            @enderror
          </div>

          <div class="mb-20">
            <label class="form-label">Publish At</label>
            <input type="datetime-local" name="published_at"
              class="form-control @error('published_at') is-invalid @enderror"
              value="{{ old('published_at', isset($blog->published_at) ? $blog->published_at->format('Y-m-d\TH:i') : '') }}">
            @error('published_at')
              <div class="invalid-feedback">{{ $message }}</div>
            @enderror
          </div>

          <button class="tf-button style-1 w208">
            <i class="icon-save"></i>
            {{ isset($blog) ? 'Update Post' : 'Create Post' }}
          </button>
        </form>
      </div>
    </div>
  </div>
@endsection
