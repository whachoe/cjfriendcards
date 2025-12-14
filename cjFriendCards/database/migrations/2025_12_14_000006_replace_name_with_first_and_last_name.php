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
        Schema::table('cards', function (Blueprint $table) {
            // Remove old column
            $table->dropColumn('name');
            
            // Add new columns
            $table->string('first_name')->after('unique_name');
            $table->string('last_name')->after('first_name');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('cards', function (Blueprint $table) {
            $table->dropColumn(['first_name', 'last_name']);
            
            $table->string('name')->after('unique_name');
        });
    }
};
