@extends(\App\Helpers\TemplateHelper::getLayout())

@php
    use App\Models\CmsContent;

    // FAQ content from CMS (section = 'faq') with sensible defaults
    $faqContents = CmsContent::where('section', 'faq')->get()->keyBy('key');
    $lang = app()->getLocale() ?? 'uz';
    $langField = 'value_' . $lang;
    $get = function($key, $default = '') use ($faqContents, $langField) {
        $c = $faqContents->get($key);
        return $c ? ($c->$langField ?? $c->value_uz ?? $default) : $default;
    };

    // Default FAQ items (used when CMS has no entries)
    $faqs = [
        ['q' => $get('q1', "Akademiyaga qanday hujjat topshiraman?"),
         'a' => $get('a1', "Onlayn qabul bo'limi orqali ariza topshirishingiz mumkin. Kerakli hujjatlarni skanerlab yuklang va arizangiz holatini kuzatib boring.")],
        ['q' => $get('q2', "O'qish to'lovi qancha?"),
         'a' => $get('a2', "To'lov miqdori yo'nalish va ta'lim shakliga qarab farqlanadi. Aniq ma'lumot uchun qabul bo'limiga murojaat qiling.")],
        ['q' => $get('q3', "Grant asosida o'qish mumkinmi?"),
         'a' => $get('a3', "Ha, davlat granti va akademiya stipendiyalari mavjud. Imtihon natijalari va reyting asosida ajratiladi.")],
        ['q' => $get('q4', "Qaysi ta'lim yo'nalishlari mavjud?"),
         'a' => $get('a4', "Turizm, mehmondo'stlik, menejment va boshqa yo'nalishlar mavjud. To'liq ro'yxat \"Yo'nalishlar\" sahifasida.")],
        ['q' => $get('q5', "Bitiruvchilarga diplom beriladimi?"),
         'a' => $get('a5', "Ha, davlat namunasidagi diplom beriladi va u xalqaro miqyosda tan olinadi.")],
    ];
@endphp

@section('title', ($get('page_title', 'Tez-tez so\'raladigan savollar')) . ' - Tourism Academy')

@section('content')
<section style="padding:60px 0;background:var(--c-bg,#f1f5f9);min-height:60vh;">
    <div class="container" style="max-width:860px;margin:0 auto;padding:0 16px;">

        <div style="text-align:center;margin-bottom:40px;">
            <h1 style="font-size:32px;font-weight:800;color:var(--c-text,#0f172a);margin-bottom:10px;">
                {{ $get('page_title', 'Tez-tez so\'raladigan savollar') }}
            </h1>
            <p style="font-size:15px;color:var(--c-text-3,#64748b);">
                {{ $get('page_subtitle', 'Eng ko\'p beriladigan savollarga javoblar') }}
            </p>
        </div>

        <div class="faq-accordion">
            @foreach($faqs as $i => $item)
            <div class="faq-item" style="background:#fff;border:1px solid var(--c-border,#e2e8f0);border-radius:14px;margin-bottom:12px;overflow:hidden;box-shadow:0 1px 3px rgba(0,0,0,.05);">
                <button type="button" class="faq-question"
                        onclick="toggleFaq({{ $i }})"
                        style="width:100%;text-align:left;padding:18px 22px;background:none;border:none;cursor:pointer;display:flex;align-items:center;justify-content:space-between;gap:12px;font-size:15.5px;font-weight:600;color:var(--c-text,#0f172a);">
                    <span>{{ $item['q'] }}</span>
                    <i class="fas fa-chevron-down faq-icon-{{ $i }}" style="transition:transform .25s;color:var(--c-primary,#4f46e5);flex-shrink:0;"></i>
                </button>
                <div class="faq-answer faq-answer-{{ $i }}" style="max-height:0;overflow:hidden;transition:max-height .3s ease;">
                    <p style="padding:0 22px 18px;margin:0;font-size:14px;line-height:1.7;color:var(--c-text-2,#475569);">
                        {{ $item['a'] }}
                    </p>
                </div>
            </div>
            @endforeach
        </div>

        <div style="text-align:center;margin-top:40px;padding:28px;background:#fff;border-radius:14px;border:1px solid var(--c-border,#e2e8f0);">
            <p style="font-size:15px;color:var(--c-text-2,#475569);margin-bottom:14px;">
                Savolingizga javob topmadingizmi?
            </p>
            <a href="{{ route('contact') }}" class="btn btn-primary"
               style="display:inline-flex;align-items:center;gap:8px;padding:11px 24px;background:linear-gradient(135deg,#4f46e5,#7c3aed);color:#fff;border-radius:10px;text-decoration:none;font-weight:600;font-size:14px;">
                <i class="fas fa-envelope"></i> Biz bilan bog'laning
            </a>
        </div>

    </div>
</section>

<script>
function toggleFaq(i) {
    var ans = document.querySelector('.faq-answer-' + i);
    var icon = document.querySelector('.faq-icon-' + i);
    if (!ans) return;
    var isOpen = ans.style.maxHeight && ans.style.maxHeight !== '0px';
    // close all
    document.querySelectorAll('.faq-answer').forEach(function(el){ el.style.maxHeight = '0'; });
    document.querySelectorAll('[class^="faq-icon-"], [class*=" faq-icon-"]').forEach(function(el){ el.style.transform = 'rotate(0deg)'; });
    if (!isOpen) {
        ans.style.maxHeight = ans.scrollHeight + 'px';
        if (icon) icon.style.transform = 'rotate(180deg)';
    }
}
</script>
@endsection
