@extends('layouts.dashboard-new')

@section('title', 'Aloqa - CMS')
@section('page-title', 'Aloqa sahifasini tahrirlash')

@section('styles')
<style>
    .content-card { border: 1px solid #e5e7eb; border-radius: 12px; background: #fff; margin-bottom: 24px; }
    .section-header { background: linear-gradient(135deg, #1e1b4b 0%, #312e81 100%); color: white; padding: 16px 20px; border-radius: 12px 12px 0 0; }
    .section-header.hero { background: linear-gradient(135deg, #0f172a 0%, #1e293b 100%); }
    .section-header.info { background: linear-gradient(135deg, #1e40af 0%, #1d4ed8 100%); }
    .section-header.form { background: linear-gradient(135deg, #065f46 0%, #047857 100%); }
    .section-header.faq  { background: linear-gradient(135deg, #b45309 0%, #d97706 100%); }
    .nav-pills .nav-link.active { background: linear-gradient(135deg, #4f46e5 0%, #7c3aed 100%); }
    .field-block { padding: 14px; border: 1px solid #e2e8f0; border-radius: 8px; margin-bottom: 14px; background: #f8fafc; }
    .field-label { font-weight: 600; color: #1e293b; margin-bottom: 8px; display: block; }
</style>
@endsection

@section('content')

    <div class="row mb-4">
        <div class="col-12">
            <div class="d-flex flex-wrap justify-content-between align-items-center gap-3">
                <div>
                    <h1 class="h3 mb-1 fw-bold"><i class="fas fa-phone text-primary me-2"></i>Aloqa sahifasini tahrirlash</h1>
                    <p class="text-muted mb-0">Sahifani uchta tilda tahrirlang — o'zgarishlar /aloqa sahifasida ko'rinadi</p>
                </div>
                <div class="d-flex gap-2">
                    <a href="{{ route('cms.dashboard') }}" class="btn btn-outline-secondary"><i class="fas fa-arrow-left me-1"></i> Orqaga</a>
                    <a href="{{ route('contact') }}" target="_blank" class="btn btn-info"><i class="fas fa-external-link-alt me-1"></i> Ko'rish</a>
                </div>
            </div>
        </div>
    </div>

    @if(session('success'))
        <div class="alert alert-success alert-dismissible fade show">
            <i class="fas fa-check-circle me-2"></i>{{ session('success') }}
            <button type="button" class="btn-close" data-bs-dismiss="alert"></button>
        </div>
    @endif

    @php
        $c = $contents->keyBy('key');
        $render = function($key, $label, $type = 'text') use ($c) {
            $item = $c->get($key);
            $html = '<div class="field-block"><span class="field-label">'.e($label).' <small class="text-muted">('.$key.')</small></span><div class="row g-2">';
            foreach (['uz' => '🇺🇿 UZ', 'en' => '🇬🇧 EN', 'ru' => '🇷🇺 RU'] as $lang => $flag) {
                $val = $item ? e($item->{'value_'.$lang}) : '';
                $html .= '<div class="col-md-4"><label class="small text-muted mb-1">'.$flag.'</label>';
                if ($type === 'textarea') {
                    $html .= '<textarea name="'.$key.'['.$lang.']" class="form-control" rows="2">'.$val.'</textarea>';
                } else {
                    $html .= '<input type="text" name="'.$key.'['.$lang.']" class="form-control" value="'.$val.'">';
                }
                $html .= '</div>';
            }
            $html .= '</div></div>';
            return $html;
        };
    @endphp

    <ul class="nav nav-pills mb-4">
        <li class="nav-item"><a class="nav-link active" data-bs-toggle="pill" href="#tab-hero"><i class="fas fa-star me-1"></i> Hero</a></li>
        <li class="nav-item"><a class="nav-link" data-bs-toggle="pill" href="#tab-info"><i class="fas fa-info-circle me-1"></i> Kontakt</a></li>
        <li class="nav-item"><a class="nav-link" data-bs-toggle="pill" href="#tab-hours"><i class="far fa-clock me-1"></i> Ish vaqti</a></li>
        <li class="nav-item"><a class="nav-link" data-bs-toggle="pill" href="#tab-form"><i class="fas fa-edit me-1"></i> Forma</a></li>
        <li class="nav-item"><a class="nav-link" data-bs-toggle="pill" href="#tab-faq"><i class="fas fa-question-circle me-1"></i> FAQ</a></li>
    </ul>

    <form action="{{ route('cms.contact.update') }}" method="POST">
        @csrf

        <div class="tab-content">
            {{-- Hero --}}
            <div class="tab-pane fade show active" id="tab-hero">
                <div class="content-card">
                    <div class="section-header hero"><h5 class="mb-0"><i class="fas fa-star me-2"></i>Hero bo'limi</h5></div>
                    <div class="p-3">
                        {!! $render('contact_page_title', 'Sahifa sarlavhasi (title)') !!}
                        {!! $render('contact_hero_badge', 'Hero badge matni') !!}
                        {!! $render('contact_hero_title', 'Hero asosiy sarlavha') !!}
                        {!! $render('contact_hero_subtitle', 'Hero qo\'shimcha matn', 'textarea') !!}
                        {!! $render('contact_breadcrumb_home', 'Breadcrumb: Bosh sahifa') !!}
                        {!! $render('contact_breadcrumb_contact', 'Breadcrumb: Aloqa') !!}
                    </div>
                </div>
            </div>

            {{-- Kontakt --}}
            <div class="tab-pane fade" id="tab-info">
                <div class="content-card">
                    <div class="section-header info"><h5 class="mb-0"><i class="fas fa-info-circle me-2"></i>Kontakt ma'lumotlari</h5></div>
                    <div class="p-3">
                        <h6 class="fw-bold text-primary mt-2"><i class="fas fa-map-marker-alt me-1"></i> Manzil</h6>
                        {!! $render('contact_address_title', 'Manzil sarlavhasi') !!}
                        {!! $render('contact_address_text', 'Manzil matni (&lt;br&gt; yangi qator)', 'textarea') !!}

                        <h6 class="fw-bold text-primary mt-4"><i class="fas fa-phone me-1"></i> Telefon</h6>
                        {!! $render('contact_phone_title', 'Telefon sarlavhasi') !!}
                        {!! $render('contact_phone_text', 'Telefon raqamlari (&lt;br&gt; yangi qator)', 'textarea') !!}

                        <h6 class="fw-bold text-primary mt-4"><i class="fas fa-envelope me-1"></i> Email</h6>
                        {!! $render('contact_email_title', 'Email sarlavhasi') !!}
                        {!! $render('contact_email_text', 'Email manzillari (&lt;br&gt; yangi qator)', 'textarea') !!}

                        <h6 class="fw-bold text-primary mt-4"><i class="fas fa-share-alt me-1"></i> Ijtimoiy tarmoqlar</h6>
                        {!! $render('contact_social_title', 'Ijtimoiy tarmoqlar sarlavhasi') !!}

                        <h6 class="fw-bold text-primary mt-4"><i class="fas fa-map-marked-alt me-1"></i> Xarita (Google Maps)</h6>
                        <div class="alert alert-info py-2" style="font-size:13px">
                            <i class="fas fa-info-circle me-1"></i>
                            Google Maps'da joyni oching → <b>Share → Embed a map</b> → HTML'dan <b>src="..."</b> ichidagi havolani yoki butun &lt;iframe&gt; kodini <b>UZ</b> katagiga joylang.
                        </div>
                        {!! $render('contact_map_embed', 'Xarita embed URL (yoki iframe kodi)', 'textarea') !!}
                    </div>
                </div>
            </div>

            {{-- Hours --}}
            <div class="tab-pane fade" id="tab-hours">
                <div class="content-card">
                    <div class="section-header"><h5 class="mb-0"><i class="far fa-clock me-2"></i>Ish vaqti</h5></div>
                    <div class="p-3">
                        {!! $render('contact_hours_title', 'Ish vaqti sarlavhasi') !!}
                        {!! $render('contact_hours_weekdays', 'Dushanba - Juma (nomi)') !!}
                        {!! $render('contact_hours_weekdays_time', 'Dushanba - Juma (vaqti)') !!}
                        {!! $render('contact_hours_saturday', 'Shanba (nomi)') !!}
                        {!! $render('contact_hours_saturday_time', 'Shanba (vaqti)') !!}
                        {!! $render('contact_hours_sunday', 'Yakshanba (nomi)') !!}
                        {!! $render('contact_hours_closed', 'Yakshanba / Dam olish matni') !!}
                    </div>
                </div>
            </div>

            {{-- Form --}}
            <div class="tab-pane fade" id="tab-form">
                <div class="content-card">
                    <div class="section-header form"><h5 class="mb-0"><i class="fas fa-edit me-2"></i>Aloqa formasi</h5></div>
                    <div class="p-3">
                        {!! $render('contact_form_title', 'Forma sarlavhasi') !!}
                        {!! $render('contact_form_subtitle', 'Forma qisqa tavsif', 'textarea') !!}
                        {!! $render('contact_form_name', 'Ism maydoni labeli') !!}
                        {!! $render('contact_form_name_placeholder', 'Ism placeholder') !!}
                        {!! $render('contact_form_phone', 'Telefon maydoni labeli') !!}
                        {!! $render('contact_form_email', 'Email maydoni labeli') !!}
                        {!! $render('contact_form_subject', 'Mavzu labeli') !!}
                        {!! $render('contact_form_select', 'Mavzu tanlash placeholder') !!}
                        {!! $render('contact_form_subject_admission', 'Mavzu: Qabul') !!}
                        {!! $render('contact_form_subject_education', 'Mavzu: Ta\'lim') !!}
                        {!! $render('contact_form_subject_general', 'Mavzu: Umumiy') !!}
                        {!! $render('contact_form_subject_partnership', 'Mavzu: Hamkorlik') !!}
                        {!! $render('contact_form_subject_other', 'Mavzu: Boshqa') !!}
                        {!! $render('contact_form_message', 'Xabar labeli') !!}
                        {!! $render('contact_form_message_placeholder', 'Xabar placeholder') !!}
                        {!! $render('contact_form_submit', 'Yuborish tugmasi') !!}
                    </div>
                </div>
            </div>

            {{-- FAQ --}}
            <div class="tab-pane fade" id="tab-faq">
                <div class="content-card">
                    <div class="section-header faq"><h5 class="mb-0"><i class="fas fa-question-circle me-2"></i>FAQ (Savol-Javob)</h5></div>
                    <div class="p-3">
                        {!! $render('contact_faq_badge', 'FAQ badge') !!}
                        {!! $render('contact_faq_title', 'FAQ sarlavhasi') !!}

                        <hr>
                        <h6 class="fw-bold text-warning">Savol 1</h6>
                        {!! $render('contact_faq1_question', '1-savol') !!}
                        {!! $render('contact_faq1_answer', '1-javob', 'textarea') !!}

                        <hr>
                        <h6 class="fw-bold text-warning">Savol 2</h6>
                        {!! $render('contact_faq2_question', '2-savol') !!}
                        {!! $render('contact_faq2_answer', '2-javob', 'textarea') !!}

                        <hr>
                        <h6 class="fw-bold text-warning">Savol 3</h6>
                        {!! $render('contact_faq3_question', '3-savol') !!}
                        {!! $render('contact_faq3_answer', '3-javob', 'textarea') !!}

                        <hr>
                        <h6 class="fw-bold text-warning">Savol 4</h6>
                        {!! $render('contact_faq4_question', '4-savol') !!}
                        {!! $render('contact_faq4_answer', '4-javob', 'textarea') !!}
                    </div>
                </div>
            </div>
        </div>

        <div class="card mt-4">
            <div class="card-body d-flex justify-content-end gap-2">
                <button type="reset" class="btn btn-secondary"><i class="fas fa-undo me-1"></i> Bekor qilish</button>
                <button type="submit" class="btn btn-primary btn-lg"><i class="fas fa-save me-1"></i> Saqlash</button>
            </div>
        </div>
    </form>
@endsection
