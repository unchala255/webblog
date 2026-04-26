<!DOCTYPE html>
<html lang="th">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>@yield('title', 'ข่าวสารสำนักทะเบียน')</title>
    <link rel="stylesheet" href="{{ asset('css/style.css') }}">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css">
    @stack('styles')
</head>
<body>
    <header>
        <div class="container">
            <nav class="navbar">
                <a href="{{ route('home') }}" class="logo">
                    <img src="{{ asset('images/logo.png') }}" alt="Logo" style="height: 40px; border-radius: 5px;">
                    <span>ข่าวสารสำนักทะเบียน</span>
                </a>
                <ul class="nav-links">
                    <li><a href="{{ route('home') }}"><i class="fas fa-home"></i> หน้าหลัก</a></li>
                    @auth
                        <li><a href="{{ route('dashboard') }}"><i class="fas fa-tachometer-alt"></i> Dashboard</a></li>
                        <li>
                            <form method="POST" action="{{ route('logout') }}" style="display: inline;">
                                @csrf
                                <a href="#" onclick="event.preventDefault(); this.closest('form').submit();"><i class="fas fa-sign-out-alt"></i> ออกจากระบบ</a>
                            </form>
                        </li>
                    @else
                        <li><a href="{{ route('login') }}" class="btn-login"><i class="fas fa-sign-in-alt"></i> เข้าสู่ระบบ</a></li>
                    @endauth
                </ul>
            </nav>
        </div>
    </header>

    <main class="main-content">
        <div class="container">
            @yield('content')
        </div>
    </main>

    <footer>
        <div class="container">
            <p>&copy; 2026 ข่าวสารสำนักทะเบียน | พัฒนาโดย สำนักทะเบียน</p>
        </div>
    </footer>

    @stack('scripts')
</body>
</html>
