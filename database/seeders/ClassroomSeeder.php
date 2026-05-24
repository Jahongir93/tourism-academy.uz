<?php

namespace Database\Seeders;

use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;

class ClassroomSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        $buildings = \App\Models\Building::all();

        // Building A - Asosiy o'quv binosi (4 floors, 10 rooms per floor)
        $buildingA = $buildings->where('code', 'A')->first();
        if ($buildingA) {
            $this->createClassroomsForBuilding($buildingA, [
                // Floor 1
                ['floor' => 1, 'rooms' => [
                    ['101', 'lecture', 60, true, false, 'Katta ma\'ruza xonasi'],
                    ['102', 'lecture', 50, true, false, 'Ma\'ruza xonasi'],
                    ['103', 'seminar', 30, true, false, 'Seminar xonasi'],
                    ['104', 'seminar', 30, true, false, 'Seminar xonasi'],
                    ['105', 'lecture', 40, true, false, 'Ma\'ruza xonasi'],
                ]],
                // Floor 2
                ['floor' => 2, 'rooms' => [
                    ['201', 'lecture', 50, true, true, 'Ma\'ruza xonasi'],
                    ['202', 'lecture', 50, true, false, 'Ma\'ruza xonasi'],
                    ['203', 'seminar', 25, true, false, 'Seminar xonasi'],
                    ['204', 'seminar', 25, true, false, 'Seminar xonasi'],
                    ['205', 'lecture', 45, true, false, 'Ma\'ruza xonasi'],
                ]],
                // Floor 3
                ['floor' => 3, 'rooms' => [
                    ['301', 'lecture', 50, true, true, 'Ma\'ruza xonasi'],
                    ['302', 'lecture', 50, true, false, 'Ma\'ruza xonasi'],
                    ['303', 'seminar', 30, true, false, 'Seminar xonasi'],
                    ['304', 'seminar', 30, true, false, 'Seminar xonasi'],
                    ['305', 'auditorium', 100, true, true, 'Katta auditoriya'],
                ]],
                // Floor 4
                ['floor' => 4, 'rooms' => [
                    ['401', 'lecture', 40, true, false, 'Ma\'ruza xonasi'],
                    ['402', 'lecture', 40, true, false, 'Ma\'ruza xonasi'],
                    ['403', 'seminar', 25, true, false, 'Seminar xonasi'],
                    ['404', 'seminar', 25, true, false, 'Seminar xonasi'],
                ]],
            ]);
        }

        // Building B - Laboratoriya binosi (3 floors)
        $buildingB = $buildings->where('code', 'B')->first();
        if ($buildingB) {
            $this->createClassroomsForBuilding($buildingB, [
                // Floor 1
                ['floor' => 1, 'rooms' => [
                    ['B101', 'computer', 30, true, true, 'Kompyuter xonasi 1'],
                    ['B102', 'computer', 30, true, true, 'Kompyuter xonasi 2'],
                    ['B103', 'lab', 25, true, true, 'Turizm laboratoriyasi'],
                    ['B104', 'lab', 25, false, false, 'Mehmonxona xizmatlari lab'],
                ]],
                // Floor 2
                ['floor' => 2, 'rooms' => [
                    ['B201', 'computer', 30, true, true, 'Kompyuter xonasi 3'],
                    ['B202', 'lab', 20, true, true, 'Til laboratoriyasi'],
                    ['B203', 'lab', 25, false, false, 'Oshpazlik laboratoriyasi'],
                    ['B204', 'seminar', 30, true, false, 'Seminar xonasi'],
                ]],
                // Floor 3
                ['floor' => 3, 'rooms' => [
                    ['B301', 'computer', 30, true, true, 'Kompyuter xonasi 4'],
                    ['B302', 'lab', 25, true, false, 'Dizayn laboratoriyasi'],
                    ['B303', 'seminar', 30, true, false, 'Seminar xonasi'],
                ]],
            ]);
        }

        // Building C - Ma'muriy bino (2 floors - kafedra xonalari)
        $buildingC = $buildings->where('code', 'C')->first();
        if ($buildingC) {
            $this->createClassroomsForBuilding($buildingC, [
                ['floor' => 1, 'rooms' => [
                    ['C101', 'seminar', 20, false, false, 'Kafedra xonasi'],
                    ['C102', 'seminar', 20, false, false, 'Kafedra xonasi'],
                    ['C103', 'seminar', 15, false, false, 'Yig\'ilish xonasi'],
                ]],
                ['floor' => 2, 'rooms' => [
                    ['C201', 'seminar', 20, false, false, 'Kafedra xonasi'],
                    ['C202', 'seminar', 20, false, false, 'Kafedra xonasi'],
                    ['C203', 'auditorium', 50, true, true, 'Yig\'ilishlar zali'],
                ]],
            ]);
        }
    }

    private function createClassroomsForBuilding($building, $floors)
    {
        foreach ($floors as $floorData) {
            $floor = $floorData['floor'];
            foreach ($floorData['rooms'] as $room) {
                \App\Models\Classroom::create([
                    'building_id' => $building->id,
                    'name' => $room[0],
                    'code' => $building->code . '-' . $room[0],
                    'floor' => $floor,
                    'type' => $room[1],
                    'capacity' => $room[2],
                    'has_projector' => $room[3],
                    'has_computer' => $room[4],
                    'has_whiteboard' => true,
                    'is_active' => true,
                    'notes' => $room[5] ?? null,
                ]);
            }
        }
    }
}
