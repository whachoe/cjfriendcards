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
            $table->dropColumn('contact_info');
            
            // Add new columns
            $table->string('address')->nullable()->after('name');
            $table->string('phone')->nullable()->after('address');
            $table->string('email_work')->nullable()->after('phone');
            $table->string('email_personal')->nullable()->after('email_work');
            $table->string('email_extra1')->nullable()->after('email_personal');
            $table->string('email_extra2')->nullable()->after('email_extra1');
            $table->string('email_extra3')->nullable()->after('email_extra2');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('cards', function (Blueprint $table) {
            $table->dropColumn([
                'address',
                'phone',
                'email_work',
                'email_personal',
                'email_extra1',
                'email_extra2',
                'email_extra3',
            ]);
            
            $table->string('contact_info')->nullable();
        });
    }
};
