<?php

namespace App\Http\Controllers;

use App\Models\Notification;
use App\Models\User;
use App\Models\Student;
use App\Models\Faculty;
use App\Models\Group;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;

class NotificationController extends Controller
{
    public function index()
    {
        $notifications = Auth::user()->notifications()
            ->orderBy('created_at', 'desc')
            ->paginate(20);

        return view('notifications.index-new', compact('notifications'));
    }

    public function getNotifications()
    {
        $notifications = Auth::user()->notifications()
            ->orderBy('created_at', 'desc')
            ->limit(10)
            ->get()
            ->map(function ($notification) {
                return [
                    'id' => $notification->id,
                    'type' => $notification->type,
                    'title' => $notification->title,
                    'message' => $notification->message,
                    'data' => $notification->data,
                    'is_read' => $notification->is_read,
                    'created_at' => $notification->created_at->diffForHumans(),
                ];
            });

        $unreadCount = Auth::user()->notifications()->unread()->count();

        return response()->json([
            'notifications' => $notifications,
            'unread_count' => $unreadCount,
        ]);
    }

    public function markAsRead($id)
    {
        $notification = Auth::user()->notifications()->findOrFail($id);
        $notification->markAsRead();

        return response()->json(['success' => true]);
    }

    public function markAllAsRead()
    {
        Auth::user()->notifications()->unread()->update([
            'is_read' => true,
            'read_at' => now(),
        ]);

        if (request()->wantsJson()) {
            return response()->json(['success' => true]);
        }

        return back()->with('success', 'Barcha bildirishnomalar o\'qilgan deb belgilandi');
    }

    public function destroy($id)
    {
        $notification = Auth::user()->notifications()->findOrFail($id);
        $notification->delete();

        return back()->with('success', 'Bildirishnoma o\'chirildi');
    }

    public static function createNotification($userId, $type, $title, $message, $data = null)
    {
        return Notification::create([
            'user_id' => $userId,
            'type' => $type,
            'title' => $title,
            'message' => $message,
            'data' => $data,
        ]);
    }

    /**
     * Show notification sender page (Marketing only)
     */
    public function sender()
    {
        if (!Auth::user()->hasRole(['Marketing', 'SuperAdmin', 'Admin'])) {
            abort(403, 'Ruxsat berilmagan');
        }

        $faculties = Faculty::where('is_active', true)->orderBy('name_uz')->get();
        $groups = Group::with('specialty.faculty')->orderBy('name')->get();

        // Get notification history
        $sentNotifications = DB::table('notifications')
            ->select('title', 'message', 'type', 'created_at', DB::raw('COUNT(*) as recipient_count'))
            ->where('type', 'marketing')
            ->groupBy('title', 'message', 'type', 'created_at')
            ->orderBy('created_at', 'desc')
            ->limit(20)
            ->get();

        return view('notifications.sender', compact('faculties', 'groups', 'sentNotifications'));
    }

    /**
     * Send notification to selected students (Marketing only)
     */
    public function send(Request $request)
    {
        if (!Auth::user()->hasRole(['Marketing', 'SuperAdmin', 'Admin'])) {
            abort(403, 'Ruxsat berilmagan');
        }

        $request->validate([
            'title' => 'required|string|max:255',
            'message' => 'required|string|max:1000',
            'target' => 'required|in:all,faculty,group,custom',
            'faculty_id' => 'required_if:target,faculty|nullable|exists:faculties,id',
            'group_id' => 'required_if:target,group|nullable|exists:groups,id',
            'user_ids' => 'required_if:target,custom|nullable|array',
        ]);

        $userIds = [];

        switch ($request->target) {
            case 'all':
                // All students
                $userIds = Student::whereNotNull('user_id')->pluck('user_id')->toArray();
                break;

            case 'faculty':
                // Students in selected faculty
                $userIds = Student::whereHas('group.specialty', function ($q) use ($request) {
                    $q->where('faculty_id', $request->faculty_id);
                })->whereNotNull('user_id')->pluck('user_id')->toArray();
                break;

            case 'group':
                // Students in selected group
                $userIds = Student::where('group_id', $request->group_id)
                    ->whereNotNull('user_id')
                    ->pluck('user_id')
                    ->toArray();
                break;

            case 'custom':
                // Custom selected users
                $userIds = $request->user_ids ?? [];
                break;
        }

        if (empty($userIds)) {
            return back()->with('error', 'Hech qanday talaba topilmadi!');
        }

        // Create notifications in bulk
        $notifications = [];
        $now = now();
        foreach ($userIds as $userId) {
            $notifications[] = [
                'user_id' => $userId,
                'type' => 'marketing',
                'title' => $request->title,
                'message' => $request->message,
                'data' => json_encode(['sender_id' => Auth::id()]),
                'is_read' => false,
                'created_at' => $now,
                'updated_at' => $now,
            ];
        }

        // Insert in chunks for better performance
        foreach (array_chunk($notifications, 500) as $chunk) {
            Notification::insert($chunk);
        }

        return back()->with('success', count($userIds) . ' ta talabaga xabar yuborildi!');
    }

    /**
     * Send notification when new lesson is created
     */
    public static function notifyNewLesson($lesson, $groupId)
    {
        $userIds = Student::where('group_id', $groupId)
            ->whereNotNull('user_id')
            ->pluck('user_id')
            ->toArray();

        foreach ($userIds as $userId) {
            self::createNotification(
                $userId,
                'lesson',
                'Yangi dars qo\'shildi',
                $lesson->subject->name_uz . ' fanidan yangi dars mavjud: ' . $lesson->title,
                ['lesson_id' => $lesson->id]
            );
        }
    }

    /**
     * Send notification when test is assigned
     */
    public static function notifyTestAssigned($exam, $groupIds)
    {
        $userIds = Student::whereIn('group_id', (array)$groupIds)
            ->whereNotNull('user_id')
            ->pluck('user_id')
            ->toArray();

        foreach ($userIds as $userId) {
            self::createNotification(
                $userId,
                'exam',
                'Yangi test belgilandi',
                $exam->title . ' - Deadline: ' . $exam->end_date->format('d.m.Y H:i'),
                ['exam_id' => $exam->id]
            );
        }
    }

    /**
     * Send notification when grade is given
     */
    public static function notifyGradeGiven($student, $subject, $grade, $gradeType = 'grade')
    {
        if (!$student->user_id) return;

        $typeLabels = [
            'grade' => 'Baho',
            'attendance' => 'Davomat',
            'homework' => 'Uy vazifasi',
            'exam' => 'Imtihon',
        ];

        self::createNotification(
            $student->user_id,
            'grade',
            ($typeLabels[$gradeType] ?? 'Baho') . ' qo\'yildi',
            $subject . ' fanidan ' . $grade . ' ball olindi',
            ['grade' => $grade, 'subject' => $subject, 'type' => $gradeType]
        );
    }

    /**
     * Send deadline reminder
     */
    public static function notifyDeadlineReminder($exam)
    {
        $groupIds = $exam->groups->pluck('id')->toArray();

        $userIds = Student::whereIn('group_id', $groupIds)
            ->whereNotNull('user_id')
            ->pluck('user_id')
            ->toArray();

        foreach ($userIds as $userId) {
            self::createNotification(
                $userId,
                'reminder',
                'Deadline yaqinlashmoqda!',
                $exam->title . ' - ' . $exam->end_date->diffForHumans(),
                ['exam_id' => $exam->id, 'deadline' => $exam->end_date->toISOString()]
            );
        }
    }
}