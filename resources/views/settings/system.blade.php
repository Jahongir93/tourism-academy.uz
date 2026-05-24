@extends('layouts.dashboard-new')

@section('title', 'Tizim Sozlamalari')
@section('page-title', 'Tizim Sozlamalari')

@section('content')
<div class="container-fluid">
    <!-- Header -->
    <div class="row mb-4">
        <div class="col-12">
            <div class="card border-0 shadow-sm bg-gradient-info text-white">
                <div class="card-body p-4">
                    <div class="d-flex justify-content-between align-items-center">
                        <div>
                            <h4 class="mb-1"><i class="fas fa-server me-2"></i>Tizim Sozlamalari</h4>
                            <p class="mb-0 opacity-75">Kesh, backup, email va tizim ishlashi bilan bog'liq sozlamalar</p>
                        </div>
                        <a href="{{ route('settings.index') }}" class="btn btn-light">
                            <i class="fas fa-arrow-left me-1"></i> Orqaga
                        </a>
                    </div>
                </div>
            </div>
        </div>
    </div>

    @if(session('success'))
    <div class="alert alert-success alert-dismissible fade show" role="alert">
        <i class="fas fa-check-circle me-2"></i>{{ session('success') }}
        <button type="button" class="btn-close" data-bs-dismiss="alert"></button>
    </div>
    @endif

    @if(session('error'))
    <div class="alert alert-danger alert-dismissible fade show" role="alert">
        <i class="fas fa-exclamation-circle me-2"></i>{{ session('error') }}
        <button type="button" class="btn-close" data-bs-dismiss="alert"></button>
    </div>
    @endif

    <form action="{{ route('settings.system.update') }}" method="POST">
        @csrf
        @method('PUT')

        <div class="row">
            <!-- Tizim Umumiy -->
            <div class="col-lg-6 mb-4">
                <div class="card border-0 shadow-sm h-100">
                    <div class="card-header bg-white">
                        <h5 class="mb-0"><i class="fas fa-cogs text-primary me-2"></i>Umumiy Sozlamalar</h5>
                    </div>
                    <div class="card-body">
                        <div class="mb-3">
                            <div class="form-check form-switch">
                                <input class="form-check-input" type="checkbox" name="maintenance_mode" id="maintenance_mode"
                                       {{ old('maintenance_mode', $settings->where('key', 'maintenance_mode')->first()?->value ?? '0') == '1' ? 'checked' : '' }}>
                                <label class="form-check-label fw-semibold" for="maintenance_mode">Ta'mirlash rejimi</label>
                            </div>
                            <small class="text-muted">Yoqilganda faqat adminlar kirishi mumkin</small>
                        </div>

                        <div class="mb-3">
                            <div class="form-check form-switch">
                                <input class="form-check-input" type="checkbox" name="debug_mode" id="debug_mode"
                                       {{ old('debug_mode', $settings->where('key', 'debug_mode')->first()?->value ?? '0') == '1' ? 'checked' : '' }}>
                                <label class="form-check-label fw-semibold" for="debug_mode">Debug rejimi</label>
                            </div>
                            <small class="text-danger">Ishlab chiqarish muhitida o'chirib qo'ying!</small>
                        </div>

                        <div class="mb-3">
                            <label class="form-label fw-semibold">Sessiya muddati (daqiqa)</label>
                            <input type="number" name="session_lifetime" class="form-control" min="5"
                                   value="{{ old('session_lifetime', $settings->where('key', 'session_lifetime')->first()?->value ?? '120') }}">
                        </div>

                        <div class="mb-3">
                            <label class="form-label fw-semibold">Fayllarni saqlash joyi</label>
                            <select name="filesystem_disk" class="form-select">
                                <option value="local" {{ old('filesystem_disk', $settings->where('key', 'filesystem_disk')->first()?->value ?? 'local') == 'local' ? 'selected' : '' }}>Lokal</option>
                                <option value="public" {{ old('filesystem_disk', $settings->where('key', 'filesystem_disk')->first()?->value ?? 'local') == 'public' ? 'selected' : '' }}>Public</option>
                                <option value="s3" {{ old('filesystem_disk', $settings->where('key', 'filesystem_disk')->first()?->value ?? 'local') == 's3' ? 'selected' : '' }}>Amazon S3</option>
                            </select>
                        </div>
                    </div>
                </div>
            </div>

            <!-- Kesh Sozlamalari -->
            <div class="col-lg-6 mb-4">
                <div class="card border-0 shadow-sm h-100">
                    <div class="card-header bg-white d-flex justify-content-between align-items-center">
                        <h5 class="mb-0"><i class="fas fa-bolt text-warning me-2"></i>Kesh Sozlamalari</h5>
                        <button type="button" class="btn btn-sm btn-outline-danger" onclick="clearCache()">
                            <i class="fas fa-trash me-1"></i> Keshni tozalash
                        </button>
                    </div>
                    <div class="card-body">
                        <div class="mb-3">
                            <div class="form-check form-switch">
                                <input class="form-check-input" type="checkbox" name="cache_enabled" id="cache_enabled"
                                       {{ old('cache_enabled', $settings->where('key', 'cache_enabled')->first()?->value ?? '1') == '1' ? 'checked' : '' }}>
                                <label class="form-check-label fw-semibold" for="cache_enabled">Keshni yoqish</label>
                            </div>
                        </div>

                        <div class="mb-3">
                            <label class="form-label fw-semibold">Kesh drayveri</label>
                            <select name="cache_driver" class="form-select">
                                <option value="file" {{ old('cache_driver', $settings->where('key', 'cache_driver')->first()?->value ?? 'file') == 'file' ? 'selected' : '' }}>Fayl</option>
                                <option value="redis" {{ old('cache_driver', $settings->where('key', 'cache_driver')->first()?->value ?? 'file') == 'redis' ? 'selected' : '' }}>Redis</option>
                                <option value="memcached" {{ old('cache_driver', $settings->where('key', 'cache_driver')->first()?->value ?? 'file') == 'memcached' ? 'selected' : '' }}>Memcached</option>
                                <option value="database" {{ old('cache_driver', $settings->where('key', 'cache_driver')->first()?->value ?? 'file') == 'database' ? 'selected' : '' }}>Database</option>
                            </select>
                        </div>

                        <div class="mb-3">
                            <label class="form-label fw-semibold">Kesh muddati (daqiqa)</label>
                            <input type="number" name="cache_ttl" class="form-control" min="1"
                                   value="{{ old('cache_ttl', $settings->where('key', 'cache_ttl')->first()?->value ?? '60') }}">
                        </div>

                        <div class="mb-3">
                            <div class="form-check form-switch">
                                <input class="form-check-input" type="checkbox" name="view_cache" id="view_cache"
                                       {{ old('view_cache', $settings->where('key', 'view_cache')->first()?->value ?? '1') == '1' ? 'checked' : '' }}>
                                <label class="form-check-label fw-semibold" for="view_cache">View keshini yoqish</label>
                            </div>
                        </div>
                    </div>
                </div>
            </div>

            <!-- Email Sozlamalari -->
            <div class="col-lg-6 mb-4">
                <div class="card border-0 shadow-sm h-100">
                    <div class="card-header bg-white d-flex justify-content-between align-items-center">
                        <h5 class="mb-0"><i class="fas fa-envelope text-success me-2"></i>Email Sozlamalari</h5>
                        <button type="button" class="btn btn-sm btn-outline-primary" data-bs-toggle="modal" data-bs-target="#testEmailModal">
                            <i class="fas fa-paper-plane me-1"></i> Test
                        </button>
                    </div>
                    <div class="card-body">
                        <div class="mb-3">
                            <label class="form-label fw-semibold">Mail drayveri</label>
                            <select name="mail_driver" class="form-select">
                                <option value="smtp" {{ old('mail_driver', $settings->where('key', 'mail_driver')->first()?->value ?? 'smtp') == 'smtp' ? 'selected' : '' }}>SMTP</option>
                                <option value="sendmail" {{ old('mail_driver', $settings->where('key', 'mail_driver')->first()?->value ?? 'smtp') == 'sendmail' ? 'selected' : '' }}>Sendmail</option>
                                <option value="mailgun" {{ old('mail_driver', $settings->where('key', 'mail_driver')->first()?->value ?? 'smtp') == 'mailgun' ? 'selected' : '' }}>Mailgun</option>
                            </select>
                        </div>

                        <div class="mb-3">
                            <label class="form-label fw-semibold">SMTP Host</label>
                            <input type="text" name="mail_host" class="form-control"
                                   value="{{ old('mail_host', $settings->where('key', 'mail_host')->first()?->value ?? 'smtp.gmail.com') }}">
                        </div>

                        <div class="mb-3">
                            <label class="form-label fw-semibold">SMTP Port</label>
                            <input type="number" name="mail_port" class="form-control"
                                   value="{{ old('mail_port', $settings->where('key', 'mail_port')->first()?->value ?? '587') }}">
                        </div>

                        <div class="mb-3">
                            <label class="form-label fw-semibold">Email manzil</label>
                            <input type="email" name="mail_username" class="form-control"
                                   value="{{ old('mail_username', $settings->where('key', 'mail_username')->first()?->value ?? '') }}">
                        </div>

                        <div class="mb-3">
                            <label class="form-label fw-semibold">Email parol</label>
                            <input type="password" name="mail_password" class="form-control"
                                   value="{{ old('mail_password', $settings->where('key', 'mail_password')->first()?->value ?? '') }}">
                        </div>

                        <div class="mb-3">
                            <label class="form-label fw-semibold">Shifrlash</label>
                            <select name="mail_encryption" class="form-select">
                                <option value="tls" {{ old('mail_encryption', $settings->where('key', 'mail_encryption')->first()?->value ?? 'tls') == 'tls' ? 'selected' : '' }}>TLS</option>
                                <option value="ssl" {{ old('mail_encryption', $settings->where('key', 'mail_encryption')->first()?->value ?? 'tls') == 'ssl' ? 'selected' : '' }}>SSL</option>
                                <option value="" {{ old('mail_encryption', $settings->where('key', 'mail_encryption')->first()?->value ?? 'tls') == '' ? 'selected' : '' }}>Yo'q</option>
                            </select>
                        </div>
                    </div>
                </div>
            </div>

            <!-- Backup Sozlamalari -->
            <div class="col-lg-6 mb-4">
                <div class="card border-0 shadow-sm h-100">
                    <div class="card-header bg-white d-flex justify-content-between align-items-center">
                        <h5 class="mb-0"><i class="fas fa-hdd text-info me-2"></i>Backup Sozlamalari</h5>
                        <button type="button" class="btn btn-sm btn-success" onclick="createBackup()">
                            <i class="fas fa-download me-1"></i> Backup yaratish
                        </button>
                    </div>
                    <div class="card-body">
                        <div class="mb-3">
                            <div class="form-check form-switch">
                                <input class="form-check-input" type="checkbox" name="auto_backup" id="auto_backup"
                                       {{ old('auto_backup', $settings->where('key', 'auto_backup')->first()?->value ?? '1') == '1' ? 'checked' : '' }}>
                                <label class="form-check-label fw-semibold" for="auto_backup">Avtomatik backup</label>
                            </div>
                        </div>

                        <div class="mb-3">
                            <label class="form-label fw-semibold">Backup chastotasi</label>
                            <select name="backup_frequency" class="form-select">
                                <option value="daily" {{ old('backup_frequency', $settings->where('key', 'backup_frequency')->first()?->value ?? 'daily') == 'daily' ? 'selected' : '' }}>Kunlik</option>
                                <option value="weekly" {{ old('backup_frequency', $settings->where('key', 'backup_frequency')->first()?->value ?? 'daily') == 'weekly' ? 'selected' : '' }}>Haftalik</option>
                                <option value="monthly" {{ old('backup_frequency', $settings->where('key', 'backup_frequency')->first()?->value ?? 'daily') == 'monthly' ? 'selected' : '' }}>Oylik</option>
                            </select>
                        </div>

                        <div class="mb-3">
                            <label class="form-label fw-semibold">Saqlash muddati (kun)</label>
                            <input type="number" name="backup_retention_days" class="form-control" min="1"
                                   value="{{ old('backup_retention_days', $settings->where('key', 'backup_retention_days')->first()?->value ?? '30') }}">
                        </div>

                        <div class="mb-3">
                            <div class="form-check form-switch">
                                <input class="form-check-input" type="checkbox" name="backup_database" id="backup_database"
                                       {{ old('backup_database', $settings->where('key', 'backup_database')->first()?->value ?? '1') == '1' ? 'checked' : '' }}>
                                <label class="form-check-label fw-semibold" for="backup_database">Ma'lumotlar bazasini backup</label>
                            </div>
                        </div>

                        <div class="mb-3">
                            <div class="form-check form-switch">
                                <input class="form-check-input" type="checkbox" name="backup_files" id="backup_files"
                                       {{ old('backup_files', $settings->where('key', 'backup_files')->first()?->value ?? '1') == '1' ? 'checked' : '' }}>
                                <label class="form-check-label fw-semibold" for="backup_files">Fayllarni backup</label>
                            </div>
                        </div>
                    </div>
                </div>
            </div>

            <!-- Log Sozlamalari -->
            <div class="col-lg-6 mb-4">
                <div class="card border-0 shadow-sm h-100">
                    <div class="card-header bg-white d-flex justify-content-between align-items-center">
                        <h5 class="mb-0"><i class="fas fa-file-alt text-secondary me-2"></i>Log Sozlamalari</h5>
                        <a href="{{ route('settings.activity-logs') }}" class="btn btn-sm btn-outline-secondary">
                            <i class="fas fa-eye me-1"></i> Loglarni ko'rish
                        </a>
                    </div>
                    <div class="card-body">
                        <div class="mb-3">
                            <label class="form-label fw-semibold">Log darajasi</label>
                            <select name="log_level" class="form-select">
                                <option value="debug" {{ old('log_level', $settings->where('key', 'log_level')->first()?->value ?? 'error') == 'debug' ? 'selected' : '' }}>Debug</option>
                                <option value="info" {{ old('log_level', $settings->where('key', 'log_level')->first()?->value ?? 'error') == 'info' ? 'selected' : '' }}>Info</option>
                                <option value="warning" {{ old('log_level', $settings->where('key', 'log_level')->first()?->value ?? 'error') == 'warning' ? 'selected' : '' }}>Warning</option>
                                <option value="error" {{ old('log_level', $settings->where('key', 'log_level')->first()?->value ?? 'error') == 'error' ? 'selected' : '' }}>Error</option>
                            </select>
                        </div>

                        <div class="mb-3">
                            <label class="form-label fw-semibold">Log kanali</label>
                            <select name="log_channel" class="form-select">
                                <option value="single" {{ old('log_channel', $settings->where('key', 'log_channel')->first()?->value ?? 'daily') == 'single' ? 'selected' : '' }}>Yagona fayl</option>
                                <option value="daily" {{ old('log_channel', $settings->where('key', 'log_channel')->first()?->value ?? 'daily') == 'daily' ? 'selected' : '' }}>Kunlik fayllar</option>
                                <option value="stack" {{ old('log_channel', $settings->where('key', 'log_channel')->first()?->value ?? 'daily') == 'stack' ? 'selected' : '' }}>Stack</option>
                            </select>
                        </div>

                        <div class="mb-3">
                            <label class="form-label fw-semibold">Log saqlash muddati (kun)</label>
                            <input type="number" name="log_retention_days" class="form-control" min="1"
                                   value="{{ old('log_retention_days', $settings->where('key', 'log_retention_days')->first()?->value ?? '14') }}">
                        </div>

                        <div class="mb-3">
                            <div class="form-check form-switch">
                                <input class="form-check-input" type="checkbox" name="activity_log" id="activity_log"
                                       {{ old('activity_log', $settings->where('key', 'activity_log')->first()?->value ?? '1') == '1' ? 'checked' : '' }}>
                                <label class="form-check-label fw-semibold" for="activity_log">Faoliyat logini yoqish</label>
                            </div>
                        </div>
                    </div>
                </div>
            </div>

            <!-- Fayl yuklash -->
            <div class="col-lg-6 mb-4">
                <div class="card border-0 shadow-sm h-100">
                    <div class="card-header bg-white">
                        <h5 class="mb-0"><i class="fas fa-upload text-danger me-2"></i>Fayl Yuklash</h5>
                    </div>
                    <div class="card-body">
                        <div class="mb-3">
                            <label class="form-label fw-semibold">Maksimal fayl hajmi (MB)</label>
                            <input type="number" name="max_file_size" class="form-control" min="1"
                                   value="{{ old('max_file_size', $settings->where('key', 'max_file_size')->first()?->value ?? '10') }}">
                        </div>

                        <div class="mb-3">
                            <label class="form-label fw-semibold">Ruxsat etilgan formatlar</label>
                            <input type="text" name="allowed_file_types" class="form-control"
                                   value="{{ old('allowed_file_types', $settings->where('key', 'allowed_file_types')->first()?->value ?? 'jpg,jpeg,png,gif,pdf,doc,docx,xls,xlsx') }}">
                            <small class="text-muted">Vergul bilan ajratilgan</small>
                        </div>

                        <div class="mb-3">
                            <label class="form-label fw-semibold">Rasm maksimal o'lchami (px)</label>
                            <input type="number" name="max_image_dimension" class="form-control" min="100"
                                   value="{{ old('max_image_dimension', $settings->where('key', 'max_image_dimension')->first()?->value ?? '2048') }}">
                        </div>

                        <div class="mb-3">
                            <div class="form-check form-switch">
                                <input class="form-check-input" type="checkbox" name="image_compression" id="image_compression"
                                       {{ old('image_compression', $settings->where('key', 'image_compression')->first()?->value ?? '1') == '1' ? 'checked' : '' }}>
                                <label class="form-check-label fw-semibold" for="image_compression">Rasmlarni siqish</label>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>

        <!-- So'nggi Backuplar -->
        <div class="row mb-4">
            <div class="col-12">
                <div class="card border-0 shadow-sm">
                    <div class="card-header bg-white">
                        <h5 class="mb-0"><i class="fas fa-history text-primary me-2"></i>So'nggi Backuplar</h5>
                    </div>
                    <div class="card-body">
                        <div class="table-responsive">
                            <table class="table table-hover">
                                <thead class="table-light">
                                    <tr>
                                        <th>#</th>
                                        <th>Fayl nomi</th>
                                        <th>Hajmi</th>
                                        <th>Sana</th>
                                        <th>Holat</th>
                                        <th>Amallar</th>
                                    </tr>
                                </thead>
                                <tbody>
                                    @forelse($backupLogs ?? [] as $index => $log)
                                    <tr>
                                        <td>{{ $index + 1 }}</td>
                                        <td><i class="fas fa-file-archive me-2 text-warning"></i>{{ $log->filename ?? 'backup.zip' }}</td>
                                        <td>{{ $log->size ?? '0 MB' }}</td>
                                        <td>{{ $log->created_at ? \Carbon\Carbon::parse($log->created_at)->format('d.m.Y H:i') : '-' }}</td>
                                        <td>
                                            @if(($log->status ?? '') == 'completed')
                                            <span class="badge bg-success"><i class="fas fa-check me-1"></i>Muvaffaqiyatli</span>
                                            @elseif(($log->status ?? '') == 'failed')
                                            <span class="badge bg-danger"><i class="fas fa-times me-1"></i>Xatolik</span>
                                            @else
                                            <span class="badge bg-warning"><i class="fas fa-clock me-1"></i>Jarayonda</span>
                                            @endif
                                        </td>
                                        <td>
                                            <button type="button" class="btn btn-sm btn-outline-primary" title="Yuklab olish">
                                                <i class="fas fa-download"></i>
                                            </button>
                                            <button type="button" class="btn btn-sm btn-outline-danger" title="O'chirish">
                                                <i class="fas fa-trash"></i>
                                            </button>
                                        </td>
                                    </tr>
                                    @empty
                                    <tr>
                                        <td colspan="6" class="text-center text-muted py-4">
                                            <i class="fas fa-database fa-2x mb-2 d-block"></i>
                                            Backuplar topilmadi
                                        </td>
                                    </tr>
                                    @endforelse
                                </tbody>
                            </table>
                        </div>
                    </div>
                </div>
            </div>
        </div>

        <!-- Submit -->
        <div class="row">
            <div class="col-12">
                <div class="card border-0 shadow-sm">
                    <div class="card-body text-end">
                        <button type="reset" class="btn btn-outline-secondary me-2">
                            <i class="fas fa-undo me-1"></i> Bekor qilish
                        </button>
                        <button type="submit" class="btn btn-info text-white">
                            <i class="fas fa-save me-1"></i> Saqlash
                        </button>
                    </div>
                </div>
            </div>
        </div>
    </form>
</div>

<!-- Modal: Test Email -->
<div class="modal fade" id="testEmailModal" tabindex="-1">
    <div class="modal-dialog">
        <div class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title"><i class="fas fa-paper-plane me-2"></i>Test Email Yuborish</h5>
                <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
            </div>
            <form action="#" method="POST" onsubmit="alert('Test email funksiyasi hozircha mavjud emas'); return false;">
                @csrf
                <div class="modal-body">
                    <div class="mb-3">
                        <label class="form-label fw-semibold">Email manzil <span class="text-danger">*</span></label>
                        <input type="email" name="test_email" class="form-control" required>
                    </div>
                </div>
                <div class="modal-footer">
                    <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Bekor qilish</button>
                    <button type="submit" class="btn btn-primary"><i class="fas fa-paper-plane me-1"></i> Yuborish</button>
                </div>
            </form>
        </div>
    </div>
</div>

<style>
.bg-gradient-info {
    background: linear-gradient(135deg, #667eea 0%, #764ba2 100%);
}
</style>

<script>
function clearCache() {
    if(confirm('Keshni tozalashni xohlaysizmi?')) {
        fetch('{{ route("settings.system.cache-clear") }}', {
            method: 'POST',
            headers: {
                'X-CSRF-TOKEN': '{{ csrf_token() }}',
                'Content-Type': 'application/json'
            }
        })
        .then(response => response.json())
        .then(data => {
            alert(data.message || 'Kesh tozalandi!');
            location.reload();
        })
        .catch(error => {
            alert('Xatolik yuz berdi!');
        });
    }
}

function createBackup() {
    if(confirm('Yangi backup yaratishni xohlaysizmi?')) {
        fetch('{{ route("settings.system.backup") }}', {
            method: 'POST',
            headers: {
                'X-CSRF-TOKEN': '{{ csrf_token() }}',
                'Content-Type': 'application/json'
            }
        })
        .then(response => response.json())
        .then(data => {
            alert(data.message || 'Backup yaratildi!');
            location.reload();
        })
        .catch(error => {
            alert('Xatolik yuz berdi!');
        });
    }
}
</script>
@endsection
