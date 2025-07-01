@extends('layouts.admin')

@section('content')
  <div class="main-content-inner">
    <div class="main-content-wrap">
      <div class="flex items-center justify-between gap20 mb-27">
        <h3>View Post</h3>
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
            <div class="text-tiny">View</div>
          </li>
        </ul>
      </div>

      <div class="wg-box p-4">
        <h2 class="mb-3">{{ $blog->title }}</h2>
        @if ($blog->image)
          <img src="{{ asset('storage/' . $blog->image) }}" class="img-fluid mb-4 rounded" alt="{{ $blog->title }}">
        @endif
        <p class="text-muted mb-2">
          Published:
          {{ $blog->published_at ? $blog->published_at->format('d M Y, H:i') : 'Draft' }}
        </p>
        @if ($blog->excerpt)
          <p class="fst-italic mb-4">{{ $blog->excerpt }}</p>
        @endif
        <div>{!! nl2br(e($blog->body)) !!}</div>
        <a href="{{ route('blog.index') }}" class="tf-button style-1 w208 mt-4">
          <i class="icon-arrow-left"></i> Back
        </a>
      </div>
    </div>
  </div>
@endsection
