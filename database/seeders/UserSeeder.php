<?php

namespace Database\Seeders;

use App\Models\User;
use Database\Factories\UserFactory;
use Illuminate\Database\Seeder;
use Illuminate\Support\Str;

class UserSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        User::create([
            'id' => Str::uuid(),
            'name' => 'Daffa',
            'username' => '17220731',
            'password' => bcrypt('password'),
            'role' => 'super_admin',
        ]);

        UserFactory::new()->count(10)->create();
    }
}
