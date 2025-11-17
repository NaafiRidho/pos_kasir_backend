<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;

class ProductSeeder extends Seeder
{
    public function run(): void
    {
        $now = now();

        // map category name to id
        $categoryIds = DB::table('categories')
            ->pluck('categories_id', 'name');

        $products = [
            [
                'name' => 'Air Mineral 600ml',
                'description' => 'Air mineral kemasan botol 600ml',
                'category' => 'Minuman',
                'cost_price' => 3000,
                'selling_price' => 5000,
                'images' => ['air_mineral_600.jpg'],
                'stock' => 200,
                'barcode' => 'AM-600',
            ],
            [
                'name' => 'Indomie Goreng',
                'description' => 'Mi instan goreng 85g',
                'category' => 'Sembako',
                'cost_price' => 2500,
                'selling_price' => 3500,
                'images' => ['indomie_goreng.jpg'],
                'stock' => 500,
                'barcode' => 'IM-001',
            ],
            [
                'name' => 'Gula Pasir 1kg',
                'description' => 'Gula pasir kemasan 1kg',
                'category' => 'Sembako',
                'cost_price' => 12000,
                'selling_price' => 15000,
                'images' => ['gula_pasir_1kg.jpg'],
                'stock' => 100,
                'barcode' => 'GP-1KG',
            ],
            [
                'name' => 'Minyak Goreng 1L',
                'description' => 'Minyak goreng kemasan 1 liter',
                'category' => 'Sembako',
                'cost_price' => 14000,
                'selling_price' => 20000,
                'images' => ['minyak_goreng_1l.jpg'],
                'stock' => 120,
                'barcode' => 'MG-1L',
            ],
            [
                'name' => 'Deterjen 800g',
                'description' => 'Deterjen bubuk 800 gram',
                'category' => 'Kebutuhan Rumah',
                'cost_price' => 12000,
                'selling_price' => 17000,
                'images' => ['deterjen_800.jpg'],
                'stock' => 80,
                'barcode' => 'DT-800',
            ],
            [
                'name' => 'Sabun Mandi 100g',
                'description' => 'Sabun mandi batangan 100 gram',
                'category' => 'Perawatan Pribadi',
                'cost_price' => 2500,
                'selling_price' => 4000,
                'images' => ['sabun_mandi_100.jpg'],
                'stock' => 200,
                'barcode' => 'SB-100',
            ],
            [
                'name' => 'Biskuit Coklat 150g',
                'description' => 'Biskuit rasa coklat 150 gram',
                'category' => 'Snack',
                'cost_price' => 8000,
                'selling_price' => 12000,
                'images' => ['biskuit_coklat_150.jpg'],
                'stock' => 150,
                'barcode' => 'BS-150',
            ],
        ];

        foreach ($products as $p) {
            $catId = $categoryIds[$p['category']] ?? null;

            DB::table('products')->updateOrInsert(
                ['barcode' => $p['barcode']],
                [
                    'categories_id' => $catId,
                    'name' => $p['name'],
                    'description' => $p['description'],
                    'cost_price' => $p['cost_price'],
                    'selling_price' => $p['selling_price'],
                    'product_images' => json_encode($p['images']),
                    'stock' => $p['stock'],
                    'created_at' => $now,
                    'updated_at' => $now,
                ]
            );
        }
    }
}
