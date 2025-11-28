<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;

class RoleSeeder extends Seeder
{
    /**
     * Seed the roles table.
     */
    public function run(): void
    {
        $now = now();

        $roles = [
            [
                'name' => 'Admin',
                'description' => 'Administrator, full access to manage the system.',
            ],
            [
                'name' => 'Kasir',
                'description' => 'Cashier role, handle sales and daily operations.',
            ],
            [
                'name' => 'Gudang',
                'description' => 'Warehouse role, manage inventory and stock.',
            ],
        ];

        foreach ($roles as $role) {
            DB::table('roles')->updateOrInsert(
                ['name' => $role['name']],
                [
                    'description' => $role['description'],
                    'created_at' => $now,
                    'updated_at' => $now,
                ]
            );
        }
    }
}
