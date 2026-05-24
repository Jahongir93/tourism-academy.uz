<?php

namespace App\Http\Controllers;

use App\Models\AttendanceRecord;
use App\Models\JournalEntry;
use App\Models\Student;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;
use Carbon\Carbon;

class AttendanceController extends Controller
{
    public function allAttendance()
    {
        $attendanceRecords = AttendanceRecord::with(['journalEntry.subject', 'journalEntry.group', 'student'])
            ->latest('lesson_date')
            ->paginate(20);
            
        $statistics = [
            'total_records' => AttendanceRecord::count(),
            'present_count' => AttendanceRecord::where('status', 'present')->count(),
            'absent_count' => AttendanceRecord::where('status', 'absent')->count(),
            'late_count' => AttendanceRecord::where('status', 'late')->count(),
        ];
        
        return view('attendance.all', compact('attendanceRecords', 'statistics'));
    }
    
    public function index($journalId)
    {
        $journal = JournalEntry::with(['group', 'subject', 'teacher', 'academicYear'])
            ->findOrFail($journalId);

        $students = Student::where('group_id', $journal->group_id)
            ->orderBy('last_name')
            ->orderBy('first_name')
            ->get();

        // Davomat recordlari
        $attendances = AttendanceRecord::where('journal_entry_id', $journal->id)
            ->orderBy('lesson_date', 'desc')
            ->get()
            ->groupBy('lesson_date');

        return view('attendance.index', compact('journal', 'students', 'attendances'));
    }

    public function create($journalId)
    {
        $journal = JournalEntry::with(['group', 'subject', 'teacher'])
            ->findOrFail($journalId);

        $students = Student::where('group_id', $journal->group_id)
            ->orderBy('last_name')
            ->orderBy('first_name')
            ->get();

        $existingAttendance = false;

        return view('attendance.create', compact('journal', 'students', 'existingAttendance'));
    }

    public function store(Request $request, JournalEntry $journal)
    {
        $validated = $request->validate([
            'lesson_date' => 'required|date',
            'lesson_type' => 'required|in:lecture,practice,lab,seminar',
            'time_slot' => 'required|string',
            'attendance' => 'required|array',
            'attendance.*.student_id' => 'required|exists:students,id',
            'attendance.*.status' => 'required|in:present,absent,excused,late',
            'attendance.*.late_minutes' => 'nullable|integer|min:0',
            'attendance.*.notes' => 'nullable|string'
        ]);

        DB::transaction(function () use ($validated, $journal) {
            foreach ($validated['attendance'] as $record) {
                AttendanceRecord::create([
                    'journal_entry_id' => $journal->id,
                    'student_id' => $record['student_id'],
                    'lesson_date' => $validated['lesson_date'],
                    'lesson_type' => $validated['lesson_type'],
                    'time_slot' => $validated['time_slot'],
                    'status' => $record['status'],
                    'late_minutes' => $record['late_minutes'] ?? null,
                    'notes' => $record['notes'] ?? null,
                    'marked_by' => Auth::id(),
                    'marked_at' => now()
                ]);
            }
        });

        return redirect()->route('attendance.index', $journal)
            ->with('success', 'Davomat muvaffaqiyatli belgilandi');
    }

    public function edit(JournalEntry $journal, $date)
    {
        $students = Student::where('group_id', $journal->group_id)
            ->orderBy('last_name')
            ->get();

        $attendances = AttendanceRecord::where('journal_entry_id', $journal->id)
            ->whereDate('lesson_date', $date)
            ->get()
            ->keyBy('student_id');

        return view('attendance.edit', compact('journal', 'students', 'attendances', 'date'));
    }

    public function update(Request $request, JournalEntry $journal, $date)
    {
        $validated = $request->validate([
            'lesson_type' => 'required|in:lecture,practice,lab,seminar',
            'time_slot' => 'required|string',
            'attendance' => 'required|array',
            'attendance.*.student_id' => 'required|exists:students,id',
            'attendance.*.status' => 'required|in:present,absent,excused,late',
            'attendance.*.late_minutes' => 'nullable|integer|min:0',
            'attendance.*.notes' => 'nullable|string'
        ]);

        DB::transaction(function () use ($validated, $journal, $date) {
            AttendanceRecord::where('journal_entry_id', $journal->id)
                ->whereDate('lesson_date', $date)
                ->delete();

            foreach ($validated['attendance'] as $record) {
                AttendanceRecord::create([
                    'journal_entry_id' => $journal->id,
                    'student_id' => $record['student_id'],
                    'lesson_date' => $date,
                    'lesson_type' => $validated['lesson_type'],
                    'time_slot' => $validated['time_slot'],
                    'status' => $record['status'],
                    'late_minutes' => $record['late_minutes'] ?? null,
                    'notes' => $record['notes'] ?? null,
                    'marked_by' => Auth::id(),
                    'marked_at' => now()
                ]);
            }
        });

        return redirect()->route('attendance.index', $journal)
            ->with('success', 'Davomat muvaffaqiyatli yangilandi');
    }

    public function report(JournalEntry $journal)
    {
        $students = Student::where('group_id', $journal->group_id)
            ->orderBy('last_name')
            ->get();

        $attendanceData = [];
        
        foreach ($students as $student) {
            $records = AttendanceRecord::where('journal_entry_id', $journal->id)
                ->where('student_id', $student->id)
                ->get();

            $attendanceData[$student->id] = [
                'student' => $student,
                'total' => $records->count(),
                'present' => $records->where('status', 'present')->count(),
                'absent' => $records->where('status', 'absent')->count(),
                'excused' => $records->where('status', 'excused')->count(),
                'late' => $records->where('status', 'late')->count(),
                'percentage' => $records->count() > 0 
                    ? round(($records->whereIn('status', ['present', 'late'])->count() / $records->count()) * 100, 2)
                    : 0
            ];
        }

        return view('attendance.report', compact('journal', 'attendanceData'));
    }

    public function bulkMark(Request $request, JournalEntry $journal)
    {
        $validated = $request->validate([
            'lesson_date' => 'required|date',
            'lesson_type' => 'required|in:lecture,practice,lab,seminar',
            'time_slot' => 'required|string',
            'status' => 'required|in:present,absent'
        ]);

        $students = Student::where('group_id', $journal->group_id)->get();

        DB::transaction(function () use ($validated, $journal, $students) {
            foreach ($students as $student) {
                AttendanceRecord::create([
                    'journal_entry_id' => $journal->id,
                    'student_id' => $student->id,
                    'lesson_date' => $validated['lesson_date'],
                    'lesson_type' => $validated['lesson_type'],
                    'time_slot' => $validated['time_slot'],
                    'status' => $validated['status'],
                    'marked_by' => Auth::id(),
                    'marked_at' => now()
                ]);
            }
        });

        return redirect()->route('attendance.index', $journal)
            ->with('success', 'Davomat muvaffaqiyatli belgilandi');
    }

    public function statistics(JournalEntry $journal)
    {
        $stats = [
            'by_lesson_type' => AttendanceRecord::where('journal_entry_id', $journal->id)
                ->select('lesson_type', 'status', DB::raw('count(*) as count'))
                ->groupBy('lesson_type', 'status')
                ->get(),
            
            'by_month' => AttendanceRecord::where('journal_entry_id', $journal->id)
                ->select(
                    DB::raw('MONTH(lesson_date) as month'),
                    'status',
                    DB::raw('count(*) as count')
                )
                ->groupBy('month', 'status')
                ->get(),
            
            'low_attendance' => Student::where('group_id', $journal->group_id)
                ->whereHas('attendanceRecords', function ($query) use ($journal) {
                    $query->where('journal_entry_id', $journal->id);
                })
                ->withCount([
                    'attendanceRecords as total_classes' => function ($query) use ($journal) {
                        $query->where('journal_entry_id', $journal->id);
                    },
                    'attendanceRecords as attended_classes' => function ($query) use ($journal) {
                        $query->where('journal_entry_id', $journal->id)
                            ->whereIn('status', ['present', 'late']);
                    }
                ])
                ->havingRaw('(attended_classes * 100.0 / total_classes) < 75')
                ->get()
        ];

        return view('attendance.statistics', compact('journal', 'stats'));
    }
}