@extends('layout')

@section('title', 'Create Friendship Card')

@section('content')
<div class="mb-6">
    <h1 class="text-3xl font-bold text-gray-900">Create New Friendship Card</h1>
</div>

<div class="bg-white rounded-lg shadow p-6 max-w-2xl">
    <form action="{{ route('cards.store') }}" method="POST">
        @csrf

        <div class="grid grid-cols-2 gap-4 mb-6">
            <div>
                <label for="first_name" class="block text-sm font-medium text-gray-700 mb-2">First Name *</label>
                <input type="text" name="first_name" id="first_name" value="{{ old('first_name') }}" required 
                       class="w-full px-4 py-2 border border-gray-300 rounded-lg focus:outline-none focus:border-blue-500">
                @error('first_name')
                    <p class="text-red-600 text-sm mt-1">{{ $message }}</p>
                @enderror
            </div>
            <div>
                <label for="last_name" class="block text-sm font-medium text-gray-700 mb-2">Last Name *</label>
                <input type="text" name="last_name" id="last_name" value="{{ old('last_name') }}" required 
                       class="w-full px-4 py-2 border border-gray-300 rounded-lg focus:outline-none focus:border-blue-500">
                @error('last_name')
                    <p class="text-red-600 text-sm mt-1">{{ $message }}</p>
                @enderror
            </div>
        </div>

        <div class="mb-6">
            <label for="unique_name" class="block text-sm font-medium text-gray-700 mb-2">Unique Name * <span class="text-gray-500 text-sm">(auto-generated)</span></label>
            <input type="text" name="unique_name" id="unique_name" value="{{ old('unique_name') }}" required 
                   class="w-full px-4 py-2 border border-gray-300 rounded-lg focus:outline-none focus:border-blue-500"
                   readonly>
            @error('unique_name')
                <p class="text-red-600 text-sm mt-1">{{ $message }}</p>
            @enderror
        </div>

        <div class="mb-6">
            <label for="address" class="block text-sm font-medium text-gray-700 mb-2">Address</label>
            <input type="text" name="address" id="address" value="{{ old('address') }}" 
                   class="w-full px-4 py-2 border border-gray-300 rounded-lg focus:outline-none focus:border-blue-500">
            @error('address')
                <p class="text-red-600 text-sm mt-1">{{ $message }}</p>
            @enderror
        </div>

        <div class="mb-6">
            <label for="phone" class="block text-sm font-medium text-gray-700 mb-2">Phone</label>
            <input type="text" name="phone" id="phone" value="{{ old('phone') }}" 
                   class="w-full px-4 py-2 border border-gray-300 rounded-lg focus:outline-none focus:border-blue-500">
            @error('phone')
                <p class="text-red-600 text-sm mt-1">{{ $message }}</p>
            @enderror
        </div>

        <div class="mb-6">
            <label for="email_work" class="block text-sm font-medium text-gray-700 mb-2">Email (Work)</label>
            <input type="email" name="email_work" id="email_work" value="{{ old('email_work') }}" 
                   class="w-full px-4 py-2 border border-gray-300 rounded-lg focus:outline-none focus:border-blue-500">
            @error('email_work')
                <p class="text-red-600 text-sm mt-1">{{ $message }}</p>
            @enderror
        </div>

        <div class="mb-6">
            <label for="email_personal" class="block text-sm font-medium text-gray-700 mb-2">Email (Personal)</label>
            <input type="email" name="email_personal" id="email_personal" value="{{ old('email_personal') }}" 
                   class="w-full px-4 py-2 border border-gray-300 rounded-lg focus:outline-none focus:border-blue-500">
            @error('email_personal')
                <p class="text-red-600 text-sm mt-1">{{ $message }}</p>
            @enderror
        </div>

        <div class="mb-6">
            <label for="email_extra1" class="block text-sm font-medium text-gray-700 mb-2">Email (Extra 1)</label>
            <input type="email" name="email_extra1" id="email_extra1" value="{{ old('email_extra1') }}" 
                   class="w-full px-4 py-2 border border-gray-300 rounded-lg focus:outline-none focus:border-blue-500">
            @error('email_extra1')
                <p class="text-red-600 text-sm mt-1">{{ $message }}</p>
            @enderror
        </div>

        <div class="mb-6">
            <label for="email_extra2" class="block text-sm font-medium text-gray-700 mb-2">Email (Extra 2)</label>
            <input type="email" name="email_extra2" id="email_extra2" value="{{ old('email_extra2') }}" 
                   class="w-full px-4 py-2 border border-gray-300 rounded-lg focus:outline-none focus:border-blue-500">
            @error('email_extra2')
                <p class="text-red-600 text-sm mt-1">{{ $message }}</p>
            @enderror
        </div>

        <div class="mb-6">
            <label for="email_extra3" class="block text-sm font-medium text-gray-700 mb-2">Email (Extra 3)</label>
            <input type="email" name="email_extra3" id="email_extra3" value="{{ old('email_extra3') }}" 
                   class="w-full px-4 py-2 border border-gray-300 rounded-lg focus:outline-none focus:border-blue-500">
            @error('email_extra3')
                <p class="text-red-600 text-sm mt-1">{{ $message }}</p>
            @enderror
        </div>

        <div class="mb-6">
            <label for="birthday" class="block text-sm font-medium text-gray-700 mb-2">Birthday</label>
            <input type="date" name="birthday" id="birthday" value="{{ old('birthday') }}" 
                   class="w-full px-4 py-2 border border-gray-300 rounded-lg focus:outline-none focus:border-blue-500">
            @error('birthday')
                <p class="text-red-600 text-sm mt-1">{{ $message }}</p>
            @enderror
        </div>

        <div class="mb-6">
            <label for="notes" class="block text-sm font-medium text-gray-700 mb-2">Notes</label>
            <textarea name="notes" id="notes" rows="4" 
                      class="w-full px-4 py-2 border border-gray-300 rounded-lg focus:outline-none focus:border-blue-500">{{ old('notes') }}</textarea>
            @error('notes')
                <p class="text-red-600 text-sm mt-1">{{ $message }}</p>
            @enderror
        </div>

        <div class="flex gap-4">
            <button type="submit" class="bg-blue-600 text-white px-6 py-2 rounded hover:bg-blue-700">Create Card</button>
            <a href="{{ route('cards.index') }}" class="bg-gray-600 text-white px-6 py-2 rounded hover:bg-gray-700">Cancel</a>
        </div>
    </form>
    
    <div class="mt-4 p-4 bg-blue-50 border border-blue-200 rounded text-blue-800 text-sm">
        <p><strong>Note:</strong> You can add relationships after creating the card.</p>
    </div>
</div>

<script>
    document.addEventListener('DOMContentLoaded', function() {
        const firstNameInput = document.getElementById('first_name');
        const lastNameInput = document.getElementById('last_name');
        const uniqueNameInput = document.getElementById('unique_name');

        function generateUniqueName() {
            const firstName = firstNameInput.value.toLowerCase().trim();
            const lastName = lastNameInput.value.toLowerCase().trim();
            
            if (firstName && lastName) {
                uniqueNameInput.value = `${firstName}-${lastName}`;
            } else {
                uniqueNameInput.value = '';
            }
        }

        firstNameInput.addEventListener('input', generateUniqueName);
        lastNameInput.addEventListener('input', generateUniqueName);
    });
</script>
@endsection
