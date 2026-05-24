<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use App\Models\Specialty;
use App\Models\Subject;
use App\Models\StudentGroup;
use App\Models\Student;
use App\Models\User;
use App\Models\Teacher;
use App\Models\AcademicYear;
use App\Models\SpecialtySubject;
use App\Models\GroupSubject;
use Illuminate\Support\Facades\Hash;

class ComprehensiveDemoDataSeeder extends Seeder
{
    private $maleFirstNames = ['Abbos', 'Akmal', 'Aziz', 'Bekzod', 'Davron', 'Eldor', 'Farrux', 'Jahongir', 'Kamol', 'Mansur', 'Nodir', 'Otabek', 'Rashid', 'Sardor', 'Timur', 'Ulugbek', 'Vali', 'Zokir'];
    private $femaleFirstNames = ['Dilnoza', 'Gulnora', 'Kamola', 'Madina', 'Nigora', 'Ozoda', 'Sevara', 'Shaxnoza', 'Vasila', 'Zarina', 'Feruza', 'Hilola'];
    private $lastNames = ['Aliyev', 'Karimov', 'Rahimov', 'Toshmatov', 'Umarov', 'Yusupov', 'Abdullayev', 'Ismoilov', 'Mahmudov', 'Nazarov', 'Qodirov', 'Sotvoldiyev'];

    public function run(): void
    {
        $this->command->info('🚀 Starting comprehensive demo data seeding...');

        // Get or create current academic year
        $currentYear = AcademicYear::firstOrCreate(
            ['year' => '2024-2025'],
            [
                'is_current' => true,
                'start_date' => '2024-09-01',
                'end_date' => '2025-06-30'
            ]
        );
        $this->command->info("✅ Academic Year: {$currentYear->year}");

        // Get all specialties
        $specialties = Specialty::all();
        if ($specialties->isEmpty()) {
            $this->command->error('❌ No specialties found! Please create specialties first.');
            return;
        }
        $this->command->info("📚 Found {$specialties->count()} specialties");

        // Get subjects
        $subjects = Subject::all();
        if ($subjects->isEmpty()) {
            $this->command->error('❌ No subjects found! Please create subjects first.');
            return;
        }
        $this->command->info("📖 Found {$subjects->count()} subjects");

        // Get or create demo teacher
        $teacher = $this->createDemoTeacher();

        foreach ($specialties as $specialty) {
            $this->command->info("\n🎓 Processing specialty: {$specialty->name_uz}");

            // Create specialty curriculum (1st semester subjects)
            $this->createSpecialtyCurriculum($specialty, $subjects);

            // Create 2-3 groups for each specialty
            $groupsCount = rand(2, 3);
            for ($g = 1; $g <= $groupsCount; $g++) {
                $group = $this->createGroup($specialty, $currentYear, $g);

                // Create students for this group
                $studentsCount = rand(20, 30);
                $this->createStudents($group, $studentsCount);

                // Link subjects to group
                $this->linkSubjectsToGroup($group, $specialty, $currentYear, $teacher);
            }
        }

        $this->command->info("\n✅ Demo data seeding completed successfully!");
        $this->showStatistics();
    }

    private function createDemoTeacher()
    {
        $user = User::firstOrCreate(
            ['email' => 'teacher@demo.uz'],
            [
                'name' => 'Demo O\'qituvchi',
                'password' => Hash::make('password')
            ]
        );

        if (!$user->hasRole('teacher')) {
            $user->assignRole('teacher');
        }

        $department = \App\Models\Department::first();

        return Teacher::firstOrCreate(
            ['user_id' => $user->id],
            [
                'first_name' => 'Demo',
                'last_name' => 'O\'qituvchi',
                'middle_name' => 'Testovich',
                'email' => 'teacher@demo.uz',
                'phone' => '+998901234567',
                'department_id' => $department->id ?? 1,
                'degree' => 'Magistr',
                'position' => 'Katta o\'qituvchi'
            ]
        );
    }

    private function createSpecialtyCurriculum($specialty, $subjects)
    {
        // Take first 5 subjects for 1st semester
        $semesterSubjects = $subjects->take(5);

        foreach ($semesterSubjects as $index => $subject) {
            SpecialtySubject::firstOrCreate(
                [
                    'specialty_id' => $specialty->id,
                    'subject_id' => $subject->id,
                    'semester' => 1
                ],
                [
                    'course_year' => 1,
                    'is_required' => true,
                    'credits' => rand(3, 6),
                    'hours_total' => rand(60, 120)
                ]
            );
        }

        $this->command->info("  📝 Created curriculum with {$semesterSubjects->count()} subjects");
    }

    private function createGroup($specialty, $currentYear, $groupNumber)
    {
        $groupName = strtoupper(substr($specialty->code ?? 'GR', 0, 3)) . '-' . $specialty->id . $groupNumber . '0' . rand(1, 9);

        $group = StudentGroup::create([
            'name' => $groupName,
            'specialty_id' => $specialty->id,
            'academic_year' => $currentYear->year,
            'course' => 1,
            'is_active' => true
        ]);

        $this->command->info("  👥 Created group: {$group->name}");
        return $group;
    }

    private function createStudents($group, $count)
    {
        for ($i = 1; $i <= $count; $i++) {
            $gender = rand(0, 1) ? 'male' : 'female';
            $firstName = $gender == 'male'
                ? $this->maleFirstNames[array_rand($this->maleFirstNames)]
                : $this->femaleFirstNames[array_rand($this->femaleFirstNames)];
            $lastName = $this->lastNames[array_rand($this->lastNames)];
            $fullName = "{$lastName} {$firstName}";

            // Create user
            $email = strtolower($firstName . '.' . $lastName . '.' . $group->id . '.' . $i . '@student.uz');
            $user = User::create([
                'name' => $fullName,
                'email' => $email,
                'password' => Hash::make('password')
            ]);

            $user->assignRole('student');

            // Create student
            Student::create([
                'user_id' => $user->id,
                'group_id' => $group->id,
                'first_name' => $firstName,
                'last_name' => $lastName,
                'full_name' => $fullName,
                'student_id' => $group->name . '-' . str_pad($i, 3, '0', STR_PAD_LEFT),
                'email' => $email,
                'phone' => '+998' . rand(90, 99) . rand(1000000, 9999999),
                'gender' => $gender,
                'birth_date' => now()->subYears(rand(18, 22))->format('Y-m-d'),
                'admission_date' => now()->subMonths(rand(1, 12))->format('Y-m-d'),
                'status' => 'active',
                'profile_completed' => true
            ]);
        }

        $this->command->info("    ✅ Created {$count} students");
    }

    private function linkSubjectsToGroup($group, $specialty, $currentYear, $teacher)
    {
        $specialtySubjects = SpecialtySubject::where('specialty_id', $specialty->id)
            ->where('semester', 1)
            ->get();

        $linkedCount = 0;
        foreach ($specialtySubjects as $ss) {
            GroupSubject::firstOrCreate(
                [
                    'student_group_id' => $group->id,
                    'subject_id' => $ss->subject_id,
                    'academic_year_id' => $currentYear->id,
                    'semester' => 1
                ],
                [
                    'teacher_id' => rand(0, 1) ? $teacher->id : null, // 50% chance vakant
                    'is_active' => true
                ]
            );
            $linkedCount++;
        }

        $this->command->info("    🔗 Linked {$linkedCount} subjects to group");
    }

    private function showStatistics()
    {
        $stats = [
            'Specialties' => Specialty::count(),
            'Student Groups' => StudentGroup::count(),
            'Students' => Student::count(),
            'Teachers' => Teacher::count(),
            'Subjects' => Subject::count(),
            'Specialty Curricula' => SpecialtySubject::count(),
            'Group Subjects' => GroupSubject::count(),
        ];

        $this->command->info("\n📊 === STATISTICS ===");
        foreach ($stats as $label => $count) {
            $this->command->info("   {$label}: {$count}");
        }
    }
}
