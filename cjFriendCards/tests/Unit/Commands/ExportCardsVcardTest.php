<?php

namespace Tests\Unit\Commands;

use App\Console\Commands\ExportCardsVcard;
use App\Models\Card;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Support\Facades\File;
use Tests\TestCase;

class ExportCardsVcardTest extends TestCase
{
    use RefreshDatabase;

    /**
     * Test exporting all cards to default directory.
     */
    public function test_export_all_cards_to_default_directory(): void
    {
        $outputDir = 'storage/exports';
        $outputPath = base_path($outputDir);

        // Clean up if directory exists
        if (File::exists($outputPath)) {
            File::deleteDirectory($outputPath);
        }

        Card::create([
            'unique_name' => 'card1',
            'first_name' => 'John',
            'last_name' => 'Doe',
            'email_work' => 'john@example.com',
        ]);

        Card::create([
            'unique_name' => 'card2',
            'first_name' => 'Jane',
            'last_name' => 'Smith',
            'phone' => '555-1234',
        ]);

        $this->artisan('cards:export-vcard')
            ->assertExitCode(0);

        $this->assertTrue(File::exists($outputPath . '/card1.vcf'));
        $this->assertTrue(File::exists($outputPath . '/card2.vcf'));

        $card1Content = File::get($outputPath . '/card1.vcf');
        $this->assertStringContainsString('FN:John Doe', $card1Content);
        $this->assertStringContainsString('EMAIL;TYPE=WORK:john@example.com', $card1Content);

        $card2Content = File::get($outputPath . '/card2.vcf');
        $this->assertStringContainsString('FN:Jane Smith', $card2Content);
        $this->assertStringContainsString('TEL:555-1234', $card2Content);

        // Clean up
        File::deleteDirectory($outputPath);
    }

    /**
     * Test exporting to custom output directory.
     */
    public function test_export_cards_to_custom_directory(): void
    {
        $outputDir = 'storage/custom_exports';
        $outputPath = base_path($outputDir);

        // Clean up if directory exists
        if (File::exists($outputPath)) {
            File::deleteDirectory($outputPath);
        }

        Card::create([
            'unique_name' => 'test_card',
            'first_name' => 'Test',
            'last_name' => 'User',
        ]);

        $this->artisan('cards:export-vcard', ['--output' => $outputDir])
            ->assertExitCode(0);

        $this->assertTrue(File::exists($outputPath . '/test_card.vcf'));

        // Clean up
        File::deleteDirectory($outputPath);
    }

    /**
     * Test exporting with no cards.
     */
    public function test_export_with_no_cards(): void
    {
        $outputDir = 'storage/exports_empty';
        $outputPath = base_path($outputDir);

        // Clean up if directory exists
        if (File::exists($outputPath)) {
            File::deleteDirectory($outputPath);
        }

        $this->artisan('cards:export-vcard', ['--output' => $outputDir])
            ->expectsOutput('No cards found to export.')
            ->assertExitCode(0);

        // Clean up
        if (File::exists($outputPath)) {
            File::deleteDirectory($outputPath);
        }
    }

    /**
     * Test export command creates output directory if it doesn't exist.
     */
    public function test_export_creates_output_directory(): void
    {
        $outputDir = 'storage/test_new_dir';
        $outputPath = base_path($outputDir);

        // Ensure directory doesn't exist
        if (File::exists($outputPath)) {
            File::deleteDirectory($outputPath);
        }

        Card::create([
            'unique_name' => 'card_for_new_dir',
            'first_name' => 'New',
            'last_name' => 'Dir',
        ]);

        $this->artisan('cards:export-vcard', ['--output' => $outputDir])
            ->assertExitCode(0);

        $this->assertTrue(File::exists($outputPath));
        $this->assertTrue(File::exists($outputPath . '/card_for_new_dir.vcf'));

        // Clean up
        File::deleteDirectory($outputPath);
    }

    /**
     * Test exporting multiple cards with various data.
     */
    public function test_export_multiple_cards_with_full_data(): void
    {
        $outputDir = 'storage/exports_full';
        $outputPath = base_path($outputDir);

        if (File::exists($outputPath)) {
            File::deleteDirectory($outputPath);
        }

        for ($i = 1; $i <= 3; $i++) {
            Card::create([
                'unique_name' => "card_{$i}",
                'first_name' => "Person{$i}",
                'last_name' => "Test",
                'phone' => "555-000{$i}",
                'email_work' => "person{$i}@work.com",
                'address' => "{$i}00 Main St",
                'birthday' => "1990-0{$i}-15",
                'notes' => "Notes for person {$i}",
            ]);
        }

        $this->artisan('cards:export-vcard', ['--output' => $outputDir])
            ->assertExitCode(0);

        for ($i = 1; $i <= 3; $i++) {
            $filePath = $outputPath . "/card_{$i}.vcf";
            $this->assertTrue(File::exists($filePath));
            
            $content = File::get($filePath);
            $this->assertStringContainsString("FN:Person{$i} Test", $content);
            $this->assertStringContainsString("TEL:555-000{$i}", $content);
            $this->assertStringContainsString("EMAIL;TYPE=WORK:person{$i}@work.com", $content);
            $this->assertStringContainsString("{$i}00 Main St", $content);
            $this->assertStringContainsString("Notes for person {$i}", $content);
        }

        // Clean up
        File::deleteDirectory($outputPath);
    }
}
