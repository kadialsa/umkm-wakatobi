@extends('layouts.app')

@section('content')
  <style>
    .article-card {
      border: 1px solid #eee;
      border-radius: .25rem;
      overflow: hidden;
      transition: box-shadow .2s;
      display: flex;
      flex-direction: column;
      height: 100%;
    }

    .article-card:hover {
      box-shadow: 0 2px 8px rgba(0, 0, 0, 0.1);
    }

    .article-card__img {
      width: 100%;
      height: 180px;
      object-fit: cover;
      display: block;
    }

    .article-card__body {
      padding: 1rem;
      flex-grow: 1;
      display: flex;
      flex-direction: column;
    }

    .article-card__title {
      font-size: 1.25rem;
      margin-bottom: .5rem;
      flex-grow: 0;
    }

    .article-card__excerpt {
      flex-grow: 1;
      margin-bottom: 1rem;
      color: #555;
    }

    .article-card__footer {
      display: flex;
      justify-content: space-between;
      align-items: center;
      padding: 0 1rem 1rem;
    }

    .article-card__date {
      font-size: .875rem;
      color: #777;
    }
  </style>

  <main class="pt-90">
    <section class="container pt-90">

      {{-- Daftar Artikel --}}
      <div class="row g-4">
        @foreach ($articles as $article)
          <div class="col-12 col-md-6 col-lg-4">
            <div class="article-card">
              {{-- Gambar --}}
              @if ($article->image)
                <img src="{{ asset('storage/' . $article->image) }}" alt="{{ $article->title }}" class="article-card__img">
              @else
                <img src="https://via.placeholder.com/400x180?text=No+Image" alt="No image" class="article-card__img">
              @endif

              {{-- Konten --}}
              <div class="article-card__body">
                <h3 class="article-card__title">
                  <a href="{{ route('blog.show', $article->slug) }}">
                    {{ \Illuminate\Support\Str::limit($article->title, 60) }}
                  </a>
                </h3>
                <p class="article-card__excerpt">
                  {{ \Illuminate\Support\Str::limit($article->excerpt ?? strip_tags($article->body), 120) }}
                </p>
              </div>

              {{-- Footer --}}
              <div class="article-card__footer">
                <span class="article-card__date">
                  {{ $article->published_at ? $article->published_at->format('d M Y') : $article->created_at->format('d M Y') }}
                </span>
                <a href="{{ route('home.articles.show', $article->slug) }}" class="btn btn-sm btn-outline-primary">
                  Read More
                </a>
              </div>
            </div>
          </div>
        @endforeach
      </div>

      {{-- Pagination --}}
      <div class="mt-5 d-flex justify-content-center">
        {{ $articles->links('pagination::bootstrap-5') }}
      </div>
    </section>
  </main>
@endsection
