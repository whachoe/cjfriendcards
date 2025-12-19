<?php

namespace Tests\Feature;

use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;
use App\Models\Card;

class CardsJsonTest extends TestCase
{
    use RefreshDatabase;

    public function test_index_returns_json()
    {
        Card::create([
            'unique_name' => 'john-doe',
            'first_name' => 'John',
            'last_name' => 'Doe',
        ]);

        $response = $this->getJson(route('cards.index'));

        $response->assertStatus(200)
            ->assertJsonStructure(['data', 'meta']);
    }

    public function test_store_returns_json_and_creates()
    {
        $payload = [
            'unique_name' => 'jane-doe',
            'first_name' => 'Jane',
            'last_name' => 'Doe',
        ];

        $response = $this->postJson(route('cards.store'), $payload);

        $response->assertStatus(201)
            ->assertJson(['message' => 'Card created successfully.'])
            ->assertJsonStructure(['message', 'data' => ['id', 'unique_name', 'first_name', 'last_name']]);

        $this->assertDatabaseHas('cards', ['unique_name' => 'jane-doe']);
    }

    public function test_show_returns_json()
    {
        $card = Card::create([
            'unique_name' => 'show-me',
            'first_name' => 'Show',
            'last_name' => 'Me',
        ]);

        $response = $this->getJson(route('cards.show', $card));

        $response->assertStatus(200)
            ->assertJsonStructure(['data', 'relationships', 'related_relationships']);
    }

    public function test_update_returns_json()
    {
        $card = Card::create([
            'unique_name' => 'updatable',
            'first_name' => 'Up',
            'last_name' => 'Date',
        ]);

        $payload = [
            'unique_name' => 'updatable',
            'first_name' => 'Updated',
            'last_name' => 'Name',
        ];

        $response = $this->patchJson(route('cards.update', $card), $payload);

        $response->assertStatus(200)
            ->assertJson(['message' => 'Card updated successfully.']);

        $this->assertDatabaseHas('cards', ['first_name' => 'Updated']);
    }

    public function test_destroy_returns_json()
    {
        $card = Card::create([
            'unique_name' => 'removable',
            'first_name' => 'Rem',
            'last_name' => 'Ove',
        ]);

        $response = $this->deleteJson(route('cards.destroy', $card));

        $response->assertStatus(200)
            ->assertJson(['message' => 'Card deleted successfully.']);

        $this->assertDatabaseMissing('cards', ['unique_name' => 'removable']);
    }

    public function test_birthday_calendar_returns_json()
    {
        $card = Card::create([
            'unique_name' => 'born',
            'first_name' => 'Born',
            'last_name' => 'Day',
            'birthday' => now()->toDateString(),
        ]);

        $response = $this->getJson(route('cards.birthday-calendar'));

        $response->assertStatus(200)
            ->assertJsonStructure(['data']);
    }
}
