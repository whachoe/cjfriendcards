@extends('layout')

@section('title', $card->name . ' - Card Details')

@section('content')
<div class="mb-6 flex justify-between items-center">
    <h1 class="text-3xl font-bold text-gray-900">{{ $card->full_name }}</h1>
    <div class="flex gap-2">
        <a href="{{ route('cards.edit', $card) }}" class="bg-gray-600 text-white px-4 py-2 rounded hover:bg-gray-700">Edit</a>
        <form action="{{ route('cards.destroy', $card) }}" method="POST" class="inline" onsubmit="return confirm('Are you sure?');">
            @csrf
            @method('DELETE')
            <button type="submit" class="bg-red-600 text-white px-4 py-2 rounded hover:bg-red-700">Delete</button>
        </form>
        <a href="{{ route('cards.index') }}" class="bg-blue-600 text-white px-4 py-2 rounded hover:bg-blue-700">Back</a>
    </div>
</div>

<div class="grid grid-cols-1 lg:grid-cols-3 gap-6">
    <!-- Card Information -->
    <div class="lg:col-span-2">
        <div class="bg-white rounded-lg shadow p-6 mb-6">
            <h2 class="text-xl font-semibold text-gray-900 mb-4">Card Information</h2>
            
            <div class="mb-4">
                <p class="text-gray-500 text-sm">Unique Name</p>
                <p class="text-gray-900 font-medium">{{ $card->unique_name }}</p>
            </div>

            <div class="mb-4">
                <p class="text-gray-500 text-sm">First Name</p>
                <p class="text-gray-900 font-medium">{{ $card->first_name }}</p>
            </div>

            <div class="mb-4">
                <p class="text-gray-500 text-sm">Last Name</p>
                <p class="text-gray-900 font-medium">{{ $card->last_name }}</p>
            </div>

            @if ($card->address || $card->phone || $card->email_work || $card->email_personal || $card->email_extra1 || $card->email_extra2 || $card->email_extra3)
                <div class="mb-4 pb-4 border-b">
                    <p class="text-gray-500 text-sm font-semibold mb-2">Contact Information</p>
                    @if ($card->address)
                        <p class="text-gray-900 text-sm mb-1"><strong>Address:</strong> {{ $card->address }}</p>
                    @endif
                    @if ($card->phone)
                        <p class="text-gray-900 text-sm mb-1"><strong>Phone:</strong> {{ $card->phone }}</p>
                    @endif
                    @if ($card->email_work)
                        <p class="text-gray-900 text-sm mb-1"><strong>Email (Work):</strong> <a href="mailto:{{ $card->email_work }}" class="text-blue-600 hover:underline">{{ $card->email_work }}</a></p>
                    @endif
                    @if ($card->email_personal)
                        <p class="text-gray-900 text-sm mb-1"><strong>Email (Personal):</strong> <a href="mailto:{{ $card->email_personal }}" class="text-blue-600 hover:underline">{{ $card->email_personal }}</a></p>
                    @endif
                    @if ($card->email_extra1)
                        <p class="text-gray-900 text-sm mb-1"><strong>Email (Extra 1):</strong> <a href="mailto:{{ $card->email_extra1 }}" class="text-blue-600 hover:underline">{{ $card->email_extra1 }}</a></p>
                    @endif
                    @if ($card->email_extra2)
                        <p class="text-gray-900 text-sm mb-1"><strong>Email (Extra 2):</strong> <a href="mailto:{{ $card->email_extra2 }}" class="text-blue-600 hover:underline">{{ $card->email_extra2 }}</a></p>
                    @endif
                    @if ($card->email_extra3)
                        <p class="text-gray-900 text-sm mb-1"><strong>Email (Extra 3):</strong> <a href="mailto:{{ $card->email_extra3 }}" class="text-blue-600 hover:underline">{{ $card->email_extra3 }}</a></p>
                    @endif
                </div>
            @endif

            @if ($card->birthday)
                <div class="mb-4">
                    <p class="text-gray-500 text-sm">Birthday</p>
                    <p class="text-gray-900">{{ $card->birthday->format('F d, Y') }} (Age: {{ (int) abs(now()->diffInYears($card->birthday)) }} years)</p>
                </div>
            @endif

            @if ($card->notes)
                <div class="mb-4">
                    <p class="text-gray-500 text-sm">Notes</p>
                    <p class="text-gray-900 whitespace-pre-line">{{ $card->notes }}</p>
                </div>
            @endif

            <div class="text-gray-500 text-sm mt-6 pt-4 border-t">
                <p>Created: {{ $card->created_at->format('M d, Y H:i') }}</p>
                <p>Last Updated: {{ $card->updated_at->format('M d, Y H:i') }}</p>
            </div>
        </div>
    </div>

    <!-- Sidebar -->
    <div>
        <div class="bg-white rounded-lg shadow p-6">
            <h3 class="text-lg font-semibold text-gray-900 mb-4">Quick Info</h3>
            <div class="space-y-3">
                <div class="bg-blue-50 p-3 rounded">
                    <p class="text-sm text-gray-500">Total Relationships</p>
                    <p class="text-2xl font-bold text-blue-600">{{ $relationships->count() + $relatedRelationships->count() }}</p>
                </div>
            </div>
        </div>
    </div>
</div>

<!-- Relationships Section -->
<div class="mt-8 space-y-6">
    <h2 class="text-2xl font-bold text-gray-900">Manage Relationships</h2>

    @include('cards.partials.relationships-list', ['relationships' => $relationships, 'relatedRelationships' => $relatedRelationships])

    @include('cards.partials.add-relationship', ['card' => $card])
</div>
@endsection
