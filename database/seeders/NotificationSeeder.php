<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use App\Models\Notification;
use App\Models\User;

class NotificationSeeder extends Seeder
{
    public function run()
    {
        $users = User::all();

        foreach ($users as $user) {
            // System notification
            Notification::create([
                'user_id' => $user->id,
                'type' => 'system',
                'title' => 'Tizimga xush kelibsiz!',
                'message' => 'Samarkand Turizm Akademiyasi platformasiga xush kelibsiz. Barcha funksiyalardan foydalanishingiz mumkin.',
                'data' => ['welcome' => true],
                'is_read' => false,
            ]);

            // Academic notification
            Notification::create([
                'user_id' => $user->id,
                'type' => 'academic',
                'title' => 'Yangi dars jadvali',
                'message' => 'Yangi hafta uchun dars jadvali tayyor. Ko\'rish uchun bosing.',
                'data' => ['schedule_week' => date('W')],
                'is_read' => false,
            ]);

            // Attendance notification
            if ($user->hasRole('Student')) {
                Notification::create([
                    'user_id' => $user->id,
                    'type' => 'attendance',
                    'title' => 'Davomat eslatmasi',
                    'message' => 'Bugungi darsga davomat qilishni unutmang!',
                    'data' => ['date' => date('Y-m-d')],
                    'is_read' => false,
                ]);
            }

            // Grade notification
            if ($user->hasRole('Student')) {
                Notification::create([
                    'user_id' => $user->id,
                    'type' => 'grade',
                    'title' => 'Yangi baho',
                    'message' => 'Matematika fanidan yangi baho qo\'yildi.',
                    'data' => ['subject' => 'Matematika', 'grade' => rand(70, 100)],
                    'is_read' => rand(0, 1),
                    'read_at' => rand(0, 1) ? now()->subHours(rand(1, 48)) : null,
                ]);
            }

            // Event notification
            Notification::create([
                'user_id' => $user->id,
                'type' => 'event',
                'title' => 'Yangi tadbir',
                'message' => 'Kelasi hafta ilmiy konferensiya bo\'lib o\'tadi. Ro\'yxatdan o\'tishni unutmang!',
                'data' => ['event_date' => now()->addWeek()->format('Y-m-d')],
                'is_read' => false,
            ]);

            // Message notification
            if (rand(0, 1)) {
                Notification::create([
                    'user_id' => $user->id,
                    'type' => 'message',
                    'title' => 'Yangi xabar',
                    'message' => 'Sizga yangi xabar keldi.',
                    'data' => ['from' => 'Admin'],
                    'is_read' => false,
                ]);
            }
        }

        $this->command->info('Sample notifications created successfully!');
    }
}