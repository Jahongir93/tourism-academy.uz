@php
    $hasGoogle = !empty(config('services.google.client_id'));
    $hasFacebook = !empty(config('services.facebook.client_id'));
    $hasTelegram = !empty(config('services.telegram.bot_username'));
    $anyConfigured = $hasGoogle || $hasFacebook || $hasTelegram;
@endphp

<div style="display:flex; flex-direction:column; gap:0.625rem; margin-bottom:1rem;">
    @if($hasGoogle)
    <a href="{{ route('auth.social.redirect', 'google') }}"
       style="display:flex; align-items:center; justify-content:center; gap:0.5rem; width:100%; padding:0.625rem 1rem; border:1px solid #d1d5db; border-radius:0.5rem; background:#fff; color:#374151; font-weight:500; text-decoration:none; transition:background .2s;"
       onmouseover="this.style.background='#f9fafb'" onmouseout="this.style.background='#fff'">
        <svg width="20" height="20" viewBox="0 0 48 48" style="flex-shrink:0;">
            <path fill="#FFC107" d="M43.6 20.5H42V20H24v8h11.3c-1.6 4.7-6.1 8-11.3 8-6.6 0-12-5.4-12-12s5.4-12 12-12c3.1 0 5.8 1.2 7.9 3l5.6-5.6C33.9 6.1 29.2 4 24 4 13 4 4 13 4 24s9 20 20 20 20-9 20-20c0-1.2-.1-2.3-.4-3.5z"/>
            <path fill="#FF3D00" d="M6.3 14.7l6.6 4.8C14.7 15.1 19 12 24 12c3.1 0 5.8 1.2 7.9 3l5.6-5.6C33.9 6.1 29.2 4 24 4 16.3 4 9.7 8.4 6.3 14.7z"/>
            <path fill="#4CAF50" d="M24 44c5.2 0 9.8-2 13.3-5.2l-6.1-5.2c-1.9 1.3-4.3 2.1-7.2 2.1-5.2 0-9.6-3.3-11.3-8l-6.5 5C9.7 39.6 16.3 44 24 44z"/>
            <path fill="#1976D2" d="M43.6 20.5H42V20H24v8h11.3c-.8 2.3-2.3 4.3-4.1 5.6l6.1 5.2c-.4.4 6.7-4.9 6.7-14.8 0-1.2-.1-2.3-.4-3.5z"/>
        </svg>
        <span>Google bilan kirish</span>
    </a>
    @endif

    @if($hasFacebook)
    <a href="{{ route('auth.social.redirect', 'facebook') }}"
       style="display:flex; align-items:center; justify-content:center; gap:0.5rem; width:100%; padding:0.625rem 1rem; border-radius:0.5rem; background:#1877F2; color:#fff; font-weight:500; text-decoration:none;">
        <svg width="20" height="20" viewBox="0 0 24 24" fill="currentColor" style="flex-shrink:0;">
            <path d="M24 12.073c0-6.627-5.373-12-12-12s-12 5.373-12 12c0 5.99 4.388 10.954 10.125 11.854v-8.385H7.078v-3.47h3.047V9.43c0-3.007 1.792-4.669 4.533-4.669 1.312 0 2.686.235 2.686.235v2.953H15.83c-1.491 0-1.956.925-1.956 1.874v2.25h3.328l-.532 3.47h-2.796v8.385C19.612 23.027 24 18.062 24 12.073z"/>
        </svg>
        <span>Facebook bilan kirish</span>
    </a>
    @endif

    @if($hasTelegram)
    <div style="display:flex; justify-content:center;">
        <script async src="https://telegram.org/js/telegram-widget.js?22"
            data-telegram-login="{{ config('services.telegram.bot_username') }}"
            data-size="large"
            data-radius="8"
            data-auth-url="{{ route('auth.telegram.callback') }}"
            data-request-access="write"></script>
    </div>
    @endif

    @if(!$anyConfigured)
    <div style="padding:0.75rem; border:1px dashed #fcd34d; background:#fffbeb; border-radius:0.5rem; color:#92400e; font-size:0.8125rem; text-align:center;">
        <strong>OAuth sozlanmagan</strong><br>
        Google/Facebook/Telegram tugmalarini ko'rish uchun <code>.env</code> ga<br>
        <code>GOOGLE_CLIENT_ID</code>, <code>FACEBOOK_CLIENT_ID</code> yoki <code>TELEGRAM_BOT_USERNAME</code> qo'shing
    </div>
    @endif
</div>
