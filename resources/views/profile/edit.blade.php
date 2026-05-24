@extends('layouts.dashboard-new')

@section('title', 'Profilni tahrirlash')
@section('page-title', 'Profilni tahrirlash')

@section('content')
<div class="page-header">
    <div class="page-header-left">
        <h1 class="page-header-title">Profilni tahrirlash</h1>
        <p class="page-header-sub">Shaxsiy ma'lumotlar va parolni yangilash</p>
    </div>
</div>

<div class="row g-4">

    {{-- Profil ma'lumotlari --}}
    <div class="col-lg-6">
        <div class="card">
            <div class="card-header d-flex align-items-center gap-2">
                <i class="fas fa-user-edit" style="color:var(--c-primary)"></i>
                <span>Shaxsiy ma'lumotlar</span>
            </div>
            <div class="card-body">
                <form method="POST" action="{{ route('profile.update') }}">
                    @csrf @method('PUT')

                    <div class="mb-3">
                        <label class="form-label">To'liq ism <span class="required">*</span></label>
                        <input type="text" name="name" class="form-control @error('name') is-invalid @enderror"
                               value="{{ old('name', $user->name) }}" placeholder="Ism Familiya" required>
                        @error('name')<div class="invalid-feedback">{{ $message }}</div>@enderror
                    </div>

                    <div class="mb-3">
                        <label class="form-label">Email <span class="required">*</span></label>
                        <input type="email" name="email" class="form-control @error('email') is-invalid @enderror"
                               value="{{ old('email', $user->email) }}" placeholder="email@example.com" required>
                        @error('email')<div class="invalid-feedback">{{ $message }}</div>@enderror
                    </div>

                    <div class="mb-4">
                        <label class="form-label">Telefon raqam</label>
                        <input type="text" name="phone" class="form-control @error('phone') is-invalid @enderror"
                               value="{{ old('phone', $user->phone ?? '') }}" placeholder="+998 90 123 45 67">
                        @error('phone')<div class="invalid-feedback">{{ $message }}</div>@enderror
                    </div>

                    {{-- Read-only info --}}
                    <div class="mb-3 p-3 rounded-lg" style="background:var(--c-bg);border:1px solid var(--c-border)">
                        <div class="d-flex gap-3 flex-wrap">
                            <div>
                                <div style="font-size:11px;color:var(--c-text-3);text-transform:uppercase;letter-spacing:.05em;font-weight:600;">Rol</div>
                                <div style="font-size:13px;font-weight:600;color:var(--c-text)">{{ $user->roles->first()->name ?? '—' }}</div>
                            </div>
                            <div>
                                <div style="font-size:11px;color:var(--c-text-3);text-transform:uppercase;letter-spacing:.05em;font-weight:600;">Ro'yxatdan o'tish</div>
                                <div style="font-size:13px;font-weight:600;color:var(--c-text)">{{ $user->created_at->format('d.m.Y') }}</div>
                            </div>
                        </div>
                    </div>

                    <button type="submit" class="btn btn-primary w-100">
                        <i class="fas fa-save"></i> Saqlash
                    </button>
                </form>
            </div>
        </div>
    </div>

    {{-- Parol o'zgartirish --}}
    <div class="col-lg-6">
        <div class="card">
            <div class="card-header d-flex align-items-center gap-2">
                <i class="fas fa-lock" style="color:var(--c-primary)"></i>
                <span>Parolni o'zgartirish</span>
            </div>
            <div class="card-body">
                <form method="POST" action="{{ route('profile.change-password') }}">
                    @csrf @method('PUT')

                    <div class="mb-3">
                        <label class="form-label">Joriy parol <span class="required">*</span></label>
                        <div class="input-group">
                            <input type="password" name="current_password" id="cur_pwd"
                                   class="form-control @error('current_password') is-invalid @enderror"
                                   placeholder="••••••••" required>
                            <button type="button" class="btn btn-secondary" onclick="togglePwd('cur_pwd',this)" tabindex="-1">
                                <i class="fas fa-eye"></i>
                            </button>
                        </div>
                        @error('current_password')<div class="invalid-feedback d-block">{{ $message }}</div>@enderror
                    </div>

                    <div class="mb-3">
                        <label class="form-label">Yangi parol <span class="required">*</span></label>
                        <div class="input-group">
                            <input type="password" name="password" id="new_pwd"
                                   class="form-control @error('password') is-invalid @enderror"
                                   placeholder="Kamida 8 belgi" required>
                            <button type="button" class="btn btn-secondary" onclick="togglePwd('new_pwd',this)" tabindex="-1">
                                <i class="fas fa-eye"></i>
                            </button>
                        </div>
                        @error('password')<div class="invalid-feedback d-block">{{ $message }}</div>@enderror
                    </div>

                    <div class="mb-4">
                        <label class="form-label">Yangi parolni tasdiqlash <span class="required">*</span></label>
                        <div class="input-group">
                            <input type="password" name="password_confirmation" id="conf_pwd"
                                   class="form-control"
                                   placeholder="Takroran kiriting" required>
                            <button type="button" class="btn btn-secondary" onclick="togglePwd('conf_pwd',this)" tabindex="-1">
                                <i class="fas fa-eye"></i>
                            </button>
                        </div>
                    </div>

                    {{-- Parol kuchi ko'rsatkichi --}}
                    <div class="mb-4">
                        <div style="font-size:12px;color:var(--c-text-3);margin-bottom:6px;">Parol kuchi</div>
                        <div style="height:6px;border-radius:999px;background:var(--c-border);overflow:hidden">
                            <div id="pwdStrengthBar" style="height:100%;width:0%;border-radius:999px;transition:all .3s;background:#ef4444"></div>
                        </div>
                        <div id="pwdStrengthText" style="font-size:11px;margin-top:4px;color:var(--c-text-3)"></div>
                    </div>

                    <button type="submit" class="btn btn-primary w-100">
                        <i class="fas fa-key"></i> Parolni o'zgartirish
                    </button>
                </form>
            </div>
        </div>

        {{-- Avatar / Avatar placeholder --}}
        <div class="card mt-4">
            <div class="card-header d-flex align-items-center gap-2">
                <i class="fas fa-user-circle" style="color:var(--c-primary)"></i>
                <span>Avatar</span>
            </div>
            <div class="card-body text-center">
                <div style="width:80px;height:80px;border-radius:50%;background:linear-gradient(135deg,var(--c-primary),var(--c-violet));display:flex;align-items:center;justify-content:center;margin:0 auto 14px;font-size:32px;font-weight:700;color:#fff">
                    {{ strtoupper(substr($user->name, 0, 1)) }}
                </div>
                <div style="font-size:13px;font-weight:600;color:var(--c-text)">{{ $user->name }}</div>
                <div style="font-size:12px;color:var(--c-text-3)">{{ $user->email }}</div>
                <div class="mt-2">
                    <span class="chip chip-primary">{{ $user->roles->first()->name ?? 'User' }}</span>
                </div>
            </div>
        </div>
    </div>

</div>
@endsection

@push('scripts')
<script>
function togglePwd(id, btn) {
    const inp = document.getElementById(id);
    const icon = btn.querySelector('i');
    if (inp.type === 'password') {
        inp.type = 'text';
        icon.className = 'fas fa-eye-slash';
    } else {
        inp.type = 'password';
        icon.className = 'fas fa-eye';
    }
}

document.getElementById('new_pwd')?.addEventListener('input', function() {
    const val = this.value;
    let strength = 0;
    if (val.length >= 8) strength++;
    if (/[A-Z]/.test(val)) strength++;
    if (/[0-9]/.test(val)) strength++;
    if (/[^A-Za-z0-9]/.test(val)) strength++;

    const bar = document.getElementById('pwdStrengthBar');
    const txt = document.getElementById('pwdStrengthText');
    const pcts  = [0, 25, 50, 75, 100];
    const colors = ['#ef4444','#f97316','#f59e0b','#10b981','#4f46e5'];
    const labels = ['','Zaif','O\'rtacha','Yaxshi','Kuchli'];

    bar.style.width  = pcts[strength] + '%';
    bar.style.background = colors[strength];
    txt.textContent  = labels[strength];
    txt.style.color  = colors[strength];
});
</script>
@endpush
