<?php

namespace App\Http\Controllers\CMS;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use App\Models\CmsContent;
use App\Models\Employee;
use Illuminate\Support\Facades\Storage;

class TeachersPageController extends Controller
{
    public function index()
    {
        // Default teachers page content structure
        $defaultTeachers = [
            // Hero Section
            ['key' => 'teachers_hero_badge', 'type' => 'text', 'value_uz' => 'BIZNING JAMOA', 'value_en' => 'OUR TEAM', 'value_ru' => 'НАША КОМАНДА', 'order' => 1],
            ['key' => 'teachers_hero_title', 'type' => 'text', 'value_uz' => "Bizning O'qituvchilar", 'value_en' => 'Our Teachers', 'value_ru' => 'Наши Преподаватели', 'order' => 2],
            ['key' => 'teachers_hero_subtitle', 'type' => 'text', 'value_uz' => 'Xalqaro tajribaga ega malakali mutaxassislar jamoasi', 'value_en' => 'A team of qualified specialists with international experience', 'value_ru' => 'Команда квалифицированных специалистов с международным опытом', 'order' => 3],

            // Stats Section — all values manually editable
            ['key' => 'teachers_stat1_value', 'type' => 'text', 'value_uz' => '50+', 'value_en' => '50+', 'value_ru' => '50+', 'order' => 9],
            ['key' => 'teachers_stat1_label', 'type' => 'text', 'value_uz' => "O'qituvchilar", 'value_en' => 'Teachers', 'value_ru' => 'Преподаватели', 'order' => 10],
            ['key' => 'teachers_stat2_value', 'type' => 'text', 'value_uz' => '15+', 'value_en' => '15+', 'value_ru' => '15+', 'order' => 11],
            ['key' => 'teachers_stat2_label', 'type' => 'text', 'value_uz' => 'PhD darajali', 'value_en' => 'PhD level', 'value_ru' => 'Уровня PhD', 'order' => 12],
            ['key' => 'teachers_stat3_value', 'type' => 'text', 'value_uz' => '10+', 'value_en' => '10+', 'value_ru' => '10+', 'order' => 13],
            ['key' => 'teachers_stat3_label', 'type' => 'text', 'value_uz' => 'Xalqaro tajriba', 'value_en' => 'International experience', 'value_ru' => 'Международный опыт', 'order' => 14],
            ['key' => 'teachers_stat4_value', 'type' => 'text', 'value_uz' => '100+', 'value_en' => '100+', 'value_ru' => '100+', 'order' => 15],
            ['key' => 'teachers_stat4_label', 'type' => 'text', 'value_uz' => 'Ilmiy maqolalar', 'value_en' => 'Scientific articles', 'value_ru' => 'Научные статьи', 'order' => 16],
        ];

        // Create default content if not exists
        foreach ($defaultTeachers as $item) {
            CmsContent::firstOrCreate(
                ['section' => 'teachers', 'key' => $item['key']],
                [
                    'type' => $item['type'],
                    'value_uz' => $item['value_uz'],
                    'value_en' => $item['value_en'],
                    'value_ru' => $item['value_ru'],
                    'order' => $item['order']
                ]
            );
        }

        $contents = CmsContent::where('section', 'teachers')->orderBy('order')->get();

        // Get teachers from Employee table (same source as /employees/teachers)
        $employeeTeachers = Employee::where('employee_type', 'teacher')
            ->with(['employmentDetail.position', 'employmentDetail.department', 'employmentDetail.faculty'])
            ->orderBy('last_name')
            ->orderBy('first_name')
            ->get()
            ->map(function ($emp) {
                $empDetail = $emp->employmentDetail;
                $positionName = $empDetail && $empDetail->position ? $empDetail->position->name : ($emp->position ?? '');
                return (object)[
                    'id' => $emp->id,
                    'source' => 'employee',
                    'name' => $emp->full_name,
                    'position' => $positionName,
                    'image' => $emp->photo_url,
                    'bio' => '',
                    'email' => $emp->email,
                    'edit_url' => route('employees.edit', $emp->id),
                    'view_url' => route('employees.show', $emp->id),
                ];
            });

        // Get legacy CMS teachers (teachers_list section)
        $cmsTeachers = CmsContent::where('section', 'teachers_list')->orderBy('order')->get()
            ->map(function ($c) {
                $data = json_decode($c->value_uz, true) ?? [];
                return (object)[
                    'id' => $c->id,
                    'source' => 'cms',
                    'name' => $data['name'] ?? '-',
                    'position' => $data['position'] ?? "O'qituvchi",
                    'image' => $data['image'] ?? null,
                    'bio' => $data['bio'] ?? '',
                    'email' => null,
                    'edit_url' => null,
                    'view_url' => null,
                ];
            });

        // Merge both sources
        $teachers = $employeeTeachers->concat($cmsTeachers);

        return view('cms.teachers.index', compact('contents', 'teachers'));
    }

    public function update(Request $request)
    {
        $data = $request->except(['_token', '_method']);

        foreach ($data as $key => $values) {
            if (is_array($values) && !$request->hasFile($key)) {
                CmsContent::updateOrCreate(
                    ['section' => 'teachers', 'key' => $key],
                    [
                        'value_uz' => $values['uz'] ?? '',
                        'value_en' => $values['en'] ?? '',
                        'value_ru' => $values['ru'] ?? '',
                    ]
                );
            }
        }

        return redirect()->route('cms.teachers.index')->with('success', "O'qituvchilar sahifasi muvaffaqiyatli yangilandi!");
    }

    public function storeTeacher(Request $request)
    {
        $request->validate([
            'name' => 'required|string|max:255',
            'bio_uz' => 'required|string',
            'image' => 'nullable|image|mimes:jpeg,png,jpg,gif|max:2048'
        ]);

        $order = CmsContent::where('section', 'teachers_list')->max('order') + 1;

        $imageUrl = null;
        if ($request->hasFile('image')) {
            $file = $request->file('image');
            $filename = time() . '_' . $file->getClientOriginalName();
            $file->move(public_path('assets/images/teachers'), $filename);
            $imageUrl = 'assets/images/teachers/' . $filename;
        }

        CmsContent::create([
            'section' => 'teachers_list',
            'key' => 'teacher_' . time(),
            'type' => 'textarea',
            'value_uz' => json_encode([
                'name' => $request->name,
                'bio' => $request->bio_uz,
                'image' => $imageUrl
            ]),
            'value_en' => json_encode([
                'name' => $request->name,
                'bio' => $request->bio_en ?? $request->bio_uz,
                'image' => $imageUrl
            ]),
            'value_ru' => json_encode([
                'name' => $request->name,
                'bio' => $request->bio_ru ?? $request->bio_uz,
                'image' => $imageUrl
            ]),
            'order' => $order
        ]);

        return redirect()->route('cms.teachers.index')->with('success', "O'qituvchi muvaffaqiyatli qo'shildi!");
    }

    public function updateTeacher(Request $request, $id)
    {
        $request->validate([
            'name' => 'required|string|max:255',
            'bio_uz' => 'required|string',
        ]);

        $teacher = CmsContent::findOrFail($id);
        $currentData = json_decode($teacher->value_uz, true);
        $imageUrl = $currentData['image'] ?? null;

        if ($request->hasFile('image')) {
            // Delete old image if exists
            if ($imageUrl && file_exists(public_path($imageUrl))) {
                unlink(public_path($imageUrl));
            }

            $file = $request->file('image');
            $filename = time() . '_' . $file->getClientOriginalName();
            $file->move(public_path('assets/images/teachers'), $filename);
            $imageUrl = 'assets/images/teachers/' . $filename;
        }

        $teacher->update([
            'value_uz' => json_encode([
                'name' => $request->name,
                'bio' => $request->bio_uz,
                'image' => $imageUrl
            ]),
            'value_en' => json_encode([
                'name' => $request->name,
                'bio' => $request->bio_en ?? $request->bio_uz,
                'image' => $imageUrl
            ]),
            'value_ru' => json_encode([
                'name' => $request->name,
                'bio' => $request->bio_ru ?? $request->bio_uz,
                'image' => $imageUrl
            ]),
        ]);

        return redirect()->route('cms.teachers.index')->with('success', "O'qituvchi ma'lumotlari yangilandi!");
    }

    public function destroyTeacher($id)
    {
        $teacher = CmsContent::findOrFail($id);
        $data = json_decode($teacher->value_uz, true);

        // Delete image if exists
        if (isset($data['image']) && $data['image'] && file_exists(public_path($data['image']))) {
            unlink(public_path($data['image']));
        }

        $teacher->delete();

        return redirect()->route('cms.teachers.index')->with('success', "O'qituvchi o'chirildi!");
    }

    /**
     * Migrate legacy CMS teachers_list records into Employee table so they appear
     * on /employees/teachers and /cms/teachers together.
     */
    public function migrateToEmployees(Request $request)
    {
        $cmsTeachers = CmsContent::where('section', 'teachers_list')->orderBy('order')->get();

        $created = 0;
        $skipped = 0;
        $errors = [];

        foreach ($cmsTeachers as $c) {
            try {
                $dataUz = json_decode($c->value_uz, true) ?? [];
                $dataEn = json_decode($c->value_en, true) ?? [];
                $dataRu = json_decode($c->value_ru, true) ?? [];

                $fullName = $dataUz['name'] ?? $dataEn['name'] ?? $dataRu['name'] ?? null;
                if (!$fullName) { $skipped++; continue; }

                $parts = preg_split('/\s+/', trim($fullName), 3);
                $lastName = $parts[0] ?? $fullName;
                $firstName = $parts[1] ?? '-';
                $middleName = $parts[2] ?? null;

                $exists = Employee::where('employee_type', 'teacher')
                    ->where(function($q) use ($fullName, $lastName, $firstName) {
                        $q->where('full_name', $fullName)
                          ->orWhere(function($q2) use ($lastName, $firstName) {
                              $q2->where('last_name', $lastName)->where('first_name', $firstName);
                          });
                    })->exists();

                if ($exists) { $skipped++; continue; }

                // Generate unique employee_code per attempt (avoid duplicate on fail)
                $baseCode = Employee::generateEmployeeCode();
                $code = $baseCode;
                $suffix = 0;
                while (Employee::where('employee_code', $code)->exists()) {
                    $suffix++;
                    $code = $baseCode . '-' . $suffix;
                }

                Employee::create([
                    'employee_code' => $code,
                    'first_name' => $firstName,
                    'last_name' => $lastName,
                    'middle_name' => $middleName,
                    'full_name' => $fullName,
                    'birth_date' => '1990-01-01',
                    'gender' => 'male',
                    'employee_type' => 'teacher',
                    'photo_url' => $dataUz['image'] ?? null,
                    'position' => $dataUz['position'] ?? "O'qituvchi",
                    'status' => 'active',
                    'hire_date' => now(),
                ]);
                $created++;
            } catch (\Exception $e) {
                $errors[] = ($dataUz['name'] ?? 'unknown') . ': ' . $e->getMessage();
            }
        }

        $msg = "Ko'chirish tugadi. Yangi qo'shildi: {$created}, o'tkazildi (dublikat): {$skipped}";
        if (!empty($errors)) {
            $msg .= '. Xatoliklar: ' . implode('; ', array_slice($errors, 0, 3));
        }

        return redirect()->route('cms.teachers.index')->with('success', $msg);
    }

    public function importTeachers()
    {
        // Static teachers data from teachers.blade.php
        $teachers = [
            [
                'name' => 'Odilov Akmaljon',
                'image' => 'Odilov Akmaljon.JPG',
                'bio_uz' => 'Iqtisod fanlari bo\'yicha falsafa doktori (PhD). London School of Business and Finance, Buyuk Britaniyada Business English o\'qigan. AQShning Columbia universitetida stajyor bo\'lgan. Hindiston, Turkiya va Xitoyda turizm bo\'yicha treninglardan o\'tgan. Xalqaro va mahalliy konferentsiyalarni tashkil etish va ishtirok etishda tajribaga ega. Turizm bo\'yicha 10 dan ortiq ilmiy maqolalar muallifi. 2022 yildan Ipak yo\'li turizm va madaniy meros xalqaro universitetida Turizm kafedrasi mudiri.',
            ],
            [
                'name' => 'Klykov Artem',
                'image' => 'Klykov Artem.JPG',
                'bio_uz' => 'Turizm kafedrasi shtatlı o\'qituvchisi (PhD). Gertsen nomidagi Davlat pedagogika universitetida PhD himoya qilgan. Shveytsariyaning Les Roches universitetida turizm va mehmondo\'stlik bo\'yicha magistratura dasturini tamomlab muvafaqqiyat bilan tugatgan. Ko\'plab xalqaro va mahalliy konferentsiyalar ishtirokchisi. Turizm va mehmondo\'stlik sohalariga oid 10 ga yaqin ilmiy maqolalar muallifi.',
            ],
            [
                'name' => 'Xusen Ibragimov',
                'image' => 'Xusen Ibragimov.JPG',
                'bio_uz' => 'Turizm kafedrasi shtatlı o\'qituvchisi (PhD). Ispaniyaning Alikante universitetida PhD dissertatsiyasini muvaffaqiyatli himoya qilgan. Iqtisodiyot mutaxassisligi. 20 dan ortiq xalqaro va mahalliy konferentsiyalar ishtirokchisi. Turizm va mehmondo\'stlik sohasida Scopus indeksli jurnallarni o\'z ichiga olgan 10 dan ortiq ilmiy maqolalar muallifi.',
            ],
            [
                'name' => 'Nilufar Rakhimova',
                'image' => 'Nilufar Rakhimova.JPG',
                'bio_uz' => 'Turizm kafedrasi shtatlı o\'qituvchisi (PhD). Westminster universitetining Toshkent kampusida PhD dissertatsiyasini muvaffaqiyatli himoya qilgan. Tadqiqot mavzulari: SDGs, barqaror turizm va iqtisodiyot. 20 dan ortiq ilmiy va xalqaro maqolalarga ega. Germaniya va Malayziyada xalqaro stajirovkalarni o\'tagan.',
            ],
            [
                'name' => 'Firuz Zokhidov',
                'image' => 'Firuz Zokhidov.JPG',
                'bio_uz' => 'Turizm kafedrasi shtatlı o\'qituvchisi (PhD). Turizm sohasida 10 yildan ortiq tajribaga ega. Samarqand Iqtisodiyot va Servis institutida bakalavr va magistratura dasturlarini tamomlab tugatgan. Hozirda Ipak yo\'li turizm va madaniy meros xalqaro universitetining Turizm kafedrasi dotsenti lavozimida ishlaydi.',
            ],
            [
                'name' => 'Maftuna Kobilova',
                'image' => 'Maftuna Kobilova.JPG',
                'bio_uz' => 'Turizm kafedrasi shtatlı o\'qituvchisi. Xitoy va Hindistonda turizm bo\'yicha stajirovkalarni o\'tgan. Shuningdek, turizmda reportaj va oilaviy fotograf sifatida faoliyat yuritadi. Ilmiy maqolalar bilan xalqaro va mahalliy konferentsiyalarda ishtirok etadi.',
            ],
            [
                'name' => 'Mehruza Vafakulova',
                'image' => 'Mehruza Vafakulova.JPG',
                'bio_uz' => 'Turizm kafedrasi shtatlı o\'qituvchisi. Janubiy Koreya Sejong universitetida magistratura dasturini tamomlab tugatgan. Turkiya va Germaniyada xalqaro dasturlar orqali ilmiy stajirovkalarni o\'tgan. Biznes boshqaruvi va turizm menejment mutaxassisi.',
            ],
            [
                'name' => 'Firdavs Shokirov',
                'image' => 'Firdavs Shokirov.JPG',
                'bio_uz' => 'Turizm kafedrasi shtatlı o\'qituvchisi. Samarqand Iqtisodiyot va Servis institutida bakalavr va magistratura dasturlarini tamomlab tugatgan. Scopus indeksli jurnallarni o\'z ichiga olgan 10 dan ortiq ilmiy maqolalar bilan turli konferentsiyalarda ishtirok etadi.',
            ],
            [
                'name' => 'Mahliyo Umarova',
                'image' => 'Mahliyo Umarova.JPG',
                'bio_uz' => 'Turizm kafedrasi shtatlı o\'qituvchisi. Samarqand Iqtisodiyot va Servis institutida bakalavr va magistratura dasturlarini tamomlab tugatgan. Turizm va mehmondo\'stlik mutaxassisi. 5 dan ortiq ilmiy maqolalar bilan turli konferentsiyalarda ishtirok etadi.',
            ],
            [
                'name' => 'Sardor Kuvandikov',
                'image' => 'Sardor Kuvandikov.JPG',
                'bio_uz' => 'Turizm kafedrasi shtatlı o\'qituvchisi. Janubiy Koreya Suwon universitetida magistratura dasturini tamomlab tugatgan. Turizm va mehmondo\'stlik boshqaruvi mutaxassisi. Scopus indeksli jurnallarni o\'z ichiga olgan 10 ga yaqin ilmiy maqolalar bilan turli konferentsiyalarda ishtirok etadi.',
            ],
            [
                'name' => 'Muslima Amriddinova',
                'image' => 'Muslima Amriddinova.JPG',
                'bio_uz' => 'Turizm kafedrasi shtatlı o\'qituvchisi. Janubiy Koreya Woosong universitetida magistratura dasturini tamomlab tugatgan. 10 ga yaqin ilmiy maqolalar bilan xalqaro va mahalliy konferentsiyalarda ishtirok etadi. ORHUN almashinuv dasturi g\'olibi.',
            ],
            [
                'name' => 'Dilrabo Mardonova',
                'image' => 'Dilrabo Mardonova.JPG',
                'bio_uz' => 'Turizm kafedrasi yarim stavkali (0.5) o\'qituvchisi. Germaniyaning Konstanz universitetida DAAD dasturi doirasida ilmiy stajirovkani o\'tgan. 20 ga yaqin ilmiy maqolalar va tezislar bilan xalqaro va mahalliy konferentsiyalarda ishtirok etadi.',
            ],
            [
                'name' => 'Elena Salnikova',
                'image' => 'Elena Salnikova.JPG',
                'bio_uz' => 'Turizm kafedrasi shtatlı o\'qituvchisi. 10 dan ortiq ilmiy maqolalar bilan turli konferentsiyalarda ishtirok etadi. Xitoyda turizm bo\'yicha stajirovkada ishtirok etgan.',
            ],
            [
                'name' => 'Mohammad Shahparan',
                'image' => 'Mohammad Shahparan.JPG',
                'bio_uz' => 'Turizm kafedrasi shtatlı o\'qituvchisi. Ipak yo\'li turizm va madaniy meros xalqaro universitetida magistratura dasturini tamomlab tugatgan. Logistika mutaxassisi. Scopus indeksli jurnallarni o\'z ichiga olgan 10 dan ortiq ilmiy maqolalar bilan turli konferentsiyalarda ishtirok etadi.',
            ],
            [
                'name' => 'Rukhshona Rofeeva',
                'image' => 'Rukhshona Rofeeva.JPG',
                'bio_uz' => 'Turizm kafedrasi yarim stavkali (0.5) o\'qituvchisi. 5 dan ortiq ilmiy maqolalar bilan xalqaro va mahalliy konferentsiyalarda ishtirok etadi. Xitoy va Germaniyada turizm stajirovkalarini o\'tgan.',
            ],
            [
                'name' => 'Erkin Ghulomkhasanov',
                'image' => 'Erkin Ghulomkhasanov.JPG',
                'bio_uz' => 'Turizm kafedrasi shtatlı o\'qituvchisi. Turizm va mehmondo\'stlik boshqaruvi mutaxassisi. 10 ga yaqin ilmiy maqolalar bilan xalqaro va mahalliy jurnallarda ishtirok etadi. Ilgari Malika Prime Hotel va Diyora Hotel mehmonxonalarida ishlagan.',
            ],
            [
                'name' => 'Muhammadmurod Yorkulov',
                'image' => 'Muhammadmurod Yorkulov.JPG',
                'bio_uz' => 'Turizm kafedrasi yarim stavkali (0.25) o\'qituvchisi. O\'quv rejalashtirish va rivojlantirish bo\'limi boshlig\'i. 10 dan ortiq ilmiy maqolalar bilan turli konferentsiyalarda ishtirok etadi. Xitoy va Germaniyada turizm stajirovkalarini o\'tgan.',
            ],
            [
                'name' => 'Botir Rakhmatullaev',
                'image' => 'Botir Rakhmatullaev.JPG',
                'bio_uz' => 'Turizm kafedrasi shtatlı o\'qituvchisi. Shveytsariyaning Swiss Hotel Management School da magistratura dasturini tamomlab tugatgan. 5 ga yaqin ilmiy maqolalar bilan turli konferentsiyalarda ishtirok etadi.',
            ],
            [
                'name' => 'Abdurasul Akhmadjonov',
                'image' => 'Abdurasul Akhmadjonov.JPG',
                'bio_uz' => 'Marg\'ilon Turizm va madaniy meros texnikumida shtatlı o\'qituvchi. Farg\'ona davlat universitetida bakalavr dasturini tamomlab tugatgan. Turizm va mehmondo\'stlik mutaxassisi. Germaniya va Rossiyada Work & Travel dasturida ishtirok etgan.',
            ],
            [
                'name' => 'Dilnoza Muxidinova',
                'image' => 'Dilnoza Muxidinova.JPG',
                'bio_uz' => 'Buxoro Turizm va madaniy meros texnikumida yarim stavkali o\'qituvchi. Buxoro davlat universitetida magistratura dasturini tamomlab tugatgan. Turizm va mehmondo\'stlik mutaxassisi. Mehmonxona resepsionisti sifatida 2 yillik tajribaga ega.',
            ],
            [
                'name' => 'Iroda Sabirjanova',
                'image' => 'Iroda Sabirjanova.JPG',
                'bio_uz' => 'Xiva Turizm va madaniy meros texnikumida shtatlı o\'qituvchi. Urganch davlat universitetida bakalavr dasturini tamomlab tugatgan. O\'qituvchi sifatida 7 yillik tajribaga ega. Samarqand, Toshkent va Irlandiyada turizm bo\'yicha kurslardan o\'tgan.',
            ],
            [
                'name' => 'Shakhriyor Mirzayorov',
                'image' => 'Shakhriyor Mirzayorov.JPG',
                'bio_uz' => 'Andijon Turizm va madaniy meros texnikumida shtatlı o\'qituvchi. Ipak yo\'li turizm va madaniy meros xalqaro universitetida bakalavr dasturini tamomlab tugatgan. Turizm menejment mutaxassisi. Turkiyada mehmondo\'stlik sohasida tajribaga ega.',
            ],
            [
                'name' => 'Rano Shukurova',
                'image' => 'Rano Shukurova.JPG',
                'bio_uz' => 'Samarqand Turizm va madaniy meros texnikumining Turizm kafedrasi shtatlı o\'qituvchisi. Samarqand davlat chet tillar institutida bakalavr, Samarqand davlat universitetida magistratura dasturini tamomlab tugatgan. Ko\'plab konferentsiyalarda ishtirok etgan va 20 dan ortiq ilmiy maqolalar nashr etgan.',
            ]
        ];

        $order = 1;
        foreach ($teachers as $teacher) {
            // Check if image exists
            $imagePath = 'assets/images/teachers/' . $teacher['image'];
            $imageUrl = file_exists(public_path($imagePath)) ? $imagePath : null;

            CmsContent::create([
                'section' => 'teachers_list',
                'key' => 'teacher_' . $order . '_' . time(),
                'type' => 'textarea',
                'value_uz' => json_encode([
                    'name' => $teacher['name'],
                    'bio' => $teacher['bio_uz'],
                    'image' => $imageUrl
                ]),
                'value_en' => json_encode([
                    'name' => $teacher['name'],
                    'bio' => $teacher['bio_uz'], // Same as UZ for now
                    'image' => $imageUrl
                ]),
                'value_ru' => json_encode([
                    'name' => $teacher['name'],
                    'bio' => $teacher['bio_uz'], // Same as UZ for now
                    'image' => $imageUrl
                ]),
                'order' => $order
            ]);
            $order++;
        }

        return redirect()->route('cms.teachers.index')->with('success', count($teachers) . " ta o'qituvchi muvaffaqiyatli import qilindi!");
    }
}
