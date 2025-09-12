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
        Schema::table('stock_movements', function (Blueprint $table) {
            // Allow reference_id and reference_type to be nullable
            $table->unsignedBigInteger('reference_id')->nullable()->change();
            $table->string('reference_type')->nullable()->change();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('stock_movements', function (Blueprint $table) {
            // Revert the columns to not be nullable
            // Note: This might fail if there are records with null values.
            $table->unsignedBigInteger('reference_id')->nullable(false)->change();
            $table->string('reference_type')->nullable(false)->change();
        });    }
};
