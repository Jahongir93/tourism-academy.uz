<?php

namespace Database\Seeders;

use App\Models\AcademicYear;
use Illuminate\Database\Seeder;

/**
 * Seeds academic years so the "O'quv yili" dropdowns (teacher-subject
 * assignment, journal, vedomost, etc.) are populated. Idempotent by year.
 */
class AcademicYearSeeder extends Seeder
{
    public function run(): void
    {
        $years = [
            ['2022-2023', '2022-09-01', '2023-08-31', false],
            ['2023-2024', '2023-09-01', '2024-08-31', false],
            ['2024-2025', '2024-09-01', '2025-08-31', false],
            ['2025-2026', '2025-09-01', '2026-08-31', true],  // joriy
            ['2026-2027', '2026-09-01', '2027-08-31', false],
        ];

        foreach ($years as [$year, $start, $end, $isCurrent]) {
            AcademicYear::updateOrCreate(
                ['year' => $year],
                [
                    'start_date' => $start,
                    'end_date'   => $end,
                    'is_current' => $isCurrent,
                ]
            );
        }

        $this->command->info('AcademicYearSeeder: ' . count($years) . " o'quv yili qo'shildi.");
    }
}
