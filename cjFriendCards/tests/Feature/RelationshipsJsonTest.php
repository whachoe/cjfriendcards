<?php

namespace Tests\Feature;

use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;
use App\Models\Card;
use App\Models\Relationship;

class RelationshipsJsonTest extends TestCase
{
    use RefreshDatabase;

    public function test_autocomplete_returns_json()
    {
        $card = Card::create([
            'unique_name' => 'a-card',
            'first_name' => 'A',
            'last_name' => 'Card',
        ]);

        $other = Card::create([
            'unique_name' => 'b-card',
            'first_name' => 'B',
            'last_name' => 'Card',
        ]);

        $response = $this->getJson(route('relationships.autocomplete', $card) . '?q=b-card');

        $response->assertStatus(200)
            ->assertJsonStructure([['id', 'unique_name', 'display']]);
    }

    public function test_store_update_and_destroy_return_json()
    {
        $card = Card::create([
            'unique_name' => 'root-card',
            'first_name' => 'Root',
            'last_name' => 'Card',
        ]);

        $other = Card::create([
            'unique_name' => 'friend-card',
            'first_name' => 'Friend',
            'last_name' => 'Card',
        ]);

        // store
        $payload = [
            'related_card_id' => $other->id,
            'relationship_type' => 'friend',
        ];

        $response = $this->postJson(route('relationships.store', $card), $payload);
        $response->assertStatus(201)
            ->assertJson(['message' => 'Relationship added successfully.'])
            ->assertJsonStructure(['message', 'data' => ['id', 'card_id', 'related_card_id', 'relationship_type']]);

        $relationshipId = $response->json('data.id');

        // update
        $updatePayload = ['relationship_type' => 'colleague', 'notes' => 'Updated note'];
        $response = $this->patchJson(route('relationships.update', [$card, $relationshipId]), $updatePayload);
        $response->assertStatus(200)
            ->assertJson(['message' => 'Relationship updated successfully.']);

        // destroy
        $response = $this->deleteJson(route('relationships.destroy', [$card, $relationshipId]));
        $response->assertStatus(200)
            ->assertJson(['message' => 'Relationship deleted successfully.']);
    }
}
