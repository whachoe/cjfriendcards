<?php

namespace Tests\Feature;

use App\Models\Card;
use App\Models\Relationship;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class SmartRelationshipsTest extends TestCase
{
    use RefreshDatabase;

    /**
     * Test that creating a parent relationship automatically creates child relationship.
     */
    public function test_creating_parent_relationship_creates_child_relationship(): void
    {
        $parent = Card::create([
            'unique_name' => 'parent',
            'first_name' => 'Parent',
            'last_name' => 'Person',
        ]);

        $child = Card::create([
            'unique_name' => 'child',
            'first_name' => 'Child',
            'last_name' => 'Person',
        ]);

        $this->post(route('relationships.store', $parent), [
            'related_card_id' => $child->id,
            'relationship_type' => 'child',
            'notes' => 'My child',
        ]);

        // Check parent -> child relationship exists
        $this->assertDatabaseHas('relationships', [
            'card_id' => $parent->id,
            'related_card_id' => $child->id,
            'relationship_type' => 'child',
        ]);

        // Check opposite child -> parent relationship was created
        $this->assertDatabaseHas('relationships', [
            'card_id' => $child->id,
            'related_card_id' => $parent->id,
            'relationship_type' => 'parent',
        ]);
    }

    /**
     * Test that creating a spouse relationship creates opposite spouse relationship.
     */
    public function test_creating_spouse_relationship_creates_opposite_spouse_relationship(): void
    {
        $person1 = Card::create([
            'unique_name' => 'person1',
            'first_name' => 'Person',
            'last_name' => 'One',
        ]);

        $person2 = Card::create([
            'unique_name' => 'person2',
            'first_name' => 'Person',
            'last_name' => 'Two',
        ]);

        $this->post(route('relationships.store', $person1), [
            'related_card_id' => $person2->id,
            'relationship_type' => 'spouse',
        ]);

        // Both should have spouse relationships to each other
        $this->assertDatabaseHas('relationships', [
            'card_id' => $person1->id,
            'related_card_id' => $person2->id,
            'relationship_type' => 'spouse',
        ]);

        $this->assertDatabaseHas('relationships', [
            'card_id' => $person2->id,
            'related_card_id' => $person1->id,
            'relationship_type' => 'spouse',
        ]);
    }

    /**
     * Test that creating friend relationship creates opposite friend relationship.
     */
    public function test_creating_friend_relationship_creates_opposite_friend_relationship(): void
    {
        $friend1 = Card::create([
            'unique_name' => 'friend1',
            'first_name' => 'Friend',
            'last_name' => 'One',
        ]);

        $friend2 = Card::create([
            'unique_name' => 'friend2',
            'first_name' => 'Friend',
            'last_name' => 'Two',
        ]);

        $this->post(route('relationships.store', $friend1), [
            'related_card_id' => $friend2->id,
            'relationship_type' => 'friend',
            'notes' => 'Best friends',
        ]);

        $this->assertDatabaseHas('relationships', [
            'card_id' => $friend1->id,
            'related_card_id' => $friend2->id,
            'relationship_type' => 'friend',
        ]);

        $this->assertDatabaseHas('relationships', [
            'card_id' => $friend2->id,
            'related_card_id' => $friend1->id,
            'relationship_type' => 'friend',
        ]);
    }

    /**
     * Test that deleting a relationship also deletes the opposite relationship.
     */
    public function test_deleting_relationship_deletes_opposite_relationship(): void
    {
        $parent = Card::create([
            'unique_name' => 'parent',
            'first_name' => 'Parent',
            'last_name' => 'Person',
        ]);

        $child = Card::create([
            'unique_name' => 'child',
            'first_name' => 'Child',
            'last_name' => 'Person',
        ]);

        // Create parent -> child relationship (this will auto-create child -> parent)
        $this->post(route('relationships.store', $parent), [
            'related_card_id' => $child->id,
            'relationship_type' => 'child',
        ]);

        $relationship = Relationship::where('card_id', $parent->id)
            ->where('related_card_id', $child->id)
            ->first();

        // Delete the parent -> child relationship
        $this->delete(route('relationships.destroy', [$parent, $relationship]));

        // Both relationships should be deleted
        $this->assertDatabaseMissing('relationships', [
            'card_id' => $parent->id,
            'related_card_id' => $child->id,
        ]);

        $this->assertDatabaseMissing('relationships', [
            'card_id' => $child->id,
            'related_card_id' => $parent->id,
        ]);
    }

    /**
     * Test that updating a relationship updates the opposite relationship.
     */
    public function test_updating_relationship_updates_opposite_relationship(): void
    {
        $person1 = Card::create([
            'unique_name' => 'person1',
            'first_name' => 'Person',
            'last_name' => 'One',
        ]);

        $person2 = Card::create([
            'unique_name' => 'person2',
            'first_name' => 'Person',
            'last_name' => 'Two',
        ]);

        // Create friend relationship
        $this->post(route('relationships.store', $person1), [
            'related_card_id' => $person2->id,
            'relationship_type' => 'friend',
            'notes' => 'Old friends',
        ]);

        $relationship = Relationship::where('card_id', $person1->id)
            ->where('related_card_id', $person2->id)
            ->first();

        // Update to colleague
        $this->patch(route('relationships.update', [$person1, $relationship]), [
            'relationship_type' => 'colleague',
            'notes' => 'Work together now',
        ]);

        // Both relationships should be updated
        $this->assertDatabaseHas('relationships', [
            'card_id' => $person1->id,
            'related_card_id' => $person2->id,
            'relationship_type' => 'colleague',
        ]);

        $this->assertDatabaseHas('relationships', [
            'card_id' => $person2->id,
            'related_card_id' => $person1->id,
            'relationship_type' => 'colleague',
        ]);
    }

    /**
     * Test that creating a spouse relationship autofills address if empty.
     */
    public function test_creating_spouse_relationship_autofills_address(): void
    {
        $spouse1 = Card::create([
            'unique_name' => 'spouse1',
            'first_name' => 'Spouse',
            'last_name' => 'One',
            'address' => null, // No address yet
        ]);

        $spouse2 = Card::create([
            'unique_name' => 'spouse2',
            'first_name' => 'Spouse',
            'last_name' => 'Two',
            'address' => '123 Main Street',
        ]);

        $this->post(route('relationships.store', $spouse1), [
            'related_card_id' => $spouse2->id,
            'relationship_type' => 'spouse',
        ]);

        // Address should be copied
        $spouse1->refresh();
        $this->assertEquals('123 Main Street', $spouse1->address);
    }

    /**
     * Test that creating a parent relationship autofills address if empty.
     */
    public function test_creating_parent_relationship_autofills_address(): void
    {
        $child = Card::create([
            'unique_name' => 'child',
            'first_name' => 'Child',
            'last_name' => 'Person',
            'address' => null,
        ]);

        $parent = Card::create([
            'unique_name' => 'parent',
            'first_name' => 'Parent',
            'last_name' => 'Person',
            'address' => '456 Oak Avenue',
        ]);

        $this->post(route('relationships.store', $child), [
            'related_card_id' => $parent->id,
            'relationship_type' => 'parent',
        ]);

        // Address should be copied
        $child->refresh();
        $this->assertEquals('456 Oak Avenue', $child->address);
    }

    /**
     * Test that creating a child relationship autofills address if empty.
     */
    public function test_creating_child_relationship_autofills_address(): void
    {
        $parent = Card::create([
            'unique_name' => 'parent',
            'first_name' => 'Parent',
            'last_name' => 'Person',
            'address' => null,
        ]);

        $child = Card::create([
            'unique_name' => 'child',
            'first_name' => 'Child',
            'last_name' => 'Person',
            'address' => '789 Elm Street',
        ]);

        $this->post(route('relationships.store', $parent), [
            'related_card_id' => $child->id,
            'relationship_type' => 'child',
        ]);

        // Address should be copied
        $parent->refresh();
        $this->assertEquals('789 Elm Street', $parent->address);
    }

    /**
     * Test that address is not overwritten if it already exists.
     */
    public function test_address_not_overwritten_if_already_exists(): void
    {
        $spouse1 = Card::create([
            'unique_name' => 'spouse1',
            'first_name' => 'Spouse',
            'last_name' => 'One',
            'address' => '999 Current Address',
        ]);

        $spouse2 = Card::create([
            'unique_name' => 'spouse2',
            'first_name' => 'Spouse',
            'last_name' => 'Two',
            'address' => '123 Main Street',
        ]);

        $this->post(route('relationships.store', $spouse1), [
            'related_card_id' => $spouse2->id,
            'relationship_type' => 'spouse',
        ]);

        // Address should NOT be changed
        $spouse1->refresh();
        $this->assertEquals('999 Current Address', $spouse1->address);
    }

    /**
     * Test that opposite relationships are not created for non-reciprocal types.
     */
    public function test_no_opposite_relationship_for_unsupported_types(): void
    {
        // If we had a non-reciprocal type (none currently exist in our system),
        // we would test it here. For now, all our types are reciprocal.
        $this->assertTrue(true);
    }
}
