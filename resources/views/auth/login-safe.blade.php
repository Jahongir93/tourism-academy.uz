@extends('layouts.app')

@section('content')
<div class="container">
    <div class="row justify-content-center">
        <div class="col-md-5">
            @if(isset($database_offline) && $database_offline)
            <div class="alert alert-warning">
                <strong>Diqqat!</strong> Ma'lumotlar bazasi mavjud emas.
                @if(config('database_fallback.demo_mode'))
                    <br>Demo rejimda kirish uchun: demo@example.com / password
                @endif
            </div>
            @endif

            <div class="card">
                <div class="card-header">{{ __('Kirish') }}</div>

                <div class="card-body">
                    <form method="POST" action="{{ route('login') }}" id="loginForm">
                        @csrf
                        <input type="hidden" name="_token" value="{{ csrf_token() }}" id="csrf_token">

                        <div class="mb-3">
                            <label for="email" class="form-label">{{ __('Email') }}</label>
                            <input id="email" type="email" class="form-control @error('email') is-invalid @enderror"
                                   name="email" value="{{ old('email') }}" required autocomplete="email" autofocus>
                            @error('email')
                                <span class="invalid-feedback" role="alert">
                                    <strong>{{ $message }}</strong>
                                </span>
                            @enderror
                        </div>

                        <div class="mb-3">
                            <label for="password" class="form-label">{{ __('Parol') }}</label>
                            <input id="password" type="password" class="form-control @error('password') is-invalid @enderror"
                                   name="password" required autocomplete="current-password">
                            @error('password')
                                <span class="invalid-feedback" role="alert">
                                    <strong>{{ $message }}</strong>
                                </span>
                            @enderror
                        </div>

                        <div class="mb-3">
                            <div class="form-check">
                                <input class="form-check-input" type="checkbox" name="remember" id="remember"
                                       {{ old('remember') ? 'checked' : '' }}>
                                <label class="form-check-label" for="remember">
                                    {{ __('Meni eslab qol') }}
                                </label>
                            </div>
                        </div>

                        <div class="mb-0">
                            <button type="submit" class="btn btn-primary w-100">
                                {{ __('Kirish') }}
                            </button>

                            @if (Route::has('password.request'))
                                <a class="btn btn-link" href="{{ route('password.request') }}">
                                    {{ __('Parolni unutdingizmi?') }}
                                </a>
                            @endif
                        </div>
                    </form>
                </div>
            </div>
        </div>
    </div>
</div>

<script>
document.addEventListener('DOMContentLoaded', function() {
    // CSRF tokenni avtomatik yangilash
    setInterval(function() {
        fetch('/database/status')
            .then(response => response.json())
            .then(data => {
                // Token yangilash kerak bo'lsa
                if (data.new_token) {
                    document.getElementById('csrf_token').value = data.new_token;
                    document.querySelector('meta[name="csrf-token"]').setAttribute('content', data.new_token);
                }
            })
            .catch(error => console.log('Token update error:', error));
    }, 600000); // Har 10 daqiqada

    // Form yuborishda xatolarni tutish
    document.getElementById('loginForm').addEventListener('submit', function(e) {
        // Tokenni tekshirish
        const token = document.querySelector('meta[name="csrf-token"]');
        if (token) {
            document.getElementById('csrf_token').value = token.getAttribute('content');
        }
    });
});
</script>
@endsection