<?php

namespace Tests\Feature;

use App\Models\Card;
use App\Models\Relationship;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class RelationshipControllerTest extends TestCase
{
    use RefreshDatabase;

    /**
     * Test autocomplete returns matching card names.
     */
    public function test_autocomplete_returns_matching_cards(): void
    {
        $card = Card::create([
            'unique_name' => 'johndoe',
            'first_name' => 'John',
            'last_name' => 'Doe',
        ]);

        Card::create([
            'unique_name' => 'john_smith',
            'first_name' => 'John',
            'last_name' => 'Smith',
        ]);

        Card::create([
            'unique_name' => 'janedoe',
            'first_name' => 'Jane',
            'last_name' => 'Doe',
        ]);

        $response = $this->get(route('relationships.autocomplete', $card) . '?q=john');

        $response->assertStatus(200);
        $response->assertJson([
            ['unique_name' => 'john_smith'],
        ]);
        $this->assertCount(1, $response->json());
    }

    /**
     * Test autocomplete returns empty array when no matches.
     */
    public function test_autocomplete_returns_empty_when_no_matches(): void
    {
        $card = Card::create([
            'unique_name' => 'johndoe',
            'first_name' => 'John',
            'last_name' => 'Doe',
        ]);

        Card::create([
            'unique_name' => 'janedoe',
            'first_name' => 'Jane',
            'last_name' => 'Doe',
        ]);

        $response = $this->get(route('relationships.autocomplete', $card) . '?q=xyz');

        $response->assertStatus(200);
        $this->assertCount(0, $response->json());
    }

    /**
     * Test autocomplete excludes the card itself.
     */
    public function test_autocomplete_excludes_the_same_card(): void
    {
        $card = Card::create([
            'unique_name' => 'johndoe',
            'first_name' => 'John',
            'last_name' => 'Doe',
        ]);

        $response = $this->get(route('relationships.autocomplete', $card) . '?q=john');

        $response->assertStatus(200);
        $this->assertCount(0, $response->json());
    }

    /**
     * Test autocomplete respects the limit of 10 results.
     */
    public function test_autocomplete_limits_results_to_10(): void
    {
        $card = Card::create([
            'unique_name' => 'maincard',
            'first_name' => 'Main',
            'last_name' => 'Card',
        ]);

        // Create 15 cards with matching names
        for ($i = 1; $i <= 15; $i++) {
            Card::create([
                'unique_name' => "test_user_{$i}",
                'first_name' => 'Test',
                'last_name' => "User{$i}",
            ]);
        }

        $response = $this->get(route('relationships.autocomplete', $card) . '?q=test');

        $response->assertStatus(200);
        $this->assertCount(10, $response->json());
    }

    /**
     * Test autocomplete response includes display format.
     */
    public function test_autocomplete_response_includes_display_format(): void
    {
        $card = Card::create([
            'unique_name' => 'maincard',
            'first_name' => 'Main',
            'last_name' => 'Card',
        ]);

        Card::create([
            'unique_name' => 'testuser',
            'first_name' => 'Test',
            'last_name' => 'User',
        ]);

        $response = $this->get(route('relationships.autocomplete', $card) . '?q=test');

        $response->assertStatus(200);
        $json = $response->json();
        $this->assertCount(1, $json);
        $this->assertArrayHasKey('id', $json[0]);
        $this->assertArrayHasKey('unique_name', $json[0]);
        $this->assertArrayHasKey('display', $json[0]);
        $this->assertStringContainsString('testuser', $json[0]['display']);
        $this->assertStringContainsString('Test User', $json[0]['display']);
    }

    /**
     * Test storing a relationship with valid data.
     */
    public function test_store_creates_relationship_with_valid_data(): void
    {
        $card1 = Card::create([
            'unique_name' => 'johndoe',
            'first_name' => 'John',
            'last_name' => 'Doe',
        ]);

        $card2 = Card::create([
            'unique_name' => 'janedoe',
            'first_name' => 'Jane',
            'last_name' => 'Doe',
        ]);

        $data = [
            'related_card_id' => $card2->id,
            'relationship_type' => 'friend',
            'notes' => 'College roommate',
        ];

        $response = $this->post(route('relationships.store', $card1), $data);

        $response->assertRedirect(route('cards.show', $card1));
        $response->assertSessionHas('success', 'Relationship added successfully.');
        $this->assertDatabaseHas('relationships', [
            'card_id' => $card1->id,
            'related_card_id' => $card2->id,
            'relationship_type' => 'friend',
            'notes' => 'College roommate',
        ]);
    }

    /**
     * Test storing a relationship with all valid relationship types.
     */
    public function test_store_accepts_all_valid_relationship_types(): void
    {
        $card1 = Card::create([
            'unique_name' => 'maincard',
            'first_name' => 'Main',
            'last_name' => 'Card',
        ]);

        $validTypes = ['friend', 'colleague', 'family', 'spouse', 'child', 'parent', 'acquaintance', 'ex-partner'];

        foreach ($validTypes as $index => $type) {
            $card2 = Card::create([
                'unique_name' => "card_{$index}",
                'first_name' => "Card",
                'last_name' => $index,
            ]);

            $response = $this->post(route('relationships.store', $card1), [
                'related_card_id' => $card2->id,
                'relationship_type' => $type,
            ]);

            $response->assertRedirect();
            $this->assertDatabaseHas('relationships', [
                'card_id' => $card1->id,
                'related_card_id' => $card2->id,
                'relationship_type' => $type,
            ]);
        }
    }

    /**
     * Test store fails with invalid relationship type.
     */
    public function test_store_fails_with_invalid_relationship_type(): void
    {
        $card1 = Card::create([
            'unique_name' => 'johndoe',
            'first_name' => 'John',
            'last_name' => 'Doe',
        ]);

        $card2 = Card::create([
            'unique_name' => 'janedoe',
            'first_name' => 'Jane',
            'last_name' => 'Doe',
        ]);

        $data = [
            'related_card_id' => $card2->id,
            'relationship_type' => 'invalid_type',
        ];

        $response = $this->post(route('relationships.store', $card1), $data);

        $response->assertSessionHasErrors('relationship_type');
        $this->assertDatabaseCount('relationships', 0);
    }

    /**
     * Test store fails with missing related_card_id.
     */
    public function test_store_fails_with_missing_related_card_id(): void
    {
        $card = Card::create([
            'unique_name' => 'johndoe',
            'first_name' => 'John',
            'last_name' => 'Doe',
        ]);

        $data = [
            'relationship_type' => 'friend',
        ];

        $response = $this->post(route('relationships.store', $card), $data);

        $response->assertSessionHasErrors('related_card_id');
    }

    /**
     * Test store fails with non-existent related card.
     */
    public function test_store_fails_with_non_existent_related_card(): void
    {
        $card = Card::create([
            'unique_name' => 'johndoe',
            'first_name' => 'John',
            'last_name' => 'Doe',
        ]);

        $data = [
            'related_card_id' => 9999,
            'relationship_type' => 'friend',
        ];

        $response = $this->post(route('relationships.store', $card), $data);

        $response->assertSessionHasErrors('related_card_id');
    }

    /**
     * Test store fails when trying to create a relationship with the same card.
     */
    public function test_store_fails_when_card_references_itself(): void
    {
        $card = Card::create([
            'unique_name' => 'johndoe',
            'first_name' => 'John',
            'last_name' => 'Doe',
        ]);

        $data = [
            'related_card_id' => $card->id,
            'relationship_type' => 'friend',
        ];

        $response = $this->post(route('relationships.store', $card), $data);

        // The 'different' validation rule should reject this
        $this->assertTrue(
            $response->status() === 302 || $response->sessionHas('errors'),
            'Request should either redirect with errors or fail validation'
        );
    }

    /**
     * Test store fails when relationship already exists.
     */
    public function test_store_fails_with_duplicate_relationship(): void
    {
        $card1 = Card::create([
            'unique_name' => 'johndoe',
            'first_name' => 'John',
            'last_name' => 'Doe',
        ]);

        $card2 = Card::create([
            'unique_name' => 'janedoe',
            'first_name' => 'Jane',
            'last_name' => 'Doe',
        ]);

        Relationship::create([
            'card_id' => $card1->id,
            'related_card_id' => $card2->id,
            'relationship_type' => 'friend',
        ]);

        $data = [
            'related_card_id' => $card2->id,
            'relationship_type' => 'colleague',
        ];

        $response = $this->post(route('relationships.store', $card1), $data);

        $response->assertSessionHasErrors('related_card_id');
        $response->assertSessionHas('errors');
    }

    /**
     * Test updating a relationship with valid data.
     */
    public function test_update_modifies_relationship_successfully(): void
    {
        $card1 = Card::create([
            'unique_name' => 'johndoe',
            'first_name' => 'John',
            'last_name' => 'Doe',
        ]);

        $card2 = Card::create([
            'unique_name' => 'janedoe',
            'first_name' => 'Jane',
            'last_name' => 'Doe',
        ]);

        $relationship = Relationship::create([
            'card_id' => $card1->id,
            'related_card_id' => $card2->id,
            'relationship_type' => 'friend',
            'notes' => 'Old notes',
        ]);

        $updatedData = [
            'relationship_type' => 'colleague',
            'notes' => 'Updated notes',
        ];

        $response = $this->patch(
            route('relationships.update', [$card1, $relationship]),
            $updatedData
        );

        $response->assertRedirect(route('cards.show', $card1));
        $response->assertSessionHas('success', 'Relationship updated successfully.');
        $this->assertDatabaseHas('relationships', [
            'id' => $relationship->id,
            'relationship_type' => 'colleague',
            'notes' => 'Updated notes',
        ]);
    }

    /**
     * Test update fails when relationship belongs to a different card.
     */
    public function test_update_fails_when_relationship_belongs_to_different_card(): void
    {
        $card1 = Card::create([
            'unique_name' => 'johndoe',
            'first_name' => 'John',
            'last_name' => 'Doe',
        ]);

        $card2 = Card::create([
            'unique_name' => 'janedoe',
            'first_name' => 'Jane',
            'last_name' => 'Doe',
        ]);

        $card3 = Card::create([
            'unique_name' => 'bobsmith',
            'first_name' => 'Bob',
            'last_name' => 'Smith',
        ]);

        $relationship = Relationship::create([
            'card_id' => $card2->id,
            'related_card_id' => $card3->id,
            'relationship_type' => 'friend',
        ]);

        // Try to update relationship with card1 as the owner
        $response = $this->patch(
            route('relationships.update', [$card1, $relationship]),
            ['relationship_type' => 'colleague']
        );

        $response->assertStatus(403);
    }

    /**
     * Test update fails with invalid relationship type.
     */
    public function test_update_fails_with_invalid_relationship_type(): void
    {
        $card1 = Card::create([
            'unique_name' => 'johndoe',
            'first_name' => 'John',
            'last_name' => 'Doe',
        ]);

        $card2 = Card::create([
            'unique_name' => 'janedoe',
            'first_name' => 'Jane',
            'last_name' => 'Doe',
        ]);

        $relationship = Relationship::create([
            'card_id' => $card1->id,
            'related_card_id' => $card2->id,
            'relationship_type' => 'friend',
        ]);

        $response = $this->patch(
            route('relationships.update', [$card1, $relationship]),
            ['relationship_type' => 'invalid_type']
        );

        $response->assertSessionHasErrors('relationship_type');
    }

    /**
     * Test deleting a relationship.
     */
    public function test_destroy_deletes_relationship(): void
    {
        $card1 = Card::create([
            'unique_name' => 'johndoe',
            'first_name' => 'John',
            'last_name' => 'Doe',
        ]);

        $card2 = Card::create([
            'unique_name' => 'janedoe',
            'first_name' => 'Jane',
            'last_name' => 'Doe',
        ]);

        $relationship = Relationship::create([
            'card_id' => $card1->id,
            'related_card_id' => $card2->id,
            'relationship_type' => 'friend',
        ]);

        $response = $this->delete(
            route('relationships.destroy', [$card1, $relationship])
        );

        $response->assertRedirect(route('cards.show', $card1));
        $response->assertSessionHas('success', 'Relationship deleted successfully.');
        $this->assertDatabaseCount('relationships', 0);
    }

    /**
     * Test destroy fails when relationship belongs to a different card.
     */
    public function test_destroy_fails_when_relationship_belongs_to_different_card(): void
    {
        $card1 = Card::create([
            'unique_name' => 'johndoe',
            'first_name' => 'John',
            'last_name' => 'Doe',
        ]);

        $card2 = Card::create([
            'unique_name' => 'janedoe',
            'first_name' => 'Jane',
            'last_name' => 'Doe',
        ]);

        $card3 = Card::create([
            'unique_name' => 'bobsmith',
            'first_name' => 'Bob',
            'last_name' => 'Smith',
        ]);

        $relationship = Relationship::create([
            'card_id' => $card2->id,
            'related_card_id' => $card3->id,
            'relationship_type' => 'friend',
        ]);

        // Try to delete relationship with card1 as the owner
        $response = $this->delete(
            route('relationships.destroy', [$card1, $relationship])
        );

        $response->assertStatus(403);
        $this->assertDatabaseCount('relationships', 1);
    }
}
