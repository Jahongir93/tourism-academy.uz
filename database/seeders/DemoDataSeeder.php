<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Hash;
use Carbon\Carbon;

class DemoDataSeeder extends Seeder
{
    public function run(): void
    {
        $this->command->info('Fakultetlar yaratilmoqda...');
        $faculties = $this->createFaculties();

        $this->command->info('Kafedralar yaratilmoqda...');
        $departments = $this->createDepartments($faculties);

        $this->command->info('Ta\'lim yo\'nalishlari yaratilmoqda...');
        $specialties = $this->createSpecialties($faculties, $departments);

        $this->command->info('Fanlar yaratilmoqda...');
        $subjects = $this->createSubjects();

        $this->command->info('O\'qituvchilar yaratilmoqda...');
        $teachers = $this->createTeachers($departments);

        $this->command->info('O\'quv yili yaratilmoqda...');
        $this->createAcademicYear();

        $this->command->info('Talaba guruhlari yaratilmoqda...');
        $studentGroups = $this->createStudentGroups($faculties, $specialties);

        $this->command->info('Talabalar yaratilmoqda...');
        $this->createStudents($specialties, $studentGroups);

        $this->command->info('Educational programs yaratilmoqda...');
        $programs = $this->createEducationalPrograms($specialties);

        $this->command->info('O\'quv rejalar yaratilmoqda...');
        $this->createCurricula($programs, $subjects);

        $this->command->info('✅ Barcha demo ma\'lumotlar muvaffaqiyatli yaratildi!');
    }

    private function createFaculties(): array
    {
        $faculties = [
            [
                'name_uz' => 'Turizm va Mehmonxona Biznesi fakulteti',
                'name_ru' => 'Факультет туризма и гостиничного бизнеса',
                'name_en' => 'Faculty of Tourism and Hospitality',
                'code' => 'TMHB',
                'short_name' => 'TMHB',
                'abbreviation' => 'TMHB',
                'phone' => '+998 66 234-56-78',
                'email' => 'tmhb@tourism.uz',
                'room' => '101',
                'website' => 'https://tourism.uz/tmhb',
                'established_date' => '2020-09-01',
                'student_capacity' => 500,
                'teacher_capacity' => 50,
                'state_funded_places' => 100,
                'order_number' => 1,
                'is_active' => true,
                'created_at' => now(),
                'updated_at' => now(),
            ],
            [
                'name_uz' => 'Xalqaro Turizm fakulteti',
                'name_ru' => 'Факультет международного туризма',
                'name_en' => 'Faculty of International Tourism',
                'code' => 'XT',
                'short_name' => 'Xalqaro Turizm',
                'abbreviation' => 'XT',
                'phone' => '+998 66 234-56-79',
                'email' => 'international@tourism.uz',
                'room' => '201',
                'website' => 'https://tourism.uz/international',
                'established_date' => '2020-09-01',
                'student_capacity' => 400,
                'teacher_capacity' => 40,
                'state_funded_places' => 80,
                'order_number' => 2,
                'is_active' => true,
                'created_at' => now(),
                'updated_at' => now(),
            ],
            [
                'name_uz' => 'Ekskursiya xizmati va Gidlik fakulteti',
                'name_ru' => 'Факультет экскурсионных услуг и гидов',
                'name_en' => 'Faculty of Tour Guide Services',
                'code' => 'EXG',
                'short_name' => 'Gidlik',
                'abbreviation' => 'EXG',
                'phone' => '+998 66 234-56-80',
                'email' => 'guide@tourism.uz',
                'room' => '301',
                'website' => 'https://tourism.uz/guide',
                'established_date' => '2021-09-01',
                'student_capacity' => 300,
                'teacher_capacity' => 30,
                'state_funded_places' => 60,
                'order_number' => 3,
                'is_active' => true,
                'created_at' => now(),
                'updated_at' => now(),
            ],
            [
                'name_uz' => 'Restoranchilik va Oshpazlik san\'ati fakulteti',
                'name_ru' => 'Факультет ресторанного дела и кулинарного искусства',
                'name_en' => 'Faculty of Restaurant and Culinary Arts',
                'code' => 'ROAS',
                'short_name' => 'Oshpazlik',
                'abbreviation' => 'ROAS',
                'phone' => '+998 66 234-56-81',
                'email' => 'culinary@tourism.uz',
                'room' => '401',
                'website' => 'https://tourism.uz/culinary',
                'established_date' => '2021-09-01',
                'student_capacity' => 350,
                'teacher_capacity' => 35,
                'state_funded_places' => 70,
                'order_number' => 4,
                'is_active' => true,
                'created_at' => now(),
                'updated_at' => now(),
            ],
            [
                'name_uz' => 'Turizm menejmenti fakulteti',
                'name_ru' => 'Факультет туристического менеджмента',
                'name_en' => 'Faculty of Tourism Management',
                'code' => 'TM',
                'short_name' => 'Menedjment',
                'abbreviation' => 'TM',
                'phone' => '+998 66 234-56-82',
                'email' => 'management@tourism.uz',
                'room' => '501',
                'website' => 'https://tourism.uz/management',
                'established_date' => '2022-09-01',
                'student_capacity' => 450,
                'teacher_capacity' => 45,
                'state_funded_places' => 90,
                'order_number' => 5,
                'is_active' => true,
                'created_at' => now(),
                'updated_at' => now(),
            ],
        ];

        foreach ($faculties as $faculty) {
            DB::table('faculties')->insert($faculty);
        }

        return DB::table('faculties')->get()->toArray();
    }

    private function createDepartments(array $faculties): array
    {
        $departments = [
            [
                'name_uz' => 'Mehmonxona xizmati kafedrasi',
                'name_ru' => 'Кафедра гостиничного сервиса',
                'name_en' => 'Department of Hotel Service',
                'name' => 'Mehmonxona xizmati kafedrasi',
                'code' => 'MXK',
                'short_name' => 'MXK',
                'type' => 'ixtisoslik',
                'faculty_id' => $faculties[0]->id,
                'phone' => '+998 66 234-50-01',
                'email' => 'hotel@tourism.uz',
                'room_number' => '102',
                'established_date' => '2020-09-01',
                'staff_capacity' => 15,
                'is_active' => true,
                'created_at' => now(),
                'updated_at' => now(),
            ],
            [
                'name_uz' => 'Turizm tashkiloti kafedrasi',
                'name_ru' => 'Кафедра организации туризма',
                'name_en' => 'Department of Tourism Organization',
                'name' => 'Turizm tashkiloti kafedrasi',
                'code' => 'TTK',
                'short_name' => 'TTK',
                'type' => 'ixtisoslik',
                'faculty_id' => $faculties[1]->id,
                'phone' => '+998 66 234-50-02',
                'email' => 'organization@tourism.uz',
                'room_number' => '202',
                'established_date' => '2020-09-01',
                'staff_capacity' => 12,
                'is_active' => true,
                'created_at' => now(),
                'updated_at' => now(),
            ],
            [
                'name_uz' => 'Ekskursiya ishi kafedrasi',
                'name_ru' => 'Кафедра экскурсионного дела',
                'name_en' => 'Department of Excursion Business',
                'name' => 'Ekskursiya ishi kafedrasi',
                'code' => 'EIK',
                'short_name' => 'EIK',
                'type' => 'ixtisoslik',
                'faculty_id' => $faculties[2]->id,
                'phone' => '+998 66 234-50-03',
                'email' => 'excursion@tourism.uz',
                'room_number' => '302',
                'established_date' => '2021-09-01',
                'staff_capacity' => 10,
                'is_active' => true,
                'created_at' => now(),
                'updated_at' => now(),
            ],
            [
                'name_uz' => 'Oshpazlik san\'ati kafedrasi',
                'name_ru' => 'Кафедра кулинарного искусства',
                'name_en' => 'Department of Culinary Arts',
                'name' => 'Oshpazlik san\'ati kafedrasi',
                'code' => 'OSK',
                'short_name' => 'OSK',
                'type' => 'ixtisoslik',
                'faculty_id' => $faculties[3]->id,
                'phone' => '+998 66 234-50-04',
                'email' => 'cooking@tourism.uz',
                'room_number' => '402',
                'established_date' => '2021-09-01',
                'staff_capacity' => 13,
                'is_active' => true,
                'created_at' => now(),
                'updated_at' => now(),
            ],
            [
                'name_uz' => 'Turizm menejmenti kafedrasi',
                'name_ru' => 'Кафедра туристического менеджмента',
                'name_en' => 'Department of Tourism Management',
                'name' => 'Turizm menejmenti kafedrasi',
                'code' => 'TMK',
                'short_name' => 'TMK',
                'type' => 'ixtisoslik',
                'faculty_id' => $faculties[4]->id,
                'phone' => '+998 66 234-50-05',
                'email' => 'management@tourism.uz',
                'room_number' => '502',
                'established_date' => '2022-09-01',
                'staff_capacity' => 14,
                'is_active' => true,
                'created_at' => now(),
                'updated_at' => now(),
            ],
        ];

        foreach ($departments as $department) {
            DB::table('departments')->insert($department);
        }

        return DB::table('departments')->get()->toArray();
    }

    private function createSpecialties(array $faculties, array $departments): array
    {
        $specialties = [
            [
                'faculty_id' => $faculties[0]->id,
                'department_id' => $departments[0]->id,
                'code' => '5230100',
                'name_uz' => 'Mehmonxona xizmati',
                'name_ru' => 'Гостиничный сервис',
                'name_en' => 'Hotel Service',
                'direction_code' => '60230100',
                'degree' => 'bakalavr',
                'education_form' => 'kunduzgi',
                'education_type' => 'shartnoma',
                'duration_years' => 4,
                'credits_required' => 240,
                'tuition_fee' => 8000000.00,
                'language' => 'uz',
                'description' => 'Mehmonxona biznesini boshqarish va xizmat ko\'rsatish',
                'is_active' => true,
                'created_at' => now(),
                'updated_at' => now(),
            ],
            [
                'faculty_id' => $faculties[1]->id,
                'department_id' => $departments[1]->id,
                'code' => '5230200',
                'name_uz' => 'Turizm (faoliyat turlari bo\'yicha)',
                'name_ru' => 'Туризм (по видам деятельности)',
                'name_en' => 'Tourism (by activity types)',
                'direction_code' => '60230200',
                'degree' => 'bakalavr',
                'education_form' => 'kunduzgi',
                'education_type' => 'byudjet',
                'duration_years' => 4,
                'credits_required' => 240,
                'tuition_fee' => 0.00,
                'language' => 'uz',
                'description' => 'Xalqaro va milliy turizm sohasida faoliyat yuritish',
                'is_active' => true,
                'created_at' => now(),
                'updated_at' => now(),
            ],
            [
                'faculty_id' => $faculties[2]->id,
                'department_id' => $departments[2]->id,
                'code' => '5230300',
                'name_uz' => 'Turistik yo\'nalish bo\'yicha ekskursiya faoliyati',
                'name_ru' => 'Экскурсионная деятельность по туристическим направлениям',
                'name_en' => 'Tour Guide Activities',
                'direction_code' => '60230300',
                'degree' => 'bakalavr',
                'education_form' => 'kunduzgi',
                'education_type' => 'shartnoma',
                'duration_years' => 4,
                'credits_required' => 240,
                'tuition_fee' => 7500000.00,
                'language' => 'uz',
                'description' => 'Professional ekskursiya xizmatlari ko\'rsatish',
                'is_active' => true,
                'created_at' => now(),
                'updated_at' => now(),
            ],
            [
                'faculty_id' => $faculties[3]->id,
                'department_id' => $departments[3]->id,
                'code' => '5811100',
                'name_uz' => 'Oshpazlik ishi va xizmat ko\'rsatish',
                'name_ru' => 'Кулинарное дело и сервис',
                'name_en' => 'Culinary Arts and Service',
                'direction_code' => '60811100',
                'degree' => 'bakalavr',
                'education_form' => 'kunduzgi',
                'education_type' => 'shartnoma',
                'duration_years' => 4,
                'credits_required' => 240,
                'tuition_fee' => 9000000.00,
                'language' => 'uz',
                'description' => 'Milliy va xalqaro oshpazlik san\'ati',
                'is_active' => true,
                'created_at' => now(),
                'updated_at' => now(),
            ],
            [
                'faculty_id' => $faculties[4]->id,
                'department_id' => $departments[4]->id,
                'code' => '5230400',
                'name_uz' => 'Turizm menejmenti',
                'name_ru' => 'Туристический менеджмент',
                'name_en' => 'Tourism Management',
                'direction_code' => '60230400',
                'degree' => 'bakalavr',
                'education_form' => 'kunduzgi',
                'education_type' => 'byudjet',
                'duration_years' => 4,
                'credits_required' => 240,
                'tuition_fee' => 0.00,
                'language' => 'uz',
                'description' => 'Turizm sohasida menedjment va marketing',
                'is_active' => true,
                'created_at' => now(),
                'updated_at' => now(),
            ],
        ];

        foreach ($specialties as $specialty) {
            DB::table('specialties')->insert($specialty);
        }

        return DB::table('specialties')->get()->toArray();
    }

    private function createSubjects(): array
    {
        $subjects = [
            [
                'code' => 'UMK-101',
                'name_uz' => 'Mehmonxona biznesi asoslari',
                'name_ru' => 'Основы гостиничного бизнеса',
                'name_en' => 'Fundamentals of Hotel Business',
                'credits' => 6,
                'total_hours' => 180,
                'subject_type' => 'mutaxassislik',
                'is_active' => true,
                'created_at' => now(),
                'updated_at' => now(),
            ],
            [
                'code' => 'UMK-102',
                'name_uz' => 'Turizm geografiyasi',
                'name_ru' => 'География туризма',
                'name_en' => 'Tourism Geography',
                'credits' => 5,
                'total_hours' => 150,
                'subject_type' => 'mutaxassislik',
                'is_active' => true,
                'created_at' => now(),
                'updated_at' => now(),
            ],
            [
                'code' => 'UMK-103',
                'name_uz' => 'Ekskursiya metodikasi',
                'name_ru' => 'Методика экскурсий',
                'name_en' => 'Excursion Methodology',
                'credits' => 5,
                'total_hours' => 150,
                'subject_type' => 'mutaxassislik',
                'is_active' => true,
                'created_at' => now(),
                'updated_at' => now(),
            ],
            [
                'code' => 'UMK-104',
                'name_uz' => 'Oshxona texnologiyasi',
                'name_ru' => 'Технология кухни',
                'name_en' => 'Kitchen Technology',
                'credits' => 7,
                'total_hours' => 210,
                'subject_type' => 'mutaxassislik',
                'is_active' => true,
                'created_at' => now(),
                'updated_at' => now(),
            ],
            [
                'code' => 'UMK-105',
                'name_uz' => 'Turizm menejmenti',
                'name_ru' => 'Менеджмент туризма',
                'name_en' => 'Tourism Management',
                'credits' => 6,
                'total_hours' => 180,
                'subject_type' => 'mutaxassislik',
                'is_active' => true,
                'created_at' => now(),
                'updated_at' => now(),
            ],
            [
                'code' => 'UMK-106',
                'name_uz' => 'Xizmat ko\'rsatish madaniyati',
                'name_ru' => 'Культура обслуживания',
                'name_en' => 'Service Culture',
                'credits' => 4,
                'total_hours' => 120,
                'subject_type' => 'umumkasbiy',
                'is_active' => true,
                'created_at' => now(),
                'updated_at' => now(),
            ],
            [
                'code' => 'UMK-107',
                'name_uz' => 'Ingliz tili (turizm)',
                'name_ru' => 'Английский язык (туризм)',
                'name_en' => 'English (Tourism)',
                'credits' => 8,
                'total_hours' => 240,
                'subject_type' => 'majburiy',
                'is_active' => true,
                'created_at' => now(),
                'updated_at' => now(),
            ],
            [
                'code' => 'UMK-108',
                'name_uz' => 'Turizm marketingi',
                'name_ru' => 'Туристический маркетинг',
                'name_en' => 'Tourism Marketing',
                'credits' => 5,
                'total_hours' => 150,
                'subject_type' => 'mutaxassislik',
                'is_active' => true,
                'created_at' => now(),
                'updated_at' => now(),
            ],
            [
                'code' => 'UMK-109',
                'name_uz' => 'O\'zbekiston tarixi va madaniyati',
                'name_ru' => 'История и культура Узбекистана',
                'name_en' => 'History and Culture of Uzbekistan',
                'credits' => 4,
                'total_hours' => 120,
                'subject_type' => 'umumkasbiy',
                'is_active' => true,
                'created_at' => now(),
                'updated_at' => now(),
            ],
            [
                'code' => 'UMK-110',
                'name_uz' => 'Restoranchilik biznesi',
                'name_ru' => 'Ресторанный бизнес',
                'name_en' => 'Restaurant Business',
                'credits' => 6,
                'total_hours' => 180,
                'subject_type' => 'mutaxassislik',
                'is_active' => true,
                'created_at' => now(),
                'updated_at' => now(),
            ],
        ];

        foreach ($subjects as $subject) {
            DB::table('subjects')->insert($subject);
        }

        return DB::table('subjects')->get()->toArray();
    }

    private function createStudentGroups(array $faculties, array $specialties): array
    {
        $departments = DB::table('departments')->get()->toArray();

        foreach ($departments as $index => $department) {
            $groupCode = 'G-' . substr($department->code, 0, 3) . '-1';

            DB::table('groups')->insert([
                'name' => $department->name_uz . ' - 1-guruh',
                'code' => $groupCode,
                'department_id' => $department->id,
                'course' => 1,
                'students_count' => 4,
                'education_type' => 'kunduzgi',
                'created_at' => now(),
                'updated_at' => now(),
            ]);
        }

        return DB::table('groups')->orderBy('id', 'desc')->limit(5)->get()->toArray();
    }

    private function createStudents(array $specialties, array $studentGroups): void
    {
        $students = [
            ['first_name' => 'Aziz', 'last_name' => 'Rahimov', 'middle_name' => 'Anvarovich', 'specialty_idx' => 0],
            ['first_name' => 'Dilshod', 'last_name' => 'Karimov', 'middle_name' => 'Akmalovich', 'specialty_idx' => 0],
            ['first_name' => 'Shohruh', 'last_name' => 'Tursunov', 'middle_name' => 'Rustamovich', 'specialty_idx' => 1],
            ['first_name' => 'Nodira', 'last_name' => 'Ahmedova', 'middle_name' => 'Davronovna', 'specialty_idx' => 1],
            ['first_name' => 'Malika', 'last_name' => 'Yusupova', 'middle_name' => 'Shavkatovna', 'specialty_idx' => 1],
            ['first_name' => 'Jasur', 'last_name' => 'Abdullayev', 'middle_name' => 'Hamidovich', 'specialty_idx' => 2],
            ['first_name' => 'Zarina', 'last_name' => 'Mirzayeva', 'middle_name' => 'Alisher qizi', 'specialty_idx' => 2],
            ['first_name' => 'Botir', 'last_name' => 'Sharipov', 'middle_name' => 'Olimovich', 'specialty_idx' => 2],
            ['first_name' => 'Dildora', 'last_name' => 'Ibragimova', 'middle_name' => 'Ulugbek qizi', 'specialty_idx' => 3],
            ['first_name' => 'Farida', 'last_name' => 'Nazarova', 'middle_name' => 'Baxodir qizi', 'specialty_idx' => 3],
            ['first_name' => 'Jamshid', 'last_name' => 'Ismoilov', 'middle_name' => 'Azimovich', 'specialty_idx' => 3],
            ['first_name' => 'Kamola', 'last_name' => 'Rahmanova', 'middle_name' => 'Mansur qizi', 'specialty_idx' => 3],
            ['first_name' => 'Laziz', 'last_name' => 'Umarov', 'middle_name' => 'Shavkatovich', 'specialty_idx' => 4],
            ['first_name' => 'Madina', 'last_name' => 'Hasanova', 'middle_name' => 'Karim qizi', 'specialty_idx' => 4],
            ['first_name' => 'Nodir', 'last_name' => 'Safarov', 'middle_name' => 'Toxirovich', 'specialty_idx' => 4],
            ['first_name' => 'Ozoda', 'last_name' => 'Muhammadova', 'middle_name' => 'Jamshid qizi', 'specialty_idx' => 4],
            ['first_name' => 'Rustam', 'last_name' => 'Alimardonov', 'middle_name' => 'Bobur o\'g\'li', 'specialty_idx' => 0],
            ['first_name' => 'Sevinch', 'last_name' => 'Toshmatova', 'middle_name' => 'Akbar qizi', 'specialty_idx' => 1],
            ['first_name' => 'Ulug\'bek', 'last_name' => 'Xolmatov', 'middle_name' => 'Dilshod o\'g\'li', 'specialty_idx' => 2],
            ['first_name' => 'Yulduz', 'last_name' => 'Qodirova', 'middle_name' => 'Sardor qizi', 'specialty_idx' => 3],
        ];

        foreach ($students as $index => $studentData) {
            $specialty = $specialties[$studentData['specialty_idx']];
            $group = $studentGroups[$studentData['specialty_idx']];
            $studentId = 'STU' . date('Y') . str_pad($index + 1, 4, '0', STR_PAD_LEFT);
            $email = strtolower($studentData['first_name'] . '.' . $studentData['last_name']) . '@student.tourism.uz';
            $phone = '+998 9' . rand(0, 9) . ' ' . rand(100, 999) . '-' . rand(10, 99) . '-' . rand(10, 99);

            // Create user
            $userId = DB::table('users')->insertGetId([
                'name' => $studentData['first_name'] . ' ' . $studentData['last_name'],
                'email' => $email,
                'phone' => $phone,
                'password' => Hash::make('password123'),
                'user_type' => 'uzbek',
                'email_verified_at' => now(),
                'created_at' => now(),
                'updated_at' => now(),
            ]);

            // Assign student role
            DB::table('model_has_roles')->insert([
                'role_id' => 4, // Student role ID
                'model_type' => 'App\Models\User',
                'model_id' => $userId,
            ]);

            // Create student record
            DB::table('students')->insert([
                'user_id' => $userId,
                'first_name' => $studentData['first_name'],
                'last_name' => $studentData['last_name'],
                'middle_name' => $studentData['middle_name'],
                'student_id' => $studentId,
                'email' => $email,
                'phone' => $phone,
                'group_id' => $group->id,
                'birth_date' => Carbon::now()->subYears(rand(18, 22))->format('Y-m-d'),
                'gender' => in_array($studentData['first_name'], ['Nodira', 'Malika', 'Zarina', 'Dildora', 'Farida', 'Kamola', 'Madina', 'Ozoda', 'Sevinch', 'Yulduz']) ? 'female' : 'male',
                'status' => 'active',
                'admission_date' => Carbon::now()->subMonths(rand(1, 3))->format('Y-m-d'),
                'created_at' => now(),
                'updated_at' => now(),
            ]);
        }
    }

    private function createEducationalPrograms(array $specialties): array
    {
        $programs = [];
        foreach ($specialties as $specialty) {
            $programId = DB::table('educational_programs')->insertGetId([
                'code' => $specialty->code,
                'name_uz' => $specialty->name_uz,
                'name_ru' => $specialty->name_ru,
                'name_en' => $specialty->name_en,
                'level' => $specialty->degree === 'bakalavr' ? 'bakalavriat' : $specialty->degree,
                'education_form' => $specialty->education_form,
                'duration_years' => $specialty->duration_years,
                'total_credits' => $specialty->credits_required,
                'faculty_id' => $specialty->faculty_id,
                'department_id' => $specialty->department_id,
                'qualification' => $specialty->name_uz,
                'description' => $specialty->description ?? '',
                'active' => true,
                'created_at' => now(),
                'updated_at' => now(),
            ]);

            $programs[] = (object)[
                'id' => $programId,
                'specialty_id' => $specialty->id,
                'code' => $specialty->code,
            ];
        }

        return $programs;
    }

    private function createCurricula(array $programs, array $subjects): void
    {
        $academicYear = '2024-2025';
        $currentYear = date('Y');

        // Subject distribution across semesters (1-8)
        $subjectDistribution = [
            1 => [0, 6, 8], // Semester 1: UMK-101, UMK-107, UMK-109
            2 => [1, 7],    // Semester 2: UMK-102, UMK-108
            3 => [2, 4],    // Semester 3: UMK-103, UMK-105
            4 => [3, 5],    // Semester 4: UMK-104, UMK-106
            5 => [9],       // Semester 5: UMK-110
            6 => [],        // Semester 6: empty for now
            7 => [],        // Semester 7: empty for now
            8 => [],        // Semester 8: empty for now
        ];

        foreach ($programs as $program) {
            $sequenceNumber = 1;

            foreach ($subjectDistribution as $semester => $subjectIndices) {
                foreach ($subjectIndices as $subjectIdx) {
                    $subject = $subjects[$subjectIdx];

                    // Calculate hours distribution
                    $totalHours = $subject->total_hours;
                    $lectureHours = (int)($totalHours * 0.4);
                    $practiceHours = (int)($totalHours * 0.3);
                    $independentHours = $totalHours - $lectureHours - $practiceHours;

                    // Map subject types to curriculum subject types
                    $curriculumSubjectType = match($subject->subject_type) {
                        'majburiy' => 'majburiy',
                        'mutaxassislik' => 'majburiy',
                        'umumkasbiy' => 'majburiy',
                        default => 'majburiy'
                    };

                    DB::table('curricula')->insert([
                        'program_id' => $program->id,
                        'subject_id' => $subject->id,
                        'specialty_id' => $program->specialty_id,
                        'academic_year' => $academicYear,
                        'semester_number' => $semester,
                        'sequence_number' => $sequenceNumber++,
                        'credits' => $subject->credits,
                        'total_hours' => $totalHours,
                        'lecture_hours' => $lectureHours,
                        'practice_hours' => $practiceHours,
                        'lab_hours' => 0,
                        'seminar_hours' => 0,
                        'independent_hours' => $independentHours,
                        'control_type' => $semester % 2 == 0 ? 'imtihon' : 'sinov',
                        'is_optional' => false,
                        'subject_type' => $curriculumSubjectType,
                        'active' => true,
                        'is_approved' => true,
                        'semester' => $semester,
                        'year' => (int)ceil($semester / 2),
                        'hours_per_week' => (int)($totalHours / 15), // 15 weeks per semester
                        'created_at' => now(),
                        'updated_at' => now(),
                    ]);
                }
            }
        }
    }

    private function createTeachers(array $departments): array
    {
        $teachers = [
            [
                'first_name' => 'Akmal',
                'last_name' => 'Rahmonov',
                'middle_name' => 'Abdullayevich',
                'department_idx' => 0,
                'degree' => 'phd',
                'position' => 'Professor'
            ],
            [
                'first_name' => 'Dilnoza',
                'last_name' => 'Karimova',
                'middle_name' => 'Shavkatovna',
                'department_idx' => 0,
                'degree' => 'magistr',
                'position' => 'Katta o\'qituvchi'
            ],
            [
                'first_name' => 'Sardor',
                'last_name' => 'Tursunov',
                'middle_name' => 'Rustamovich',
                'department_idx' => 1,
                'degree' => 'phd',
                'position' => 'Dotsent'
            ],
            [
                'first_name' => 'Malika',
                'last_name' => 'Yusupova',
                'middle_name' => 'Baxodirovna',
                'department_idx' => 1,
                'degree' => 'magistr',
                'position' => 'O\'qituvchi'
            ],
            [
                'first_name' => 'Jamshid',
                'last_name' => 'Alimov',
                'middle_name' => 'Kamolovich',
                'department_idx' => 2,
                'degree' => 'dsc',
                'position' => 'Professor'
            ],
            [
                'first_name' => 'Zilola',
                'last_name' => 'Mirzayeva',
                'middle_name' => 'Akramovna',
                'department_idx' => 2,
                'degree' => 'magistr',
                'position' => 'Katta o\'qituvchi'
            ],
            [
                'first_name' => 'Botir',
                'last_name' => 'Usmonov',
                'middle_name' => 'Abbosovich',
                'department_idx' => 3,
                'degree' => 'magistr',
                'position' => 'O\'qituvchi'
            ],
            [
                'first_name' => 'Sevara',
                'last_name' => 'Xolmatova',
                'middle_name' => 'Dilshodovna',
                'department_idx' => 3,
                'degree' => 'phd',
                'position' => 'Dotsent'
            ],
            [
                'first_name' => 'Farxod',
                'last_name' => 'Abdullayev',
                'middle_name' => 'Sobirovich',
                'department_idx' => 4,
                'degree' => 'phd',
                'position' => 'Professor'
            ],
            [
                'first_name' => 'Madina',
                'last_name' => 'Qodirova',
                'middle_name' => 'Rustamovna',
                'department_idx' => 4,
                'degree' => 'magistr',
                'position' => 'Katta o\'qituvchi'
            ],
        ];

        foreach ($teachers as $teacherData) {
            $department = $departments[$teacherData['department_idx']];
            $email = strtolower($teacherData['first_name'] . '.' . $teacherData['last_name']) . '@tourism.uz';
            $phone = '+998 9' . rand(0, 9) . ' ' . rand(100, 999) . '-' . rand(10, 99) . '-' . rand(10, 99);

            // Create user for teacher
            $userId = DB::table('users')->insertGetId([
                'name' => $teacherData['first_name'] . ' ' . $teacherData['last_name'],
                'email' => $email,
                'phone' => $phone,
                'password' => Hash::make('teacher123'),
                'user_type' => 'uzbek',
                'email_verified_at' => now(),
                'created_at' => now(),
                'updated_at' => now(),
            ]);

            // Assign teacher role (role_id: 3)
            DB::table('model_has_roles')->insert([
                'role_id' => 3,
                'model_type' => 'App\\Models\\User',
                'model_id' => $userId,
            ]);

            // Create teacher record
            DB::table('teachers')->insert([
                'first_name' => $teacherData['first_name'],
                'last_name' => $teacherData['last_name'],
                'middle_name' => $teacherData['middle_name'],
                'email' => $email,
                'phone' => $phone,
                'department_id' => $department->id,
                'degree' => $teacherData['degree'],
                'position' => $teacherData['position'],
                'user_id' => $userId,
                'created_at' => now(),
                'updated_at' => now(),
            ]);
        }

        return DB::table('teachers')->get()->toArray();
    }

    private function createAcademicYear(): void
    {
        // Check if academic year already exists
        $existing = DB::table('academic_years')->where('name', '2024-2025')->first();

        if (!$existing) {
            DB::table('academic_years')->insert([
                'name' => '2024-2025',
                'start_date' => '2024-09-01',
                'end_date' => '2025-06-30',
                'is_current' => true,
                'created_at' => now(),
                'updated_at' => now(),
            ]);
        }
    }
}
