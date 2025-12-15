@extends('layout')

@section('title', 'Edit Friendship Card')

@section('content')
<div class="mb-6">
    <h1 class="text-3xl font-bold text-[#8b4513]">Edit Friendship Card</h1>
</div>

<div class="grid grid-cols-1 lg:grid-cols-3 gap-6">
    <!-- Edit Form -->
    <div class="lg:col-span-2">
        <div class="bg-[#fef3c7] rounded-lg shadow p-6 border border-[#ff6b35]">
            <form action="{{ route('cards.update', $card) }}" method="POST">
                @csrf
                @method('PATCH')

                <div class="grid grid-cols-2 gap-4 mb-6">
                    <div>
                        <label for="first_name" class="block text-sm font-medium text-[#8b4513] mb-2">First Name *</label>
                        <input type="text" name="first_name" id="first_name" value="{{ old('first_name', $card->first_name) }}" required 
                               class="w-full px-4 py-2 border border-[#ff6b35] rounded-lg focus:outline-none focus:border-[#d7263d]">
                        @error('first_name')
                            <p class="text-[#d7263d] text-sm mt-1">{{ $message }}</p>
                        @enderror
                    </div>
                    <div>
                        <label for="last_name" class="block text-sm font-medium text-[#8b4513] mb-2">Last Name *</label>
                        <input type="text" name="last_name" id="last_name" value="{{ old('last_name', $card->last_name) }}" required 
                               class="w-full px-4 py-2 border border-[#ff6b35] rounded-lg focus:outline-none focus:border-[#d7263d]">
                        @error('last_name')
                            <p class="text-[#d7263d] text-sm mt-1">{{ $message }}</p>
                        @enderror
                    </div>
                </div>

                <div class="mb-6">
                    <label for="unique_name" class="block text-sm font-medium text-[#8b4513] mb-2">Unique Name * <span class="text-[#d7263d] text-sm">(auto-generated)</span></label>
                    <input type="text" name="unique_name" id="unique_name" value="{{ old('unique_name', $card->unique_name) }}" required 
                           class="w-full px-4 py-2 border border-[#ff6b35] rounded-lg focus:outline-none focus:border-[#d7263d]">
                    @error('unique_name')
                        <p class="text-[#d7263d] text-sm mt-1">{{ $message }}</p>
                    @enderror
                </div>

                <div class="mb-6">
                    <label for="address" class="block text-sm font-medium text-[#8b4513] mb-2">Address</label>
                    <input type="text" name="address" id="address" value="{{ old('address', $card->address) }}" 
                           class="w-full px-4 py-2 border border-[#ff6b35] rounded-lg focus:outline-none focus:border-[#d7263d]">
                    @error('address')
                        <p class="text-red-600 text-sm mt-1">{{ $message }}</p>
                    @enderror
                </div>

                <div class="mb-6">
                    <label for="phone" class="block text-sm font-medium text-[#8b4513] mb-2">Phone</label>
                    <input type="text" name="phone" id="phone" value="{{ old('phone', $card->phone) }}" 
                           class="w-full px-4 py-2 border border-[#ff6b35] rounded-lg focus:outline-none focus:border-[#d7263d]">
                    @error('phone')
                        <p class="text-[#d7263d] text-sm mt-1">{{ $message }}</p>
                    @enderror
                </div>

                <div class="mb-6">
                    <label for="email_work" class="block text-sm font-medium text-[#8b4513] mb-2">Email (Work)</label>
                    <input type="email" name="email_work" id="email_work" value="{{ old('email_work', $card->email_work) }}" 
                           class="w-full px-4 py-2 border border-[#ff6b35] rounded-lg focus:outline-none focus:border-[#d7263d]">
                    @error('email_work')
                        <p class="text-[#d7263d] text-sm mt-1">{{ $message }}</p>
                    @enderror
                </div>

                <div class="mb-6">
                    <label for="email_personal" class="block text-sm font-medium text-[#8b4513] mb-2">Email (Personal)</label>
                    <input type="email" name="email_personal" id="email_personal" value="{{ old('email_personal', $card->email_personal) }}" 
                           class="w-full px-4 py-2 border border-[#ff6b35] rounded-lg focus:outline-none focus:border-[#d7263d]">
                    @error('email_personal')
                        <p class="text-[#d7263d] text-sm mt-1">{{ $message }}</p>
                    @enderror
                </div>

                <div class="mb-6">
                    <label for="email_extra1" class="block text-sm font-medium text-[#8b4513] mb-2">Email (Extra 1)</label>
                    <input type="email" name="email_extra1" id="email_extra1" value="{{ old('email_extra1', $card->email_extra1) }}" 
                           class="w-full px-4 py-2 border border-[#ff6b35] rounded-lg focus:outline-none focus:border-[#d7263d]">
                    @error('email_extra1')
                        <p class="text-[#d7263d] text-sm mt-1">{{ $message }}</p>
                    @enderror
                </div>

                <div class="mb-6">
                    <label for="email_extra2" class="block text-sm font-medium text-[#8b4513] mb-2">Email (Extra 2)</label>
                    <input type="email" name="email_extra2" id="email_extra2" value="{{ old('email_extra2', $card->email_extra2) }}" 
                           class="w-full px-4 py-2 border border-[#ff6b35] rounded-lg focus:outline-none focus:border-[#d7263d]">
                    @error('email_extra2')
                        <p class="text-[#d7263d] text-sm mt-1">{{ $message }}</p>
                    @enderror
                </div>

                <div class="mb-6">
                    <label for="email_extra3" class="block text-sm font-medium text-[#8b4513] mb-2">Email (Extra 3)</label>
                    <input type="email" name="email_extra3" id="email_extra3" value="{{ old('email_extra3', $card->email_extra3) }}" 
                           class="w-full px-4 py-2 border border-[#ff6b35] rounded-lg focus:outline-none focus:border-[#d7263d]">
                    @error('email_extra3')
                        <p class="text-[#d7263d] text-sm mt-1">{{ $message }}</p>
                    @enderror
                </div>

                <div class="mb-6">
                    <label for="birthday" class="block text-sm font-medium text-[#8b4513] mb-2">Birthday (dd-mm-yyyy)</label>
                    <input type="text" name="birthday_display" id="birthday" placeholder="dd-mm-yyyy" value="{{ old('birthday', $card->birthday?->format('d-m-Y')) }}" 
                           class="w-full px-4 py-2 border border-[#ff6b35] rounded-lg focus:outline-none focus:border-[#d7263d]"
                           pattern="\d{2}-\d{2}-\d{4}">
                    <input type="hidden" name="birthday" id="birthday_hidden" value="{{ old('birthday', $card->birthday?->format('Y-m-d')) }}">
                    <p class="text-[#d7263d] text-xs mt-1">Enter date as dd-mm-yyyy (e.g., 25-12-1990)</p>
                    @error('birthday')
                        <p class="text-[#d7263d] text-sm mt-1">{{ $message }}</p>
                    @enderror
                </div>

                <script>
                    document.getElementById('birthday').addEventListener('blur', function() {
                        const value = this.value.trim();
                        if (value) {
                            const parts = value.split('-');
                            if (parts.length === 3) {
                                const day = parts[0];
                                const month = parts[1];
                                const year = parts[2];
                                const isoDate = `${year}-${month.padStart(2, '0')}-${day.padStart(2, '0')}`;
                                document.getElementById('birthday_hidden').value = isoDate;
                            }
                        }
                    });
                </script>

                <div class="mb-6">
                    <label for="notes" class="block text-sm font-medium text-[#8b4513] mb-2">Notes</label>
                    <textarea name="notes" id="notes" rows="4" 
                              class="w-full px-4 py-2 border border-[#ff6b35] rounded-lg focus:outline-none focus:border-[#d7263d]">{{ old('notes', $card->notes) }}</textarea>
                    @error('notes')
                        <p class="text-red-600 text-sm mt-1">{{ $message }}</p>
                    @enderror
                </div>

                <div class="flex gap-4">
                    <button type="submit" class="bg-[#ff6b35] text-white px-6 py-2 rounded hover:bg-[#d7263d]">Update Card</button>
                    <a href="{{ route('cards.show', $card) }}" class="bg-[#f5c518] text-[#8b4513] px-6 py-2 rounded hover:bg-[#ff6b35]">Cancel</a>
                </div>
            </form>
        </div>
    </div>

    <!-- Current Info Sidebar -->
    <div>
        <div class="bg-[#fef3c7] rounded-lg shadow p-6 sticky top-8 border border-[#ff6b35]">
            <h3 class="text-lg font-semibold text-[#8b4513] mb-4">Current Information</h3>
            <div class="space-y-3 text-sm">
                <div>
                    <p class="text-[#d7263d]">Unique Name</p>
                    <p class="text-[#8b4513] font-medium">{{ $card->unique_name }}</p>
                </div>
                <div>
                    <p class="text-[#d7263d]">Full Name</p>
                    <p class="text-[#8b4513] font-medium">{{ $card->full_name }}</p>
                </div>
                @if ($card->birthday)
                    <div>
                        <p class="text-[#d7263d]">Current Age</p>
                        <p class="text-[#8b4513] font-medium">{{ (int) abs(now()->diffInYears($card->birthday)) }} years</p>
                    </div>
                @endif
                <div class="pt-4 border-t border-[#ff6b35] mt-4">
                    <p class="text-[#d7263d] text-xs">Created: {{ $card->created_at->format('M d, Y') }}</p>
                    <p class="text-[#d7263d] text-xs">Updated: {{ $card->updated_at->format('M d, Y') }}</p>
                </div>
            </div>
        </div>
    </div>
</div>

<!-- Relationships Section -->
<div class="mt-8 space-y-6">
    <h2 class="text-2xl font-bold text-[#8b4513]">Manage Relationships</h2>

    @include('cards.partials.relationships-list', ['relationships' => $card->relationships, 'relatedRelationships' => $card->relatedRelationships])

    @include('cards.partials.add-relationship', ['card' => $card])
</div>
@endsection
