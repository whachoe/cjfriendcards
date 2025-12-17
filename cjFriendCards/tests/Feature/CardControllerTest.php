<?php

namespace Tests\Feature;

use App\Models\Card;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class CardControllerTest extends TestCase
{
    use RefreshDatabase;

    /**
     * Test retrieving all cards with index method.
     */
    public function test_index_returns_all_cards(): void
    {
        Card::factory()->count(3)->create();

        $response = $this->get(route('cards.index'));

        $response->assertStatus(200);
        $response->assertViewIs('cards.index');
        $response->assertViewHas('cards');
        $this->assertCount(3, $response->viewData('cards'));
    }

    /**
     * Test index method with sort_order parameter ascending.
     */
    public function test_index_sorts_cards_ascending(): void
    {
        Card::create([
            'unique_name' => 'charlie',
            'first_name' => 'Charlie',
            'last_name' => 'Brown',
        ]);
        Card::create([
            'unique_name' => 'alice',
            'first_name' => 'Alice',
            'last_name' => 'Adams',
        ]);
        Card::create([
            'unique_name' => 'bob',
            'first_name' => 'Bob',
            'last_name' => 'Baker',
        ]);

        $response = $this->get(route('cards.index') . '?sort_order=asc');

        $response->assertStatus(200);
        $cards = $response->viewData('cards');
        $this->assertEquals('Adams', $cards[0]->last_name);
        $this->assertEquals('Baker', $cards[1]->last_name);
        $this->assertEquals('Brown', $cards[2]->last_name);
    }

    /**
     * Test index method with sort_order parameter descending.
     */
    public function test_index_sorts_cards_descending(): void
    {
        Card::create([
            'unique_name' => 'charlie',
            'first_name' => 'Charlie',
            'last_name' => 'Brown',
        ]);
        Card::create([
            'unique_name' => 'alice',
            'first_name' => 'Alice',
            'last_name' => 'Adams',
        ]);

        $response = $this->get(route('cards.index') . '?sort_order=desc');

        $response->assertStatus(200);
        $cards = $response->viewData('cards');
        $this->assertEquals('Brown', $cards[0]->last_name);
        $this->assertEquals('Adams', $cards[1]->last_name);
    }

    /**
     * Test create form is displayed.
     */
    public function test_create_returns_create_view(): void
    {
        $response = $this->get(route('cards.create'));

        $response->assertStatus(200);
        $response->assertViewIs('cards.create');
        $response->assertViewHas('allCards');
    }

    /**
     * Test storing a new card with valid data.
     */
    public function test_store_creates_card_with_valid_data(): void
    {
        $data = [
            'unique_name' => 'johndoe',
            'first_name' => 'John',
            'last_name' => 'Doe',
            'address' => '123 Main St',
            'phone' => '555-1234',
            'email_work' => 'john@work.com',
            'email_personal' => 'john@personal.com',
            'birthday' => '1990-05-15',
            'notes' => 'A good friend',
        ];

        $response = $this->post(route('cards.store'), $data);

        $response->assertRedirect(route('cards.index'));
        $response->assertSessionHas('success', 'Card created successfully.');
        $this->assertDatabaseHas('cards', [
            'unique_name' => 'johndoe',
            'first_name' => 'John',
            'last_name' => 'Doe',
            'phone' => '555-1234',
        ]);
    }

    /**
     * Test storing a card with minimal required data.
     */
    public function test_store_creates_card_with_minimal_data(): void
    {
        $data = [
            'unique_name' => 'minimal_user',
            'first_name' => 'Min',
            'last_name' => 'User',
        ];

        $response = $this->post(route('cards.store'), $data);

        $response->assertRedirect(route('cards.index'));
        $this->assertDatabaseHas('cards', [
            'unique_name' => 'minimal_user',
            'first_name' => 'Min',
            'last_name' => 'User',
        ]);
    }

    /**
     * Test store fails with missing required fields.
     */
    public function test_store_fails_with_missing_required_fields(): void
    {
        $data = [
            'first_name' => 'John',
            'last_name' => 'Doe',
        ];

        $response = $this->post(route('cards.store'), $data);

        $response->assertSessionHasErrors('unique_name');
        $this->assertDatabaseCount('cards', 0);
    }

    /**
     * Test store fails with duplicate unique_name.
     */
    public function test_store_fails_with_duplicate_unique_name(): void
    {
        Card::create([
            'unique_name' => 'johndoe',
            'first_name' => 'John',
            'last_name' => 'Doe',
        ]);

        $data = [
            'unique_name' => 'johndoe',
            'first_name' => 'Jane',
            'last_name' => 'Smith',
        ];

        $response = $this->post(route('cards.store'), $data);

        $response->assertSessionHasErrors('unique_name');
        $this->assertDatabaseCount('cards', 1);
    }

    /**
     * Test store validates email fields.
     */
    public function test_store_fails_with_invalid_email(): void
    {
        $data = [
            'unique_name' => 'johndoe',
            'first_name' => 'John',
            'last_name' => 'Doe',
            'email_work' => 'invalid-email',
        ];

        $response = $this->post(route('cards.store'), $data);

        $response->assertSessionHasErrors('email_work');
    }

    /**
     * Test showing a specific card.
     */
    public function test_show_displays_card_with_relationships(): void
    {
        $card = Card::create([
            'unique_name' => 'johndoe',
            'first_name' => 'John',
            'last_name' => 'Doe',
        ]);

        $response = $this->get(route('cards.show', $card));

        $response->assertStatus(200);
        $response->assertViewIs('cards.show');
        $response->assertViewHas('card', $card);
        $response->assertViewHas('relationships');
        $response->assertViewHas('relatedRelationships');
    }

    /**
     * Test edit form is displayed for a card.
     */
    public function test_edit_returns_edit_view(): void
    {
        $card = Card::create([
            'unique_name' => 'johndoe',
            'first_name' => 'John',
            'last_name' => 'Doe',
        ]);

        $response = $this->get(route('cards.edit', $card));

        $response->assertStatus(200);
        $response->assertViewIs('cards.edit');
        $response->assertViewHas('card', $card);
        $response->assertViewHas('allCards');
    }

    /**
     * Test updating a card with valid data.
     */
    public function test_update_modifies_card_successfully(): void
    {
        $card = Card::create([
            'unique_name' => 'johndoe',
            'first_name' => 'John',
            'last_name' => 'Doe',
            'phone' => '555-1234',
        ]);

        $updatedData = [
            'unique_name' => 'johndoe',
            'first_name' => 'Jonathan',
            'last_name' => 'Smith',
            'phone' => '555-5678',
            'email_personal' => 'jonathan@email.com',
        ];

        $response = $this->patch(route('cards.update', $card), $updatedData);

        $response->assertRedirect(route('cards.show', $card));
        $response->assertSessionHas('success', 'Card updated successfully.');
        $this->assertDatabaseHas('cards', [
            'id' => $card->id,
            'first_name' => 'Jonathan',
            'last_name' => 'Smith',
            'phone' => '555-5678',
            'email_personal' => 'jonathan@email.com',
        ]);
    }

    /**
     * Test updating a card with the same unique_name.
     */
    public function test_update_allows_same_unique_name(): void
    {
        $card = Card::create([
            'unique_name' => 'johndoe',
            'first_name' => 'John',
            'last_name' => 'Doe',
        ]);

        $data = [
            'unique_name' => 'johndoe',
            'first_name' => 'John',
            'last_name' => 'Smith',
        ];

        $response = $this->patch(route('cards.update', $card), $data);

        $response->assertRedirect();
        $this->assertDatabaseHas('cards', [
            'id' => $card->id,
            'last_name' => 'Smith',
        ]);
    }

    /**
     * Test update fails with duplicate unique_name from another card.
     */
    public function test_update_fails_with_duplicate_unique_name_from_other_card(): void
    {
        $card1 = Card::create([
            'unique_name' => 'johndoe',
            'first_name' => 'John',
            'last_name' => 'Doe',
        ]);
        Card::create([
            'unique_name' => 'janedoe',
            'first_name' => 'Jane',
            'last_name' => 'Doe',
        ]);

        $data = [
            'unique_name' => 'janedoe',
            'first_name' => 'John',
            'last_name' => 'Doe',
        ];

        $response = $this->patch(route('cards.update', $card1), $data);

        $response->assertSessionHasErrors('unique_name');
    }

    /**
     * Test deleting a card.
     */
    public function test_destroy_deletes_card(): void
    {
        $card = Card::create([
            'unique_name' => 'johndoe',
            'first_name' => 'John',
            'last_name' => 'Doe',
        ]);

        $response = $this->delete(route('cards.destroy', $card));

        $response->assertRedirect(route('cards.index'));
        $response->assertSessionHas('success', 'Card deleted successfully.');
        $this->assertDatabaseCount('cards', 0);
    }

    /**
     * Test birthday calendar view displays cards with birthdays.
     */
    public function test_birthday_calendar_displays_cards_with_birthdays(): void
    {
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
            'birthday' => '1988-03-22',
        ]);
        Card::create([
            'unique_name' => 'nobirtday',
            'first_name' => 'No',
            'last_name' => 'Birthday',
            'birthday' => null,
        ]);

        $response = $this->get(route('cards.birthday-calendar'));

        $response->assertStatus(200);
        $response->assertViewIs('cards.birthday-calendar');
        $cards = $response->viewData('cards');
        $this->assertCount(2, $cards);
    }

    /**
     * Test birthday calendar view is empty when no cards have birthdays.
     */
    public function test_birthday_calendar_empty_with_no_birthdays(): void
    {
        Card::factory()->count(3)->create(['birthday' => null]);

        $response = $this->get(route('cards.birthday-calendar'));

        $response->assertStatus(200);
        $this->assertCount(0, $response->viewData('cards'));
    }
}
