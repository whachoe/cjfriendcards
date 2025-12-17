<?php

namespace App\Http\Controllers;

use App\Models\Card;
use Illuminate\Http\Request;
use Illuminate\View\View;
use Illuminate\Http\RedirectResponse;

class CardController extends Controller
{
    /**
     * Display a listing of all cards.
     */
    public function index(Request $request): View
    {
        $sortOrder = $request->query('sort_order', 'asc');
        $nextSortOrder = $sortOrder === 'asc' ? 'desc' : 'asc';
        $cards = Card::orderBy('last_name', $sortOrder)->get();
        return view('cards.index', compact('cards', 'sortOrder', 'nextSortOrder'));
    }

    /**
     * Show the form for creating a new card.
     */
    public function create(): View
    {
        $allCards = Card::all();
        return view('cards.create', compact('allCards'));
    }

    /**
     * Store a newly created card in storage.
     */
    public function store(Request $request): RedirectResponse
    {
        $validated = $request->validate([
            'unique_name' => 'required|string|unique:cards,unique_name',
            'first_name' => 'required|string',
            'last_name' => 'required|string',
            'address' => 'nullable|string',
            'phone' => 'nullable|string',
            'email_work' => 'nullable|email',
            'email_personal' => 'nullable|email',
            'email_extra1' => 'nullable|email',
            'email_extra2' => 'nullable|email',
            'email_extra3' => 'nullable|email',
            'birthday' => 'nullable|date',
            'notes' => 'nullable|string',
        ]);

        Card::create($validated);

        return redirect()->route('cards.index')->with('success', 'Card created successfully.');
    }

    /**
     * Display the specified card.
     */
    public function show(Card $card): View
    {
        $relationships = $card->relationships()->with('relatedCard')->get();
        $relatedRelationships = $card->relatedRelationships()->with('card')->get();
        return view('cards.show', compact('card', 'relationships', 'relatedRelationships'));
    }

    /**
     * Show the form for editing the specified card.
     */
    public function edit(Card $card): View
    {
        $allCards = Card::where('id', '!=', $card->id)->get();
        return view('cards.edit', compact('card', 'allCards'));
    }

    /**
     * Update the specified card in storage.
     */
    public function update(Request $request, Card $card): RedirectResponse
    {
        $validated = $request->validate([
            'unique_name' => 'required|string|unique:cards,unique_name,' . $card->id,
            'first_name' => 'required|string',
            'last_name' => 'required|string',
            'address' => 'nullable|string',
            'phone' => 'nullable|string',
            'email_work' => 'nullable|email',
            'email_personal' => 'nullable|email',
            'email_extra1' => 'nullable|email',
            'email_extra2' => 'nullable|email',
            'email_extra3' => 'nullable|email',
            'birthday' => 'nullable|date',
            'notes' => 'nullable|string',
        ]);

        $card->update($validated);

        return redirect()->route('cards.show', $card)->with('success', 'Card updated successfully.');
    }

    /**
     * Remove the specified card from storage.
     */
    public function destroy(Card $card): RedirectResponse
    {
        $card->delete();
        return redirect()->route('cards.index')->with('success', 'Card deleted successfully.');
    }

    /**
     * Display the birthday calendar view.
     */
    public function birthdayCalendar(): View
    {
        $cards = Card::whereNotNull('birthday')->get();
        return view('cards.birthday-calendar', compact('cards'));
    }

    /**
     * Export a card as vCard format.
     */
    public function exportVcard(Card $card)
    {
        $vcard = $card->toVcard();

        return response($vcard, 200, [
            'Content-Type' => 'text/vcard; charset=utf-8',
            'Content-Disposition' => 'attachment; filename="' . $card->unique_name . '.vcf"',
        ]);
    }
}
