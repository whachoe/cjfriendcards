@extends('layout')

@section('title', 'Friendship Cards')

@section('content')
<div class="mb-6 flex justify-between items-center">
    <h1 class="text-3xl font-bold text-primary-dark">Cardbox</h1>
    <a href="{{ route('cards.index', ['sort_order' => $nextSortOrder]) }}" class="bg-primary-accent text-white px-4 py-2 rounded hover:bg-primary-danger flex items-center gap-2">
        <span>Sort by Last Name</span>
        <span class="text-sm">{{ $sortOrder === 'asc' ? '↑' : '↓' }}</span>
    </a>
</div>

@if ($cards->isEmpty())
    <div class="bg-primary-light p-8 rounded-lg shadow text-center border border-primary-accent">
        <p class="text-primary-dark mb-4">No cards yet. Create your first friendship card!</p>
        <a href="{{ route('cards.create') }}" class="bg-primary-accent text-white px-6 py-2 rounded hover:bg-primary-danger">Create Card</a>
    </div>
@else
    <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-3 gap-6">
        @foreach ($cards as $card)
            <div class="bg-primary-light rounded-lg shadow hover:shadow-lg transition-shadow border border-primary-accent">
                <div class="p-6">
                    <h2 class="text-xl font-semibold text-primary-dark mb-2">{{ $card->full_name }}</h2>
                    <p class="text-primary-danger text-sm mb-4">{{ $card->unique_name }}</p>
                    
                    @if ($card->phone)
                        <p class="text-primary-dark text-sm mb-2"><strong>Phone:</strong> {{ $card->phone }}</p>
                    @endif
                    
                    @if ($card->email_personal)
                        <p class="text-primary-dark text-sm mb-2"><strong>Email:</strong> {{ $card->email_personal }}</p>
                    @endif
                    
                    @if ($card->birthday)
                        <p class="text-primary-dark mb-4"><strong>Birthday:</strong> {{ $card->birthday->format('M d, Y') }}</p>
                    @endif
                    
                    @if ($card->notes)
                        <p class="text-primary-dark mb-4 text-sm">{{ Str::limit($card->notes, 100) }}</p>
                    @endif

                    <div class="flex gap-2">
                        <a href="{{ route('cards.show', $card) }}" class="flex-1 bg-primary-accent text-white text-center px-4 py-2 rounded hover:bg-primary-danger text-sm">View</a>
                        <a href="{{ route('cards.edit', $card) }}" class="flex-1 bg-primary-secondary text-primary-dark text-center px-4 py-2 rounded hover:bg-primary-accent text-sm">Edit</a>
                        <form action="{{ route('cards.destroy', $card) }}" method="POST" class="flex-1" onsubmit="return confirm('Are you sure?');">
                            @csrf
                            @method('DELETE')
                            <button type="submit" class="w-full bg-primary-danger text-white px-4 py-2 rounded hover:bg-primary-dark text-sm">Delete</button>
                        </form>
                    </div>
                </div>
            </div>
        @endforeach
    </div>
@endif
@endsection
