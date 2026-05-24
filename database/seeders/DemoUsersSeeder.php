<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use App\Models\User;
use App\Models\Employee;
use App\Models\Student;
use App\Models\Department;
use App\Models\Faculty;
use App\Models\AcademicGroup;
use Illuminate\Support\Facades\Hash;
use Spatie\Permission\Models\Role;

class DemoUsersSeeder extends Seeder
{
    public function run()
    {
        // Ensure roles exist
        $roles = [
            'SuperAdmin',
            'Admin',
            'Teacher',
            'Student',
            'HR',
            'Finance',
            'Marketing',
            'Rector',
            'Dean',
            'Department Head'
        ];

        foreach ($roles as $roleName) {
            Role::firstOrCreate(['name' => $roleName]);
        }

        // Get or create demo faculty and department
        $faculty = Faculty::firstOrCreate(
            ['code' => 'TF'],
            [
                'name_uz' => 'Turizm fakulteti',
                'name_ru' => 'Факультет туризма',
                'name_en' => 'Faculty of Tourism',
                'code' => 'TF',
                'short_name' => 'TF',
                'dean_name' => 'Karimov Aziz',
                'phone' => '+998901234567',
                'email' => 'tourism@academy.uz',
                'is_active' => true,
                'room' => '401',
                'student_capacity' => 500,
                'teacher_capacity' => 50
            ]
        );

        $department = Department::firstOrCreate(
            ['code' => 'TK'],
            [
                'name' => 'Turizm kafedrasi',
                'faculty_id' => $faculty->id,
                'code' => 'TK',
                'head_name' => 'Saidov Jamshid'
            ]
        );


        // Get or create demo group
        $group = AcademicGroup::firstOrCreate(
            ['name' => 'TUR-401'],
            [
                'name' => 'TUR-401',
                'course' => 4,
                'faculty_id' => $faculty->id,
                'language' => 'uz',
                'academic_year' => '2024-2025',
                'semester' => 7,
                'max_students' => 30,
                'current_students' => 25,
                'curator_name' => 'Rustamov Sardor',
                'monitor_name' => 'Aliyev Jasur',
                'monitor_phone' => '+998901234569',
                'is_active' => true
            ]
        );

        // Demo users data
        $demoUsers = [
            [
                'name' => 'Super Admin',
                'email' => 'superadmin@demo.uz',
                'password' => 'demo12345',
                'role' => 'SuperAdmin',
                'phone' => '+998901111111',
                'address' => 'Samarqand sh., Universitet xiyoboni 15',
                'birth_date' => '1985-05-10',
                'gender' => 'male',
                'passport_series' => 'AA',
                'passport_number' => '1234567',
                'is_employee' => true,
                'employee_data' => [
                    'employee_id_number' => 'EMP001',
                    'department_id' => $department->id,
                    'hire_date' => '2020-01-15',
                    'employment_type' => 'full_time',
                    'specialization' => 'Boshqaruv',
                    'degree' => 'PhD',
                    'academic_title' => 'Professor'
                ]
            ],
            [
                'name' => 'Admin User',
                'email' => 'admin@demo.uz',
                'password' => 'demo12345',
                'role' => 'Admin',
                'phone' => '+998902222222',
                'address' => 'Samarqand sh., Rudaki ko\'chasi 25',
                'birth_date' => '1990-08-20',
                'gender' => 'female',
                'passport_series' => 'AB',
                'passport_number' => '2345678',
                'is_employee' => true,
                'employee_data' => [
                    'employee_id_number' => 'EMP002',
                    'department_id' => $department->id,
                    'hire_date' => '2021-03-10',
                    'employment_type' => 'full_time',
                    'specialization' => 'IT va ma\'lumotlar bazasi',
                    'degree' => 'Master',
                    'academic_title' => 'Katta o\'qituvchi'
                ]
            ],
            [
                'name' => 'O\'qituvchi Rahimov',
                'email' => 'teacher@demo.uz',
                'password' => 'demo12345',
                'role' => 'Teacher',
                'phone' => '+998903333333',
                'address' => 'Samarqand sh., Dahbed ko\'chasi 45',
                'birth_date' => '1988-03-15',
                'gender' => 'male',
                'passport_series' => 'AC',
                'passport_number' => '3456789',
                'is_employee' => true,
                'employee_data' => [
                    'employee_id_number' => 'EMP003',
                    'department_id' => $department->id,
                    'hire_date' => '2019-09-01',
                    'employment_type' => 'full_time',
                    'specialization' => 'Turizm geografiyasi',
                    'degree' => 'PhD',
                    'academic_title' => 'Dotsent',
                    'teaching_load' => 720,
                    'subjects' => ['Turizm geografiyasi', 'Ekskursiya ishi asoslari']
                ]
            ],
            [
                'name' => 'Talaba Azimov',
                'email' => 'student@demo.uz',
                'password' => 'demo12345',
                'role' => 'Student',
                'phone' => '+998904444444',
                'address' => 'Samarqand sh., Gagarin ko\'chasi 67',
                'birth_date' => '2003-06-25',
                'gender' => 'male',
                'passport_series' => 'AD',
                'passport_number' => '4567890',
                'is_student' => true,
                'student_data' => [
                    'student_id' => 'STU20240001',
                    'hemis_id' => 'HEMIS123456',
                    'group_id' => $group->id,
                    'faculty_id' => $faculty->id,
                    'course' => 4,
                    'admission_year' => 2020,
                    'study_type' => 'kunduzgi',
                    'education_form' => 'grant',
                    'language' => 'uz',
                    'gpa' => 4.2,
                    'scholarship_amount' => 550000
                ]
            ],
            [
                'name' => 'HR Manager',
                'email' => 'hr@demo.uz',
                'password' => 'demo12345',
                'role' => 'HR',
                'phone' => '+998905555555',
                'address' => 'Samarqand sh., Amir Temur ko\'chasi 100',
                'birth_date' => '1992-11-30',
                'gender' => 'female',
                'passport_series' => 'AE',
                'passport_number' => '5678901',
                'is_employee' => true,
                'employee_data' => [
                    'employee_id_number' => 'EMP004',
                    'department_id' => $department->id,
                    'hire_date' => '2022-02-15',
                    'employment_type' => 'full_time',
                    'specialization' => 'Kadrlar boshqaruvi',
                    'degree' => 'Bachelor',
                    'office_room' => '205',
                    'work_phone' => '+998662345678'
                ]
            ],
            [
                'name' => 'Moliya Boshqarmasi',
                'email' => 'finance@demo.uz',
                'password' => 'demo12345',
                'role' => 'Finance',
                'phone' => '+998906666666',
                'address' => 'Samarqand sh., Registanskaya ko\'chasi 15',
                'birth_date' => '1987-04-18',
                'gender' => 'male',
                'passport_series' => 'AF',
                'passport_number' => '6789012',
                'is_employee' => true,
                'employee_data' => [
                    'employee_id_number' => 'EMP005',
                    'department_id' => $department->id,
                    'hire_date' => '2018-06-01',
                    'employment_type' => 'full_time',
                    'specialization' => 'Moliya va buxgalteriya',
                    'degree' => 'Master',
                    'office_room' => '110',
                    'work_phone' => '+998662345679'
                ]
            ],
            [
                'name' => 'Marketing Menejeri',
                'email' => 'marketing@demo.uz',
                'password' => 'demo12345',
                'role' => 'Marketing',
                'phone' => '+998907777777',
                'address' => 'Samarqand sh., Buyuk Ipak Yo\'li 45',
                'birth_date' => '1993-09-22',
                'gender' => 'female',
                'passport_series' => 'AG',
                'passport_number' => '7890123',
                'is_employee' => true,
                'employee_data' => [
                    'employee_id_number' => 'EMP006',
                    'department_id' => $department->id,
                    'hire_date' => '2021-11-10',
                    'employment_type' => 'full_time',
                    'specialization' => 'Marketing va reklama',
                    'degree' => 'Bachelor',
                    'office_room' => '312',
                    'work_phone' => '+998662345680'
                ]
            ],
            [
                'name' => 'Rektor Karimov',
                'email' => 'rector@demo.uz',
                'password' => 'demo12345',
                'role' => 'Rector',
                'phone' => '+998908888888',
                'address' => 'Samarqand sh., Universitet xiyoboni 1',
                'birth_date' => '1975-02-14',
                'gender' => 'male',
                'passport_series' => 'AH',
                'passport_number' => '8901234',
                'is_employee' => true,
                'employee_data' => [
                    'employee_id_number' => 'EMP007',
                    'department_id' => $department->id,
                    'hire_date' => '2015-08-01',
                    'employment_type' => 'full_time',
                    'specialization' => 'Ta\'lim boshqaruvi',
                    'degree' => 'DSc',
                    'academic_title' => 'Professor',
                    'office_room' => '501',
                    'work_phone' => '+998662345600'
                ]
            ],
            [
                'name' => 'Dekan Saidova',
                'email' => 'dean@demo.uz',
                'password' => 'demo12345',
                'role' => 'Dean',
                'phone' => '+998909999999',
                'address' => 'Samarqand sh., Bo\'stonsaroy ko\'chasi 78',
                'birth_date' => '1980-07-03',
                'gender' => 'female',
                'passport_series' => 'AI',
                'passport_number' => '9012345',
                'is_employee' => true,
                'employee_data' => [
                    'employee_id_number' => 'EMP008',
                    'department_id' => $department->id,
                    'hire_date' => '2017-09-15',
                    'employment_type' => 'full_time',
                    'specialization' => 'Turizm menejmenti',
                    'degree' => 'PhD',
                    'academic_title' => 'Dotsent',
                    'office_room' => '401',
                    'work_phone' => '+998662345650'
                ]
            ],
            [
                'name' => 'Kafedra Mudiri',
                'email' => 'department@demo.uz',
                'password' => 'demo12345',
                'role' => 'Department Head',
                'phone' => '+998901010101',
                'address' => 'Samarqand sh., Mirzo Ulug\'bek ko\'chasi 56',
                'birth_date' => '1982-12-08',
                'gender' => 'male',
                'passport_series' => 'AJ',
                'passport_number' => '0123456',
                'is_employee' => true,
                'employee_data' => [
                    'employee_id_number' => 'EMP009',
                    'department_id' => $department->id,
                    'hire_date' => '2016-04-20',
                    'employment_type' => 'full_time',
                    'specialization' => 'Mehmonxona xo\'jaligi',
                    'degree' => 'PhD',
                    'academic_title' => 'Dotsent',
                    'teaching_load' => 540,
                    'office_room' => '303',
                    'work_phone' => '+998662345670'
                ]
            ]
        ];

        foreach ($demoUsers as $userData) {
            // Create user
            $user = User::create([
                'name' => $userData['name'],
                'email' => $userData['email'],
                'password' => Hash::make($userData['password']),
                'phone' => $userData['phone'],
                'address' => $userData['address'],
                'birth_date' => $userData['birth_date'],
                'gender' => $userData['gender'],
                'passport_series' => $userData['passport_series'],
                'passport_number' => $userData['passport_number'],
                'email_verified_at' => now(),
            ]);

            // Assign role
            $user->assignRole($userData['role']);

            // Create employee record if needed
            if (isset($userData['is_employee']) && $userData['is_employee']) {
                $employeeData = $userData['employee_data'];
                $employeeData['user_id'] = $user->id;
                $employeeData['full_name'] = $userData['name'];
                $employeeData['email'] = $userData['email'];
                $employeeData['phone'] = $userData['phone'];
                $employeeData['address'] = $userData['address'];
                $employeeData['birth_date'] = $userData['birth_date'];
                $employeeData['gender'] = $userData['gender'];
                $employeeData['passport_series'] = $userData['passport_series'];
                $employeeData['passport_number'] = $userData['passport_number'];
                $employeeData['status'] = 'active';

                // Handle subjects array if exists
                if (isset($employeeData['subjects'])) {
                    $employeeData['subjects'] = json_encode($employeeData['subjects']);
                }

                Employee::create($employeeData);
            }

            // Create student record if needed
            if (isset($userData['is_student']) && $userData['is_student']) {
                $studentData = $userData['student_data'];
                $studentData['user_id'] = $user->id;
                $studentData['full_name'] = $userData['name'];
                $studentData['email'] = $userData['email'];
                $studentData['phone'] = $userData['phone'];
                $studentData['address'] = $userData['address'];
                $studentData['birth_date'] = $userData['birth_date'];
                $studentData['gender'] = $userData['gender'];
                $studentData['passport_series'] = $userData['passport_series'];
                $studentData['passport_number'] = $userData['passport_number'];
                $studentData['status'] = 'active';

                Student::create($studentData);
            }
        }

        $this->command->info('Demo users created successfully!');
        $this->command->table(
            ['Role', 'Email', 'Password'],
            collect($demoUsers)->map(function ($user) {
                return [
                    $user['role'],
                    $user['email'],
                    'demo12345'
                ];
            })
        );
    }
}