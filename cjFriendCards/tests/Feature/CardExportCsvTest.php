<?php

namespace Tests\Feature;

use App\Models\Card;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class CardExportCsvTest extends TestCase
{
    use RefreshDatabase;

    /**
     * Test exporting all cards as CSV.
     */
    public function test_export_all_cards_as_csv(): void
    {
        Card::create([
            'unique_name' => 'johndoe',
            'first_name' => 'John',
            'last_name' => 'Doe',
            'phone' => '555-1234',
            'email_work' => 'john@work.com',
            'email_personal' => 'john@personal.com',
            'address' => '123 Main St',
            'birthday' => '1990-05-15',
            'notes' => 'Test notes',
        ]);

        Card::create([
            'unique_name' => 'janedoe',
            'first_name' => 'Jane',
            'last_name' => 'Doe',
            'phone' => '555-5678',
            'email_work' => 'jane@work.com',
        ]);

        $response = $this->get(route('cards.export-csv'));

        $response->assertStatus(200);
        $response->assertHeader('Content-Type', 'text/csv; charset=utf-8');
        
        $content = $response->getContent();
        
        // Verify CSV headers
        $this->assertStringContainsString('First Name,Last Name,Unique Name,Phone,Email (Work),Email (Personal),Email (Extra 1),Email (Extra 2),Email (Extra 3),Address,Birthday,Notes', $content);
        
        // Verify first card data
        $this->assertStringContainsString('"John","Doe","johndoe","555-1234","john@work.com","john@personal.com","","","","123 Main St","1990-05-15","Test notes"', $content);
        
        // Verify second card data
        $this->assertStringContainsString('"Jane","Doe","janedoe","555-5678","jane@work.com"', $content);
    }

    /**
     * Test CSV export with minimal card data.
     */
    public function test_export_csv_with_minimal_data(): void
    {
        Card::create([
            'unique_name' => 'minimal',
            'first_name' => 'Minimal',
            'last_name' => 'Card',
        ]);

        $response = $this->get(route('cards.export-csv'));

        $response->assertStatus(200);
        $content = $response->getContent();
        
        $this->assertStringContainsString('First Name,Last Name,Unique Name', $content);
        $this->assertStringContainsString('"Minimal","Card","minimal"', $content);
    }

    /**
     * Test CSV export with special characters and quotes.
     */
    public function test_export_csv_with_special_characters(): void
    {
        Card::create([
            'unique_name' => 'special',
            'first_name' => 'Jean-Pierre',
            'last_name' => "O'Brien",
            'phone' => '555-1234',
            'notes' => 'This has "quotes" and,commas inside',
        ]);

        $response = $this->get(route('cards.export-csv'));

        $response->assertStatus(200);
        $content = $response->getContent();
        
        // Verify that quotes are properly escaped (doubled) inside CSV fields
        $this->assertStringContainsString('"Jean-Pierre","O\'Brien"', $content);
        $this->assertStringContainsString('This has ""quotes"" and,commas inside', $content);
    }

    /**
     * Test CSV export with no cards.
     */
    public function test_export_csv_with_no_cards(): void
    {
        $response = $this->get(route('cards.export-csv'));

        $response->assertStatus(200);
        $response->assertHeader('Content-Type', 'text/csv; charset=utf-8');
        
        $content = $response->getContent();
        
        // Should only contain the header row
        $this->assertStringContainsString('First Name,Last Name,Unique Name,Phone,Email (Work),Email (Personal),Email (Extra 1),Email (Extra 2),Email (Extra 3),Address,Birthday,Notes', $content);
        
        // Should not contain any data rows (only the header)
        $lines = explode("\n", trim($content));
        $this->assertCount(1, $lines);
    }

    /**
     * Test CSV export includes all email types.
     */
    public function test_export_csv_with_all_email_types(): void
    {
        Card::create([
            'unique_name' => 'allemails',
            'first_name' => 'Email',
            'last_name' => 'Tester',
            'email_work' => 'work@example.com',
            'email_personal' => 'personal@example.com',
            'email_extra1' => 'extra1@example.com',
            'email_extra2' => 'extra2@example.com',
            'email_extra3' => 'extra3@example.com',
        ]);

        $response = $this->get(route('cards.export-csv'));

        $response->assertStatus(200);
        $content = $response->getContent();
        
        $this->assertStringContainsString('work@example.com', $content);
        $this->assertStringContainsString('personal@example.com', $content);
        $this->assertStringContainsString('extra1@example.com', $content);
        $this->assertStringContainsString('extra2@example.com', $content);
        $this->assertStringContainsString('extra3@example.com', $content);
    }
}
