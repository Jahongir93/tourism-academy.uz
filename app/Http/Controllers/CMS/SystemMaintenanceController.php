<?php

namespace App\Http\Controllers\CMS;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Artisan;
use Illuminate\Support\Facades\Log;

/**
 * Lets an admin run deployment/maintenance artisan commands from the CMS
 * settings UI (no terminal access on the server).
 *
 * Protected by the CMS route group middleware; SuperAdmin has full access.
 */
class SystemMaintenanceController extends Controller
{
    /**
     * Single tasks: key => [command, args, label]
     */
    private function tasks(): array
    {
        return [
            'migrate'        => ['migrate',        ['--force' => true], 'Migratsiya (yangi jadval/ustun)'],
            'optimize-clear' => ['optimize:clear', [],                  'Barcha keshni tozalash'],
            'config-cache'   => ['config:cache',   [],                  'Config keshlash'],
            'route-cache'    => ['route:cache',    [],                  'Route keshlash'],
            'view-cache'     => ['view:cache',     [],                  'View keshlash'],
            'event-cache'    => ['event:cache',    [],                  'Event keshlash'],
            'storage-link'   => ['storage:link',   [],                  'Storage link'],
            'up'             => ['up',             [],                  'Saytni yoqish'],
            'down'           => ['down',           [],                  'Texnik rejim (saytni vaqtincha yopish)'],
            'seed-academic'  => ['db:seed',        ['--class' => 'AcademicYearSeeder', '--force' => true], "O'quv yillarini kiritish"],
            'seed-teachers'  => ['db:seed',        ['--class' => 'TeachersSeeder', '--force' => true],     "O'qituvchilarni kiritish"],
        ];
    }

    /** Run one task. */
    public function run(Request $request, string $task)
    {
        $tasks = $this->tasks();
        if (!isset($tasks[$task])) {
            return response()->json(['success' => false, 'message' => 'Noma\'lum amal'], 404);
        }

        [$cmd, $args, $label] = $tasks[$task];
        $result = $this->call($cmd, $args, $label);

        return response()->json([
            'success' => $result['ok'],
            'result'  => $result,
        ]);
    }

    /**
     * Full deploy sequence — exactly the commands run after a git pull.
     */
    public function deploy()
    {
        $sequence = [
            ['migrate',        ['--force' => true], 'Migratsiya'],
            ['optimize:clear', [],                  'Keshni tozalash'],
            ['config:cache',   [],                  'Config keshlash'],
            ['route:cache',    [],                  'Route keshlash'],
            ['view:cache',     [],                  'View keshlash'],
            ['event:cache',    [],                  'Event keshlash'],
            ['storage:link',   [],                  'Storage link'],
            ['up',             [],                  'Saytni yoqish'],
        ];

        $results = [];
        foreach ($sequence as [$cmd, $args, $label]) {
            $results[] = $this->call($cmd, $args, $label);
        }

        $allOk = collect($results)->every(fn($r) => $r['ok'] || $r['skipped']);

        return response()->json([
            'success' => $allOk,
            'results' => $results,
        ]);
    }

    /** Execute one artisan command safely, returning a normalized result. */
    private function call(string $cmd, array $args, string $label): array
    {
        try {
            Artisan::call($cmd, $args);
            $output = trim(Artisan::output());

            return [
                'label'   => $label,
                'command' => $cmd,
                'ok'      => true,
                'skipped' => false,
                'output'  => $output !== '' ? $output : 'Bajarildi',
            ];
        } catch (\Throwable $e) {
            // storage:link fails harmlessly when public/storage already exists
            $skipped = $cmd === 'storage:link' && str_contains(strtolower($e->getMessage()), 'exist');

            Log::warning("CMS maintenance command failed: {$cmd}", ['error' => $e->getMessage()]);

            return [
                'label'   => $label,
                'command' => $cmd,
                'ok'      => false,
                'skipped' => $skipped,
                'output'  => $skipped ? 'Allaqachon mavjud (o\'tkazib yuborildi)' : $e->getMessage(),
            ];
        }
    }
}
