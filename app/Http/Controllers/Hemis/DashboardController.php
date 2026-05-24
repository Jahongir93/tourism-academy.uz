<?php

namespace App\Http\Controllers\Hemis;

use App\Http\Controllers\Controller;
use App\Models\Hemis\Faculty;
use App\Models\Hemis\Student;
use App\Models\Hemis\University;
use App\Models\User;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;

class DashboardController extends Controller
{
    /**
     * Rektor dashboard
     */
    public function rectorDashboard()
    {
        $university = University::first();
        
        $stats = [
            'total_students' => Student::active()->count(),
            'total_teachers' => User::role('Teacher')->count(),
            'total_faculties' => Faculty::active()->count(),
            'total_departments' => DB::table('departments')->where('is_active', true)->count(),
            'by_degree' => [
                'bakalavr' => Student::active()
                    ->whereHas('specialty', fn($q) => $q->where('degree', 'bakalavr'))
                    ->count(),
                'magistr' => Student::active()
                    ->whereHas('specialty', fn($q) => $q->where('degree', 'magistr'))
                    ->count(),
                'doktorantura' => Student::active()
                    ->whereHas('specialty', fn($q) => $q->where('degree', 'doktorantura'))
                    ->count(),
            ],
            'by_education_type' => [
                'byudjet' => Student::active()->where('education_type', 'byudjet')->count(),
                'shartnoma' => Student::active()->where('education_type', 'shartnoma')->count(),
            ],
            'by_education_form' => [
                'kunduzgi' => Student::active()->where('education_form', 'kunduzgi')->count(),
                'sirtqi' => Student::active()->where('education_form', 'sirtqi')->count(),
                'kechki' => Student::active()->where('education_form', 'kechki')->count(),
                'masofaviy' => Student::active()->where('education_form', 'masofaviy')->count(),
            ],
        ];

        $faculties = Faculty::with(['specialties', 'departments'])
            ->withCount(['students' => fn($q) => $q->where('status', 'active')])
            ->ordered()
            ->get();

        return view('hemis.dashboard.rector', compact('stats', 'faculties', 'university'));
    }

    /**
     * Dekan dashboard
     */
    public function deanDashboard()
    {
        $user = Auth::user();
        $faculty = Faculty::where('dean_user_id', $user->id)->first();
        
        if (!$faculty) {
            abort(403, 'Siz dekan sifatida tayinlanmagansiz');
        }

        $stats = [
            'total_students' => $faculty->students()->active()->count(),
            'total_groups' => $faculty->groups()->where('is_active', true)->count(),
            'total_specialties' => $faculty->specialties()->where('is_active', true)->count(),
            'total_departments' => $faculty->departments()->where('is_active', true)->count(),
            'by_course' => [
                '1' => $faculty->students()->active()->where('course', 1)->count(),
                '2' => $faculty->students()->active()->where('course', 2)->count(),
                '3' => $faculty->students()->active()->where('course', 3)->count(),
                '4' => $faculty->students()->active()->where('course', 4)->count(),
            ],
        ];

        $specialties = $faculty->specialties()
            ->withCount(['students' => fn($q) => $q->where('status', 'active')])
            ->get();

        $departments = $faculty->departments()
            ->with('head')
            ->withCount('subjects')
            ->get();

        return view('hemis.dashboard.dean', compact('faculty', 'stats', 'specialties', 'departments'));
    }

    /**
     * O'qituvchi dashboard
     */
    public function teacherDashboard()
    {
        $user = Auth::user();
        
        $subjects = DB::table('teacher_subjects')
            ->join('subjects', 'teacher_subjects.subject_id', '=', 'subjects.id')
            ->where('teacher_subjects.teacher_id', $user->id)
            ->where('teacher_subjects.academic_year', date('Y'))
            ->select('subjects.*')
            ->get();

        $groups = DB::table('teacher_subjects')
            ->join('academic_groups', 'teacher_subjects.group_id', '=', 'academic_groups.id')
            ->where('teacher_subjects.teacher_id', $user->id)
            ->where('teacher_subjects.academic_year', date('Y'))
            ->select('academic_groups.*')
            ->distinct()
            ->get();

        $totalStudents = DB::table('students')
            ->whereIn('group_id', $groups->pluck('id'))
            ->where('status', 'active')
            ->count();

        $schedule = DB::table('schedules')
            ->where('teacher_id', $user->id)
            ->where('academic_year', date('Y'))
            ->orderBy('day_of_week')
            ->orderBy('start_time')
            ->get();

        return view('hemis.dashboard.teacher', compact('subjects', 'groups', 'totalStudents', 'schedule'));
    }

    /**
     * Talaba dashboard
     */
    public function studentDashboard()
    {
        $user = Auth::user();
        $student = Student::where('user_id', $user->id)->first();
        
        if (!$student) {
            abort(403, 'Talaba ma\'lumotlari topilmadi');
        }

        $currentGrades = $student->grades()
            ->with('subject', 'teacher')
            ->where('academic_year', date('Y'))
            ->where('semester', $student->semester)
            ->get();

        $schedule = DB::table('schedules')
            ->join('subjects', 'schedules.subject_id', '=', 'subjects.id')
            ->join('users as teachers', 'schedules.teacher_id', '=', 'teachers.id')
            ->where('schedules.group_id', $student->group_id)
            ->where('schedules.academic_year', date('Y'))
            ->select(
                'schedules.*',
                'subjects.name_uz as subject_name',
                'teachers.name as teacher_name'
            )
            ->orderBy('day_of_week')
            ->orderBy('start_time')
            ->get();

        $attendance = $student->attendance()
            ->where('academic_year', date('Y'))
            ->where('semester', $student->semester)
            ->get()
            ->groupBy('subject_id');

        $payments = null;
        if ($student->education_type === 'shartnoma') {
            $payments = $student->payments()
                ->where('academic_year', date('Y'))
                ->orderBy('due_date')
                ->get();
        }

        return view('hemis.dashboard.student', compact('student', 'currentGrades', 'schedule', 'attendance', 'payments'));
    }

    /**
     * Universal statistics API
     */
    public function getStatistics(Request $request)
    {
        $type = $request->get('type', 'general');
        $facultyId = $request->get('faculty_id');
        $specialtyId = $request->get('specialty_id');
        
        $stats = [];

        switch($type) {
            case 'students':
                $query = Student::active();
                if ($facultyId) $query->where('faculty_id', $facultyId);
                if ($specialtyId) $query->where('specialty_id', $specialtyId);
                
                $stats = [
                    'total' => $query->count(),
                    'by_gender' => [
                        'erkak' => (clone $query)->where('gender', 'erkak')->count(),
                        'ayol' => (clone $query)->where('gender', 'ayol')->count(),
                    ],
                    'by_course' => [
                        '1' => (clone $query)->where('course', 1)->count(),
                        '2' => (clone $query)->where('course', 2)->count(),
                        '3' => (clone $query)->where('course', 3)->count(),
                        '4' => (clone $query)->where('course', 4)->count(),
                    ],
                ];
                break;
                
            case 'academic':
                $stats = [
                    'average_gpa' => Student::active()->avg('gpa') ?? 0,
                    'excellent_students' => Student::active()->where('gpa', '>=', 4.5)->count(),
                    'failed_students' => DB::table('grades')
                        ->where('academic_year', date('Y'))
                        ->where('status', 'failed')
                        ->distinct('student_id')
                        ->count(),
                ];
                break;
        }

        return response()->json($stats);
    }
}