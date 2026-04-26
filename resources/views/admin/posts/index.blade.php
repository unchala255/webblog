@extends('admin.layouts.app')

@section('title', 'จัดการบทความ')
@section('header', 'จัดการบทความ')

@section('content')
    <div class="admin-card">
        <div style="display: flex; justify-content: space-between; align-items: center; margin-bottom: 20px;">
            <h3><i class="fas fa-list"></i> รายการบทความ</h3>
            <a href="{{ route('admin.posts.create') }}" class="btn btn-primary"><i class="fas fa-plus-circle"></i> เพิ่มบทความใหม่</a>
        </div>
        
        <div class="table-container">
            <table>
                <thead>
                    <tr>
                        <th>ID</th>
                        <th>หัวข้อ</th>
                        <th>หมวดหมู่</th>
                        <th>ผู้เขียน</th>
                        <th>สถานะ</th>
                        <th>เข้าชม</th>
                        <th>วันที่</th>
                        <th>จัดการ</th>
                    </tr>
                </thead>
                <tbody>
                    @foreach($posts as $post)
                    <tr>
                        <td>{{ $post->id }}</td>
                        <td>{{ $post->title }}</td>
                        <td>{{ $post->category->name ?? '-' }}</td>
                        <td>{{ $post->author->display_name ?? '-' }}</td>
                        <td>
                            <span class="status-badge {{ $post->status }}">
                                {{ $post->status }}
                            </span>
                        </td>
                        <td>{{ $post->view_count }}</td>
                        <td>{{ $post->created_at->format('d/m/Y') }}</td>
                        <td class="table-actions">
                            <a href="{{ route('admin.posts.edit', $post->id) }}" class="btn-action btn-edit">
                                <i class="fas fa-edit"></i> แก้ไข
                            </a>
                            <form action="{{ route('admin.posts.destroy', $post->id) }}" method="POST" style="display: inline-block;" onsubmit="return confirm('ต้องการลบบทความนี้หรือไม่?');">
                                @csrf
                                @method('DELETE')
                                <button type="submit" class="btn-action btn-delete" style="border: none; font-family: inherit;">
                                    <i class="fas fa-trash"></i> ลบ
                                </button>
                            </form>
                        </td>
                    </tr>
                    @endforeach
                </tbody>
            </table>
        </div>
        
        <div class="pagination">
            {{ $posts->links() }}
        </div>
    </div>
@endsection
