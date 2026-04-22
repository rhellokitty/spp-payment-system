<?php

namespace Database\Seeders;

use Database\Factories\StudentFactory;
use Database\Factories\UserFactory;
use Illuminate\Database\Seeder;

class StudentSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        UserFactory::new()->student()->count(10)->create()->each(function ($user) {
            StudentFactory::new()->create(['user_id' => $user->id]);
        });
    }
}
