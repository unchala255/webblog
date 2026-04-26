@extends('layouts.main')

@section('content')
    <section class="hero">
        <h1><i class="fas fa-newspaper"></i> ข่าวสารสำนักทะเบียน</h1>
        <p>ติดตามข่าวสารและกิจกรรมล่าสุดของสำนักทะเบียน</p>
    </section>

    <section class="categories-section">
        <h2 class="section-title"><i class="fas fa-tags"></i> หมวดหมู่</h2>
        <div class="categories-grid">
            <a href="{{ route('home') }}" class="category-tag {{ !$category_id ? 'active' : '' }}">
                <i class="fas fa-globe"></i> ทั้งหมด
            </a>
            @foreach($categories as $cat)
                <a href="{{ route('home', ['category' => $cat->id]) }}" 
                   class="category-tag {{ $category_id == $cat->id ? 'active' : '' }}">
                    {{ $cat->name }}
                </a>
            @endforeach
        </div>
    </section>

    <form method="GET" action="{{ route('home') }}" class="search-box">
        @if($category_id)
            <input type="hidden" name="category" value="{{ $category_id }}">
        @endif
        <input type="text" name="search" placeholder="ค้นหาบทความ..." value="{{ $search }}">
        <button type="submit"><i class="fas fa-search"></i> ค้นหา</button>
    </form>

    @if($search)
        <div class="alert alert-info">
            <i class="fas fa-search"></i> ผลการค้นหา: "{{ $search }}" ({{ $posts->total() }} รายการ)
        </div>
    @endif

    <section class="posts-grid">
        @forelse($posts as $post)
            <article class="post-card">
                <div class="post-image">
                    @if($post->featured_image)
                        <img src="{{ asset($post->featured_image) }}" alt="{{ $post->title }}">
                    @else
                        <i class="fas fa-newspaper"></i>
                    @endif
                    <span class="category-badge">{{ $post->category->name ?? 'ไม่มีหมวดหมู่' }}</span>
                </div>
                <div class="post-content">
                    <h3 class="post-title">
                        <a href="{{ route('post.show', $post->id) }}">
                            {{ $post->title }}
                        </a>
                    </h3>
                    <p class="post-excerpt">{{ $post->excerpt }}</p>
                    <div class="post-meta">
                        <div class="author">
                            <i class="fas fa-user"></i> {{ $post->author->display_name ?? 'ไม่ระบุ' }}
                        </div>
                        <div class="date">
                            <i class="fas fa-calendar"></i> 
                            {{ $post->published_at ? $post->published_at->format('d/m/Y') : $post->created_at->format('d/m/Y') }}
                        </div>
                    </div>
                </div>
            </article>
        @empty
            <div class="empty-state" style="grid-column: 1/-1;">
                <i class="fas fa-folder-open"></i>
                <h3>ไม่พบบทความ</h3>
                <p>ลองค้นหาด้วยคำอื่นหรือเลือกหมวดหมู่อื่น</p>
            </div>
        @endforelse
    </section>

    <div class="pagination">
        {{ $posts->links() }}
    </div>
@endsection
