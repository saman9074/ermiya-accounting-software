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
        Schema::create('stock_movements', function (Blueprint $table) {
            $table->id();
            $table->foreignId('product_id')->constrained('products')->onDelete('cascade');
            $table->morphs('reference'); // Reference to the source document (e.g., InvoiceItem)
            $table->string('type'); // e.g., sale, purchase, initial_stock, adjustment
            $table->decimal('quantity_change', 15, 2); // Negative for outgoing, positive for incoming
            $table->decimal('stock_after', 15, 2); // Stock level after this movement
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('stock_movements');
    }
};
