<!DOCTYPE html>
<html lang="th">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Admin - @yield('title', 'Dashboard')</title>
    <link rel="stylesheet" href="{{ asset('css/style.css') }}">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css">
    @stack('styles')
</head>
<body>
    <div class="admin-layout">
        <aside class="sidebar">
            <div class="sidebar-header">
                <img src="{{ asset('images/logo.png') }}" alt="Logo" style="height: 50px; margin-bottom: 10px; border-radius: 8px;">
                <h2>ข่าวสารสำนักทะเบียน</h2>
                <p>ระบบจัดการหลังบ้าน</p>
            </div>
            <nav class="sidebar-menu">
                <a href="{{ route('admin.dashboard') }}" class="{{ request()->routeIs('admin.dashboard') ? 'active' : '' }}"><i class="fas fa-home"></i> Dashboard</a>
                <a href="{{ route('admin.posts.index') }}" class="{{ request()->routeIs('admin.posts.*') ? 'active' : '' }}"><i class="fas fa-newspaper"></i> บทความ</a>
                <a href="{{ route('admin.categories.index') }}" class="{{ request()->routeIs('admin.categories.*') ? 'active' : '' }}"><i class="fas fa-tags"></i> หมวดหมู่</a>
                <a href="{{ route('admin.users.index') }}" class="{{ request()->routeIs('admin.users.*') ? 'active' : '' }}"><i class="fas fa-users"></i> ผู้ใช้งาน</a>
                <a href="{{ route('home') }}" target="_blank"><i class="fas fa-eye"></i> ดูเว็บไซต์</a>
                <form method="POST" action="{{ route('logout') }}">
                    @csrf
                    <a href="#" onclick="event.preventDefault(); this.closest('form').submit();">
                        <i class="fas fa-sign-out-alt"></i> ออกจากระบบ
                    </a>
                </form>
            </nav>
        </aside>
        
        <main class="main-admin">
            <header class="admin-header">
                <h1>@yield('header')</h1>
                <div>
                    <span style="color: var(--primary);">
                        <i class="fas fa-user-circle"></i> {{ Auth::user()->display_name }}
                    </span>
                </div>
            </header>
            
            <div class="admin-content">
                @if(session('success'))
                    <div class="alert alert-success">
                        <i class="fas fa-check-circle"></i> {{ session('success') }}
                    </div>
                @endif

                @if(session('error'))
                    <div class="alert alert-danger">
                        <i class="fas fa-exclamation-circle"></i> {{ session('error') }}
                    </div>
                @endif

                @yield('content')
            </div>
        </main>
    </div>

    @stack('scripts')
</body>
</html>
