@extends('layout')

@section('title', 'cjFriendCards')

@section('content')
<div class="mb-6 flex justify-between items-center">
    <h1 class="text-3xl font-bold text-primary-dark">Cardbox</h1>
    <div class="flex items-center gap-3">
        <span>Sort by Last Name</span>
        <a href="{{ route('cards.index', ['sort_order' => $nextSortOrder]) }}" class="text-primary-accent px-4 py-2 rounded hover:text-primary-danger flex items-center gap-2">            
            <span class="text-sm">{{ $sortOrder === 'asc' ? '↑' : '↓' }}</span>
        </a>
        <div class="flex gap-2">
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
        <a href="{{ route('cards.create') }}" class="bg-primary-accent text-white px-6 py-2 rounded hover:brightness-110 transition">Create Card</a>
    </div>
@else
    <!-- Grid View -->
    <div id="grid-view" class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-3 gap-6">
        @foreach ($cards as $card)
            <div class="rounded-lg border border-primary-accent card-item flex flex-col h-full" style="background-color: var(--color-light); background-image: url('{{ asset('images/card-bg.jpg') }}'); background-size: cover; background-attachment: fixed;">
                <div class="p-6 flex-grow">
                    <h2 class="text-xl font-semibold text-primary-dark mb-2">{{ $card->full_name }}</h2>
                    <p class="text-primary-danger text-sm mb-4">{{ $card->unique_name }}</p>
                    
                    @if ($card->phone)
                        <p class="text-primary-dark text-sm mb-2"><strong>Phone:</strong> {{ $card->phone }}</p>
                    @endif
                    
                    @if ($card->email_personal)
                        <p class="text-primary-dark text-sm mb-2"><strong>Email:</strong> {{ $card->email_personal }}</p>
                    @endif
                    
                    @if ($card->birthday)
                        <p class="text-primary-dark mb-4"><strong>Birthday:</strong> {{ $card->birthday->format('M d, Y') }} ({{ $card->getAge() }})</p>
                    @endif
                    
                    <!-- @if ($card->notes)
                        <p class="text-primary-dark mb-4 text-sm">{{ Str::limit($card->notes, 100) }}</p>
                    @endif -->
                </div>

                <div class="px-6 pb-6">
                    <div class="flex gap-2">
                        <a href="{{ route('cards.show', $card) }}" class="flex-1 bg-primary-accent text-white text-center px-4 py-2 rounded hover:brightness-110 transition text-sm flex justify-center items-center" title="View">
                            <svg xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke-width="1.5" stroke="currentColor" class="w-3 h-3">
                                <path stroke-linecap="round" stroke-linejoin="round" d="M2.036 12.322a1.012 1.012 0 010-.639C3.423 7.51 7.36 4.5 12 4.5c4.638 0 8.573 3.007 9.963 7.178.07.207.07.431 0 .639C20.577 16.49 16.64 19.5 12 19.5c-4.638 0-8.573-3.007-9.963-7.178z" />
                                <path stroke-linecap="round" stroke-linejoin="round" d="M15 12a3 3 0 11-6 0 3 3 0 016 0z" />
                            </svg>
                        </a>
                        <a href="{{ route('cards.edit', $card) }}" class="flex-1 bg-primary-secondary text-primary-dark text-center px-4 py-2 rounded hover:brightness-110 transition text-sm flex justify-center items-center" title="Edit">
                            <svg xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke-width="1.5" stroke="currentColor" class="w-3 h-3">
                                <path stroke-linecap="round" stroke-linejoin="round" d="M16.862 4.487l1.687-1.688a1.875 1.875 0 112.652 2.652L10.582 16.07a4.5 4.5 0 01-1.897 1.13L6 18l.8-2.685a4.5 4.5 0 011.13-1.897l8.932-8.931zm0 0L19.5 7.125M18 14v4.75A2.25 2.25 0 0115.75 21H5.25A2.25 2.25 0 013 18.75V8.25A2.25 2.25 0 015.25 6H10" />
                            </svg>
                        </a>
                        <form action="{{ route('cards.destroy', $card) }}" method="POST" class="flex-1" onsubmit="return confirm('Are you sure?');">
                            @csrf
                            @method('DELETE')
                            <button type="submit" class="w-full bg-primary-danger text-white px-4 py-2 rounded hover:brightness-110 transition text-sm flex justify-center items-center" title="Delete">
                                <svg xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke-width="1.5" stroke="currentColor" class="w-3 h-3">
                                    <path stroke-linecap="round" stroke-linejoin="round" d="M14.74 9l-.346 9m-4.788 0L9.26 9m9.968-3.21c.342.052.682.107 1.022.166m-1.022-.165L18.16 19.673a2.25 2.25 0 01-2.244 2.077H8.084a2.25 2.25 0 01-2.244-2.077L4.772 5.79m14.456 0a48.108 48.108 0 00-3.478-.397m-12 .562c.34-.059.68-.114 1.022-.165m0 0a48.11 48.11 0 013.478-.397m7.5 0v-.916c0-1.18-.91-2.164-2.09-2.201a51.964 51.964 0 00-3.32 0c-1.18.037-2.09 1.022-2.09 2.201v.916m7.5 0a48.667 48.667 0 00-7.5 0" />
                                </svg>
                            </button>
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
                                <a href="{{ route('cards.show', $card) }}" class="bg-primary-accent text-white px-3 py-1 rounded text-sm hover:brightness-110 transition flex items-center" title="View">
                                    <svg xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke-width="1.5" stroke="currentColor" class="w-3 h-3">
                                        <path stroke-linecap="round" stroke-linejoin="round" d="M2.036 12.322a1.012 1.012 0 010-.639C3.423 7.51 7.36 4.5 12 4.5c4.638 0 8.573 3.007 9.963 7.178.07.207.07.431 0 .639C20.577 16.49 16.64 19.5 12 19.5c-4.638 0-8.573-3.007-9.963-7.178z" />
                                        <path stroke-linecap="round" stroke-linejoin="round" d="M15 12a3 3 0 11-6 0 3 3 0 016 0z" />
                                    </svg>
                                </a>
                                <a href="{{ route('cards.edit', $card) }}" class="bg-primary-secondary text-primary-dark px-3 py-1 rounded text-sm hover:brightness-110 transition flex items-center" title="Edit">
                                    <svg xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke-width="1.5" stroke="currentColor" class="w-3 h-3">
                                        <path stroke-linecap="round" stroke-linejoin="round" d="M16.862 4.487l1.687-1.688a1.875 1.875 0 112.652 2.652L10.582 16.07a4.5 4.5 0 01-1.897 1.13L6 18l.8-2.685a4.5 4.5 0 011.13-1.897l8.932-8.931zm0 0L19.5 7.125M18 14v4.75A2.25 2.25 0 0115.75 21H5.25A2.25 2.25 0 013 18.75V8.25A2.25 2.25 0 015.25 6H10" />
                                    </svg>
                                </a>
                                <form action="{{ route('cards.destroy', $card) }}" method="POST" class="inline" onsubmit="return confirm('Are you sure?');">
                                    @csrf
                                    @method('DELETE')
                                    <button type="submit" class="bg-primary-danger text-white px-3 py-1 rounded text-sm hover:brightness-110 transition flex items-center" title="Delete">
                                        <svg xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke-width="1.5" stroke="currentColor" class="w-3 h-3">
                                            <path stroke-linecap="round" stroke-linejoin="round" d="M14.74 9l-.346 9m-4.788 0L9.26 9m9.968-3.21c.342.052.682.107 1.022.166m-1.022-.165L18.16 19.673a2.25 2.25 0 01-2.244 2.077H8.084a2.25 2.25 0 01-2.244-2.077L4.772 5.79m14.456 0a48.108 48.108 0 00-3.478-.397m-12 .562c.34-.059.68-.114 1.022-.165m0 0a48.11 48.11 0 013.478-.397m7.5 0v-.916c0-1.18-.91-2.164-2.09-2.201a51.964 51.964 0 00-3.32 0c-1.18.037-2.09 1.022-2.09 2.201v.916m7.5 0a48.667 48.667 0 00-7.5 0" />
                                        </svg>
                                    </button>
                                </form>
                            </div>
                        </td>
                    </tr>
                @endforeach
            </tbody>
        </table>
    </div>

    <div class="mt-8 flex justify-center">
        <a href="{{ route('cards.export-csv') }}" class="bg-primary-accent text-white px-6 py-2 rounded hover:brightness-110 transition">Export All to CSV</a>
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
