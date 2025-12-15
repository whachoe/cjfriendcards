@extends('layout')

@section('title', 'Birthday Calendar')

@section('content')
<div class="mb-6">
    <h1 class="text-3xl font-bold text-[#8b4513]">Birthday Calendar</h1>
    <p class="text-[#d7263d] mt-2">View upcoming birthdays of your friends</p>
</div>

@if ($cards->isEmpty())
    <div class="bg-[#fef3c7] rounded-lg shadow p-8 text-center border border-[#ff6b35]">
        <p class="text-[#8b4513] mb-4">No birthdays tracked yet. Add birthday information to your cards!</p>
        <a href="{{ route('cards.create') }}" class="bg-[#ff6b35] text-white px-6 py-2 rounded hover:bg-[#d7263d]">Create Card</a>
    </div>
@else
    <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-3 gap-6">
        @php
            $cardsByMonth = $cards->groupBy(function ($card) {
                return $card->birthday->format('m');
            });
        @endphp

        @foreach (['01' => 'January', '02' => 'February', '03' => 'March', '04' => 'April', '05' => 'May', '06' => 'June', '07' => 'July', '08' => 'August', '09' => 'September', '10' => 'October', '11' => 'November', '12' => 'December'] as $monthNum => $monthName)
            <div class="bg-[#fef3c7] rounded-lg shadow overflow-hidden border border-[#ff6b35]">
                <div class="bg-[#8b4513] text-[#fef3c7] p-4">
                    <h2 class="text-lg font-semibold">{{ $monthName }}</h2>
                </div>
                <div class="p-4">
                    @if (isset($cardsByMonth[$monthNum]))
                        <div class="space-y-3">
                            @foreach ($cardsByMonth[$monthNum]->sortBy(function ($card) { return $card->birthday->format('d'); }) as $card)
                                <div class="p-3 bg-[#fef3c7] rounded border border-[#ff6b35] hover:bg-[#fff8e1] transition">
                                    <div class="flex justify-between items-start mb-1">
                                        <a href="{{ route('cards.show', $card) }}" class="text-[#ff6b35] hover:underline font-medium">
                                            {{ $card->full_name }}
                                        </a>
                                        <span class="text-xs font-semibold text-[#8b4513] bg-[#f5c518] px-2 py-1 rounded">
                                            Turns {{ (int) abs(now()->diffInYears($card->birthday)) + (now()->format('m-d') < $card->birthday->format('m-d') ? 0 : 1) }}
                                        </span>
                                    </div>
                                    <p class="text-sm text-[#8b4513]">{{ $card->birthday->format('F d') }}</p>
                                    @if ($card->phone)
                                        <p class="text-xs text-[#d7263d] mt-1">📱 {{ $card->phone }}</p>
                                    @elseif ($card->email_personal)
                                        <p class="text-xs text-[#d7263d] mt-1">✉️ {{ $card->email_personal }}</p>
                                    @endif
                                </div>
                            @endforeach
                        </div>
                    @else
                        <p class="text-[#d7263d] text-center py-4 text-sm">No birthdays</p>
                    @endif
                </div>
            </div>
        @endforeach
    </div>

    <!-- Summary Section -->
    <div class="mt-8 bg-[#fef3c7] rounded-lg shadow p-6 border border-[#ff6b35]">
        <h2 class="text-xl font-semibold text-[#8b4513] mb-4">Summary</h2>
        <div class="grid grid-cols-1 md:grid-cols-3 gap-4">
            <div class="bg-[#fef3c7] p-4 rounded-lg border border-[#ff6b35]">
                <p class="text-[#d7263d] text-sm">Total Cards with Birthdays</p>
                <p class="text-3xl font-bold text-[#ff6b35]">{{ $cards->count() }}</p>
            </div>
            
            @php
                $upcomingBirthdays = $cards->filter(function ($card) {
                    $nextBirthday = $card->birthday->copy()->year(now()->year);
                    if ($nextBirthday < now()) {
                        $nextBirthday->addYear();
                    }
                    return $nextBirthday->diffInDays(now()) <= 30;
                });
            @endphp
            
            <div class="bg-[#fef3c7] p-4 rounded-lg border border-[#ff6b35]">
                <p class="text-[#d7263d] text-sm">Upcoming This Month</p>
                <p class="text-3xl font-bold text-[#8b4513]">{{ $upcomingBirthdays->count() }}</p>
            </div>

            <div class="bg-[#fef3c7] p-4 rounded-lg border border-[#ff6b35]">
                <p class="text-[#d7263d] text-sm">Average Age</p>
                @php
                    $avgAge = $cards->avg(function ($card) {
                        return (int) abs(now()->diffInYears($card->birthday));
                    });
                @endphp
                <p class="text-3xl font-bold text-[#d7263d]">{{ round($avgAge) }} years</p>
            </div>
        </div>
    </div>
@endif
@endsection
