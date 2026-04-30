<?php

namespace Database\Seeders;

use App\Models\User;
use Database\Factories\UserFactory;
use Illuminate\Database\Seeder;
use Illuminate\Support\Str;
use Spatie\Permission\Models\Role;

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
            'username' => 'superAdmin',
            'password' => bcrypt('password'),
        ])->assignRole(Role::findByName('super_admin', 'sanctum'));

        User::create([
            'id' => Str::uuid(),
            'name' => 'Nabila',
            'username' => 'teachers',
            'password' => bcrypt('password'),
        ])->assignRole(Role::findByName('teacher', 'sanctum'));


        UserFactory::new()->count(10)->create();
    }
}
