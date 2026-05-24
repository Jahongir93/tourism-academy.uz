<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use App\Models\Assignment;
use App\Models\AssignmentSubmission;
use App\Models\Student;
use Carbon\Carbon;

class AssignmentController extends Controller
{
    /**
     * Display a listing of assignments for the student.
     */
    public function index()
    {
        $user = Auth::user();
        $student = Student::where('user_id', $user->id)->first();

        if (!$student) {
            return redirect()->route('dashboard')->with('error', 'Talaba ma\'lumotlari topilmadi.');
        }

        // Get all assignments for student's group
        $assignments = Assignment::with(['subject', 'teacher.user'])
            ->where(function($query) use ($student) {
                $query->whereJsonContains('group_ids', $student->group_id)
                      ->orWhereJsonContains('group_ids', (string)$student->group_id);
            })
            ->orderBy('deadline', 'desc')
            ->paginate(15);

        // Get submission status for each assignment
        $submittedIds = $student->assignmentSubmissions()->pluck('assignment_id')->toArray();

        $assignments->getCollection()->transform(function($assignment) use ($submittedIds, $student) {
            $submission = $student->assignmentSubmissions()->where('assignment_id', $assignment->id)->first();

            $assignment->is_submitted = in_array($assignment->id, $submittedIds);
            $assignment->submission = $submission;
            $assignment->is_overdue = Carbon::parse($assignment->deadline)->isPast() && !$assignment->is_submitted;
            $assignment->days_until = now()->diffInDays(Carbon::parse($assignment->deadline), false);

            return $assignment;
        });

        return view('student.assignments.index', compact('assignments', 'student'));
    }

    /**
     * Show the form for creating a new assignment submission.
     */
    public function show($id)
    {
        $user = Auth::user();
        $student = Student::where('user_id', $user->id)->first();

        if (!$student) {
            return redirect()->route('dashboard')->with('error', 'Talaba ma\'lumotlari topilmadi.');
        }

        $assignment = Assignment::with(['subject', 'teacher.user', 'submissions'])
            ->findOrFail($id);

        // Check if student's group has access to this assignment
        $groupIds = $assignment->group_ids ?? [];
        if (!in_array($student->group_id, $groupIds)) {
            abort(403, 'Bu topshiriqqa kirishga ruxsat yo\'q.');
        }

        $submission = $student->assignmentSubmissions()->where('assignment_id', $id)->first();

        return view('student.assignments.show', compact('assignment', 'student', 'submission'));
    }

    /**
     * Store a newly created assignment submission.
     */
    public function submit(Request $request, $id)
    {
        $user = Auth::user();
        $student = Student::where('user_id', $user->id)->first();

        if (!$student) {
            return redirect()->route('dashboard')->with('error', 'Talaba ma\'lumotlari topilmadi.');
        }

        $assignment = Assignment::findOrFail($id);

        // Check if already submitted
        $existingSubmission = $student->assignmentSubmissions()->where('assignment_id', $id)->first();
        if ($existingSubmission) {
            return redirect()->back()->with('error', 'Bu topshiriq allaqachon topshirilgan.');
        }

        $request->validate([
            'text_content' => 'nullable|string',
            'files.*' => 'nullable|file|max:10240', // 10MB max per file
        ]);

        $files = [];
        if ($request->hasFile('files')) {
            foreach ($request->file('files') as $file) {
                $path = $file->store('assignments', 'public');
                $files[] = [
                    'name' => $file->getClientOriginalName(),
                    'path' => $path,
                    'size' => $file->getSize()
                ];
            }
        }

        $status = Carbon::now()->isAfter($assignment->deadline) ? 'late' : 'submitted';

        AssignmentSubmission::create([
            'assignment_id' => $id,
            'student_id' => $student->id,
            'submitted_at' => now(),
            'text_content' => $request->text_content,
            'files' => $files,
            'status' => $status
        ]);

        return redirect()->route('assignments.show', $id)->with('success', 'Topshiriq muvaffaqiyatli topshirildi!');
    }
}
