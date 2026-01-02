<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;
use Illuminate\Support\Facades\DB;

return new class extends Migration
{
    /**
     * Run the migrations.
     */
    public function up(): void
    {
        // Get all cards
        $cards = DB::table('cards')->get();
        
        foreach ($cards as $card) {
            // Remove all non-alpha characters except hyphens, convert to lowercase
            $sanitized = strtolower(preg_replace('/[^a-zA-Z-]/', '', $card->unique_name));
            
            // Skip if already in correct format
            if ($sanitized === $card->unique_name) {
                continue;
            }
            
            // Handle potential duplicates by appending a number
            $uniqueName = $sanitized;
            $counter = 1;
            
            while (DB::table('cards')
                ->where('unique_name', $uniqueName)
                ->where('id', '!=', $card->id)
                ->exists()) {
                $uniqueName = $sanitized . '-' . $counter;
                $counter++;
            }
            
            // Update the card
            DB::table('cards')
                ->where('id', $card->id)
                ->update(['unique_name' => $uniqueName]);
        }
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        // No reverse operation - we can't recover the original invalid characters
        // This migration is one-way
    }
};
