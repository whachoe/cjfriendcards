@extends('layout')

@section('title', $card->name . ' - Card Details')

@section('content')
<div class="mb-6 flex justify-between items-center">
    <h1 class="text-3xl font-bold text-[#8b4513]">{{ $card->full_name }}</h1>
    <div class="flex gap-2">
        <a href="{{ route('cards.edit', $card) }}" class="bg-[#f5c518] text-[#8b4513] px-4 py-2 rounded hover:bg-[#ff6b35]">Edit</a>
        <form action="{{ route('cards.destroy', $card) }}" method="POST" class="inline" onsubmit="return confirm('Are you sure?');">
            @csrf
            @method('DELETE')
            <button type="submit" class="bg-[#d7263d] text-white px-4 py-2 rounded hover:bg-[#8b4513]">Delete</button>
        </form>
        <a href="{{ route('cards.index') }}" class="bg-[#ff6b35] text-white px-4 py-2 rounded hover:bg-[#d7263d]">Back</a>
    </div>
</div>

<div class="grid grid-cols-1 lg:grid-cols-3 gap-6">
    <!-- Card Information -->
    <div class="lg:col-span-2">
        <div class="bg-[#fef3c7] rounded-lg shadow p-6 mb-6 border border-[#ff6b35]">
            <h2 class="text-xl font-semibold text-[#8b4513] mb-4">Card Information</h2>
            
            <div class="mb-4">
                <p class="text-[#d7263d] text-sm">Unique Name</p>
                <p class="text-[#8b4513] font-medium">{{ $card->unique_name }}</p>
            </div>

            <div class="mb-4">
                <p class="text-[#d7263d] text-sm">First Name</p>
                <p class="text-[#8b4513] font-medium">{{ $card->first_name }}</p>
            </div>

            <div class="mb-4">
                <p class="text-[#d7263d] text-sm">Last Name</p>
                <p class="text-[#8b4513] font-medium">{{ $card->last_name }}</p>
            </div>

            @if ($card->address || $card->phone || $card->email_work || $card->email_personal || $card->email_extra1 || $card->email_extra2 || $card->email_extra3)
                <div class="mb-4 pb-4 border-b border-[#ff6b35]">
                    <p class="text-[#d7263d] text-sm font-semibold mb-2">Contact Information</p>
                    @if ($card->address)
                        <p class="text-[#8b4513] text-sm mb-1"><strong>Address:</strong> {{ $card->address }}</p>
                    @endif
                    @if ($card->phone)
                        <p class="text-[#8b4513] text-sm mb-1"><strong>Phone:</strong> {{ $card->phone }}</p>
                    @endif
                    @if ($card->email_work)
                        <p class="text-[#8b4513] text-sm mb-1"><strong>Email (Work):</strong> <a href="mailto:{{ $card->email_work }}" class="text-[#ff6b35] hover:underline">{{ $card->email_work }}</a></p>
                    @endif
                    @if ($card->email_personal)
                        <p class="text-[#8b4513] text-sm mb-1"><strong>Email (Personal):</strong> <a href="mailto:{{ $card->email_personal }}" class="text-[#ff6b35] hover:underline">{{ $card->email_personal }}</a></p>
                    @endif
                    @if ($card->email_extra1)
                        <p class="text-[#8b4513] text-sm mb-1"><strong>Email (Extra 1):</strong> <a href="mailto:{{ $card->email_extra1 }}" class="text-[#ff6b35] hover:underline">{{ $card->email_extra1 }}</a></p>
                    @endif
                    @if ($card->email_extra2)
                        <p class="text-[#8b4513] text-sm mb-1"><strong>Email (Extra 2):</strong> <a href="mailto:{{ $card->email_extra2 }}" class="text-[#ff6b35] hover:underline">{{ $card->email_extra2 }}</a></p>
                    @endif
                    @if ($card->email_extra3)
                        <p class="text-[#8b4513] text-sm mb-1"><strong>Email (Extra 3):</strong> <a href="mailto:{{ $card->email_extra3 }}" class="text-[#ff6b35] hover:underline">{{ $card->email_extra3 }}</a></p>
                    @endif
                </div>
            @endif

            @if ($card->birthday)
                <div class="mb-4">
                    <p class="text-[#d7263d] text-sm">Birthday</p>
                    <p class="text-[#8b4513]">{{ $card->birthday->format('F d, Y') }} (Age: {{ (int) abs(now()->diffInYears($card->birthday)) }} years)</p>
                </div>
            @endif

            @if ($card->notes)
                <div class="mb-4">
                    <p class="text-[#d7263d] text-sm">Notes</p>
                    <p class="text-[#8b4513] whitespace-pre-line">{{ $card->notes }}</p>
                </div>
            @endif

            <div class="text-[#d7263d] text-sm mt-6 pt-4 border-t border-[#ff6b35]">
                <p>Created: {{ $card->created_at->format('M d, Y H:i') }}</p>
                <p>Last Updated: {{ $card->updated_at->format('M d, Y H:i') }}</p>
            </div>
        </div>
    </div>

    <!-- Sidebar -->
    <div>
        <div class="bg-[#fef3c7] rounded-lg shadow p-6 border border-[#ff6b35]">
            <h3 class="text-lg font-semibold text-[#8b4513] mb-4">Quick Info</h3>
            <div class="space-y-3">
                <div class="bg-[#fef3c7] p-3 rounded border border-[#ff6b35]">
                    <p class="text-sm text-[#d7263d]">Total Relationships</p>
                    <p class="text-2xl font-bold text-[#ff6b35]">{{ $relationships->count() + $relatedRelationships->count() }}</p>
                </div>
            </div>
        </div>
    </div>
</div>

<!-- Relationships Section -->
<div class="mt-8 space-y-6">
    <h2 class="text-2xl font-bold text-[#8b4513]">Manage Relationships</h2>

    @include('cards.partials.relationships-list', ['relationships' => $relationships, 'relatedRelationships' => $relatedRelationships])

    @include('cards.partials.add-relationship', ['card' => $card])
</div>
@endsection
