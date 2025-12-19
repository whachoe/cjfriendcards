<?php

namespace App\Http\Controllers;

use App\Models\Card;
use App\Models\Relationship;
use Illuminate\Http\Request;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\JsonResponse;

class RelationshipController extends Controller
{
    /**
     * Get autocomplete suggestions for card unique_names.
     */
    public function autocomplete(Request $request, Card $card): JsonResponse
    {
        $query = $request->get('q', '');
        
        $cards = Card::where('id', '!=', $card->id)
            ->where('unique_name', 'like', "%{$query}%")
            ->limit(10)
            ->get(['id', 'unique_name', 'first_name', 'last_name'])
            ->map(fn ($c) => [
                'id' => $c->id,
                'unique_name' => $c->unique_name,
                'display' => "{$c->unique_name} ({$c->full_name})",
            ]);

        return response()->json($cards);
    }

    /**
     * Store a new relationship.
     */
    public function store(Request $request, Card $card)
    {
        $validated = $request->validate([
            'related_card_id' => 'required|exists:cards,id|different:card_id',
            'relationship_type' => 'required|in:friend,colleague,family,spouse,child,parent,acquaintance,ex-partner',
            'notes' => 'nullable|string',
        ]);

        // Check if relationship already exists
        $exists = Relationship::where('card_id', $card->id)
            ->where('related_card_id', $validated['related_card_id'])
            ->exists();

        if ($exists) {
            if ($request->wantsJson()) {
                return response()->json(['errors' => ['related_card_id' => 'This relationship already exists.']], 422);
            }

            return redirect()->back()->withErrors(['related_card_id' => 'This relationship already exists.']);
        }

        $validated['card_id'] = $card->id;
        $relationship = Relationship::create($validated);

        if ($request->wantsJson()) {
            return response()->json(['message' => 'Relationship added successfully.', 'data' => $relationship], 201);
        }

        return redirect()->route('cards.show', $card)->with('success', 'Relationship added successfully.');
    }

    /**
     * Update a relationship.
     */
    public function update(Request $request, Card $card, Relationship $relationship)
    {
        // Ensure the relationship belongs to this card
        if ($relationship->card_id !== $card->id) {
            abort(403);
        }

        $validated = $request->validate([
            'relationship_type' => 'required|in:best_friend,colleague,family,spouse,child,parent,acquaintance,ex-partner',
            'notes' => 'nullable|string',
        ]);

        $relationship->update($validated);

        if ($request->wantsJson()) {
            return response()->json(['message' => 'Relationship updated successfully.', 'data' => $relationship]);
        }

        return redirect()->route('cards.show', $card)->with('success', 'Relationship updated successfully.');
    }

    /**
     * Delete a relationship.
     */
    public function destroy(Request $request, Card $card, Relationship $relationship)
    {
        // Ensure the relationship belongs to this card
        if ($relationship->card_id !== $card->id) {
            abort(403);
        }

        $relationship->delete();

        if ($request->wantsJson()) {
            return response()->json(['message' => 'Relationship deleted successfully.']);
        }

        return redirect()->route('cards.show', $card)->with('success', 'Relationship deleted successfully.');
    }
}
