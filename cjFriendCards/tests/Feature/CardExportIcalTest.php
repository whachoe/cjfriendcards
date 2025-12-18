<?php

namespace Tests\Feature;

use App\Models\Card;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class CardExportIcalTest extends TestCase
{
    use RefreshDatabase;

    /**
     * Test exporting birthdays as iCal format.
     */
    public function test_export_birthdays_as_ical(): void
    {
        // Create cards with birthdays
        Card::create([
            'unique_name' => 'johndoe',
            'first_name' => 'John',
            'last_name' => 'Doe',
            'birthday' => '1990-05-15',
        ]);

        Card::create([
            'unique_name' => 'janedoe',
            'first_name' => 'Jane',
            'last_name' => 'Doe',
            'birthday' => '1992-12-25',
        ]);

        // Create a card without birthday (should not be included)
        Card::create([
            'unique_name' => 'nobirthdaycard',
            'first_name' => 'No',
            'last_name' => 'Birthday',
        ]);

        $response = $this->get('/api/v1/export/birthdays/ical');

        $response->assertStatus(200);
        $response->assertHeader('Content-Type', 'text/calendar; charset=utf-8');
        $this->assertStringContainsString('attachment', $response->headers->get('Content-Disposition'));
        $this->assertStringContainsString('.ics', $response->headers->get('Content-Disposition'));

        $content = $response->getContent();

        // Verify iCal structure
        $this->assertStringContainsString('BEGIN:VCALENDAR', $content);
        $this->assertStringContainsString('END:VCALENDAR', $content);
        $this->assertStringContainsString('VERSION:2.0', $content);
        $this->assertStringContainsString('PRODID:-//cjFriendCards//EN', $content);
        $this->assertStringContainsString('CALSCALE:GREGORIAN', $content);
        $this->assertStringContainsString('METHOD:PUBLISH', $content);
        $this->assertStringContainsString('X-WR-CALNAME:Friend Birthdays', $content);
        $this->assertStringContainsString('X-WR-TIMEZONE:UTC', $content);

        // Verify birthday events
        $this->assertStringContainsString('BEGIN:VEVENT', $content);
        $this->assertStringContainsString('END:VEVENT', $content);
        $this->assertStringContainsString('SUMMARY:Birthday: John Doe', $content);
        $this->assertStringContainsString('SUMMARY:Birthday: Jane Doe', $content);

        // Verify that card without birthday is not included
        $this->assertStringNotContainsString('No Birthday', $content);

        // Verify event properties
        $this->assertStringContainsString('STATUS:CONFIRMED', $content);
        $this->assertStringContainsString('DTSTART;VALUE=DATE:', $content);
        $this->assertStringContainsString('DTEND;VALUE=DATE:', $content);
        $this->assertStringContainsString('DTSTAMP:', $content);
        $this->assertStringContainsString('UID:', $content);
    }

    /**
     * Test exporting birthdays with no cards having birthdays.
     */
    public function test_export_birthdays_empty(): void
    {
        // Create a card without birthday
        Card::create([
            'unique_name' => 'nobirthdaycard',
            'first_name' => 'No',
            'last_name' => 'Birthday',
        ]);

        $response = $this->get('/api/v1/export/birthdays/ical');

        $response->assertStatus(200);
        $response->assertHeader('Content-Type', 'text/calendar; charset=utf-8');

        $content = $response->getContent();

        // Should still have valid iCal structure but no events
        $this->assertStringContainsString('BEGIN:VCALENDAR', $content);
        $this->assertStringContainsString('END:VCALENDAR', $content);
        $this->assertStringNotContainsString('BEGIN:VEVENT', $content);
    }

    /**
     * Test that birthday events include age calculation.
     */
    public function test_birthday_event_includes_age(): void
    {
        Card::create([
            'unique_name' => 'johndoe',
            'first_name' => 'John',
            'last_name' => 'Doe',
            'birthday' => '1990-05-15',
        ]);

        $response = $this->get('/api/v1/export/birthdays/ical');

        $response->assertStatus(200);
        $content = $response->getContent();

        $age = now()->year - 1990 + 1;

        // Verify that description includes age information
        $this->assertStringContainsString('DESCRIPTION:John Doe turns', $content);
        $this->assertStringContainsString("$age years old", $content);
    }

    /**
     * Test that exported iCal file has correct filename format.
     */
    public function test_ical_file_has_correct_filename(): void
    {
        Card::create([
            'unique_name' => 'johndoe',
            'first_name' => 'John',
            'last_name' => 'Doe',
            'birthday' => '1990-05-15',
        ]);

        $response = $this->get('/api/v1/export/birthdays/ical');

        $response->assertStatus(200);
        $disposition = $response->headers->get('Content-Disposition');
        
        $this->assertStringContainsString('attachment', $disposition);
        $this->assertStringContainsString('birthdays_', $disposition);
        $this->assertStringContainsString('.ics', $disposition);
    }

    /**
     * Test that multiple birthdays are properly formatted.
     */
    public function test_multiple_birthdays_formatting(): void
    {
        // Create multiple cards with birthdays
        for ($i = 1; $i <= 5; $i++) {
            Card::create([
                'unique_name' => "person$i",
                'first_name' => "Person",
                'last_name' => "Number$i",
                'birthday' => "1985-0{$i}-01",
            ]);
        }

        $response = $this->get('/api/v1/export/birthdays/ical');

        $response->assertStatus(200);
        $content = $response->getContent();

        // Count VEVENT entries
        $eventCount = substr_count($content, 'BEGIN:VEVENT');
        $this->assertEquals(5, $eventCount, 'Should have 5 birthday events');

        // Verify all names are in the calendar
        for ($i = 1; $i <= 5; $i++) {
            $this->assertStringContainsString("Person Number$i", $content);
        }
    }
}
