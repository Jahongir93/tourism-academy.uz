<?php

namespace Database\Seeders;

use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;

class BuildingSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        $buildings = [
            [
                'name' => 'Asosiy o\'quv binosi',
                'code' => 'A',
                'address' => 'Buxoro shahri, Alpomish ko\'chasi, 11',
                'total_floors' => 4,
                'total_rooms' => 40,
                'type' => 'academic',
                'is_active' => true,
                'description' => 'Asosiy o\'quv binosi - ma\'ruzalar va amaliy mashg\'ulotlar uchun'
            ],
            [
                'name' => 'Laboratoriya binosi',
                'code' => 'B',
                'address' => 'Buxoro shahri, Alpomish ko\'chasi, 13',
                'total_floors' => 3,
                'total_rooms' => 25,
                'type' => 'academic',
                'is_active' => true,
                'description' => 'Kompyuter va boshqa laboratoriyalar joylashgan bino'
            ],
            [
                'name' => 'Ma\'muriy bino',
                'code' => 'C',
                'address' => 'Buxoro shahri, Alpomish ko\'chasi, 15',
                'total_floors' => 2,
                'total_rooms' => 15,
                'type' => 'administrative',
                'is_active' => true,
                'description' => 'Ma\'muriyat va kafedra xonalari'
            ],
            [
                'name' => 'Sport majmuasi',
                'code' => 'D',
                'address' => 'Buxoro shahri, Alpomish ko\'chasi, 17',
                'total_floors' => 2,
                'total_rooms' => 10,
                'type' => 'academic',
                'is_active' => true,
                'description' => 'Sport zallar va amaliy mashg\'ulotlar uchun'
            ]
        ];

        foreach ($buildings as $building) {
            \App\Models\Building::create($building);
        }
    }
}
