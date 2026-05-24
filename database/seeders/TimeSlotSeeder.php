<?php

namespace Database\Seeders;

use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;
use App\Models\TimeSlot;

class TimeSlotSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        $timeSlots = [
            [
                'slot_number' => 1,
                'start_time' => '08:00:00',
                'end_time' => '09:30:00',
                'is_break' => false,
                'slot_type' => 'regular',
            ],
            [
                'slot_number' => 2,
                'start_time' => '09:40:00',
                'end_time' => '11:10:00',
                'is_break' => false,
                'slot_type' => 'regular',
            ],
            [
                'slot_number' => 3,
                'start_time' => '11:20:00',
                'end_time' => '12:50:00',
                'is_break' => false,
                'slot_type' => 'regular',
            ],
            [
                'slot_number' => 4,
                'start_time' => '13:30:00',
                'end_time' => '15:00:00',
                'is_break' => false,
                'slot_type' => 'regular',
            ],
            [
                'slot_number' => 5,
                'start_time' => '15:10:00',
                'end_time' => '16:40:00',
                'is_break' => false,
                'slot_type' => 'regular',
            ],
            [
                'slot_number' => 6,
                'start_time' => '16:50:00',
                'end_time' => '18:20:00',
                'is_break' => false,
                'slot_type' => 'regular',
            ],
        ];

        foreach ($timeSlots as $slot) {
            TimeSlot::create($slot);
        }

        $this->command->info('6 ta vaqt oralig\'i yaratildi!');
    }
}
