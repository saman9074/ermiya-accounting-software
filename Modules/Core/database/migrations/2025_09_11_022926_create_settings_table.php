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
        // We use a key-value structure for flexibility.
        // This allows adding new settings in the future without changing the database schema.
        Schema::create('settings', function (Blueprint $table) {
            // The 'key' column will store the unique name of the setting, e.g., 'company_name'.
            $table->string('key')->primary();

            // The 'value' column will store the setting's value.
            // It's nullable to allow for settings that might not have a value initially.
            $table->text('value')->nullable();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('settings');
    }
};
