@extends('layout')

@section('title', $card->name . ' - Card Details')

@section('content')
<div class="mb-6 flex items-center">
    <a href="{{ route('cards.index') }}" class="text-primary-accent hover:text-primary-danger transition text-2xl">←</a>
    <h1 class="text-3xl font-bold text-primary-dark ml-4">{{ $card->full_name }}</h1>
</div>

<div class="grid grid-cols-1 lg:grid-cols-3 gap-6">
    <!-- Card Information -->
    <div class="lg:col-span-2">
        <div class="bg-primary-light rounded-lg shadow p-6 mb-6 border border-primary-accent">
            <h2 class="text-xl font-semibold text-primary-dark mb-4">Card Information</h2>
            
            <div class="mb-4">
                <p class="text-primary-danger text-sm">Unique Name</p>
                <p class="text-primary-dark font-medium">{{ $card->unique_name }}</p>
            </div>

            <div class="mb-4">
                <p class="text-primary-danger text-sm">First Name</p>
                <p class="text-primary-dark font-medium">{{ $card->first_name }}</p>
            </div>

            <div class="mb-4">
                <p class="text-primary-danger text-sm">Last Name</p>
                <p class="text-primary-dark font-medium">{{ $card->last_name }}</p>
            </div>

            @if ($card->address || $card->phone || $card->email_work || $card->email_personal || $card->email_extra1 || $card->email_extra2 || $card->email_extra3)
                <div class="mb-4 pb-4 border-b border-primary-accent">
                    <p class="text-primary-danger text-sm font-semibold mb-2">Contact Information</p>
                    @if ($card->address)
                        <p class="text-primary-dark text-sm mb-1"><strong>Address:</strong> {{ $card->address }}</p>
                    @endif
                    @if ($card->phone)
                        <p class="text-primary-dark text-sm mb-1"><strong>Phone:</strong> {{ $card->phone }}</p>
                    @endif
                    @if ($card->email_work)
                        <p class="text-primary-dark text-sm mb-1"><strong>Email (Work):</strong> <a href="mailto:{{ $card->email_work }}" class="text-primary-accent hover:underline">{{ $card->email_work }}</a></p>
                    @endif
                    @if ($card->email_personal)
                        <p class="text-primary-dark text-sm mb-1"><strong>Email (Personal):</strong> <a href="mailto:{{ $card->email_personal }}" class="text-primary-accent hover:underline">{{ $card->email_personal }}</a></p>
                    @endif
                    @if ($card->email_extra1)
                        <p class="text-primary-dark text-sm mb-1"><strong>Email (Extra 1):</strong> <a href="mailto:{{ $card->email_extra1 }}" class="text-primary-accent hover:underline">{{ $card->email_extra1 }}</a></p>
                    @endif
                    @if ($card->email_extra2)
                        <p class="text-primary-dark text-sm mb-1"><strong>Email (Extra 2):</strong> <a href="mailto:{{ $card->email_extra2 }}" class="text-primary-accent hover:underline">{{ $card->email_extra2 }}</a></p>
                    @endif
                    @if ($card->email_extra3)
                        <p class="text-primary-dark text-sm mb-1"><strong>Email (Extra 3):</strong> <a href="mailto:{{ $card->email_extra3 }}" class="text-primary-accent hover:underline">{{ $card->email_extra3 }}</a></p>
                    @endif
                </div>
            @endif

            @if ($card->birthday)
                <div class="mb-4">
                    <p class="text-primary-danger text-sm">Birthday</p>
                    <p class="text-primary-dark">{{ $card->birthday->format('F d, Y') }} (Age: {{ (int) abs(now()->diffInYears($card->birthday)) }} years)</p>
                </div>
            @endif

            @if ($card->notes)
                <div class="mb-4">
                    <p class="text-primary-danger text-sm">Notes</p>
                    <p class="text-primary-dark whitespace-pre-line">{{ $card->notes }}</p>
                </div>
            @endif

            <div class="text-primary-danger text-sm mt-6 pt-4 border-t border-primary-accent">
                <p>Created: {{ $card->created_at->format('M d, Y H:i') }}</p>
                <p>Last Updated: {{ $card->updated_at->format('M d, Y H:i') }}</p>
            </div>
        </div>
    </div>

    <!-- Sidebar -->
    <div>
        <div class="bg-primary-light rounded-lg shadow p-6 border border-primary-accent">
            <h3 class="text-lg font-semibold text-primary-dark mb-4">Quick Info</h3>
            <div class="space-y-3">
                <div class="bg-primary-light p-3 rounded border border-primary-accent">
                    <p class="text-sm text-primary-danger">Total Relationships</p>
                    <p class="text-2xl font-bold text-primary-accent">{{ $relationships->count() + $relatedRelationships->count() }}</p>
                </div>
            </div>
        </div>

        <div class="flex gap-2 mt-6 flex-col">
            <a href="{{ route('cards.edit', $card) }}" class="bg-primary-secondary text-primary-dark px-4 py-2 rounded hover:bg-primary-accent text-center">Edit</a>
            <a href="{{ route('cards.export-vcard', $card) }}" class="bg-primary-accent text-white px-4 py-2 rounded hover:bg-primary-secondary text-center">Export vCard</a>
            <form action="{{ route('cards.destroy', $card) }}" method="POST" onsubmit="return confirm('Are you sure?');">
                @csrf
                @method('DELETE')
                <button type="submit" class="w-full bg-primary-danger text-white px-4 py-2 rounded hover:bg-primary-dark">Delete</button>
            </form>
        </div>
    </div>
</div>

<!-- Relationships Section -->
<div class="mt-8 space-y-6">
    <h2 class="text-2xl font-bold text-primary-dark">Manage Relationships</h2>

    @include('cards.partials.relationships-list', ['relationships' => $relationships, 'relatedRelationships' => $relatedRelationships])

    @include('cards.partials.add-relationship', ['card' => $card])
</div>
@endsection
