<?php

namespace App\Console\Commands;

use App\Models\GroupSubject;
use App\Models\VedomostSheet;
use App\Models\AcademicYear;
use Illuminate\Console\Command;

class GenerateVedomostSheets extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'vedomost:generate
                            {--year-id= : Academic year ID}
                            {--semester= : Semester number (1 or 2)}
                            {--force : Recreate existing sheets}';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'O\'qituvchilarga biriktirilgan fan+guruh uchun vedomost varaqlarini avtomatik yaratish';

    /**
     * Execute the console command.
     */
    public function handle()
    {
        $this->info('Vedomost varaqlarini yaratish boshlandi...');

        // Get academic year
        $yearId = $this->option('year-id');
        if (!$yearId) {
            $currentYear = AcademicYear::where('is_current', true)->first();
            if (!$currentYear) {
                $this->error('Joriy o\'quv yili topilmadi! --year-id parametrini kiriting.');
                return 1;
            }
            $yearId = $currentYear->id;
            $this->info("Joriy o'quv yili: {$currentYear->name}");
        }

        // Get semester
        $semester = $this->option('semester');
        $force = $this->option('force');

        // Build query
        $query = GroupSubject::where('is_active', true)
            ->where('academic_year_id', $yearId)
            ->whereNotNull('teacher_id')
            ->with(['group', 'subject', 'teacher']);

        if ($semester) {
            $query->where('semester', $semester);
        }

        $groupSubjects = $query->get();

        if ($groupSubjects->isEmpty()) {
            $this->warn('Hech qanday guruh-fan biriktirilmagan topilmadi.');
            return 0;
        }

        $this->info("Topildi: {$groupSubjects->count()} ta guruh-fan biriktirilgan");

        $created = 0;
        $skipped = 0;
        $updated = 0;

        foreach ($groupSubjects as $gs) {
            // Check if already exists
            $existing = VedomostSheet::where('group_id', $gs->group_id)
                ->where('subject_id', $gs->subject_id)
                ->where('academic_year_id', $gs->academic_year_id)
                ->where('semester', $gs->semester)
                ->first();

            if ($existing && !$force) {
                $skipped++;
                continue;
            }

            $data = [
                'group_id' => $gs->group_id,
                'subject_id' => $gs->subject_id,
                'teacher_id' => $gs->teacher_id,
                'academic_year_id' => $gs->academic_year_id,
                'semester' => $gs->semester,
                'credits' => $gs->subject->credits ?? 3,
                'assessment_type' => 'exam',
                'status' => 'draft',
                'is_active' => true,
            ];

            if ($existing && $force) {
                $existing->update($data);
                $updated++;
                $this->line("  Yangilandi: {$gs->group->name} - {$gs->subject->name}");
            } else {
                VedomostSheet::create($data);
                $created++;
                $this->line("  Yaratildi: {$gs->group->name} - {$gs->subject->name} (O'qituvchi: {$gs->teacher->name})");
            }
        }

        $this->newLine();
        $this->info("✓ Yaratilgan: {$created}");
        if ($updated > 0) {
            $this->info("✓ Yangilangan: {$updated}");
        }
        if ($skipped > 0) {
            $this->warn("⊘ O'tkazilgan (mavjud): {$skipped}");
        }
        $this->info('Jarayon tugadi!');

        return 0;
    }
}
