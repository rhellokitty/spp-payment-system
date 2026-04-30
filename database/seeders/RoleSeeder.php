<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use Spatie\Permission\Models\Permission;
use Spatie\Permission\Models\Role;

class RoleSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        Role::firstOrCreate([
            'name' => 'super_admin',
            'guard_name' => 'sanctum',
        ])->givePermissionTo(Permission::all());


        Role::firstOrCreate([
            'name' => 'student',
            'guard_name' => 'sanctum',
        ])->givePermissionTo([
            'dashboard-menu',

            'student-menu',
            'student-list',

            'class-room-menu',
            'class-room-list',

            'bill-menu',
            'bill-list',

        ]);


        Role::firstOrCreate([
            'name' => 'teacher',
            'guard_name' => 'sanctum',
        ])->givePermissionTo([
            'dashboard-menu',

            'student-menu',
            'student-list',

            'class-room-menu',
            'class-room-list',

            'bill-menu',
            'bill-list',

            'transaction-menu',
            'transaction-list',
            'class-room-create',
        ]);

        Role::firstOrCreate([
            'name' => 'admin',
            'guard_name' => 'sanctum',
        ])->givePermissionTo([
            'dashboard-menu',

            'student-menu',
            'student-list',
            'student-create',
            'student-edit',

            'teacher-menu',
            'teacher-list',
            'teacher-create',
            'teacher-edit',

            'payment-type-menu',
            'payment-type-list',
            'payment-type-create',
            'payment-type-edit',

            'class-room-menu',
            'class-room-list',
            'class-room-create',
            'class-room-edit',

            'bill-menu',
            'bill-list',
            'bill-create',
            'bill-edit',

            'transaction-menu',
            'transaction-list',
        ]);
    }
}
