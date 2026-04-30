<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use Spatie\Permission\Models\Permission;

class PermissionSeeder extends Seeder
{

    private $permissions = [
        'dashboard' => [
            'menu'
        ],

        'student' => [
            'menu',
            'list',
            'create',
            'edit',
            'delete',
        ],

        'teacher' => [
            'menu',
            'list',
            'create',
            'edit',
            'delete',
        ],

        'payment-type' => [
            'menu',
            'list',
            'create',
            'edit',
            'delete',
        ],

        'class-room' => [
            'menu',
            'list',
            'create',
            'edit',
            'delete',
        ],

        'bill' => [
            'menu',
            'list',
            'create',
            'edit',
            'delete',
        ],

        'transaction' => [
            'menu',
            'list',
            'create',
        ],

    ];

    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        foreach ($this->permissions as $key => $value) {
            foreach ($value as $permission) {
                Permission::firstOrCreate([
                    'name' => $key . '-' . $permission,
                    'guard_name' => 'sanctum'
                ]);
            }
        }
    }
}
