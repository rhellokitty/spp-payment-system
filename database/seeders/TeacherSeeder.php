<?php

namespace Database\Seeders;

use App\Models\Teacher;
use Database\Factories\TeacherFactory;
use Database\Factories\UserFactory;
use Illuminate\Database\Seeder;

class TeacherSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        UserFactory::new()->teacher()->count(10)->create()->each(function ($user) {
            TeacherFactory::new()->create(['user_id' => $user->id]);
        });
    }
}
