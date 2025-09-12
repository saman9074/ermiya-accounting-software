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
        Schema::create('person_groups', function (Blueprint $table) {
            $table->id();
            $table->string('name')->unique();
            // This links a person group to a specific price list from the Inventory module
            $table->foreignId('price_list_id')->nullable()->constrained('price_lists')->onDelete('set null');
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('person_groups');
    }
};
