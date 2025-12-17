<?php

namespace Tests\Feature;

use App\Models\Card;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class CardExportVcardTest extends TestCase
{
    use RefreshDatabase;

    /**
     * Test exporting a single card as vCard via web endpoint.
     */
    public function test_export_card_as_vcard(): void
    {
        $card = Card::create([
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

        $response = $this->get(route('cards.export-vcard', $card));

        $response->assertStatus(200);
        $response->assertHeader('Content-Type', 'text/vcard; charset=utf-8');
        $response->assertHeader('Content-Disposition', 'attachment; filename="johndoe.vcf"');
        
        $content = $response->getContent();
        $this->assertStringContainsString('BEGIN:VCARD', $content);
        $this->assertStringContainsString('VERSION:3.0', $content);
        $this->assertStringContainsString('FN:John Doe', $content);
        $this->assertStringContainsString('N:Doe;John', $content);
        $this->assertStringContainsString('TEL:555-1234', $content);
        $this->assertStringContainsString('EMAIL;TYPE=WORK:john@work.com', $content);
        $this->assertStringContainsString('EMAIL;TYPE=PERSONAL:john@personal.com', $content);
        $this->assertStringContainsString('ADR;;123 Main St', $content);
        $this->assertStringContainsString('BDAY:1990-05-15', $content);
        $this->assertStringContainsString('NOTE:Test notes', $content);
        $this->assertStringContainsString('END:VCARD', $content);
    }

    /**
     * Test exporting a card with minimal data.
     */
    public function test_export_card_with_minimal_data(): void
    {
        $card = Card::create([
            'unique_name' => 'minimal',
            'first_name' => 'Jane',
            'last_name' => 'Smith',
        ]);

        $response = $this->get(route('cards.export-vcard', $card));

        $response->assertStatus(200);
        $content = $response->getContent();
        
        $this->assertStringContainsString('BEGIN:VCARD', $content);
        $this->assertStringContainsString('FN:Jane Smith', $content);
        $this->assertStringContainsString('N:Smith;Jane', $content);
        $this->assertStringContainsString('END:VCARD', $content);
        
        // Should not contain optional fields
        $this->assertStringNotContainsString('TEL:', $content);
        $this->assertStringNotContainsString('EMAIL', $content);
    }

    /**
     * Test Card model toVcard method.
     */
    public function test_card_to_vcard_method(): void
    {
        $card = Card::create([
            'unique_name' => 'testuser',
            'first_name' => 'Test',
            'last_name' => 'User',
            'phone' => '555-9999',
            'email_work' => 'test@work.com',
        ]);

        $vcard = $card->toVcard();

        $this->assertIsString($vcard);
        $this->assertStringContainsString('BEGIN:VCARD', $vcard);
        $this->assertStringContainsString('VERSION:3.0', $vcard);
        $this->assertStringContainsString('FN:Test User', $vcard);
        $this->assertStringContainsString('N:User;Test', $vcard);
        $this->assertStringContainsString('TEL:555-9999', $vcard);
        $this->assertStringContainsString('EMAIL;TYPE=WORK:test@work.com', $vcard);
        $this->assertStringContainsString('END:VCARD', $vcard);
    }

    /**
     * Test Card model toVcard with all email fields.
     */
    public function test_card_to_vcard_with_all_emails(): void
    {
        $card = Card::create([
            'unique_name' => 'allemails',
            'first_name' => 'Email',
            'last_name' => 'Tester',
            'email_work' => 'work@example.com',
            'email_personal' => 'personal@example.com',
            'email_extra1' => 'extra1@example.com',
            'email_extra2' => 'extra2@example.com',
            'email_extra3' => 'extra3@example.com',
        ]);

        $vcard = $card->toVcard();

        $this->assertStringContainsString('EMAIL;TYPE=WORK:work@example.com', $vcard);
        $this->assertStringContainsString('EMAIL;TYPE=PERSONAL:personal@example.com', $vcard);
        $this->assertStringContainsString('EMAIL;TYPE=OTHER:extra1@example.com', $vcard);
        $this->assertStringContainsString('EMAIL;TYPE=OTHER:extra2@example.com', $vcard);
        $this->assertStringContainsString('EMAIL;TYPE=OTHER:extra3@example.com', $vcard);
    }

    /**
     * Test Card model toVcard with special characters.
     */
    public function test_card_to_vcard_with_special_characters(): void
    {
        $card = Card::create([
            'unique_name' => 'special',
            'first_name' => "Jean-Pierre",
            'last_name' => "O'Brien",
            'notes' => "Line 1\nLine 2\nLine 3",
        ]);

        $vcard = $card->toVcard();

        $this->assertStringContainsString('FN:Jean-Pierre O\'Brien', $vcard);
        $this->assertStringContainsString('N:O\'Brien;Jean-Pierre', $vcard);
        $this->assertStringContainsString('NOTE:Line 1', $vcard);
    }
}
