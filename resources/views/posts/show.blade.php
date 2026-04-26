@extends('layouts.main')

@section('title', $post->title . ' - ข่าวสารสำนักทะเบียน')

@section('content')
    <a href="{{ route('home') }}" class="back-link" style="color: var(--primary); margin-bottom: 20px; display: inline-flex; align-items: center; gap: 8px;">
        <i class="fas fa-arrow-left"></i> กลับไปหน้าหลัก
    </a>
    
    <div class="post-detail">
        <div class="post-detail-header">
            <span class="status-badge published">{{ $post->category->name ?? 'ไม่มีหมวดหมู่' }}</span>
            <h1>{{ $post->title }}</h1>
            <p>
                <i class="fas fa-user"></i> {{ $post->author->display_name ?? 'ไม่ระบุ' }} | 
                <i class="fas fa-calendar"></i> {{ $post->published_at ? $post->published_at->format('d M Y') : $post->created_at->format('d M Y') }} | 
                <i class="fas fa-eye"></i> {{ $post->view_count }} ครั้ง
            </p>
        </div>
        <div class="post-detail-body">
            @if($post->featured_image)
                <img src="{{ asset($post->featured_image) }}" alt="{{ $post->title }}">
            @endif
            
            {!! $post->content !!}
        </div>
    </div>
@endsection
