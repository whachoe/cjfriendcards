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
    public function index(Request $request)
    {
        $sortOrder = $request->query('sort_order', 'asc');
        $nextSortOrder = $sortOrder === 'asc' ? 'desc' : 'asc';
        $cards = Card::orderBy('last_name', $sortOrder)->get();
        if ($request->wantsJson()) {
            return response()->json([
                'data' => $cards,
                'meta' => [
                    'sort_order' => $sortOrder,
                    'next_sort_order' => $nextSortOrder,
                ],
            ]);
        }

        return view('cards.index', compact('cards', 'sortOrder', 'nextSortOrder'));
    }

    /**
     * Show the form for creating a new card.
     */
    public function create(Request $request)
    {
        $allCards = Card::all();
        if ($request->wantsJson()) {
            return response()->json(['data' => $allCards]);
        }

        return view('cards.create', compact('allCards'));
    }

    /**
     * Store a newly created card in storage.
     */
    public function store(Request $request)
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

        $card = Card::create($validated);

        if ($request->wantsJson()) {
            return response()->json(['message' => 'Card created successfully.', 'data' => $card], 201);
        }

        return redirect()->route('cards.index')->with('success', 'Card created successfully.');
    }

    /**
     * Display the specified card.
     */
    public function show(Request $request, Card $card)
    {
        $relationships = $card->relationships()->with('relatedCard')->get();
        $relatedRelationships = $card->relatedRelationships()->with('card')->get();

        if ($request->wantsJson()) {
            return response()->json([
                'data' => $card,
                'relationships' => $relationships,
                'related_relationships' => $relatedRelationships,
            ]);
        }

        return view('cards.show', compact('card', 'relationships', 'relatedRelationships'));
    }

    /**
     * Show the form for editing the specified card.
     */
    public function edit(Request $request, Card $card)
    {
        $allCards = Card::where('id', '!=', $card->id)->get();
        if ($request->wantsJson()) {
            return response()->json(['data' => $card, 'all' => $allCards]);
        }

        return view('cards.edit', compact('card', 'allCards'));
    }

    /**
     * Update the specified card in storage.
     */
    public function update(Request $request, Card $card)
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

        if ($request->wantsJson()) {
            return response()->json(['message' => 'Card updated successfully.', 'data' => $card]);
        }

        return redirect()->route('cards.show', $card)->with('success', 'Card updated successfully.');
    }

    /**
     * Remove the specified card from storage.
     */
    public function destroy(Request $request, Card $card)
    {
        $card->delete();

        if ($request->wantsJson()) {
            return response()->json(['message' => 'Card deleted successfully.']);
        }

        return redirect()->route('cards.index')->with('success', 'Card deleted successfully.');
    }

    /**
     * Display the birthday calendar view.
     */
    public function birthdayCalendar(Request $request)
    {
        $cards = Card::whereNotNull('birthday')->get();
        if ($request->wantsJson()) {
            return response()->json(['data' => $cards]);
        }

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

    /**
     * Export all cards as CSV format.
     */
    public function exportCsv()
    {
        $cards = Card::orderBy('last_name')->get();

        $csv = "First Name,Last Name,Unique Name,Phone,Email (Work),Email (Personal),Email (Extra 1),Email (Extra 2),Email (Extra 3),Address,Birthday,Notes\n";

        foreach ($cards as $card) {
            $csv .= "\"" . str_replace('"', '""', $card->first_name) . "\",";
            $csv .= "\"" . str_replace('"', '""', $card->last_name) . "\",";
            $csv .= "\"" . str_replace('"', '""', $card->unique_name) . "\",";
            $csv .= "\"" . str_replace('"', '""', $card->phone ?? '') . "\",";
            $csv .= "\"" . str_replace('"', '""', $card->email_work ?? '') . "\",";
            $csv .= "\"" . str_replace('"', '""', $card->email_personal ?? '') . "\",";
            $csv .= "\"" . str_replace('"', '""', $card->email_extra1 ?? '') . "\",";
            $csv .= "\"" . str_replace('"', '""', $card->email_extra2 ?? '') . "\",";
            $csv .= "\"" . str_replace('"', '""', $card->email_extra3 ?? '') . "\",";
            $csv .= "\"" . str_replace('"', '""', $card->address ?? '') . "\",";
            $csv .= "\"" . str_replace('"', '""', $card->birthday?->format('Y-m-d') ?? '') . "\",";
            $csv .= "\"" . str_replace('"', '""', $card->notes ?? '') . "\"\n";
        }

        return response($csv, 200, [
            'Content-Type' => 'text/csv; charset=utf-8',
            'Content-Disposition' => 'attachment; filename="cards_' . date('Y-m-d_H-i-s') . '.csv"',
        ]);
    }

    /**
     * Export all birthdays as iCal format.
     */
    public function exportBirthdaysIcal()
    {
        $cards = Card::whereNotNull('birthday')->orderBy('birthday')->get();

        $ical = "BEGIN:VCALENDAR\r\n";
        $ical .= "VERSION:2.0\r\n";
        $ical .= "PRODID:-//cjFriendCards//EN\r\n";
        $ical .= "CALSCALE:GREGORIAN\r\n";
        $ical .= "METHOD:PUBLISH\r\n";
        $ical .= "X-WR-CALNAME:Friend Birthdays\r\n";
        $ical .= "X-WR-TIMEZONE:UTC\r\n";
        $ical .= "DESCRIPTION:Birthdays of friends from cjFriendCards\r\n";

        foreach ($cards as $card) {
            $birthdayMonth = $card->birthday->format('m');
            $birthdayDay = $card->birthday->format('d');
            $year = now()->year;

            // If birthday has already passed this year, put it in next year
            $birthdayThisYear = \Carbon\Carbon::createFromDate($year, $birthdayMonth, $birthdayDay);
            if ($birthdayThisYear->isPast()) {
                $year++;
            }

            $eventDate = \Carbon\Carbon::createFromDate($year, $birthdayMonth, $birthdayDay);
            $nextDay = $eventDate->copy()->addDay();
            $age = (int) abs($eventDate->diffInYears($card->birthday));
            
            $uid = md5($card->id . '-birthday') . '@cjfriendcards.local';
            $ical .= "BEGIN:VEVENT\r\n";
            $ical .= "UID:" . $uid . "\r\n";
            $ical .= "DTSTART;VALUE=DATE:" . $eventDate->format('Ymd') . "\r\n";
            $ical .= "DTEND;VALUE=DATE:" . $nextDay->format('Ymd') . "\r\n";
            $ical .= "DTSTAMP:" . now()->format('Ymd\THis\Z') . "\r\n";
            $ical .= "CREATED:" . $card->created_at->format('Ymd\THis\Z') . "\r\n";
            $ical .= "LAST-MODIFIED:" . $card->updated_at->format('Ymd\THis\Z') . "\r\n";
            $ical .= "SUMMARY:Birthday: " . $card->full_name . "\r\n";
            $ical .= "DESCRIPTION:" . $card->full_name . " turns " . $age . " years old\r\n";
            $ical .= "LOCATION:\r\n";
            $ical .= "STATUS:CONFIRMED\r\n";
            $ical .= "SEQUENCE:0\r\n";
            $ical .= "END:VEVENT\r\n";
        }

        $ical .= "END:VCALENDAR\r\n";

        return response($ical, 200, [
            'Content-Type' => 'text/calendar; charset=utf-8',
            'Content-Disposition' => 'attachment; filename="birthdays_' . date('Y-m-d_H-i-s') . '.ics"',
        ]);
    }
}
