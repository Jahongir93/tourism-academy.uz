<?php

namespace App\Services;

use App\Models\Student;
use App\Models\User;
use App\Models\Faculty;
use App\Models\Specialty;
use App\Models\AcademicGroup;
use App\Models\Subject;
use App\Models\Grade;
use Illuminate\Support\Facades\Http;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Cache;

class HemisDataSyncService
{
    protected $hemisUrl;
    protected $clientId;
    protected $clientSecret;
    protected $token;

    public function __construct()
    {
        $this->hemisUrl = config('services.hemis.base_url', 'https://hemis.uz');
        $this->clientId = config('services.hemis.client_id');
        $this->clientSecret = config('services.hemis.client_secret');
    }

    /**
     * Get service access token
     */
    protected function getServiceToken(): ?string
    {
        if ($this->token) {
            return $this->token;
        }

        return Cache::remember('hemis_service_token', 3600, function () {
            try {
                $response = Http::post($this->hemisUrl . '/oauth/token', [
                    'grant_type' => 'client_credentials',
                    'client_id' => $this->clientId,
                    'client_secret' => $this->clientSecret,
                ]);

                if ($response->successful()) {
                    $data = $response->json();
                    $this->token = $data['access_token'] ?? null;
                    return $this->token;
                }

                Log::error('Failed to get HEMIS service token: ' . $response->body());
                return null;

            } catch (\Exception $e) {
                Log::error('HEMIS service token error: ' . $e->getMessage());
                return null;
            }
        });
    }

    /**
     * Make authenticated request to HEMIS API
     */
    protected function makeRequest(string $endpoint, string $method = 'GET', array $data = []): ?array
    {
        $token = $this->getServiceToken();

        if (!$token) {
            return null;
        }

        try {
            $request = Http::withToken($token);

            $response = match(strtoupper($method)) {
                'GET' => $request->get($this->hemisUrl . $endpoint, $data),
                'POST' => $request->post($this->hemisUrl . $endpoint, $data),
                'PUT' => $request->put($this->hemisUrl . $endpoint, $data),
                'DELETE' => $request->delete($this->hemisUrl . $endpoint, $data),
                default => throw new \Exception('Unsupported HTTP method')
            };

            if ($response->successful()) {
                return $response->json();
            }

            Log::error("HEMIS API request failed: {$endpoint} - " . $response->body());
            return null;

        } catch (\Exception $e) {
            Log::error("HEMIS API request error: {$endpoint} - " . $e->getMessage());
            return null;
        }
    }

    /**
     * Sync all students from HEMIS
     */
    public function syncStudents(int $limit = 100, int $offset = 0): array
    {
        $data = $this->makeRequest('/api/students', 'GET', [
            'limit' => $limit,
            'offset' => $offset,
        ]);

        if (!$data || !isset($data['items'])) {
            return ['success' => false, 'message' => 'Failed to fetch students from HEMIS'];
        }

        $synced = 0;
        $created = 0;
        $updated = 0;
        $errors = [];

        DB::beginTransaction();
        try {
            foreach ($data['items'] as $hemisStudent) {
                try {
                    $result = $this->syncStudent($hemisStudent);
                    if ($result['created']) {
                        $created++;
                    } else {
                        $updated++;
                    }
                    $synced++;
                } catch (\Exception $e) {
                    $errors[] = [
                        'hemis_id' => $hemisStudent['id'] ?? 'unknown',
                        'error' => $e->getMessage()
                    ];
                }
            }

            DB::commit();

            return [
                'success' => true,
                'total' => count($data['items']),
                'synced' => $synced,
                'created' => $created,
                'updated' => $updated,
                'errors' => $errors,
                'has_more' => $data['has_more'] ?? false,
            ];

        } catch (\Exception $e) {
            DB::rollBack();
            Log::error('Student sync transaction failed: ' . $e->getMessage());
            return [
                'success' => false,
                'message' => 'Transaction failed: ' . $e->getMessage()
            ];
        }
    }

    /**
     * Sync single student
     */
    protected function syncStudent(array $hemisData): array
    {
        // Find or create user
        $user = User::where('hemis_id', $hemisData['id'])->first();

        $userData = [
            'name' => $hemisData['full_name'] ?? $hemisData['first_name'] . ' ' . $hemisData['last_name'],
            'first_name' => $hemisData['first_name'] ?? null,
            'last_name' => $hemisData['last_name'] ?? null,
            'middle_name' => $hemisData['middle_name'] ?? null,
            'email' => $hemisData['email'] ?? null,
            'phone' => $hemisData['phone'] ?? null,
            'hemis_id' => $hemisData['id'],
            'user_type' => 'uzbek',
            'birth_date' => $hemisData['birth_date'] ?? null,
            'gender' => $hemisData['gender'] ?? null,
            'passport_series' => $hemisData['passport_series'] ?? null,
            'passport_number' => $hemisData['passport_number'] ?? null,
            'address_permanent' => $hemisData['address'] ?? null,
        ];

        $created = false;
        if (!$user) {
            $userData['password'] = \Hash::make(uniqid());
            $userData['phone_verified_at'] = now();
            $user = User::create($userData);
            $user->assignRole('Student');
            $created = true;
        } else {
            $user->update($userData);
        }

        // Find or create faculty
        $faculty = null;
        if (isset($hemisData['faculty_id'])) {
            $faculty = Faculty::where('code', $hemisData['faculty_code'])->first();
            if (!$faculty && isset($hemisData['faculty_name'])) {
                $faculty = Faculty::create([
                    'code' => $hemisData['faculty_code'],
                    'name_uz' => $hemisData['faculty_name'],
                    'is_active' => true,
                ]);
            }
        }

        // Find or create specialty
        $specialty = null;
        if (isset($hemisData['specialty_id'])) {
            $specialty = Specialty::where('code', $hemisData['specialty_code'])->first();
            if (!$specialty && isset($hemisData['specialty_name'])) {
                $specialty = Specialty::create([
                    'code' => $hemisData['specialty_code'],
                    'name_uz' => $hemisData['specialty_name'],
                    'faculty_id' => $faculty?->id,
                    'is_active' => true,
                ]);
            }
        }

        // Find or create group
        $group = null;
        if (isset($hemisData['group_id'])) {
            $group = AcademicGroup::where('name', $hemisData['group_name'])->first();
            if (!$group && isset($hemisData['group_name'])) {
                $group = AcademicGroup::create([
                    'name' => $hemisData['group_name'],
                    'faculty_id' => $faculty?->id,
                    'specialty_id' => $specialty?->id,
                    'course' => $hemisData['course'] ?? 1,
                    'is_active' => true,
                ]);
            }
        }

        // Create or update student record
        $studentData = [
            'user_id' => $user->id,
            'student_id' => $hemisData['student_id'] ?? $hemisData['id'],
            'first_name' => $hemisData['first_name'] ?? null,
            'last_name' => $hemisData['last_name'] ?? null,
            'middle_name' => $hemisData['middle_name'] ?? null,
            'email' => $hemisData['email'] ?? null,
            'phone' => $hemisData['phone'] ?? null,
            'faculty_id' => $faculty?->id,
            'group_id' => $group?->id,
            'birth_date' => $hemisData['birth_date'] ?? null,
            'gender' => $hemisData['gender'] ?? null,
            'admission_date' => $hemisData['admission_date'] ?? now(),
            'status' => $hemisData['status'] ?? 'active',
            'passport_series' => $hemisData['passport_series'] ?? null,
            'passport_number' => $hemisData['passport_number'] ?? null,
            'address' => $hemisData['address'] ?? null,
        ];

        $student = Student::updateOrCreate(
            ['user_id' => $user->id],
            $studentData
        );

        return [
            'created' => $created,
            'student' => $student,
            'user' => $user,
        ];
    }

    /**
     * Sync all teachers from HEMIS
     */
    public function syncTeachers(int $limit = 100, int $offset = 0): array
    {
        $data = $this->makeRequest('/api/teachers', 'GET', [
            'limit' => $limit,
            'offset' => $offset,
        ]);

        if (!$data || !isset($data['items'])) {
            return ['success' => false, 'message' => 'Failed to fetch teachers from HEMIS'];
        }

        $synced = 0;
        $created = 0;
        $updated = 0;
        $errors = [];

        DB::beginTransaction();
        try {
            foreach ($data['items'] as $hemisTeacher) {
                try {
                    $result = $this->syncTeacher($hemisTeacher);
                    if ($result['created']) {
                        $created++;
                    } else {
                        $updated++;
                    }
                    $synced++;
                } catch (\Exception $e) {
                    $errors[] = [
                        'hemis_id' => $hemisTeacher['id'] ?? 'unknown',
                        'error' => $e->getMessage()
                    ];
                }
            }

            DB::commit();

            return [
                'success' => true,
                'total' => count($data['items']),
                'synced' => $synced,
                'created' => $created,
                'updated' => $updated,
                'errors' => $errors,
                'has_more' => $data['has_more'] ?? false,
            ];

        } catch (\Exception $e) {
            DB::rollBack();
            Log::error('Teacher sync transaction failed: ' . $e->getMessage());
            return [
                'success' => false,
                'message' => 'Transaction failed: ' . $e->getMessage()
            ];
        }
    }

    /**
     * Sync single teacher
     */
    protected function syncTeacher(array $hemisData): array
    {
        $user = User::where('hemis_id', $hemisData['id'])->first();

        $userData = [
            'name' => $hemisData['full_name'] ?? $hemisData['first_name'] . ' ' . $hemisData['last_name'],
            'first_name' => $hemisData['first_name'] ?? null,
            'last_name' => $hemisData['last_name'] ?? null,
            'middle_name' => $hemisData['middle_name'] ?? null,
            'email' => $hemisData['email'] ?? null,
            'phone' => $hemisData['phone'] ?? null,
            'hemis_id' => $hemisData['id'],
            'user_type' => 'uzbek',
            'employee_type' => 'teacher',
        ];

        $created = false;
        if (!$user) {
            $userData['password'] = \Hash::make(uniqid());
            $userData['phone_verified_at'] = now();
            $user = User::create($userData);
            $user->assignRole('Teacher');
            $created = true;
        } else {
            $user->update($userData);
        }

        return [
            'created' => $created,
            'user' => $user,
        ];
    }

    /**
     * Sync student grades from HEMIS
     */
    public function syncStudentGrades(string $hemisStudentId): array
    {
        $data = $this->makeRequest("/api/students/{$hemisStudentId}/grades");

        if (!$data || !isset($data['grades'])) {
            return ['success' => false, 'message' => 'Failed to fetch grades from HEMIS'];
        }

        $student = Student::whereHas('user', function($q) use ($hemisStudentId) {
            $q->where('hemis_id', $hemisStudentId);
        })->first();

        if (!$student) {
            return ['success' => false, 'message' => 'Student not found in local database'];
        }

        $synced = 0;
        $errors = [];

        DB::beginTransaction();
        try {
            foreach ($data['grades'] as $hemisGrade) {
                try {
                    // Find or create subject
                    $subject = Subject::where('code', $hemisGrade['subject_code'])->first();
                    if (!$subject && isset($hemisGrade['subject_name'])) {
                        $subject = Subject::create([
                            'code' => $hemisGrade['subject_code'],
                            'name_uz' => $hemisGrade['subject_name'],
                            'is_active' => true,
                        ]);
                    }

                    if ($subject) {
                        Grade::updateOrCreate(
                            [
                                'student_id' => $student->id,
                                'subject_id' => $subject->id,
                                'semester' => $hemisGrade['semester'] ?? null,
                                'academic_year' => $hemisGrade['academic_year'] ?? null,
                            ],
                            [
                                'grade' => $hemisGrade['grade'],
                                'grade_point' => $hemisGrade['grade_point'] ?? $this->convertGradeToPoint($hemisGrade['grade']),
                                'credits' => $hemisGrade['credits'] ?? 0,
                                'assessment_type' => $hemisGrade['assessment_type'] ?? 'exam',
                                'assessment_date' => $hemisGrade['assessment_date'] ?? now(),
                            ]
                        );
                        $synced++;
                    }
                } catch (\Exception $e) {
                    $errors[] = [
                        'subject_code' => $hemisGrade['subject_code'] ?? 'unknown',
                        'error' => $e->getMessage()
                    ];
                }
            }

            DB::commit();

            return [
                'success' => true,
                'synced' => $synced,
                'errors' => $errors,
            ];

        } catch (\Exception $e) {
            DB::rollBack();
            Log::error('Grade sync transaction failed: ' . $e->getMessage());
            return [
                'success' => false,
                'message' => 'Transaction failed: ' . $e->getMessage()
            ];
        }
    }

    /**
     * Convert grade to grade point (4.0 scale)
     */
    protected function convertGradeToPoint($grade): float
    {
        if (is_numeric($grade)) {
            $grade = (int)$grade;
            if ($grade >= 86) return 4.0;
            if ($grade >= 71) return 3.0;
            if ($grade >= 55) return 2.0;
            return 0.0;
        }

        return match(strtoupper($grade)) {
            'A', 'A+' => 4.0,
            'A-' => 3.7,
            'B+' => 3.3,
            'B' => 3.0,
            'B-' => 2.7,
            'C+' => 2.3,
            'C' => 2.0,
            'C-' => 1.7,
            'D+' => 1.3,
            'D' => 1.0,
            'F' => 0.0,
            default => 0.0
        };
    }

    /**
     * Get curriculum from HEMIS
     */
    public function getCurriculum(string $specialtyCode): ?array
    {
        return $this->makeRequest("/api/curriculum/{$specialtyCode}");
    }

    /**
     * Get academic calendar from HEMIS
     */
    public function getAcademicCalendar(): ?array
    {
        return $this->makeRequest('/api/academic-calendar');
    }

    /**
     * Get schedule from HEMIS
     */
    public function getSchedule(string $groupName): ?array
    {
        return $this->makeRequest("/api/schedule/{$groupName}");
    }

    /**
     * Full system sync - all data
     */
    public function fullSync(): array
    {
        $results = [
            'started_at' => now()->toDateTimeString(),
            'students' => [],
            'teachers' => [],
            'success' => true,
            'errors' => [],
        ];

        try {
            // Sync students
            $offset = 0;
            $limit = 100;
            do {
                $studentResult = $this->syncStudents($limit, $offset);
                if ($studentResult['success']) {
                    $results['students'][] = $studentResult;
                    $hasMore = $studentResult['has_more'] ?? false;
                    $offset += $limit;
                } else {
                    $results['success'] = false;
                    $results['errors'][] = 'Student sync failed: ' . ($studentResult['message'] ?? 'Unknown error');
                    break;
                }
            } while ($hasMore);

            // Sync teachers
            $offset = 0;
            do {
                $teacherResult = $this->syncTeachers($limit, $offset);
                if ($teacherResult['success']) {
                    $results['teachers'][] = $teacherResult;
                    $hasMore = $teacherResult['has_more'] ?? false;
                    $offset += $limit;
                } else {
                    $results['success'] = false;
                    $results['errors'][] = 'Teacher sync failed: ' . ($teacherResult['message'] ?? 'Unknown error');
                    break;
                }
            } while ($hasMore);

            $results['completed_at'] = now()->toDateTimeString();

        } catch (\Exception $e) {
            $results['success'] = false;
            $results['errors'][] = 'Full sync failed: ' . $e->getMessage();
            Log::error('HEMIS full sync error: ' . $e->getMessage());
        }

        return $results;
    }
}
