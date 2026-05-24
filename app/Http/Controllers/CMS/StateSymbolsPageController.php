<?php

namespace App\Http\Controllers\CMS;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use Illuminate\Support\Str;
use App\Models\CmsContent;

class StateSymbolsPageController extends Controller
{
    const UPLOAD_DIR = 'uploads/state-symbols';

    const ALLOWED_IMAGE_KEYS = [
        'symbols_flag_image',
        'symbols_emblem_image',
    ];

    public function index()
    {
        // Default state symbols content
        $defaultContent = [
            // Flag Section
            ['key' => 'symbols_flag_image', 'type' => 'image', 'value_uz' => '', 'value_en' => '', 'value_ru' => '', 'order' => 0],
            ['key' => 'symbols_flag_adopted', 'type' => 'text', 'value_uz' => '1991-yil 18-noyabr', 'value_en' => 'November 18, 1991', 'value_ru' => '18 ноября 1991', 'order' => 1],

            // Flag Colors
            ['key' => 'symbols_flag_color1_name', 'type' => 'text', 'value_uz' => "Ko'k rang", 'value_en' => 'Blue', 'value_ru' => 'Синий', 'order' => 2],
            ['key' => 'symbols_flag_color1_meaning', 'type' => 'text', 'value_uz' => "Osmon va suv ramzi, Amir Temur bayrog'ining rangi", 'value_en' => 'Symbol of sky and water, color of Amir Temur\'s flag', 'value_ru' => 'Символ неба и воды, цвет флага Амира Темура', 'order' => 3],
            ['key' => 'symbols_flag_color1_hex', 'type' => 'text', 'value_uz' => '#0099B5', 'value_en' => '#0099B5', 'value_ru' => '#0099B5', 'order' => 4],

            ['key' => 'symbols_flag_color2_name', 'type' => 'text', 'value_uz' => 'Oq rang', 'value_en' => 'White', 'value_ru' => 'Белый', 'order' => 5],
            ['key' => 'symbols_flag_color2_meaning', 'type' => 'text', 'value_uz' => 'Tinchlik va poklik ramzi', 'value_en' => 'Symbol of peace and purity', 'value_ru' => 'Символ мира и чистоты', 'order' => 6],
            ['key' => 'symbols_flag_color2_hex', 'type' => 'text', 'value_uz' => '#FFFFFF', 'value_en' => '#FFFFFF', 'value_ru' => '#FFFFFF', 'order' => 7],

            ['key' => 'symbols_flag_color3_name', 'type' => 'text', 'value_uz' => 'Yashil rang', 'value_en' => 'Green', 'value_ru' => 'Зелёный', 'order' => 8],
            ['key' => 'symbols_flag_color3_meaning', 'type' => 'text', 'value_uz' => 'Tabiat, yangi hayot va hosildorlik ramzi', 'value_en' => 'Symbol of nature, new life and fertility', 'value_ru' => 'Символ природы, новой жизни и плодородия', 'order' => 9],
            ['key' => 'symbols_flag_color3_hex', 'type' => 'text', 'value_uz' => '#1EB53A', 'value_en' => '#1EB53A', 'value_ru' => '#1EB53A', 'order' => 10],

            ['key' => 'symbols_flag_color4_name', 'type' => 'text', 'value_uz' => 'Qizil chiziqlar', 'value_en' => 'Red stripes', 'value_ru' => 'Красные полосы', 'order' => 11],
            ['key' => 'symbols_flag_color4_meaning', 'type' => 'text', 'value_uz' => 'Hayot kuchi ramzi', 'value_en' => 'Symbol of life force', 'value_ru' => 'Символ жизненной силы', 'order' => 12],
            ['key' => 'symbols_flag_color4_hex', 'type' => 'text', 'value_uz' => '#CE1126', 'value_en' => '#CE1126', 'value_ru' => '#CE1126', 'order' => 13],

            // Flag Elements
            ['key' => 'symbols_flag_element1', 'type' => 'text', 'value_uz' => 'Yarim oy — mustaqillik ramzi', 'value_en' => 'Crescent — symbol of independence', 'value_ru' => 'Полумесяц — символ независимости', 'order' => 14],
            ['key' => 'symbols_flag_element2', 'type' => 'text', 'value_uz' => '12 ta yulduz — 12 oy va tarixiy viloyatlar ramzi', 'value_en' => '12 stars — symbol of 12 months and historical regions', 'value_ru' => '12 звёзд — символ 12 месяцев и исторических областей', 'order' => 15],

            // Emblem Section
            ['key' => 'symbols_emblem_adopted', 'type' => 'text', 'value_uz' => '1992-yil 2-iyul', 'value_en' => 'July 2, 1992', 'value_ru' => '2 июля 1992', 'order' => 20],
            ['key' => 'symbols_emblem_image', 'type' => 'image', 'value_uz' => '', 'value_en' => '', 'value_ru' => '', 'order' => 21],

            // Emblem Elements
            ['key' => 'symbols_emblem_element1_name', 'type' => 'text', 'value_uz' => 'Humo qushi', 'value_en' => 'Humo bird', 'value_ru' => 'Птица Хумо', 'order' => 22],
            ['key' => 'symbols_emblem_element1_meaning', 'type' => 'text', 'value_uz' => "Baxt va erkinlik ramzi, qanoti yozilgan holda tasvirlangan", 'value_en' => 'Symbol of happiness and freedom, depicted with spread wings', 'value_ru' => 'Символ счастья и свободы, изображённая с распростёртыми крыльями', 'order' => 23],

            ['key' => 'symbols_emblem_element2_name', 'type' => 'text', 'value_uz' => 'Quyosh', 'value_en' => 'Sun', 'value_ru' => 'Солнце', 'order' => 24],
            ['key' => 'symbols_emblem_element2_meaning', 'type' => 'text', 'value_uz' => 'Nur va issiqlik manbai, tinch hayot ramzi', 'value_en' => 'Source of light and warmth, symbol of peaceful life', 'value_ru' => 'Источник света и тепла, символ мирной жизни', 'order' => 25],

            ['key' => 'symbols_emblem_element3_name', 'type' => 'text', 'value_uz' => 'Daryolar', 'value_en' => 'Rivers', 'value_ru' => 'Реки', 'order' => 26],
            ['key' => 'symbols_emblem_element3_meaning', 'type' => 'text', 'value_uz' => 'Amudaryo va Sirdaryo — hayot manbai', 'value_en' => 'Amu Darya and Syr Darya — source of life', 'value_ru' => 'Амударья и Сырдарья — источник жизни', 'order' => 27],

            ['key' => 'symbols_emblem_element4_name', 'type' => 'text', 'value_uz' => "Bug'doy boshoqlari", 'value_en' => 'Wheat ears', 'value_ru' => 'Пшеничные колосья', 'order' => 28],
            ['key' => 'symbols_emblem_element4_meaning', 'type' => 'text', 'value_uz' => 'Non va farovonlik ramzi', 'value_en' => 'Symbol of bread and prosperity', 'value_ru' => 'Символ хлеба и изобилия', 'order' => 29],

            ['key' => 'symbols_emblem_element5_name', 'type' => 'text', 'value_uz' => 'Paxta chanoqlari', 'value_en' => 'Cotton bolls', 'value_ru' => 'Коробочки хлопка', 'order' => 30],
            ['key' => 'symbols_emblem_element5_meaning', 'type' => 'text', 'value_uz' => "O'zbekistonning asosiy boyligi", 'value_en' => 'Main wealth of Uzbekistan', 'value_ru' => 'Главное богатство Узбекистана', 'order' => 31],

            ['key' => 'symbols_emblem_element6_name', 'type' => 'text', 'value_uz' => 'Sakkiz qirrali yulduz', 'value_en' => 'Eight-pointed star', 'value_ru' => 'Восьмиконечная звезда', 'order' => 32],
            ['key' => 'symbols_emblem_element6_meaning', 'type' => 'text', 'value_uz' => "Birlik va mustahkamlik ramzi, ichida \"O'zbekiston\" yozuvi", 'value_en' => 'Symbol of unity and strength, with inscription "O\'zbekiston"', 'value_ru' => 'Символ единства и прочности, с надписью "O\'zbekiston"', 'order' => 33],

            // Anthem Section
            ['key' => 'symbols_anthem_title', 'type' => 'text', 'value_uz' => "O'zbekiston Respublikasining Davlat Madhiyasi", 'value_en' => 'State Anthem of the Republic of Uzbekistan', 'value_ru' => 'Государственный гимн Республики Узбекистан', 'order' => 40],
            ['key' => 'symbols_anthem_adopted', 'type' => 'text', 'value_uz' => '1992-yil 10-dekabr', 'value_en' => 'December 10, 1992', 'value_ru' => '10 декабря 1992', 'order' => 41],
            ['key' => 'symbols_anthem_author_lyrics', 'type' => 'text', 'value_uz' => 'Abdulla Oripov', 'value_en' => 'Abdulla Oripov', 'value_ru' => 'Абдулла Орипов', 'order' => 42],
            ['key' => 'symbols_anthem_author_music', 'type' => 'text', 'value_uz' => 'Mutal Burhonov', 'value_en' => 'Mutal Burhonov', 'value_ru' => 'Мутал Бурхонов', 'order' => 43],
            ['key' => 'symbols_anthem_audio', 'type' => 'file', 'value_uz' => '', 'value_en' => '', 'value_ru' => '', 'order' => 44],

            // Anthem Verses
            ['key' => 'symbols_anthem_verse1', 'type' => 'textarea', 'value_uz' => "Serquyosh hur o'lkam, elga baxt, najot,\nSen o'zing do'stlarga yo'ldosh, mehribon!\nYashnagay to abad ilmu fan, ijod,\nShuhrating porlasin toki bor jahon!", 'value_en' => "My sunny free land, happiness and salvation to the people,\nYou yourself are a companion to friends, dear one!\nMay science and art flourish forever,\nMay your glory shine as long as the world exists!", 'value_ru' => "Мой солнечный свободный край, народу счастье и спасенье,\nТы сам друзьям товарищ, добрый друг!\nДа будут вечно наука, творчество, искусство,\nДа будет славен ты пока есть мир!", 'order' => 45],

            ['key' => 'symbols_anthem_chorus', 'type' => 'textarea', 'value_uz' => "Oltin bu vodiylar — jon O'zbekiston,\nAjdodlar mardona ruhi senga yor!\nUlug' xalq qudrati jo'sh urgan zamon,\nOlamni mahliyo aylagan diyor!", 'value_en' => "Golden are these valleys — dear Uzbekistan,\nThe valiant spirit of ancestors is with you!\nWhen the great people\'s power surges,\nA land that enchants the world!", 'value_ru' => "Златые эти долины — родной Узбекистан,\nДух мужественных предков с тобой!\nКогда бурлит мощь великого народа,\nКрай, очаровавший мир!", 'order' => 46],

            ['key' => 'symbols_anthem_verse2', 'type' => 'textarea', 'value_uz' => "Bag'ri keng o'zbekning o'chmas iymoni,\nErkin, yosh avlodlar senga zo'r qanot!\nIstiqlol mash'ali, tinchlik posboni,\nXaqsevar, ona yurt, mangu bo'l obod!", 'value_en' => "The Uzbek\'s broad heart holds undying faith,\nFree young generations are your great wings!\nTorch of independence, guardian of peace,\nTruth-loving motherland, may you be eternally prosperous!", 'value_ru' => "В широком сердце узбека — неугасимая вера,\nСвободные молодые поколения — твои могучие крылья!\nФакел независимости, страж мира,\nЛюбящая правду Родина, будь вечно благоденствующей!", 'order' => 47],

            // Footer text
            ['key' => 'symbols_footer_title', 'type' => 'text', 'value_uz' => 'Davlat ramzlariga hurmat', 'value_en' => 'Respect for State Symbols', 'value_ru' => 'Уважение к государственным символам', 'order' => 50],
            ['key' => 'symbols_footer_text', 'type' => 'textarea', 'value_uz' => "O'zbekiston Respublikasining davlat ramzlari — Davlat bayrog'i, Davlat gerbi va Davlat madhiyasi milliy mustaqillik, erkinlik va xalqimizning buyuk kelajagiga ishonchning ramzlaridir. Har bir fuqaro ularni hurmat qilishi va e'zozlashi lozim.", 'value_en' => "The state symbols of the Republic of Uzbekistan — the State Flag, State Emblem and State Anthem are symbols of national independence, freedom and our people's belief in a great future. Every citizen must respect and honor them.", 'value_ru' => 'Государственные символы Республики Узбекистан — Государственный флаг, Государственный герб и Государственный гимн являются символами национальной независимости, свободы и веры нашего народа в великое будущее. Каждый гражданин должен уважать и почитать их.', 'order' => 51],
        ];

        foreach ($defaultContent as $item) {
            CmsContent::firstOrCreate(
                ['section' => 'state_symbols', 'key' => $item['key']],
                [
                    'type'     => $item['type'],
                    'value_uz' => $item['value_uz'],
                    'value_en' => $item['value_en'],
                    'value_ru' => $item['value_ru'],
                    'order'    => $item['order'],
                ]
            );
        }

        $contents = CmsContent::where('section', 'state_symbols')->orderBy('order')->get();

        return view('cms.state-symbols.index', compact('contents'));
    }

    public function update(Request $request)
    {
        $data = $request->except(['_token', '_method']);

        foreach ($data as $key => $values) {
            if (is_array($values)) {
                CmsContent::updateOrCreate(
                    ['section' => 'state_symbols', 'key' => $key],
                    [
                        'value_uz' => $values['uz'] ?? '',
                        'value_en' => $values['en'] ?? '',
                        'value_ru' => $values['ru'] ?? '',
                    ]
                );
            }
        }

        // Audio file upload — direct move, no symlink
        if ($request->hasFile('symbols_anthem_audio')) {
            $file = $request->file('symbols_anthem_audio');
            if ($file->isValid()) {
                $uploadDir = public_path('uploads/state-symbols/audio');
                if (!is_dir($uploadDir)) {
                    mkdir($uploadDir, 0755, true);
                }
                $filename = 'anthem_' . time() . '_' . Str::random(8) . '.' . $file->getClientOriginalExtension();
                $file->move($uploadDir, $filename);
                $path = 'uploads/state-symbols/audio/' . $filename;

                CmsContent::updateOrCreate(
                    ['section' => 'state_symbols', 'key' => 'symbols_anthem_audio'],
                    ['value_uz' => $path, 'value_en' => $path, 'value_ru' => $path, 'type' => 'file']
                );
            }
        }

        return redirect()->route('cms.state-symbols.index')->with('success', "Davlat ramzlari sahifasi muvaffaqiyatli yangilandi!");
    }

    /** AJAX: upload image directly to public/uploads/state-symbols/ */
    public function uploadImage(Request $request)
    {
        $key = $request->input('key');

        if (!in_array($key, self::ALLOWED_IMAGE_KEYS)) {
            return response()->json(['success' => false, 'message' => 'Noto\'g\'ri kalit'], 422);
        }

        $file = $request->file('image');
        if (!$file || !$file->isValid() || !str_starts_with($file->getMimeType(), 'image/')) {
            return response()->json(['success' => false, 'message' => 'Rasm fayli noto\'g\'ri'], 422);
        }

        $uploadDir = public_path(self::UPLOAD_DIR);
        if (!is_dir($uploadDir)) {
            mkdir($uploadDir, 0755, true);
        }

        // Delete old file if it was uploaded before
        $existing = CmsContent::where('section', 'state_symbols')->where('key', $key)->first();
        if ($existing && str_starts_with($existing->value_uz ?? '', 'uploads/')) {
            $oldPath = public_path($existing->value_uz);
            if (file_exists($oldPath)) {
                @unlink($oldPath);
            }
        }

        $ext      = $file->getClientOriginalExtension();
        $filename = $key . '_' . time() . '_' . Str::random(8) . '.' . $ext;
        $file->move($uploadDir, $filename);
        $path = self::UPLOAD_DIR . '/' . $filename;

        CmsContent::updateOrCreate(
            ['section' => 'state_symbols', 'key' => $key],
            ['value_uz' => $path, 'value_en' => $path, 'value_ru' => $path, 'type' => 'image']
        );

        return response()->json([
            'success' => true,
            'path'    => $path,
            'url'     => asset($path),
            'message' => 'Rasm muvaffaqiyatli yuklandi',
        ]);
    }

    /** AJAX: delete image */
    public function deleteImage(Request $request)
    {
        $key = $request->input('key');

        if (!in_array($key, self::ALLOWED_IMAGE_KEYS)) {
            return response()->json(['success' => false, 'message' => 'Noto\'g\'ri kalit'], 422);
        }

        $existing = CmsContent::where('section', 'state_symbols')->where('key', $key)->first();
        if ($existing && str_starts_with($existing->value_uz ?? '', 'uploads/')) {
            $oldPath = public_path($existing->value_uz);
            if (file_exists($oldPath)) {
                @unlink($oldPath);
            }
        }

        CmsContent::where('section', 'state_symbols')->where('key', $key)
            ->update(['value_uz' => '', 'value_en' => '', 'value_ru' => '']);

        return response()->json(['success' => true, 'message' => 'Rasm o\'chirildi']);
    }
}
