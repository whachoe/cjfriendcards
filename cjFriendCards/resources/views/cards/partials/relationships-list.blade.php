<!-- Relationships List -->
<div class="bg-white rounded-lg shadow p-6">
    <h3 class="text-lg font-semibold text-gray-900 mb-4">Relationships</h3>

    @php
        $allRelationships = collect();
        if (isset($relationships)) {
            $allRelationships = $relationships;
        }
        if (isset($relatedRelationships)) {
            $allRelationships = $allRelationships->concat($relatedRelationships);
        }
    @endphp

    @if ($allRelationships->isEmpty())
        <p class="text-gray-500 text-center py-4">No relationships yet. Add one below!</p>
    @else
        <div class="space-y-3">
            {{-- Direct relationships (where this card is card_id) --}}
            @foreach ($relationships ?? [] as $rel)
                <div class="p-4 bg-gray-50 rounded border border-gray-200 flex justify-between items-start">
                    <div class="flex-1">
                        <div class="flex items-center gap-2 mb-1">
                            <a href="{{ route('cards.show', $rel->relatedCard) }}" class="text-blue-600 hover:underline font-medium">
                                {{ $rel->relatedCard->full_name }}
                            </a>
                            <span class="bg-blue-100 text-blue-800 text-xs font-semibold px-2 py-1 rounded">
                                {{ ucfirst(str_replace('_', ' ', $rel->relationship_type)) }}
                            </span>
                        </div>
                        @if ($rel->notes)
                            <p class="text-sm text-gray-600">{{ $rel->notes }}</p>
                        @endif
                    </div>
                    <div class="flex gap-2 ml-4">
                        <button 
                            type="button"
                            onclick="editRelationship({{ $rel->id }})"
                            class="text-gray-600 hover:text-gray-900 text-sm"
                        >
                            ✎
                        </button>
                        <form action="{{ route('relationships.destroy', [$card, $rel]) }}" method="POST" class="inline" onsubmit="return confirm('Delete this relationship?');">
                            @csrf
                            @method('DELETE')
                            <button type="submit" class="text-red-600 hover:text-red-900 text-sm">✕</button>
                        </form>
                    </div>
                </div>
            @endforeach

            {{-- Inverse relationships (where this card is related_card_id) --}}
            @foreach ($relatedRelationships ?? [] as $rel)
                <div class="p-4 bg-blue-50 rounded border border-blue-200 flex justify-between items-start opacity-75">
                    <div class="flex-1">
                        <div class="flex items-center gap-2 mb-1">
                            <a href="{{ route('cards.show', $rel->card) }}" class="text-blue-600 hover:underline font-medium">
                                {{ $rel->card->full_name }}
                            </a>
                            <span class="bg-blue-200 text-blue-900 text-xs font-semibold px-2 py-1 rounded">
                                {{ ucfirst(str_replace('_', ' ', $rel->relationship_type)) }}
                            </span>
                            <span class="text-xs text-blue-700 italic">(connected by them)</span>
                        </div>
                        @if ($rel->notes)
                            <p class="text-sm text-gray-600">{{ $rel->notes }}</p>
                        @endif
                    </div>
                </div>
            @endforeach
        </div>
    @endif
</div>

<!-- Edit Relationship Modal (hidden by default) -->
<div id="edit_modal" class="hidden fixed inset-0 bg-black bg-opacity-50 flex items-center justify-center z-50">
    <div class="bg-white rounded-lg shadow p-6 max-w-md w-full mx-4">
        <h3 class="text-lg font-semibold text-gray-900 mb-4">Edit Relationship</h3>
        
        <form id="edit_form" method="POST">
            @csrf
            @method('PATCH')
            
            <div class="mb-4">
                <label for="edit_relationship_type" class="block text-sm font-medium text-gray-700 mb-2">Relationship Type *</label>
                <select name="relationship_type" id="edit_relationship_type" required 
                        class="w-full px-4 py-2 border border-gray-300 rounded-lg focus:outline-none focus:border-blue-500">
                    <option value="best_friend">Best Friend</option>
                    <option value="colleague">Colleague</option>
                    <option value="family">Family</option>
                    <option value="spouse">Spouse</option>
                    <option value="child">Child</option>
                    <option value="parent">Parent</option>
                    <option value="acquaintance">Acquaintance</option>
                    <option value="ex-partner">Ex-Partner</option>
                </select>
            </div>

            <div class="mb-4">
                <label for="edit_notes" class="block text-sm font-medium text-gray-700 mb-2">Notes</label>
                <textarea name="notes" id="edit_notes" rows="2" 
                          class="w-full px-4 py-2 border border-gray-300 rounded-lg focus:outline-none focus:border-blue-500"></textarea>
            </div>

            <div class="flex gap-2 justify-end">
                <button type="button" onclick="closeEditModal()" class="px-4 py-2 text-gray-700 bg-gray-200 rounded hover:bg-gray-300">Cancel</button>
                <button type="submit" class="px-4 py-2 bg-blue-600 text-white rounded hover:bg-blue-700">Update</button>
            </div>
        </form>
    </div>
</div>

<script>
// Store relationship data temporarily
let relationshipData = {
    @foreach ($relationships as $rel)
        {{ $rel->id }}: {
            type: '{{ $rel->relationship_type }}',
            notes: `{{ $rel->notes }}`,
            url: '{{ route("relationships.update", [$card, $rel]) }}'
        },
    @endforeach
};

function editRelationship(id) {
    const data = relationshipData[id];
    if (!data) return;

    document.getElementById('edit_relationship_type').value = data.type;
    document.getElementById('edit_notes').value = data.notes;
    document.getElementById('edit_form').action = data.url;
    document.getElementById('edit_modal').classList.remove('hidden');
}

function closeEditModal() {
    document.getElementById('edit_modal').classList.add('hidden');
}

// Close modal on outside click
document.getElementById('edit_modal').addEventListener('click', function(e) {
    if (e.target === this) {
        closeEditModal();
    }
});
</script>
