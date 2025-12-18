@extends('layout')

@section('title', 'Friendship Cards')

@section('content')
<div class="mb-6 flex justify-between items-center">
    <h1 class="text-3xl font-bold text-primary-dark">Cardbox</h1>
    <div class="flex items-center gap-3">
        <span>Sort by Last Name</span>
        <a href="{{ route('cards.index', ['sort_order' => $nextSortOrder]) }}" class="text-primary-accent px-4 py-2 rounded hover:text-primary-danger flex items-center gap-2">            
            <span class="text-sm">{{ $sortOrder === 'asc' ? '↑' : '↓' }}</span>
        </a>
        <div class="flex gap-2">
            <span class="text-sm">View:</span>
            <button id="view-grid-btn" class="view-toggle active w-10 h-10 flex items-center justify-center transition" title="Grid view">
                <span class="text-xl">⊞</span>
            </button>
            <button id="view-list-btn" class="view-toggle w-10 h-10 flex items-center justify-center transition" title="List view">
                <span class="text-xl">≡</span>
            </button>
        </div>
    </div>
</div>

@if ($cards->isEmpty())
    <div class="bg-primary-light p-8 rounded-lg shadow text-center border border-primary-accent">
        <p class="text-primary-dark mb-4">No cards yet. Create your first friendship card!</p>
        <a href="{{ route('cards.create') }}" class="bg-primary-accent text-white px-6 py-2 rounded hover:bg-primary-danger">Create Card</a>
    </div>
@else
    <!-- Grid View -->
    <div id="grid-view" class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-3 gap-6">
        @foreach ($cards as $card)
            <div class="bg-primary-light rounded-lg border border-primary-accent card-item">
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

    <!-- List View -->
    <div id="list-view" class="border hidden bg-primary-light border-primary-accent rounded-lg overflow-hidden">
        <table class="w-full">
            <tbody>
                @foreach ($cards as $card)
                    <tr class="hover:bg-primary-secondary transition">
                        <td class="">
                            <a href="{{ route('cards.show', $card) }}" class="text-primary-accent hover:text-primary-danger font-medium">{{ $card->first_name }} {{ $card->last_name }}</a>
                        </td>
                        <td class="text-right">
                            <div class="flex gap-2 justify-end">
                                <a href="{{ route('cards.show', $card) }}" class="bg-primary-accent text-white px-4 py-1 rounded text-sm hover:bg-primary-danger">View</a>
                                <a href="{{ route('cards.edit', $card) }}" class="bg-primary-secondary text-primary-dark px-4 py-1 rounded text-sm hover:bg-primary-accent">Edit</a>
                                <form action="{{ route('cards.destroy', $card) }}" method="POST" class="inline" onsubmit="return confirm('Are you sure?');">
                                    @csrf
                                    @method('DELETE')
                                    <button type="submit" class="bg-primary-danger text-white px-4 py-1 rounded text-sm hover:bg-primary-dark">Delete</button>
                                </form>
                            </div>
                        </td>
                    </tr>
                @endforeach
            </tbody>
        </table>
    </div>

    <div class="mt-8 flex justify-center">
        <a href="{{ route('cards.export-csv') }}" class="bg-primary-accent text-white px-6 py-2 rounded hover:bg-primary-danger">Export All to CSV</a>
    </div>

    <script>
        document.getElementById('view-grid-btn').addEventListener('click', function() {
            document.getElementById('grid-view').classList.remove('hidden');
            document.getElementById('list-view').classList.add('hidden');
            document.getElementById('view-grid-btn').classList.add('active');
            document.getElementById('view-list-btn').classList.remove('active');
            localStorage.setItem('cardViewMode', 'grid');
        });

        document.getElementById('view-list-btn').addEventListener('click', function() {
            document.getElementById('grid-view').classList.add('hidden');
            document.getElementById('list-view').classList.remove('hidden');
            document.getElementById('view-grid-btn').classList.remove('active');
            document.getElementById('view-list-btn').classList.add('active');
            localStorage.setItem('cardViewMode', 'list');
        });

        // Load saved view preference
        const savedViewMode = localStorage.getItem('cardViewMode') || 'grid';
        if (savedViewMode === 'list') {
            document.getElementById('view-list-btn').click();
        }
    </script>
@endif
@endsection
