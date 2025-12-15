@extends('layout')

@section('title', 'Friendship Cards')

@section('content')
<div class="mb-6">
    <h1 class="text-3xl font-bold text-[#8b4513]">Friendship Cards</h1>
</div>

@if ($cards->isEmpty())
    <div class="bg-[#fef3c7] p-8 rounded-lg shadow text-center border border-[#ff6b35]">
        <p class="text-[#8b4513] mb-4">No cards yet. Create your first friendship card!</p>
        <a href="{{ route('cards.create') }}" class="bg-[#ff6b35] text-white px-6 py-2 rounded hover:bg-[#d7263d]">Create Card</a>
    </div>
@else
    <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-3 gap-6">
        @foreach ($cards as $card)
            <div class="bg-[#fef3c7] rounded-lg shadow hover:shadow-lg transition-shadow border border-[#ff6b35]">
                <div class="p-6">
                    <h2 class="text-xl font-semibold text-[#8b4513] mb-2">{{ $card->full_name }}</h2>
                    <p class="text-[#d7263d] text-sm mb-4">{{ $card->unique_name }}</p>
                    
                    @if ($card->phone)
                        <p class="text-[#8b4513] text-sm mb-2"><strong>Phone:</strong> {{ $card->phone }}</p>
                    @endif
                    
                    @if ($card->email_personal)
                        <p class="text-[#8b4513] text-sm mb-2"><strong>Email:</strong> {{ $card->email_personal }}</p>
                    @endif
                    
                    @if ($card->birthday)
                        <p class="text-[#8b4513] mb-4"><strong>Birthday:</strong> {{ $card->birthday->format('M d, Y') }}</p>
                    @endif
                    
                    @if ($card->notes)
                        <p class="text-[#8b4513] mb-4 text-sm">{{ Str::limit($card->notes, 100) }}</p>
                    @endif

                    <div class="flex gap-2">
                        <a href="{{ route('cards.show', $card) }}" class="flex-1 bg-[#ff6b35] text-white text-center px-4 py-2 rounded hover:bg-[#d7263d] text-sm">View</a>
                        <a href="{{ route('cards.edit', $card) }}" class="flex-1 bg-[#f5c518] text-[#8b4513] text-center px-4 py-2 rounded hover:bg-[#ff6b35] text-sm">Edit</a>
                        <form action="{{ route('cards.destroy', $card) }}" method="POST" class="flex-1" onsubmit="return confirm('Are you sure?');">
                            @csrf
                            @method('DELETE')
                            <button type="submit" class="w-full bg-[#d7263d] text-white px-4 py-2 rounded hover:bg-[#8b4513] text-sm">Delete</button>
                        </form>
                    </div>
                </div>
            </div>
        @endforeach
    </div>
@endif
@endsection
