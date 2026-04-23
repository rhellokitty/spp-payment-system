<?php

namespace Database\Seeders;

use App\Models\ClassRoom;
use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;
use Illuminate\Support\Str;

class ClassRoomSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        $classrooms = [
            ['grade' => '7', 'name' => 'A', 'start_year' => 2024],
            ['grade' => '7', 'name' => 'B', 'start_year' => 2024],
            ['grade' => '7', 'name' => 'C', 'start_year' => 2024],
            ['grade' => '8', 'name' => 'A', 'start_year' => 2024],
            ['grade' => '8', 'name' => 'B', 'start_year' => 2024],
            ['grade' => '8', 'name' => 'C', 'start_year' => 2024],
            ['grade' => '9', 'name' => 'A', 'start_year' => 2024],
            ['grade' => '9', 'name' => 'B', 'start_year' => 2024],
            ['grade' => '9', 'name' => 'C', 'start_year' => 2024],
        ];

        foreach ($classrooms as $classroom) {
            ClassRoom::create([
                'id' => Str::uuid(),
                'school_level' => 'SMP',
                'name' => $classroom['name'],
                'grade' => $classroom['grade'],
                'start_year' => $classroom['start_year'],
                'end_year' => $classroom['start_year'] + 3,
            ]);
        }
    }
}
