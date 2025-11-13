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
        Schema::table('users', function (Blueprint $table) {
            // add after id to keep readability
            $table->unsignedBigInteger('role_id')->nullable()->after('user_id');
            $table->uuid('uuid')->nullable()->after('role_id');
            $table->string('username')->unique()->after('name');

            $table->foreign('role_id')
                ->references('role_id')
                ->on('roles')
                ->nullOnDelete()
                ->cascadeOnUpdate();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('users', function (Blueprint $table) {
            $table->dropForeign(['role_id']);
            $table->dropColumn(['role_id', 'uuid', 'username']);
        });
    }
};
