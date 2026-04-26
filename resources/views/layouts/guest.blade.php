<!DOCTYPE html>
<html lang="th">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>{{ config('app.name', 'ข่าวสารสำนักทะเบียน') }} - เข้าสู่ระบบ</title>
    <link rel="stylesheet" href="{{ asset('css/style.css') }}">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css">
    <style>
        body {
            background: linear-gradient(135deg, #f5f7fa 0%, #c3cfe2 100%);
            display: flex;
            align-items: center;
            justify-content: center;
            min-height: 100vh;
            margin: 0;
        }
        .login-container {
            background: white;
            padding: 40px;
            border-radius: 15px;
            box-shadow: 0 10px 25px rgba(0,0,0,0.1);
            width: 100%;
            max-width: 400px;
            text-align: center;
        }
        .login-logo img {
            height: 80px;
            margin-bottom: 20px;
            border-radius: 10px;
        }
        .login-container h2 {
            margin-bottom: 30px;
            color: var(--primary);
        }
    </style>
</head>
<body>
    <div class="login-container">
        <div class="login-logo">
            <a href="/">
                <img src="{{ asset('images/logo.png') }}" alt="Logo">
            </a>
        </div>
        {{ $slot }}
    </div>
</body>
</html>
