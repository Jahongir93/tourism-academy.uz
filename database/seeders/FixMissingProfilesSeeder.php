<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use App\Models\User;
use App\Models\Teacher;
use App\Models\Student;
use App\Models\Department;
use App\Models\StudentGroup;
use Illuminate\Support\Facades\DB;

class FixMissingProfilesSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        $this->command->info('🔍 Checking for missing profiles...');

        // Fix teachers
        $this->fixTeacherProfiles();

        // Fix students
        $this->fixStudentProfiles();

        $this->command->info('✅ All profiles have been created!');
    }

    /**
     * Create Teacher profiles for users with Teacher role
     */
    private function fixTeacherProfiles()
    {
        // Get all users with Teacher role but no Teacher profile
        $teacherUsers = User::role(['Teacher', 'teacher'])
            ->whereDoesntHave('teacher')
            ->get();

        if ($teacherUsers->isEmpty()) {
            $this->command->info('✓ All teachers already have profiles');
            return;
        }

        $this->command->info("📝 Creating profiles for {$teacherUsers->count()} teachers...");

        // Get first department or create a default one
        $department = Department::first();
        if (!$department) {
            $department = Department::create([
                'name' => 'Umumiy bo\'lim',
                'code' => 'GEN',
                'faculty_id' => 1
            ]);
        }

        foreach ($teacherUsers as $user) {
            // Parse name into parts
            $nameParts = explode(' ', $user->name);
            $firstName = $nameParts[0] ?? 'Unknown';
            $lastName = $nameParts[1] ?? '';
            $middleName = $nameParts[2] ?? '';

            Teacher::create([
                'user_id' => $user->id,
                'first_name' => $firstName,
                'last_name' => $lastName,
                'middle_name' => $middleName,
                'email' => $user->email,
                'phone' => $user->phone ?? '+998000000000',
                'department_id' => $department->id,
                'degree' => 'bakalavr',
                'position' => 'O\'qituvchi'
            ]);

            $this->command->info("  ✓ Created profile for: {$user->name}");
        }

        $this->command->info("✅ Created {$teacherUsers->count()} teacher profiles");
    }

    /**
     * Create Student profiles for users with Student role
     */
    private function fixStudentProfiles()
    {
        // Get all users with Student role but no Student profile
        $studentUsers = User::role(['Student', 'student'])
            ->whereDoesntHave('student')
            ->get();

        if ($studentUsers->isEmpty()) {
            $this->command->info('✓ All students already have profiles');
            return;
        }

        $this->command->info("📝 Creating profiles for {$studentUsers->count()} students...");

        // Get first group or create a default one
        $group = StudentGroup::first();
        if (!$group) {
            $group = StudentGroup::create([
                'name' => 'DEFAULT-101',
                'faculty_id' => 1,
                'course' => 1,
                'semester' => 1,
                'is_active' => true
            ]);
        }

        $counter = Student::max('id') + 1;

        foreach ($studentUsers as $user) {
            // Parse name into parts
            $nameParts = explode(' ', $user->name);
            $firstName = $nameParts[0] ?? 'Unknown';
            $lastName = implode(' ', array_slice($nameParts, 1)) ?: 'Student';

            Student::create([
                'user_id' => $user->id,
                'group_id' => $group->id,
                'student_id' => 'STD' . date('Y') . str_pad($counter++, 4, '0', STR_PAD_LEFT),
                'full_name' => $user->name,
                'first_name' => $firstName,
                'last_name' => $lastName,
                'email' => $user->email,
                'phone' => $user->phone ?? '+998000000000',
                'birth_date' => $user->birth_date ?? '2000-01-01',
                'gender' => $user->gender ?? 'male',
                'passport_series' => $user->passport_series ?? 'AA',
                'passport_number' => $user->passport_number ?? '0000000',
                'address' => $user->address ?? 'Not specified',
                'faculty_id' => 1,
                'status' => 'active',
                'admission_date' => now(),
                'profile_completed' => false
            ]);

            $this->command->info("  ✓ Created profile for: {$user->name}");
        }

        $this->command->info("✅ Created {$studentUsers->count()} student profiles");
    }
}
