<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;

class CategorySeeder extends Seeder
{
    public function run(): void
    {
        $now = now();

        $categories = [
            ['name' => 'Sembako', 'description' => 'Kebutuhan pokok (beras, gula, minyak, mie, dll)'],
            ['name' => 'Minuman', 'description' => 'Aneka minuman kemasan'],
            ['name' => 'Snack', 'description' => 'Cemilan & snack kemasan'],
            ['name' => 'Kebutuhan Rumah', 'description' => 'Home essentials & kebersihan'],
            ['name' => 'Perawatan Pribadi', 'description' => 'Sabun, sampo, dsb.'],
        ];

        foreach ($categories as $c) {
            DB::table('categories')->updateOrInsert(
                ['name' => $c['name']],
                [
                    'description' => $c['description'],
                    'created_at' => $now,
                    'updated_at' => $now,
                ]
            );
        }
    }
}
