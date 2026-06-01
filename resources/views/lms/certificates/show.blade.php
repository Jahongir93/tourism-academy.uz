@extends('layouts.dashboard-new')

@section('title', 'Sertifikat — ' . $certificate->title)
@section('page-title', 'Sertifikat ko\'rish')

@section('styles')
<style>
@media print {
    @page { size: A4 landscape; margin: 0; }
    body { print-color-adjust: exact; -webkit-print-color-adjust: exact; }
    .no-print { display: none !important; }
    #certificate { position: absolute; left: 0; top: 0; width: 100%; box-shadow: none !important; }
    body * { visibility: hidden; }
    #certificate, #certificate * { visibility: visible; }
}
</style>
@endsection

@section('content')

<x-lms-alerts />

{{-- Controls --}}
<div class="card mb-4 no-print">
    <div class="card-body py-2">
        <div class="d-flex align-items-center justify-content-between gap-3">
            <div class="d-flex align-items-center gap-3">
                <a href="{{ route('lms.certificates.index') }}" class="btn btn-sm btn-outline-secondary">
                    <i class="fas fa-arrow-left me-1"></i>Orqaga
                </a>
                <div style="width:1px;height:24px;background:var(--c-border)"></div>
                <div style="font-size:14px;font-weight:700;color:var(--c-text)">{{ $certificate->title }}</div>
            </div>
            <div class="d-flex gap-2">
                <a href="{{ route('lms.certificates.download', $certificate) }}" class="btn btn-sm" style="background:var(--c-teal);color:#fff">
                    <i class="fas fa-download me-1"></i>Yuklab olish
                </a>
                <button onclick="window.print()" class="btn btn-sm btn-outline-secondary">
                    <i class="fas fa-print me-1"></i>Chop etish
                </button>
            </div>
        </div>
    </div>
</div>

{{-- Certificate preview --}}
<div style="max-width:900px;margin:0 auto 24px">
    <div id="certificate" class="card overflow-hidden" style="box-shadow:0 4px 24px rgba(0,0,0,.12)">
        {{-- Decorative header --}}
        <div style="position:relative;height:128px;background:linear-gradient(135deg,#14b8a6,#10b981,#0ea5e9);overflow:hidden">
            <div style="position:absolute;top:0;left:0;width:256px;height:256px;background:rgba(255,255,255,.1);border-radius:50%;transform:translate(-50%,-50%)"></div>
            <div style="position:absolute;top:0;right:0;width:192px;height:192px;background:rgba(255,255,255,.1);border-radius:50%;transform:translate(50%,-50%)"></div>
            <div style="position:relative;z-index:1;height:100%;display:flex;align-items:center;justify-content:center;text-align:center">
                <div>
                    <div style="width:64px;height:64px;background:#fff;border-radius:50%;display:flex;align-items:center;justify-content:center;margin:0 auto 8px;box-shadow:0 2px 8px rgba(0,0,0,.15)">
                        <i class="fas fa-university" style="color:#14b8a6;font-size:24px"></i>
                    </div>
                    <h2 style="color:#fff;font-size:18px;font-weight:700;margin:0">Turizm Akademiyasi</h2>
                    <p style="color:rgba(255,255,255,.8);font-size:12px;margin:2px 0 0">Tourism Academy of Uzbekistan</p>
                </div>
            </div>
        </div>

        {{-- Certificate body --}}
        <div style="padding:48px;position:relative">
            {{-- Corner decorations --}}
            <div style="position:absolute;top:24px;left:24px;width:40px;height:40px;border-top:3px solid #14b8a6;border-left:3px solid #14b8a6;border-radius:4px 0 0 0"></div>
            <div style="position:absolute;top:24px;right:24px;width:40px;height:40px;border-top:3px solid #14b8a6;border-right:3px solid #14b8a6;border-radius:0 4px 0 0"></div>
            <div style="position:absolute;bottom:24px;left:24px;width:40px;height:40px;border-bottom:3px solid #14b8a6;border-left:3px solid #14b8a6;border-radius:0 0 0 4px"></div>
            <div style="position:absolute;bottom:24px;right:24px;width:40px;height:40px;border-bottom:3px solid #14b8a6;border-right:3px solid #14b8a6;border-radius:0 0 4px 0"></div>

            <div style="position:relative;z-index:1;text-align:center">
                {{-- Label --}}
                <div style="margin-bottom:20px">
                    <p style="color:#14b8a6;font-size:11px;font-weight:700;letter-spacing:.15em;text-transform:uppercase;margin-bottom:6px">Sertifikat</p>
                    <div style="width:80px;height:3px;background:linear-gradient(90deg,#14b8a6,#10b981);border-radius:2px;margin:0 auto"></div>
                </div>

                <h1 style="font-size:28px;font-weight:700;color:var(--c-text);margin-bottom:24px">{{ $certificate->title }}</h1>

                {{-- Recipient --}}
                <div style="margin-bottom:24px">
                    <p style="font-size:14px;color:var(--c-text-3);margin-bottom:6px">Ushbu sertifikat quyidagi shaxsga berildi:</p>
                    <p style="font-size:24px;font-weight:700;color:var(--c-text);margin-bottom:8px">{{ $certificate->user?->name }}</p>
                    <div style="width:200px;height:1px;background:var(--c-border);margin:0 auto"></div>
                </div>

                @if($certificate->subject)
                <div style="margin-bottom:24px">
                    <p style="font-size:14px;color:var(--c-text-2)">
                        <span style="font-weight:700;color:#14b8a6">{{ $certificate->subject->name_uz }}</span>
                        fanini muvaffaqiyatli o'zlashtirganligini tasdiqlovchi
                    </p>
                </div>
                @endif

                {{-- Stats row --}}
                <div class="row g-3 justify-content-center mb-6" style="max-width:480px;margin:0 auto 24px">
                    @if($certificate->score)
                    <div class="col-4">
                        <div style="padding:12px;border-radius:10px;background:rgba(20,184,166,.06);border:1px solid rgba(20,184,166,.15)">
                            <div style="font-size:11px;color:var(--c-text-3);margin-bottom:4px">Natija</div>
                            <div style="font-size:20px;font-weight:700;color:#14b8a6">{{ $certificate->score }}%</div>
                        </div>
                    </div>
                    @endif
                    <div class="col-4">
                        <div style="padding:12px;border-radius:10px;background:rgba(14,165,233,.06);border:1px solid rgba(14,165,233,.15)">
                            <div style="font-size:11px;color:var(--c-text-3);margin-bottom:4px">Berilgan sana</div>
                            <div style="font-size:14px;font-weight:700;color:var(--c-text)">{{ $certificate->issue_date?->format('d.m.Y') }}</div>
                        </div>
                    </div>
                    <div class="col-4">
                        <div style="padding:12px;border-radius:10px;background:rgba(124,58,237,.06);border:1px solid rgba(124,58,237,.15)">
                            <div style="font-size:11px;color:var(--c-text-3);margin-bottom:4px">Sertifikat raqami</div>
                            <div style="font-size:12px;font-weight:700;color:var(--c-text)">{{ $certificate->certificate_number }}</div>
                        </div>
                    </div>
                </div>

                {{-- Footer row: signature / QR / stamp --}}
                <div class="d-flex align-items-end justify-content-between mt-4 pt-4" style="border-top:1px solid var(--c-border)">
                    <div class="text-start">
                        <div style="height:48px;margin-bottom:6px;display:flex;align-items:flex-end">
                            <span style="font-family:'Brush Script MT',cursive;font-size:32px;color:#14b8a6">Rahbar</span>
                        </div>
                        <div style="border-top:2px solid var(--c-text);padding-top:4px">
                            <div style="font-size:13px;font-weight:600;color:var(--c-text)">{{ $certificate->signed_by ?? 'Direktor' }}</div>
                            <div style="font-size:11px;color:var(--c-text-3)">Direktor</div>
                        </div>
                    </div>

                    <div class="text-center">
                        <div style="display:inline-block;padding:8px;border:3px solid #14b8a6;border-radius:10px;background:#fff">
                            <div id="qrcode-{{ $certificate->id }}" style="width:80px;height:80px"></div>
                        </div>
                        <div style="font-size:10px;color:var(--c-text-3);margin-top:4px">Tekshirish uchun skan qiling</div>
                    </div>

                    <div class="text-end">
                        <svg width="100" height="100" viewBox="0 0 120 120" xmlns="http://www.w3.org/2000/svg">
                            <circle cx="60" cy="60" r="55" fill="none" stroke="#14b8a6" stroke-width="3"/>
                            <circle cx="60" cy="60" r="50" fill="none" stroke="#14b8a6" stroke-width="1.5"/>
                            <defs><path id="cp" d="M 60, 60 m -45, 0 a 45,45 0 1,1 90,0 a 45,45 0 1,1 -90,0"/></defs>
                            <text font-size="10" fill="#14b8a6" font-weight="bold">
                                <textPath href="#cp" startOffset="5%">TURIZM AKADEMIYASI • TOURISM ACADEMY</textPath>
                            </text>
                            <circle cx="60" cy="60" r="22" fill="#14b8a6" opacity="0.1"/>
                            <text x="60" y="67" text-anchor="middle" font-size="22" fill="#14b8a6">★</text>
                        </svg>
                        <div style="font-size:11px;color:var(--c-text-3);margin-top:2px">Rasmiy muhri</div>
                    </div>
                </div>
            </div>
        </div>

        {{-- Footer --}}
        <div style="padding:12px 32px;border-top:1px solid var(--c-border);background:var(--c-bg)">
            <div class="d-flex flex-wrap justify-content-between gap-3" style="font-size:12px;color:var(--c-text-3)">
                <span><i class="fas fa-globe me-1" style="color:#14b8a6"></i>www.tac.uz</span>
                <span><i class="fas fa-envelope me-1" style="color:#14b8a6"></i>info@tac.uz</span>
                <span><i class="fas fa-phone me-1" style="color:#14b8a6"></i>+998 71 123 45 67</span>
                @if($certificate->expiry_date)
                <span><i class="fas fa-calendar-check me-1" style="color:#14b8a6"></i>Amal qilish muddati: {{ $certificate->expiry_date->format('d.m.Y') }}</span>
                @endif
            </div>
        </div>
    </div>
</div>

{{-- Verification info --}}
<div class="card no-print" style="max-width:900px;margin:0 auto">
    <div class="card-body">
        <div class="d-flex align-items-start gap-3">
            <i class="fas fa-info-circle mt-1" style="color:var(--c-sky);font-size:18px;flex-shrink:0"></i>
            <div>
                <div style="font-size:13px;font-weight:700;color:var(--c-text);margin-bottom:4px">Sertifikatni tekshirish</div>
                <p style="font-size:13px;color:var(--c-text-2);margin-bottom:8px">
                    Ushbu sertifikatning haqiqiyligini quyidagi manzilda tekshirishingiz mumkin:
                </p>
                <code style="font-size:12px;background:var(--c-bg);border:1px solid var(--c-border);padding:4px 10px;border-radius:6px;color:var(--c-text)">
                    {{ url('/verify/' . $certificate->certificate_number) }}
                </code>
            </div>
        </div>
    </div>
</div>

@endsection

@push('scripts')
<script src="{{ asset('vendor/qrcode/qrcode.min.js') }}"></script>
<script>
new QRCode(document.getElementById("qrcode-{{ $certificate->id }}"), {
    text: "{{ url('/verify/' . $certificate->certificate_number) }}",
    width: 80,
    height: 80,
    colorDark: "#14b8a6",
    colorLight: "#ffffff",
    correctLevel: QRCode.CorrectLevel.H
});
</script>
@endpush
