<?php

namespace App\Console\Commands;

use App\Http\Controllers\NotificationController;
use App\Models\LmsExam;
use Illuminate\Console\Command;

class SendExamDeadlineReminders extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'notifications:exam-reminders {--hours=24 : Hours before deadline}';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Send reminders for exams with upcoming deadlines';

    /**
     * Execute the console command.
     */
    public function handle()
    {
        $hours = (int) $this->option('hours');
        $now = now();
        $deadline = $now->copy()->addHours($hours);

        $this->info("Checking for exams ending between {$now} and {$deadline}...");

        // Find published exams that end within the next X hours
        $upcomingExams = LmsExam::where('is_published', true)
            ->where('status', 'active')
            ->where('end_time', '>', $now)
            ->where('end_time', '<=', $deadline)
            ->get();

        $this->info("Found {$upcomingExams->count()} exams with upcoming deadlines.");

        $sentCount = 0;
        foreach ($upcomingExams as $exam) {
            try {
                NotificationController::notifyDeadlineReminder($exam);
                $this->line("  - Sent reminders for: {$exam->title}");
                $sentCount++;
            } catch (\Exception $e) {
                $this->error("  - Error sending reminders for {$exam->title}: {$e->getMessage()}");
            }
        }

        $this->info("Done! Sent reminders for {$sentCount} exams.");

        return Command::SUCCESS;
    }
}
