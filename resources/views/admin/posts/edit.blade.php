@extends('admin.layouts.app')

@section('title', 'แก้ไขบทความ')
@section('header', 'แก้ไขบทความ')

@section('content')
    <div class="admin-card">
        <form method="POST" action="{{ route('admin.posts.update', $post->id) }}">
            @csrf
            @method('PUT')
            <div class="form-row">
                <div class="form-group">
                    <label>หัวข้อ</label>
                    <input type="text" name="title" value="{{ old('title', $post->title) }}" required>
                    @error('title') <span style="color: red;">{{ $message }}</span> @enderror
                </div>
                <div class="form-group">
                    <label>Slug</label>
                    <input type="text" name="slug" value="{{ old('slug', $post->slug) }}">
                    @error('slug') <span style="color: red;">{{ $message }}</span> @enderror
                </div>
            </div>
            <div class="form-group">
                <label>ย่อเนื้อหา</label>
                <textarea name="excerpt" rows="2">{{ old('excerpt', $post->excerpt) }}</textarea>
            </div>
            <div class="form-group">
                <label>เนื้อหา</label>
                <textarea name="content" rows="10">{{ old('content', $post->content) }}</textarea>
                @error('content') <span style="color: red;">{{ $message }}</span> @enderror
            </div>
            <div class="form-row">
                <div class="form-group">
                    <label>หมวดหมู่</label>
                    <select name="category_id">
                        <option value="">-- เลือกหมวดหมู่ --</option>
                        @foreach($categories as $cat)
                            <option value="{{ $cat->id }}" {{ old('category_id', $post->category_id) == $cat->id ? 'selected' : '' }}>{{ $cat->name }}</option>
                        @endforeach
                    </select>
                </div>
                <div class="form-group">
                    <label>สถานะ</label>
                    <select name="status">
                        <option value="draft" {{ old('status', $post->status) == 'draft' ? 'selected' : '' }}>ฉบับร่าง</option>
                        <option value="published" {{ old('status', $post->status) == 'published' ? 'selected' : '' }}>เผยแพร่</option>
                        <option value="archived" {{ old('status', $post->status) == 'archived' ? 'selected' : '' }}>เก็บถาวร</option>
                    </select>
                </div>
            </div>
            <div class="form-group">
                <label>รูปภาพ (Path)</label>
                <input type="text" name="featured_image" value="{{ old('featured_image', $post->featured_image) }}">
            </div>
            <div class="form-actions">
                <button type="submit" class="btn btn-primary"><i class="fas fa-save"></i> บันทึกการแก้ไข</button>
                <a href="{{ route('admin.posts.index') }}" class="btn btn-secondary">ยกเลิก</a>
            </div>
        </form>
    </div>
@endsection
