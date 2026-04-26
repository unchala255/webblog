@extends('admin.layouts.app')

@section('title', 'เพิ่มบทความใหม่')
@section('header', 'เพิ่มบทความใหม่')

@section('content')
    <div class="admin-card">
        <form method="POST" action="{{ route('admin.posts.store') }}">
            @csrf
            <div class="form-row">
                <div class="form-group">
                    <label>หัวข้อ</label>
                    <input type="text" name="title" value="{{ old('title') }}" required placeholder="กรอกหัวข้อบทความ">
                    @error('title') <span style="color: red;">{{ $message }}</span> @enderror
                </div>
                <div class="form-group">
                    <label>Slug</label>
                    <input type="text" name="slug" value="{{ old('slug') }}" placeholder="url-friendly-slug (ว่างไว้เพื่อสร้างอัตโนมัติ)">
                    @error('slug') <span style="color: red;">{{ $message }}</span> @enderror
                </div>
            </div>
            <div class="form-group">
                <label>ย่อเนื้อหา</label>
                <textarea name="excerpt" rows="2" placeholder="สรุปเนื้อหาสั้นๆ">{{ old('excerpt') }}</textarea>
            </div>
            <div class="form-group">
                <label>เนื้อหา</label>
                <textarea name="content" rows="10" placeholder="เนื้อหาบทความ">{{ old('content') }}</textarea>
                @error('content') <span style="color: red;">{{ $message }}</span> @enderror
            </div>
            <div class="form-row">
                <div class="form-group">
                    <label>หมวดหมู่</label>
                    <select name="category_id">
                        <option value="">-- เลือกหมวดหมู่ --</option>
                        @foreach($categories as $cat)
                            <option value="{{ $cat->id }}" {{ old('category_id') == $cat->id ? 'selected' : '' }}>{{ $cat->name }}</option>
                        @endforeach
                    </select>
                </div>
                <div class="form-group">
                    <label>สถานะ</label>
                    <select name="status">
                        <option value="draft" {{ old('status') == 'draft' ? 'selected' : '' }}>ฉบับร่าง</option>
                        <option value="published" {{ old('status') == 'published' ? 'selected' : '' }}>เผยแพร่</option>
                        <option value="archived" {{ old('status') == 'archived' ? 'selected' : '' }}>เก็บถาวร</option>
                    </select>
                </div>
            </div>
            <div class="form-group">
                <label>รูปภาพ (Path)</label>
                <input type="text" name="featured_image" value="{{ old('featured_image') }}" placeholder="images/example.jpg">
            </div>
            <div class="form-actions">
                <button type="submit" class="btn btn-primary"><i class="fas fa-plus"></i> เพิ่มบทความ</button>
                <a href="{{ route('admin.posts.index') }}" class="btn btn-secondary">ยกเลิก</a>
            </div>
        </form>
    </div>
@endsection
