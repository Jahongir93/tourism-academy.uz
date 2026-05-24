<?php

namespace App\Http\Controllers\Student;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use App\Models\Student;

class ScheduleController extends Controller
{
    public function index(Request $request)
    {
        $user = Auth::user();
        $student = Student::where('user_id', $user->id)->first();

        if (!$student) {
            return redirect()->route('student.dashboard')
                ->with('error', 'Student ma\'lumotlari topilmadi');
        }

        // Agar guruhda darslar biriktirilmagan bo'lsa
        if (!$student->group_id) {
            return view('student.schedule.index', [
                'student' => $student,
                'hasSchedule' => false,
                'message' => 'Siz hali guruhga biriktirilmagan'
            ]);
        }

        // Database'dan dars jadvalini olish (Schedule -> ScheduleSlot strukturasi)
        $scheduleMain = \App\Models\Schedule::where('group_id', $student->group_id)
            ->where('status', 'active')
            ->first();

        // Agar schedule yo'q bo'lsa
        if (!$scheduleMain) {
            return view('student.schedule.index', [
                'student' => $student,
                'hasSchedule' => false,
                'message' => 'Guruhingiz uchun hali darslar biriktirilmagan'
            ]);
        }

        // Schedule slot'larni olish
        $scheduleSlots = \App\Models\ScheduleSlot::where('schedule_id', $scheduleMain->id)
            ->with(['subject', 'teacher', 'room'])
            ->orderBy('day_of_week')
            ->orderBy('time_slot')
            ->get();

        // Agar slot'lar bo'sh bo'lsa
        if ($scheduleSlots->isEmpty()) {
            return view('student.schedule.index', [
                'student' => $student,
                'hasSchedule' => false,
                'message' => 'Guruhingiz uchun hali darslar biriktirilmagan'
            ]);
        }

        // Darslarni kun bo'yicha guruhlash
        $schedule = $this->formatScheduleSlots($scheduleSlots);
        $hasSchedule = true;

        return view('student.schedule.index', compact('student', 'schedule', 'hasSchedule'));
    }

    private function formatScheduleSlots($scheduleSlots)
    {
        $dayNames = [
            '1' => 'Dushanba',
            '2' => 'Seshanba',
            '3' => 'Chorshanba',
            '4' => 'Payshanba',
            '5' => 'Juma',
            '6' => 'Shanba',
        ];

        $lessonTypes = [
            'lecture' => 'Ma\'ruza',
            'practice' => 'Amaliyot',
            'seminar' => 'Seminar',
            'lab' => 'Laboratoriya',
        ];

        $timeSlots = [
            '1' => ['start' => '08:30', 'end' => '09:50'],
            '2' => ['start' => '10:00', 'end' => '11:20'],
            '3' => ['start' => '11:30', 'end' => '12:50'],
            '4' => ['start' => '13:30', 'end' => '14:50'],
            '5' => ['start' => '15:00', 'end' => '16:20'],
        ];

        $formatted = [];

        foreach ($dayNames as $dayNum => $dayUz) {
            $formatted[$dayUz] = [];

            // Filter by day_number (handles both string and int day_of_week values)
            $daySchedules = $scheduleSlots->filter(function($slot) use ($dayNum) {
                return $slot->day_number == $dayNum;
            });

            foreach ($daySchedules as $slot) {
                $timeSlot = $timeSlots[$slot->time_slot] ?? ['start' => '00:00', 'end' => '00:00'];

                // Get teacher name
                $teacherName = 'N/A';
                if ($slot->teacher) {
                    $teacherName = trim(($slot->teacher->last_name ?? '') . ' ' . ($slot->teacher->first_name ?? ''));
                    if (empty($teacherName)) {
                        $teacherName = $slot->teacher->name ?? 'N/A';
                    }
                }

                $formatted[$dayUz][] = [
                    'time' => $timeSlot['start'] . ' - ' . $timeSlot['end'],
                    'pair' => $slot->time_slot . '-juft',
                    'subject' => $slot->subject->name_uz ?? $slot->subject->name ?? 'N/A',
                    'type' => $lessonTypes[$slot->lesson_type] ?? $slot->lesson_type,
                    'teacher' => $teacherName,
                    'room' => $slot->room->name ?? 'N/A',
                    'building' => $slot->room->building->name ?? $slot->room->building ?? 'Asosiy bino',
                ];
            }
        }

        return $formatted;
    }

    private function getWeeklySchedule($student)
    {
        // Demo dars jadvali - keyinchalik database'dan olinadi
        return [
            'Dushanba' => [
                [
                    'time' => '08:30 - 09:50',
                    'pair' => '1-juft',
                    'subject' => 'Algoritmlar va ma\'lumotlar tuzilmasi',
                    'type' => 'Ma\'ruza',
                    'teacher' => 'Prof. Karimov J.A.',
                    'room' => '301-xona',
                    'building' => 'Asosiy bino',
                ],
                [
                    'time' => '10:00 - 11:20',
                    'pair' => '2-juft',
                    'subject' => 'Web dasturlash',
                    'type' => 'Amaliyot',
                    'teacher' => 'Aliyev S.R.',
                    'room' => 'Lab-2',
                    'building' => 'IT markaz',
                ],
                [
                    'time' => '11:30 - 12:50',
                    'pair' => '3-juft',
                    'subject' => 'Ma\'lumotlar bazasi',
                    'type' => 'Seminar',
                    'teacher' => 'Nurmatov B.X.',
                    'room' => '205-xona',
                    'building' => 'Asosiy bino',
                ],
            ],
            'Seshanba' => [
                [
                    'time' => '08:30 - 09:50',
                    'pair' => '1-juft',
                    'subject' => 'Dasturiy injiniring',
                    'type' => 'Ma\'ruza',
                    'teacher' => 'Doc. Rahimov A.A.',
                    'room' => '202-xona',
                    'building' => 'Asosiy bino',
                ],
                [
                    'time' => '10:00 - 11:20',
                    'pair' => '2-juft',
                    'subject' => 'Kompyuter tarmoqlari',
                    'type' => 'Amaliyot',
                    'teacher' => 'Hasanov M.K.',
                    'room' => 'Lab-1',
                    'building' => 'IT markaz',
                ],
            ],
            'Chorshanba' => [
                [
                    'time' => '08:30 - 09:50',
                    'pair' => '1-juft',
                    'subject' => 'Matematik analiz',
                    'type' => 'Ma\'ruza',
                    'teacher' => 'Prof. Ismoilov Z.Z.',
                    'room' => '105-xona',
                    'building' => 'Asosiy bino',
                ],
                [
                    'time' => '10:00 - 11:20',
                    'pair' => '2-juft',
                    'subject' => 'Algoritmlar va ma\'lumotlar tuzilmasi',
                    'type' => 'Amaliyot',
                    'teacher' => 'Prof. Karimov J.A.',
                    'room' => 'Lab-3',
                    'building' => 'IT markaz',
                ],
            ],
            'Payshanba' => [
                [
                    'time' => '08:30 - 09:50',
                    'pair' => '1-juft',
                    'subject' => 'O\'zbek tili va adabiyoti',
                    'type' => 'Seminar',
                    'teacher' => 'Abdullayeva N.S.',
                    'room' => '308-xona',
                    'building' => 'Asosiy bino',
                ],
                [
                    'time' => '10:00 - 11:20',
                    'pair' => '2-juft',
                    'subject' => 'Ma\'lumotlar bazasi',
                    'type' => 'Amaliyot',
                    'teacher' => 'Nurmatov B.X.',
                    'room' => 'Lab-2',
                    'building' => 'IT markaz',
                ],
            ],
            'Juma' => [
                [
                    'time' => '08:30 - 09:50',
                    'pair' => '1-juft',
                    'subject' => 'Web dasturlash',
                    'type' => 'Ma\'ruza',
                    'teacher' => 'Aliyev S.R.',
                    'room' => '201-xona',
                    'building' => 'Asosiy bino',
                ],
                [
                    'time' => '10:00 - 11:20',
                    'pair' => '2-juft',
                    'subject' => 'Jismoniy tarbiya',
                    'type' => 'Amaliyot',
                    'teacher' => 'Yusupov R.T.',
                    'room' => 'Sport zali',
                    'building' => 'Sport majmuasi',
                ],
            ],
            'Shanba' => [],
        ];
    }
}
