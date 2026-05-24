<?php

namespace App\Http\Controllers;

use App\Models\AdmissionApplication;
use App\Models\AdmissionFormField;
use App\Models\AdmissionFormValue;
use App\Models\AdmissionSetting;
use App\Models\Faculty;
use App\Models\Group;
use App\Models\Specialty;
use App\Models\Student;
use App\Models\User;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Storage;
use PhpOffice\PhpSpreadsheet\Spreadsheet;
use PhpOffice\PhpSpreadsheet\Writer\Xlsx;
use PhpOffice\PhpSpreadsheet\Style\Fill;
use PhpOffice\PhpSpreadsheet\Style\Border;
use PhpOffice\PhpSpreadsheet\Style\Alignment;
use PhpOffice\PhpSpreadsheet\Style\Color;

class AdmissionController extends Controller
{
    /**
     * Show application form
     */
    public function apply()
    {
        try {
            $faculties = Faculty::where('is_active', true)->orderBy('name_uz')->get();
            $specialties = Specialty::with('faculty')->orderBy('name_uz')->get();
        } catch (\Exception $e) {
            \Log::error('Admission apply error: ' . $e->getMessage());
            $faculties = collect([]);
            $specialties = collect([]);
        }

        // Load dynamic form fields from database
        try {
            $formFields = AdmissionFormField::active()
                ->ordered()
                ->get()
                ->groupBy('step');

            // Get total steps
            $totalSteps = $formFields->keys()->max() ?: 4;

            // Get step titles for navigation
            $stepTitles = [];
            foreach ($formFields as $step => $fields) {
                $heading = $fields->firstWhere('field_type', 'heading');
                $stepTitles[$step] = $heading ? $heading->label_uz : 'Bosqich ' . $step;
            }
        } catch (\Exception $e) {
            \Log::error('Form fields load error: ' . $e->getMessage());
            $formFields = collect([]);
            $totalSteps = 4;
            $stepTitles = [
                1 => 'Shaxsiy ma\'lumotlar',
                2 => 'Ta\'lim ma\'lumotlari',
                3 => 'Yo\'nalish tanlash',
                4 => 'Hujjatlar'
            ];
        }

        // Load contact settings
        $contactSettings = $this->getContactSettings();

        return view('admission.apply', compact('faculties', 'specialties', 'formFields', 'totalSteps', 'stepTitles', 'contactSettings'));
    }

    /**
     * Get contact settings for the admission form
     */
    private function getContactSettings(): array
    {
        $setting = AdmissionSetting::where('key', 'contact_info')->first();

        if ($setting && is_array($setting->value)) {
            return $setting->value;
        }

        // Default contact settings
        return [
            'phone' => '+998 90 123-45-67',
            'telegram' => '@tourism_admission',
            'email' => 'admission@tourism.uz',
            'show_help_section' => true
        ];
    }

    /**
     * Store new application
     */
    public function store(Request $request)
    {
        // Load form fields for dynamic validation
        $formFields = AdmissionFormField::active()->get();

        // Build validation rules dynamically
        $validationRules = $this->buildValidationRules($formFields);

        $validated = $request->validate($validationRules);

        DB::beginTransaction();
        $uploadedFiles = [];

        try {
            // Generate application number
            $year = date('Y');
            $lastApp = AdmissionApplication::where('application_number', 'like', "APP-{$year}-%")
                ->orderBy('application_number', 'desc')
                ->first();

            $number = $lastApp
                ? intval(substr($lastApp->application_number, -5)) + 1
                : 1;

            $applicationNumber = sprintf("APP-%s-%05d", $year, $number);

            // Process file uploads
            $uploadedFiles = $this->processFileUploads($request, $formFields);

            // Build application data from validated fields
            $applicationData = [
                'application_number' => $applicationNumber,
                'status' => 'pending',
                'applied_at' => now(),
                'form_data' => $validated,
                'form_version' => 1,
            ];

            // Map common fields to main table columns for backward compatibility
            $mainTableFields = [
                'first_name', 'last_name', 'middle_name', 'birth_date', 'gender',
                'passport_series', 'passport_number', 'jshshir', 'phone', 'email',
                'region', 'district', 'address', 'education_type', 'education_name',
                'graduation_year', 'dtm_score', 'faculty_id', 'specialty_id',
                'education_form', 'education_language'
            ];

            foreach ($mainTableFields as $field) {
                if (isset($validated[$field])) {
                    $applicationData[$field] = $validated[$field];
                }
            }

            // Map legacy file paths for backward compatibility
            $legacyFilePaths = [
                'photo' => 'photo_path',
                'passport_copy' => 'passport_copy_path',
                'diploma_copy' => 'diploma_copy_path',
                'certificate_copy' => 'certificate_copy_path',
            ];

            foreach ($legacyFilePaths as $fieldKey => $columnName) {
                if (isset($uploadedFiles[$fieldKey])) {
                    $applicationData[$columnName] = $uploadedFiles[$fieldKey];
                }
            }

            // Create application
            $application = AdmissionApplication::create($applicationData);

            // Store all field values in admission_form_values table
            foreach ($formFields as $field) {
                if ($field->field_type === 'heading') {
                    continue;
                }

                $value = $request->input($field->field_key);
                $filePath = $uploadedFiles[$field->field_key] ?? null;

                if ($value !== null || $filePath !== null) {
                    AdmissionFormValue::create([
                        'application_id' => $application->id,
                        'field_key' => $field->field_key,
                        'value' => is_array($value) ? json_encode($value) : $value,
                        'file_path' => $filePath,
                    ]);
                }
            }

            DB::commit();

            return redirect()->route('admission.success', $application->application_number)
                ->with('success', 'Arizangiz muvaffaqiyatli qabul qilindi!');

        } catch (\Exception $e) {
            DB::rollBack();

            // Delete uploaded files on failure
            foreach ($uploadedFiles as $filePath) {
                if ($filePath) {
                    Storage::disk('public')->delete($filePath);
                }
            }

            \Log::error('Admission store error: ' . $e->getMessage());

            return back()->withInput()
                ->with('error', 'Xatolik yuz berdi: ' . $e->getMessage());
        }
    }

    /**
     * Build validation rules from form fields
     */
    private function buildValidationRules($formFields): array
    {
        $rules = [];

        foreach ($formFields as $field) {
            if ($field->field_type === 'heading') {
                continue;
            }

            $rules[$field->field_key] = implode('|', $field->getValidationRulesArray());
        }

        return $rules;
    }

    /**
     * Process file uploads for form fields
     */
    private function processFileUploads(Request $request, $formFields): array
    {
        $uploadedFiles = [];

        foreach ($formFields->where('field_type', 'file') as $field) {
            if ($request->hasFile($field->field_key)) {
                $file = $request->file($field->field_key);
                if ($file->isValid()) {
                    $uploadedFiles[$field->field_key] = $file->store(
                        $field->getStoragePath(),
                        'public'
                    );
                }
            }
        }

        return $uploadedFiles;
    }

    /**
     * Show success page
     */
    public function success($applicationNumber)
    {
        $application = AdmissionApplication::where('application_number', $applicationNumber)
            ->firstOrFail();

        return view('admission.success', compact('application'));
    }

    /**
     * Check application status
     */
    public function checkStatus(Request $request)
    {
        if ($request->isMethod('post')) {
            $request->validate([
                'application_number' => 'required|string',
                'passport' => 'required|string'
            ]);

            $application = AdmissionApplication::where('application_number', $request->application_number)
                ->where(function($query) use ($request) {
                    $query->where('passport_series', substr($request->passport, 0, 2))
                          ->where('passport_number', substr($request->passport, 2));
                })
                ->first();

            if (!$application) {
                return back()->with('error', 'Ariza topilmadi. Ma\'lumotlarni tekshiring.');
            }

            return view('admission.status', compact('application'));
        }

        return view('admission.check-status');
    }

    /**
     * Show admission info page
     */
    public function info()
    {
        $faculties = Faculty::where('is_active', true)
            ->withCount('specialties')
            ->get();

        $statistics = [
            'total_applications' => AdmissionApplication::count(),
            'pending' => AdmissionApplication::where('status', 'pending')->count(),
            'accepted' => AdmissionApplication::where('status', 'accepted')->count(),
            'rejected' => AdmissionApplication::where('status', 'rejected')->count(),
        ];

        return view('admission.info', compact('faculties', 'statistics'));
    }

    /**
     * Marketing dashboard - list all applications
     */
    public function applications(Request $request)
    {
        // Check if user has marketing role
        if (!Auth::user()->hasRole(['Marketing', 'SuperAdmin', 'Admin'])) {
            abort(403, 'Ruxsat berilmagan');
        }

        $query = AdmissionApplication::with(['faculty', 'specialty']);

        // Filters
        if ($request->has('status')) {
            $query->where('status', $request->status);
        }

        if ($request->has('faculty_id')) {
            $query->where('faculty_id', $request->faculty_id);
        }

        if ($request->has('education_form')) {
            $query->where('education_form', $request->education_form);
        }

        if ($request->has('search')) {
            $search = $request->search;
            $query->where(function($q) use ($search) {
                $q->where('application_number', 'like', "%{$search}%")
                  ->orWhere('first_name', 'like', "%{$search}%")
                  ->orWhere('last_name', 'like', "%{$search}%")
                  ->orWhere('phone', 'like', "%{$search}%")
                  ->orWhere('email', 'like', "%{$search}%");
            });
        }

        $applications = $query->orderBy('applied_at', 'desc')->paginate(20);

        $faculties = Faculty::all();

        return view('admission.applications', compact('applications', 'faculties'));
    }

    /**
     * View single application
     */
    public function viewApplication(AdmissionApplication $application)
    {
        // Check if user has marketing role
        if (!Auth::user()->hasRole(['Marketing', 'SuperAdmin', 'Admin'])) {
            abort(403, 'Ruxsat berilmagan');
        }

        $application->load(['faculty', 'specialty']);

        // Get groups - filter by application's faculty/specialty if set,
        // otherwise show all active groups (e.g. for apply-single form without faculty)
        $groupsQuery = Group::where('is_active', true);

        if ($application->faculty_id || $application->specialty_id) {
            $groupsQuery->where(function($q) use ($application) {
                if ($application->faculty_id) {
                    $q->orWhere('faculty_id', $application->faculty_id);
                }
                if ($application->specialty_id) {
                    $q->orWhere('specialty_id', $application->specialty_id);
                }
            });
        }

        $groups = $groupsQuery->orderBy('name')->get();

        // Generate suggested student ID
        $year = date('Y');
        $lastStudent = Student::where('student_id', 'like', "STD{$year}%")
            ->orderBy('student_id', 'desc')
            ->first();
        $nextNumber = $lastStudent
            ? intval(substr($lastStudent->student_id, -4)) + 1
            : 1;
        $suggestedStudentId = 'STD' . $year . str_pad($nextNumber, 4, '0', STR_PAD_LEFT);

        return view('admission.view-application', compact('application', 'groups', 'suggestedStudentId'));
    }

    /**
     * Update application status
     */
    public function updateStatus(Request $request, AdmissionApplication $application)
    {
        // Check if user has marketing role
        if (!Auth::user()->hasRole(['Marketing', 'SuperAdmin', 'Admin'])) {
            abort(403, 'Ruxsat berilmagan');
        }

        $validated = $request->validate([
            'status' => 'required|in:pending,reviewing,accepted,rejected,waitlist',
            'notes' => 'nullable|string|max:500'
        ]);

        $application->update([
            'status' => $validated['status'],
            'reviewed_by' => Auth::id(),
            'reviewed_at' => now(),
            'notes' => $validated['notes']
        ]);

        return redirect()->route('admission.view-application', $application)
            ->with('success', 'Ariza holati yangilandi!');
    }

    /**
     * Accept application and enroll as student
     */
    public function acceptAndEnroll(Request $request, AdmissionApplication $application)
    {
        // Check if user has marketing role
        if (!Auth::user()->hasRole(['Marketing', 'SuperAdmin', 'Admin'])) {
            abort(403, 'Ruxsat berilmagan');
        }

        // Validate request
        $validated = $request->validate([
            'group_id' => 'nullable|exists:groups,id',
            'student_id' => 'nullable|string|max:20|unique:students,student_id',
        ]);

        // Check if already enrolled
        $existingStudent = Student::where('email', $application->email)
            ->orWhere(function($q) use ($application) {
                $q->where('passport_series', $application->passport_series)
                  ->where('passport_number', $application->passport_number);
            })
            ->first();

        if ($existingStudent) {
            return back()->with('error', 'Bu abituriyent allaqachon talaba sifatida ro\'yxatdan o\'tgan!');
        }

        DB::beginTransaction();
        try {
            // Create user account for student
            $user = User::create([
                'name' => $application->full_name,
                'email' => $application->email,
                'password' => Hash::make($application->passport_series . $application->passport_number),
            ]);

            // Assign student role
            if (method_exists($user, 'assignRole')) {
                $user->assignRole('Student');
            }

            // Copy photo if exists
            $photoUrl = null;
            if ($application->photo_path && Storage::disk('public')->exists($application->photo_path)) {
                $newPhotoPath = 'students/photos/' . basename($application->photo_path);
                Storage::disk('public')->copy($application->photo_path, $newPhotoPath);
                $photoUrl = $newPhotoPath;
            }

            // Get group info if selected
            $group = $validated['group_id'] ? Group::find($validated['group_id']) : null;
            $course = $group ? $group->course : 1;
            $semester = $group ? $group->semester : 1;

            // Create student record
            $studentData = [
                'user_id' => $user->id,
                'group_id' => $validated['group_id'] ?? null,
                'first_name' => $application->first_name,
                'last_name' => $application->last_name,
                'middle_name' => $application->middle_name,
                'full_name' => $application->full_name,
                'birth_date' => $application->birth_date,
                'gender' => $application->gender,
                'passport_series' => $application->passport_series,
                'passport_number' => $application->passport_number,
                'phone' => $application->phone,
                'email' => $application->email,
                'address' => $application->address,
                'faculty_id' => $application->faculty_id,
                'specialty_id' => $application->specialty_id,
                'course' => $course,
                'semester' => $semester,
                'status' => 'active',
                'admission_date' => now(),
                'registration_date' => now(),
                'photo_url' => $photoUrl,
            ];

            // Use custom student_id if provided
            if (!empty($validated['student_id'])) {
                $studentData['student_id'] = $validated['student_id'];
            }

            $student = Student::create($studentData);

            // Update group students count
            if ($group) {
                $group->increment('current_students');
            }

            // Update application status
            $application->update([
                'status' => 'accepted',
                'reviewed_by' => Auth::id(),
                'reviewed_at' => now(),
                'notes' => ($application->notes ? $application->notes . "\n" : '') .
                          'Talaba sifatida ro\'yxatga olindi: ' . now()->format('d.m.Y H:i') .
                          ' | Talaba ID: ' . $student->student_id .
                          ($group ? ' | Guruh: ' . $group->name : '')
            ]);

            DB::commit();

            $message = 'Abituriyent muvaffaqiyatli talaba sifatida ro\'yxatga olindi! Talaba ID: ' . $student->student_id;
            if ($group) {
                $message .= ' | Guruh: ' . $group->name;
            }

            return redirect()->route('admission.view-application', $application)
                ->with('success', $message);

        } catch (\Exception $e) {
            DB::rollBack();
            \Log::error('Enroll student error: ' . $e->getMessage());
            return back()->with('error', 'Xatolik yuz berdi: ' . $e->getMessage());
        }
    }

    /**
     * Statistics dashboard
     */
    public function statistics()
    {
        // Check if user has marketing role
        if (!Auth::user()->hasRole(['Marketing', 'SuperAdmin', 'Admin'])) {
            abort(403, 'Ruxsat berilmagan');
        }

        // Statistics by region
        $byRegion = AdmissionApplication::select('region', DB::raw('COUNT(*) as count'))
            ->groupBy('region')
            ->orderBy('count', 'desc')
            ->get();

        // Statistics by faculty
        $byFaculty = AdmissionApplication::select('faculty_id', DB::raw('COUNT(*) as count'))
            ->with('faculty:id,name_uz')
            ->groupBy('faculty_id')
            ->get();

        // Statistics by education type
        $byEducation = AdmissionApplication::select('education_type', DB::raw('COUNT(*) as count'))
            ->groupBy('education_type')
            ->get();

        // Age statistics
        $byAge = AdmissionApplication::selectRaw('
                CASE
                    WHEN TIMESTAMPDIFF(YEAR, birth_date, CURDATE()) < 18 THEN "18 yoshgacha"
                    WHEN TIMESTAMPDIFF(YEAR, birth_date, CURDATE()) BETWEEN 18 AND 20 THEN "18-20 yosh"
                    WHEN TIMESTAMPDIFF(YEAR, birth_date, CURDATE()) BETWEEN 21 AND 25 THEN "21-25 yosh"
                    WHEN TIMESTAMPDIFF(YEAR, birth_date, CURDATE()) BETWEEN 26 AND 30 THEN "26-30 yosh"
                    ELSE "30 yoshdan katta"
                END as age_group,
                COUNT(*) as count
            ')
            ->groupBy('age_group')
            ->get();

        // Status statistics
        $byStatus = AdmissionApplication::select('status', DB::raw('COUNT(*) as count'))
            ->groupBy('status')
            ->get();

        // Daily applications (last 30 days)
        $dailyApplications = AdmissionApplication::selectRaw('DATE(applied_at) as date, COUNT(*) as count')
            ->where('applied_at', '>=', now()->subDays(30))
            ->groupBy('date')
            ->orderBy('date')
            ->get();

        return view('admission.statistics', compact(
            'byRegion',
            'byFaculty',
            'byEducation',
            'byAge',
            'byStatus',
            'dailyApplications'
        ));
    }

    /**
     * Delete application
     */
    public function deleteApplication(AdmissionApplication $application)
    {
        // Check if user has marketing role
        if (!Auth::user()->hasRole(['Marketing', 'SuperAdmin', 'Admin'])) {
            abort(403, 'Ruxsat berilmagan');
        }

        // Delete uploaded files
        if ($application->photo_path) {
            Storage::disk('public')->delete($application->photo_path);
        }
        if ($application->passport_copy_path) {
            Storage::disk('public')->delete($application->passport_copy_path);
        }
        if ($application->diploma_copy_path) {
            Storage::disk('public')->delete($application->diploma_copy_path);
        }
        if ($application->certificate_copy_path) {
            Storage::disk('public')->delete($application->certificate_copy_path);
        }

        $application->delete();

        return response()->json(['success' => true]);
    }

    /**
     * Admission settings page
     */
    public function settings()
    {
        // Check if user has marketing role
        if (!Auth::user()->hasRole(['Marketing', 'SuperAdmin', 'Admin'])) {
            abort(403, 'Ruxsat berilmagan');
        }

        // Get all settings as key-value pairs
        $settingsData = \App\Models\AdmissionSetting::pluck('value', 'key')->toArray();

        // Decode JSON values
        $settings = [];
        foreach ($settingsData as $key => $value) {
            $decoded = json_decode($value, true);
            if (is_array($decoded)) {
                $settings = array_merge($settings, $decoded);
            } else {
                $settings[$key] = $decoded ?: $value;
            }
        }

        // Set defaults if not exists
        $defaults = [
            'admission_open' => true,
            'max_applications' => 1000,
            'academic_year' => '2024-2025',
            'admission_type' => 'bakalavr',
            'admission_info' => 'O\'zbekiston Turizm va Madaniy Meros Universiteti online qabul tizimi',
            'admission_start' => now()->format('Y-m-d\TH:i'),
            'admission_end' => now()->addMonths(3)->format('Y-m-d\TH:i'),
            'document_start' => now()->format('Y-m-d\TH:i'),
            'document_end' => now()->addMonths(2)->format('Y-m-d\TH:i'),
            'exam_date' => now()->addMonths(3)->format('Y-m-d\TH:i'),
            'results_date' => now()->addMonths(4)->format('Y-m-d\TH:i'),
            'min_score' => 56,
            'min_age' => 16,
            'max_age' => 35,
            'email_notifications' => true,
            'sms_notifications' => false,
            'admin_email' => 'admission@tourism.uz',
            'notification_template' => 'Hurmatli {name}, sizning arizangiz muvaffaqiyatli qabul qilindi. Ariza raqami: {application_number}',
            'application_fee' => 50000,
            'payment_required' => false,
            'payment_methods' => ['payme' => true, 'click' => true, 'bank' => false],
            'bank_details' => ''
        ];

        $settings = array_merge($defaults, $settings);

        return view('admission.settings', compact('settings'));
    }

    /**
     * Update admission settings
     */
    public function updateSettings(Request $request)
    {
        // Check if user has marketing role
        if (!Auth::user()->hasRole(['Marketing', 'SuperAdmin', 'Admin'])) {
            abort(403, 'Ruxsat berilmagan');
        }

        // Save each setting separately
        foreach ($request->all() as $key => $value) {
            if ($key !== '_token' && $key !== '_method') {
                // Handle checkbox arrays
                if (is_array($value)) {
                    $value = json_encode($value);
                } elseif ($value === 'on') {
                    $value = true;
                } elseif ($value === null) {
                    $value = false;
                }

                \App\Models\AdmissionSetting::updateOrCreate(
                    ['key' => $key],
                    ['value' => is_bool($value) ? json_encode($value) : $value]
                );
            }
        }

        return redirect()->route('admission.settings')
            ->with('success', 'Sozlamalar muvaffaqiyatli yangilandi!');
    }

    /**
     * Form builder page
     */
    public function forms()
    {
        // Check if user has marketing role
        if (!Auth::user()->hasRole(['Marketing', 'SuperAdmin', 'Admin'])) {
            abort(403, 'Ruxsat berilmagan');
        }

        // Get form fields from database
        $formFields = AdmissionFormField::ordered()->get();

        // If AJAX request, return JSON for form builder
        if (request()->ajax()) {
            $fieldsData = $formFields->map(function ($field) {
                return [
                    'id' => $field->id,
                    'field_key' => $field->field_key,
                    'field_type' => $field->field_type,
                    'label_uz' => $field->label_uz,
                    'label_ru' => $field->label_ru,
                    'label_en' => $field->label_en,
                    'placeholder' => $field->placeholder ?? '',
                    'is_required' => $field->is_required,
                    'options' => $field->options ?? [],
                    'step' => $field->step,
                    'sort_order' => $field->sort_order,
                    'file_config' => $field->file_config ?? [
                        'max_size' => 5120,
                        'allowed_extensions' => ['pdf', 'jpg', 'jpeg', 'png'],
                        'storage_path' => 'admission/uploads'
                    ],
                    'is_active' => $field->is_active,
                    'validation_rules' => $field->validation_rules ?? []
                ];
            });

            $contactSettings = $this->getContactSettings();
            return response()->json(['fields' => $fieldsData, 'contactSettings' => $contactSettings]);
        }

        $contactSettings = $this->getContactSettings();
        return view('admission.forms', compact('formFields', 'contactSettings'));
    }

    /**
     * Update form fields
     */
    public function updateForms(Request $request)
    {
        // Check if user has marketing role
        if (!Auth::user()->hasRole(['Marketing', 'SuperAdmin', 'Admin'])) {
            abort(403, 'Ruxsat berilmagan');
        }

        $fields = $request->input('fields', []);
        $contactSettings = $request->input('contactSettings', null);

        DB::beginTransaction();
        try {
            // Update contact settings if provided
            if ($contactSettings !== null) {
                AdmissionSetting::updateOrCreate(
                    ['key' => 'contact_info'],
                    ['value' => $contactSettings]
                );
            }
            // Get existing field keys to track which ones to deactivate
            $existingKeys = AdmissionFormField::pluck('field_key')->toArray();
            $updatedKeys = [];

            foreach ($fields as $index => $fieldData) {
                $fieldKey = $fieldData['field_key'] ?? $fieldData['name'] ?? 'field_' . time() . '_' . $index;

                AdmissionFormField::updateOrCreate(
                    ['field_key' => $fieldKey],
                    [
                        'field_type' => $fieldData['field_type'] ?? $fieldData['type'] ?? 'text',
                        'label_uz' => $fieldData['label_uz'] ?? $fieldData['label'] ?? 'Yangi maydon',
                        'label_ru' => $fieldData['label_ru'] ?? null,
                        'label_en' => $fieldData['label_en'] ?? null,
                        'placeholder' => $fieldData['placeholder'] ?? null,
                        'options' => !empty($fieldData['options']) ? $fieldData['options'] : null,
                        'is_required' => $fieldData['is_required'] ?? $fieldData['required'] ?? false,
                        'step' => $fieldData['step'] ?? 1,
                        'sort_order' => $fieldData['sort_order'] ?? $index,
                        'file_config' => !empty($fieldData['file_config']) ? $fieldData['file_config'] : null,
                        'validation_rules' => !empty($fieldData['validation_rules']) ? $fieldData['validation_rules'] : null,
                        'is_active' => $fieldData['is_active'] ?? true,
                    ]
                );

                $updatedKeys[] = $fieldKey;
            }

            // Deactivate fields that were removed
            $removedKeys = array_diff($existingKeys, $updatedKeys);
            if (!empty($removedKeys)) {
                AdmissionFormField::whereIn('field_key', $removedKeys)->update(['is_active' => false]);
            }

            DB::commit();

            if ($request->ajax()) {
                return response()->json(['success' => true, 'message' => 'Forma muvaffaqiyatli saqlandi!']);
            }

            return redirect()->route('admission.forms')
                ->with('success', 'Forma sozlamalari yangilandi!');

        } catch (\Exception $e) {
            DB::rollBack();
            \Log::error('Form update error: ' . $e->getMessage());

            if ($request->ajax()) {
                return response()->json(['success' => false, 'message' => $e->getMessage()], 500);
            }

            return back()->with('error', 'Xatolik yuz berdi: ' . $e->getMessage());
        }
    }

    /**
     * Reset form fields to defaults
     */
    public function resetForms(Request $request)
    {
        // Check if user has marketing role
        if (!Auth::user()->hasRole(['Marketing', 'SuperAdmin', 'Admin'])) {
            abort(403, 'Ruxsat berilmagan');
        }

        DB::beginTransaction();
        try {
            // Delete all existing fields
            AdmissionFormField::truncate();

            // Run the seeder to restore defaults
            $seeder = new \Database\Seeders\AdmissionFormFieldSeeder();
            $seeder->run();

            DB::commit();

            if ($request->ajax()) {
                return response()->json(['success' => true, 'message' => 'Default maydonlar tiklandi!']);
            }

            return redirect()->route('admission.forms')
                ->with('success', 'Default maydonlar tiklandi!');

        } catch (\Exception $e) {
            DB::rollBack();
            \Log::error('Form reset error: ' . $e->getMessage());

            if ($request->ajax()) {
                return response()->json(['success' => false, 'message' => $e->getMessage()], 500);
            }

            return back()->with('error', 'Xatolik yuz berdi: ' . $e->getMessage());
        }
    }

    /**
     * Store single-page application
     */
    public function storeSingle(Request $request)
    {
        try {
            $validated = $request->validate([
                'firstName' => 'required|string|max:255',
                'lastName' => 'required|string|max:255',
                'dateOfBirth' => 'required|date',
                'gender' => 'required|in:male,female,other',
                'phone' => 'required|string|max:20',
                'email' => 'required|email|max:255',
                'nationality' => 'required|string|max:2',
                'occupation' => 'required|string',
                'jobPosition' => 'nullable|string|max:255',
                'institution' => 'nullable|string|max:255',
                'experience' => 'required|string',
                'employmentType' => 'required|string',
                'degree' => 'required|string',
                'graduationYear' => 'required|integer|min:1950|max:' . date('Y'),
                'lastDegreeInstitution' => 'nullable|string|max:255',
                'fieldOfStudy' => 'nullable|string|max:255',
                'cv' => 'required|file|mimes:pdf,doc,docx|max:2048',
                'passport' => 'required|file|mimes:jpg,jpeg,png,pdf|max:2048',
                'selectedModules' => 'required|string',
                'termsAccepted' => 'required|accepted',
            ]);
        } catch (\Illuminate\Validation\ValidationException $e) {
            return response()->json([
                'success' => false,
                'message' => 'Validation failed',
                'errors' => $e->errors()
            ], 422);
        }

        // Check for duplicate email before starting transaction
        $email = trim($validated['email']);
        $existingByEmail = AdmissionApplication::where('email', $email)->first();
        if ($existingByEmail) {
            return response()->json([
                'success' => false,
                'message' => 'Bu email manzili bilan oldin ariza topshirilgan. Iltimos, boshqa email manzilidan foydalaning yoki ariza holatini tekshiring.',
                'error_type' => 'duplicate_email'
            ], 422);
        }

        // Check for duplicate phone
        $phone = trim($validated['phone']);
        $existingByPhone = AdmissionApplication::where('phone', $phone)->first();
        if ($existingByPhone) {
            return response()->json([
                'success' => false,
                'message' => 'Bu telefon raqami bilan oldin ariza topshirilgan. Iltimos, boshqa telefon raqamidan foydalaning yoki ariza holatini tekshiring.',
                'error_type' => 'duplicate_phone'
            ], 422);
        }

        DB::beginTransaction();
        $uploadedFiles = [];

        try {
            // Generate application number
            $year = date('Y');
            $lastApp = AdmissionApplication::where('application_number', 'like', "APP-{$year}-%")
                ->orderBy('application_number', 'desc')
                ->first();

            $number = $lastApp
                ? intval(substr($lastApp->application_number, -5)) + 1
                : 1;

            $applicationNumber = sprintf("APP-%s-%05d", $year, $number);

            // Process file uploads
            if ($request->hasFile('cv')) {
                $cvPath = $request->file('cv')->store('admission/cv', 'public');
                $uploadedFiles['cv'] = $cvPath;
            }

            if ($request->hasFile('passport')) {
                $passportPath = $request->file('passport')->store('admission/passport', 'public');
                $uploadedFiles['passport'] = $passportPath;
            }

            // Create application
            $application = AdmissionApplication::create([
                'application_number' => $applicationNumber,
                'first_name' => $validated['firstName'],
                'last_name' => $validated['lastName'],
                'middle_name' => null,
                'birth_date' => $validated['dateOfBirth'],
                'gender' => $validated['gender'],
                'passport_series' => null,
                'passport_number' => null,
                'jshshir' => null,
                'phone' => $validated['phone'],
                'email' => $validated['email'],
                'region' => null,
                'district' => null,
                'address' => null,
                'education_type' => $validated['degree'] ?? null,
                'education_name' => $validated['lastDegreeInstitution'] ?? null,
                'graduation_year' => $validated['graduationYear'] ?? null,
                'dtm_score' => null,
                'faculty_id' => null,
                'specialty_id' => null,
                'education_form' => null,
                'education_language' => null,
                'photo_path' => null,
                'passport_copy_path' => $uploadedFiles['passport'] ?? null,
                'diploma_copy_path' => $uploadedFiles['cv'] ?? null,
                'certificate_copy_path' => null,
                'status' => 'pending',
                'applied_at' => now(),
                'form_data' => $validated,
                'form_version' => 2, // Version 2 for single-page form
            ]);

            DB::commit();

            return response()->json([
                'success' => true,
                'message' => 'Application submitted successfully!',
                'application_number' => $applicationNumber
            ]);

        } catch (\Exception $e) {
            DB::rollBack();

            // Delete uploaded files on failure
            foreach ($uploadedFiles as $filePath) {
                if ($filePath) {
                    Storage::disk('public')->delete($filePath);
                }
            }

            \Log::error('Admission single store error: ' . $e->getMessage());

            return response()->json([
                'success' => false,
                'message' => 'Error submitting application: ' . $e->getMessage()
            ], 500);
        }
    }

    /**
     * Export applications to Excel
     */
    public function export(Request $request)
    {
        // Check if user has marketing role
        if (!Auth::user()->hasRole(['Marketing', 'SuperAdmin', 'Admin'])) {
            abort(403, 'Ruxsat berilmagan');
        }

        $query = AdmissionApplication::with(['faculty', 'specialty']);

        // Apply filters
        if ($request->has('status')) {
            $query->where('status', $request->status);
        }
        if ($request->has('faculty_id')) {
            $query->where('faculty_id', $request->faculty_id);
        }
        if ($request->has('date_from')) {
            $query->whereDate('applied_at', '>=', $request->date_from);
        }
        if ($request->has('date_to')) {
            $query->whereDate('applied_at', '<=', $request->date_to);
        }

        $applications = $query->orderBy('applied_at', 'desc')->get();

        $spreadsheet = new Spreadsheet();
        $sheet = $spreadsheet->getActiveSheet();
        $sheet->setTitle('Arizalar');

        // ===== TITLE ROW =====
        $sheet->mergeCells('A1:O1');
        $sheet->setCellValue('A1', 'QABUL ARIZALARI RO\'YXATI');
        $sheet->getStyle('A1')->applyFromArray([
            'font' => ['bold' => true, 'size' => 16, 'color' => ['rgb' => 'FFFFFF']],
            'fill' => ['fillType' => Fill::FILL_SOLID, 'startColor' => ['rgb' => '1B4F72']],
            'alignment' => ['horizontal' => Alignment::HORIZONTAL_CENTER, 'vertical' => Alignment::VERTICAL_CENTER],
        ]);
        $sheet->getRowDimension(1)->setRowHeight(40);

        // ===== INFO ROW =====
        $sheet->mergeCells('A2:O2');
        $filterInfo = 'Jami: ' . $applications->count() . ' ta ariza';
        if ($request->has('status')) $filterInfo .= ' | Holat: ' . $request->status;
        if ($request->has('date_from')) $filterInfo .= ' | ' . $request->date_from . ' dan';
        if ($request->has('date_to')) $filterInfo .= ' ' . $request->date_to . ' gacha';
        $filterInfo .= ' | Sana: ' . date('d.m.Y H:i');
        $sheet->setCellValue('A2', $filterInfo);
        $sheet->getStyle('A2')->applyFromArray([
            'font' => ['italic' => true, 'size' => 10, 'color' => ['rgb' => '2C3E50']],
            'fill' => ['fillType' => Fill::FILL_SOLID, 'startColor' => ['rgb' => 'D6EAF8']],
            'alignment' => ['horizontal' => Alignment::HORIZONTAL_CENTER, 'vertical' => Alignment::VERTICAL_CENTER],
        ]);
        $sheet->getRowDimension(2)->setRowHeight(25);

        // ===== COLUMN HEADERS (Row 3) =====
        $headers = [
            'A' => '№',
            'B' => 'Ariza raqami',
            'C' => 'F.I.O',
            'D' => 'Tug\'ilgan sana',
            'E' => 'JSHSHIR',
            'F' => 'Telefon',
            'G' => 'Email',
            'H' => 'Viloyat',
            'I' => 'Tuman',
            'J' => 'Fakultet',
            'K' => 'Yo\'nalish',
            'L' => 'Ta\'lim shakli',
            'M' => 'Ta\'lim tili',
            'N' => 'Holat',
            'O' => 'Ariza sanasi',
        ];

        foreach ($headers as $col => $title) {
            $sheet->setCellValue($col . '3', $title);
        }

        $sheet->getStyle('A3:O3')->applyFromArray([
            'font' => ['bold' => true, 'size' => 11, 'color' => ['rgb' => 'FFFFFF']],
            'fill' => ['fillType' => Fill::FILL_SOLID, 'startColor' => ['rgb' => '2E86C1']],
            'alignment' => ['horizontal' => Alignment::HORIZONTAL_CENTER, 'vertical' => Alignment::VERTICAL_CENTER, 'wrapText' => true],
            'borders' => [
                'allBorders' => ['borderStyle' => Border::BORDER_THIN, 'color' => ['rgb' => '1B4F72']],
            ],
        ]);
        $sheet->getRowDimension(3)->setRowHeight(30);

        // ===== DATA ROWS =====
        $statusColors = [
            'pending'   => ['bg' => 'FFF3CD', 'text' => '856404'],
            'reviewing' => ['bg' => 'D1ECF1', 'text' => '0C5460'],
            'accepted'  => ['bg' => 'D4EDDA', 'text' => '155724'],
            'rejected'  => ['bg' => 'F8D7DA', 'text' => '721C24'],
            'waitlist'  => ['bg' => 'E2E3E5', 'text' => '383D41'],
        ];

        $row = 4;
        foreach ($applications as $index => $app) {
            $isEven = ($index % 2 === 0);
            $rowBgColor = $isEven ? 'F8F9FA' : 'FFFFFF';

            $sheet->setCellValue("A{$row}", $index + 1);
            $sheet->setCellValue("B{$row}", $app->application_number);
            $sheet->setCellValue("C{$row}", $app->full_name);
            $sheet->setCellValue("D{$row}", $app->birth_date ? $app->birth_date->format('d.m.Y') : '');
            $sheet->setCellValueExplicit("E{$row}", $app->jshshir, \PhpOffice\PhpSpreadsheet\Cell\DataType::TYPE_STRING);
            $sheet->setCellValue("F{$row}", $app->phone);
            $sheet->setCellValue("G{$row}", $app->email);
            $sheet->setCellValue("H{$row}", $app->region);
            $sheet->setCellValue("I{$row}", $app->district);
            $sheet->setCellValue("J{$row}", $app->faculty->name_uz ?? '');
            $sheet->setCellValue("K{$row}", $app->specialty->name_uz ?? '');
            $sheet->setCellValue("L{$row}", $app->education_form_text);
            $sheet->setCellValue("M{$row}", $app->education_language_text);
            $sheet->setCellValue("N{$row}", $app->status_text);
            $sheet->setCellValue("O{$row}", $app->applied_at ? $app->applied_at->format('d.m.Y H:i') : '');

            // Row background (zebra)
            $sheet->getStyle("A{$row}:O{$row}")->applyFromArray([
                'fill' => ['fillType' => Fill::FILL_SOLID, 'startColor' => ['rgb' => $rowBgColor]],
                'borders' => [
                    'allBorders' => ['borderStyle' => Border::BORDER_THIN, 'color' => ['rgb' => 'D5D8DC']],
                ],
                'alignment' => ['vertical' => Alignment::VERTICAL_CENTER, 'wrapText' => true],
            ]);

            // Status cell color
            $sc = $statusColors[$app->status] ?? null;
            if ($sc) {
                $sheet->getStyle("N{$row}")->applyFromArray([
                    'fill' => ['fillType' => Fill::FILL_SOLID, 'startColor' => ['rgb' => $sc['bg']]],
                    'font' => ['bold' => true, 'color' => ['rgb' => $sc['text']]],
                    'alignment' => ['horizontal' => Alignment::HORIZONTAL_CENTER],
                ]);
            }

            // Number column center
            $sheet->getStyle("A{$row}")->getAlignment()->setHorizontal(Alignment::HORIZONTAL_CENTER);
            // Date columns center
            $sheet->getStyle("D{$row}")->getAlignment()->setHorizontal(Alignment::HORIZONTAL_CENTER);
            $sheet->getStyle("O{$row}")->getAlignment()->setHorizontal(Alignment::HORIZONTAL_CENTER);

            $sheet->getRowDimension($row)->setRowHeight(22);
            $row++;
        }

        // ===== SUMMARY ROW =====
        $summaryRow = $row;
        $sheet->mergeCells("A{$summaryRow}:I{$summaryRow}");
        $sheet->setCellValue("A{$summaryRow}", "Jami arizalar soni: {$applications->count()}");

        $accepted = $applications->where('status', 'accepted')->count();
        $pending = $applications->where('status', 'pending')->count();
        $rejected = $applications->where('status', 'rejected')->count();
        $reviewing = $applications->where('status', 'reviewing')->count();

        $sheet->mergeCells("J{$summaryRow}:O{$summaryRow}");
        $sheet->setCellValue("J{$summaryRow}", "Qabul: {$accepted} | Kutilmoqda: {$pending} | Ko'rilmoqda: {$reviewing} | Rad: {$rejected}");

        $sheet->getStyle("A{$summaryRow}:O{$summaryRow}")->applyFromArray([
            'font' => ['bold' => true, 'size' => 11, 'color' => ['rgb' => 'FFFFFF']],
            'fill' => ['fillType' => Fill::FILL_SOLID, 'startColor' => ['rgb' => '1B4F72']],
            'alignment' => ['horizontal' => Alignment::HORIZONTAL_CENTER, 'vertical' => Alignment::VERTICAL_CENTER],
            'borders' => [
                'allBorders' => ['borderStyle' => Border::BORDER_THIN, 'color' => ['rgb' => '1B4F72']],
            ],
        ]);
        $sheet->getRowDimension($summaryRow)->setRowHeight(28);

        // ===== COLUMN WIDTHS =====
        $widths = ['A' => 5, 'B' => 16, 'C' => 28, 'D' => 14, 'E' => 16, 'F' => 16, 'G' => 22, 'H' => 16, 'I' => 16, 'J' => 22, 'K' => 22, 'L' => 14, 'M' => 12, 'N' => 18, 'O' => 18];
        foreach ($widths as $col => $w) {
            $sheet->getColumnDimension($col)->setWidth($w);
        }

        // ===== FREEZE & FILTER =====
        $sheet->freezePane('A4');
        $sheet->setAutoFilter("A3:O3");

        // ===== PRINT SETTINGS =====
        $sheet->getPageSetup()->setOrientation(\PhpOffice\PhpSpreadsheet\Worksheet\PageSetup::ORIENTATION_LANDSCAPE);
        $sheet->getPageSetup()->setFitToWidth(1);
        $sheet->getPageSetup()->setFitToHeight(0);

        // Output
        $filename = 'Arizalar_' . date('Y-m-d_H-i-s') . '.xlsx';

        header('Content-Type: application/vnd.openxmlformats-officedocument.spreadsheetml.sheet');
        header('Content-Disposition: attachment; filename="' . $filename . '"');
        header('Cache-Control: max-age=0');

        $writer = new Xlsx($spreadsheet);
        $writer->save('php://output');
        exit;
    }

    /**
     * Export single application to PDF
     */
    public function exportPdf(AdmissionApplication $application, Request $request)
    {
        // Load relationships
        $application->load(['faculty', 'specialty', 'reviewer']);

        // Generate PDF
        $pdf = \PDF::loadView('admission.pdf.application', [
            'application' => $application
        ]);

        // Set PDF options
        $pdf->setPaper('a4', 'portrait');
        $pdf->setOption('enable-local-file-access', true);

        // Get filename
        $filename = 'Ariza_' . $application->application_number . '.pdf';

        // Check if download parameter is present
        if ($request->has('download')) {
            return $pdf->download($filename);
        }

        // Otherwise, display in browser
        return $pdf->stream($filename);
    }
}