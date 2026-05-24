<?php

namespace App\Observers;

use App\Models\StudentGroup;
use App\Models\JournalEntry;
use App\Models\AcademicYear;
use App\Models\Subject;
use Illuminate\Support\Facades\Log;

class StudentGroupObserver
{
    /**
     * Handle the StudentGroup "created" event.
     */
    public function created(StudentGroup $studentGroup): void
    {
        // StudentGroup yaratilganda groups jadvaliga ham yozish (journal_entries uchun)
        // Birinchi mavjud department_id ni topish
        $firstDepartment = \App\Models\Department::first();

        $groupId = \DB::table('groups')->insertGetId([
            'name' => $studentGroup->name,
            'code' => $studentGroup->code ?? $studentGroup->name,
            'department_id' => $firstDepartment->id ?? 1,
            'course' => $studentGroup->course ?? 1,
            'created_at' => now(),
            'updated_at' => now(),
        ]);

        Log::info("Groups jadvaliga yozildi: ID #{$groupId}");

        // Guruh uchun avtomatik jurnal yaratish
        $this->createJournalsForGroup($studentGroup, $groupId);
    }

    /**
     * Create journal entries for the group
     */
    private function createJournalsForGroup(StudentGroup $studentGroup, int $groupId): void
    {
        // Joriy akademik yilni olish
        $currentAcademicYear = AcademicYear::where('is_current', true)->first();

        if (!$currentAcademicYear) {
            Log::warning("Guruh #{$studentGroup->id} uchun jurnal yaratilmadi: joriy akademik yil topilmadi");
            return;
        }

        // Barcha aktiv fanlarni olish (soddalashtirilgan versiya)
        $subjects = Subject::where('is_active', true)->limit(5)->get();

        if ($subjects->isEmpty()) {
            Log::warning("Guruh #{$studentGroup->id} uchun jurnal yaratilmadi: aktiv fanlar topilmadi");
            return;
        }

        // Birinchi mavjud teacherni topish
        $firstTeacher = \App\Models\Teacher::first();

        if (!$firstTeacher) {
            Log::warning("Guruh #{$studentGroup->id} uchun jurnal yaratilmadi: teacher topilmadi");
            return;
        }

        Log::info("Guruh #{$studentGroup->id} uchun {$subjects->count()} ta fan topildi");

        foreach ($subjects as $subject) {
            try {
                // Har bir fan uchun jurnal yaratish
                $journal = JournalEntry::create([
                    'subject_id' => $subject->id,
                    'group_id' => $groupId, // groups jadvalidagi ID
                    'teacher_id' => $firstTeacher->id, // Birinchi mavjud teacher
                    'academic_year_id' => $currentAcademicYear->id,
                    'semester_id' => 1, // Default semester
                ]);

                Log::info("Jurnal yaratildi: #{$journal->id} - Fan: {$subject->name_uz}");
            } catch (\Exception $e) {
                Log::error("Jurnal yaratishda xatolik (Fan: {$subject->id}): " . $e->getMessage());
            }
        }

        Log::info("Guruh #{$studentGroup->id} uchun jami {$subjects->count()} ta jurnal yaratildi");
    }

    /**
     * Handle the StudentGroup "updated" event.
     */
    public function updated(StudentGroup $studentGroup): void
    {
        //
    }

    /**
     * Handle the StudentGroup "deleted" event.
     */
    public function deleted(StudentGroup $studentGroup): void
    {
        //
    }

    /**
     * Handle the StudentGroup "restored" event.
     */
    public function restored(StudentGroup $studentGroup): void
    {
        //
    }

    /**
     * Handle the StudentGroup "force deleted" event.
     */
    public function forceDeleted(StudentGroup $studentGroup): void
    {
        //
    }
}
