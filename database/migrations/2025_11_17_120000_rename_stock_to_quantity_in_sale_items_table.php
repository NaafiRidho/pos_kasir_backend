<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;
use Illuminate\Support\Facades\DB;

return new class extends Migration {
    public function up(): void
    {
        // Rename column stock -> quantity. Using raw SQL fallback if renameColumn unavailable.
        if (Schema::hasColumn('sale_items', 'stock') && ! Schema::hasColumn('sale_items', 'quantity')) {
            try {
                Schema::table('sale_items', function (Blueprint $table) {
                    $table->renameColumn('stock', 'quantity');
                });
            } catch (Throwable $e) {
                // Fallback for environments without doctrine/dbal
                DB::statement('ALTER TABLE sale_items CHANGE stock quantity INT NOT NULL');
            }
        }
    }

    public function down(): void
    {
        if (Schema::hasColumn('sale_items', 'quantity') && ! Schema::hasColumn('sale_items', 'stock')) {
            try {
                Schema::table('sale_items', function (Blueprint $table) {
                    $table->renameColumn('quantity', 'stock');
                });
            } catch (Throwable $e) {
                DB::statement('ALTER TABLE sale_items CHANGE quantity stock INT NOT NULL');
            }
        }
    }
};
