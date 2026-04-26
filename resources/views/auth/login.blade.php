<x-guest-layout>
    <h2><i class="fas fa-lock"></i> เข้าสู่ระบบ</h2>

    @if (session('status'))
        <div class="alert alert-success" style="margin-bottom: 20px;">
            {{ session('status') }}
        </div>
    @endif

    @if ($errors->any())
        <div class="alert alert-danger" style="margin-bottom: 20px; text-align: left;">
            <ul style="margin: 0; padding-left: 20px;">
                @foreach ($errors->all() as $error)
                    <li>{{ $error }}</li>
                @endforeach
            </ul>
        </div>
    @endif

    <form method="POST" action="{{ route('login') }}" style="text-align: left;">
        @csrf

        <div class="form-group">
            <label for="email">อีเมล</label>
            <input id="email" type="email" name="email" value="{{ old('email') }}" required autofocus style="width: 100%; padding: 10px; border: 1px solid #ddd; border-radius: 5px;">
        </div>

        <div class="form-group" style="margin-top: 20px;">
            <label for="password">รหัสผ่าน</label>
            <input id="password" type="password" name="password" required autocomplete="current-password" style="width: 100%; padding: 10px; border: 1px solid #ddd; border-radius: 5px;">
        </div>

        <div style="margin-top: 20px; display: flex; align-items: center; gap: 8px;">
            <input id="remember_me" type="checkbox" name="remember">
            <label for="remember_me" style="margin: 0; font-size: 0.9rem; color: #666;">จดจำฉันไว้</label>
        </div>

        <div style="margin-top: 30px; display: flex; flex-direction: column; gap: 15px;">
            <button type="submit" class="btn btn-primary" style="width: 100%; padding: 12px; font-size: 1.1rem;">
                เข้าสู่ระบบ
            </button>
            
            @if (Route::has('password.request'))
                <a href="{{ route('password.request') }}" style="text-align: center; font-size: 0.85rem; color: #666; text-decoration: none;">
                    ลืมรหัสผ่านใช่หรือไม่?
                </a>
            @endif
        </div>
    </form>
</x-guest-layout>
