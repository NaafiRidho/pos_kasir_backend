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
        Schema::create('sales', function (Blueprint $table) {
            $table->id('sale_id');
            // FK to users.user_id (custom PK)
            $table->unsignedBigInteger('user_id');

            // FK to payments.payment_id
            $table->unsignedBigInteger('payment_id')->nullable();

            $table->decimal('subtotal', 15, 2)->default(0);
            $table->decimal('discount_amount', 15, 2)->default(0);
            $table->decimal('tax_amount', 15, 2)->default(0);
            $table->decimal('total_amount', 15, 2)->default(0);
            $table->string('payment_status')->default('unpaid');
            $table->date('sale_date')->nullable();
            $table->timestamps();

            $table->foreign('payment_id')
                ->references('payment_id')
                ->on('payments')
                ->nullOnDelete()
                ->cascadeOnUpdate();

            $table->foreign('user_id')
                ->references('user_id')
                ->on('users')
                ->restrictOnDelete()
                ->cascadeOnUpdate();

            $table->index(['user_id', 'payment_id']);
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('sales');
    }
};
