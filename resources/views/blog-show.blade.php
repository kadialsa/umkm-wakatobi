@extends('layouts.app')

@section('content')
  <main class="pt-90">
    <section class="container pt-90">

      {{-- Judul --}}
      <h1 class="mb-3">{{ $article->title }}</h1>

      {{-- Tanggal --}}
      <p class="text-muted">
        {{ $article->published_at ? $article->published_at->format('d M Y') : $article->created_at->format('d M Y') }}
      </p>

      {{-- Gambar --}}
      @if ($article->image)
        <img src="{{ asset('storage/' . $article->image) }}" alt="{{ $article->title }}" class="img-fluid mb-4">
      @endif

      {{-- Konten --}}
      <div class="article-content">
        {!! $article->body !!}
      </div>
    </section>
  </main>
@endsection
