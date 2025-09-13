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
        Schema::table('invoices', function (Blueprint $table) {
            $table->decimal('subtotal_amount', 15, 2)->after('person_id'); // Sum of items after line discounts
            $table->string('discount_type')->nullable()->after('total_amount');
            $table->decimal('discount_value', 15, 2)->default(0)->after('discount_type');
            $table->decimal('discount_amount', 15, 2)->default(0)->after('discount_value');
            // Make total_amount nullable for a moment to change it
            $table->decimal('total_amount', 15, 2)->nullable()->change();
        });

        // Recalculate existing totals (optional but good practice)
        DB::statement('UPDATE invoices SET subtotal_amount = total_amount');

        Schema::table('invoices', function (Blueprint $table) {
            $table->decimal('total_amount', 15, 2)->nullable(false)->change();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('invoices', function (Blueprint $table) {
            $table->dropColumn(['subtotal_amount', 'discount_type', 'discount_value', 'discount_amount']);
        });
    }
};
