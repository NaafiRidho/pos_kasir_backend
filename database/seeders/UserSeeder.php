<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Str;

class UserSeeder extends Seeder
{
    /**
     * Seed the users table with an admin user.
     */
    public function run(): void
    {
        $now = now();

        // find Admin role id if exists
        $adminRoleId = DB::table('roles')->where('name', 'Admin')->value('role_id');
        $kasirRoleId = DB::table('roles')->where('name', 'Kasir')->value('role_id');

        DB::table('users')->updateOrInsert(
            [
                'name' => 'Nervalina',
                'username' => 'valina',
                'email' => 'valina@gmail.com',
                'password' => Hash::make('password'),
                'uuid' => (string) Str::uuid(),
                'role_id' => $adminRoleId,
                'created_at' => $now,
                'updated_at' => $now,
            ],
            [
                'name' => 'Dewi',
                'username' => 'dewi',
                'email' => 'dewi@gmail.com',
                'password' => Hash::make('password'),
                'uuid' => (string) Str::uuid(),
                'role_id' => $kasirRoleId,
                'created_at' => $now,
                'updated_at' => $now,
            ]
        );
    }
}
