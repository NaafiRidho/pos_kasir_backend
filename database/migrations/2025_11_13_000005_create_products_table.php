<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    /**
     * Run the migrations.
     */
    public function up(): void
    {
        Schema::create('products', function (Blueprint $table) {
            $table->id('product_id');
            $table->unsignedBigInteger('categories_id')->nullable();
            $table->string('name');
            $table->text('description')->nullable();
            $table->decimal('cost_price', 15, 2)->default(0);
            $table->decimal('selling_price', 15, 2)->default(0);
            $table->text('product_images')->nullable();
            $table->integer('stock')->default(0);
            $table->string('barcode')->nullable();
            $table->timestamps();

            $table->foreign('categories_id')
                ->references('categories_id')
                ->on('categories')
                ->nullOnDelete()
                ->cascadeOnUpdate();

            $table->index(['categories_id']);
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('products');
    }
};
