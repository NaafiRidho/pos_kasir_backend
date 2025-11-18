<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;

class PaymentSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        $now = now();

        $methods = [
            'Cash',
            'Debit Card',
            'Credit Card',
            'Bank Transfer',
            'QRIS',
            'OVO',
            'GoPay',
            'ShopeePay',
        ];

        foreach ($methods as $method) {
            DB::table('payments')->updateOrInsert(
                ['payment_method' => $method],
                [
                    'payment_method' => $method,
                    'created_at' => $now,
                    'updated_at' => $now,
                ]
            );
        }
    }
}
