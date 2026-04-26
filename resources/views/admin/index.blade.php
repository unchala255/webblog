@extends('admin.layouts.app')

@section('title', 'Dashboard')
@section('header', 'Dashboard')

@section('content')
    <div class="stats-grid">
        <div class="stat-card">
            <div class="stat-icon blue"><i class="fas fa-newspaper"></i></div>
            <div class="stat-info">
                <h4>บทความทั้งหมด</h4>
                <div class="number">{{ $stats['posts'] }}</div>
            </div>
        </div>
        <div class="stat-card">
            <div class="stat-icon green"><i class="fas fa-check-circle"></i></div>
            <div class="stat-info">
                <h4>เผยแพร่แล้ว</h4>
                <div class="number">{{ $stats['published'] }}</div>
            </div>
        </div>
        <div class="stat-card">
            <div class="stat-icon orange"><i class="fas fa-edit"></i></div>
            <div class="stat-info">
                <h4>ฉบับร่าง</h4>
                <div class="number">{{ $stats['draft'] }}</div>
            </div>
        </div>
        <div class="stat-card">
            <div class="stat-icon red"><i class="fas fa-eye"></i></div>
            <div class="stat-info">
                <h4>ยอดเข้าชม</h4>
                <div class="number">{{ number_format($stats['views']) }}</div>
            </div>
        </div>
    </div>
    
    <div class="admin-card">
        <h3><i class="fas fa-clock"></i> บทความล่าสุด</h3>
        <div class="table-container">
            <table>
                <thead>
                    <tr>
                        <th>หัวข้อ</th>
                        <th>ผู้เขียน</th>
                        <th>สถานะ</th>
                        <th>วันที่</th>
                        <th>จำนวนเข้าชม</th>
                    </tr>
                </thead>
                <tbody>
                    @foreach($recent_posts as $post)
                    <tr>
                        <td>{{ $post->title }}</td>
                        <td>{{ $post->author->display_name ?? 'ไม่ระบุ' }}</td>
                        <td>
                            <span class="status-badge {{ $post->status }}">
                                {{ $post->status }}
                            </span>
                        </td>
                        <td>{{ $post->created_at->format('d/m/Y') }}</td>
                        <td>{{ $post->view_count }}</td>
                    </tr>
                    @endforeach
                </tbody>
            </table>
        </div>
    </div>
    
    <div style="display: grid; grid-template-columns: repeat(auto-fit, minmax(250px, 1fr)); gap: 20px;">
        <div class="admin-card">
            <h3><i class="fas fa-tags"></i> หมวดหมู่</h3>
            <p style="font-size: 2rem; color: var(--primary);">{{ $stats['categories'] }}</p>
            <a href="{{ route('admin.categories.index') }}" class="btn btn-sm btn-primary" style="margin-top: 10px;">จัดการหมวดหมู่</a>
        </div>
        <div class="admin-card">
            <h3><i class="fas fa-users"></i> ผู้ใช้งาน</h3>
            <p style="font-size: 2rem; color: var(--primary);">{{ $stats['users'] }}</p>
            <a href="{{ route('admin.users.index') }}" class="btn btn-sm btn-primary" style="margin-top: 10px;">จัดการผู้ใช้</a>
        </div>
    </div>
@endsection
