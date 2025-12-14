<!-- Add Relationship Form -->
<div class="bg-white rounded-lg shadow p-6">
    <h3 class="text-lg font-semibold text-gray-900 mb-4">Add New Relationship</h3>
    
    <form action="{{ route('relationships.store', $card) }}" method="POST" hx-boost="true">
        @csrf
        
        <div class="mb-4">
            <label for="related_card_id" class="block text-sm font-medium text-gray-700 mb-2">Card *</label>
            <input 
                type="hidden" 
                name="related_card_id" 
                id="related_card_id"
                required
                class="related_card_id_input"
            />
            <input 
                type="text" 
                id="unique_name_search" 
                placeholder="Search by card name..." 
                autocomplete="off"
                class="w-full px-4 py-2 border border-gray-300 rounded-lg focus:outline-none focus:border-blue-500"
            />
            <div id="autocomplete_results" class="mt-2 border border-gray-300 rounded-lg hidden max-h-48 overflow-y-auto bg-white"></div>
            @error('related_card_id')
                <p class="text-red-600 text-sm mt-1">{{ $message }}</p>
            @enderror
        </div>

        <div class="mb-4">
            <label for="relationship_type" class="block text-sm font-medium text-gray-700 mb-2">Relationship Type *</label>
            <select name="relationship_type" id="relationship_type" required 
                    class="w-full px-4 py-2 border border-gray-300 rounded-lg focus:outline-none focus:border-blue-500">
                <option value="">Select a relationship type</option>
                <option value="friend">Friend</option>
                <option value="colleague">Colleague</option>
                <option value="family">Family</option>
                <option value="spouse">Spouse</option>
                <option value="child">Child</option>
                <option value="parent">Parent</option>
                <option value="acquaintance">Acquaintance</option>
                <option value="ex-partner">Ex-Partner</option>
            </select>
            @error('relationship_type')
                <p class="text-red-600 text-sm mt-1">{{ $message }}</p>
            @enderror
        </div>

        <div class="mb-4">
            <label for="notes" class="block text-sm font-medium text-gray-700 mb-2">Notes</label>
            <textarea name="notes" id="notes" rows="2" 
                      class="w-full px-4 py-2 border border-gray-300 rounded-lg focus:outline-none focus:border-blue-500"></textarea>
            @error('notes')
                <p class="text-red-600 text-sm mt-1">{{ $message }}</p>
            @enderror
        </div>

        <button type="submit" class="bg-blue-600 text-white px-6 py-2 rounded hover:bg-blue-700">Add Relationship</button>
    </form>
</div>

<script>
document.addEventListener('DOMContentLoaded', function() {
    const searchInput = document.getElementById('unique_name_search');
    const resultsDiv = document.getElementById('autocomplete_results');
    const hiddenInput = document.getElementById('related_card_id');
    let selectedCard = null;

    searchInput.addEventListener('input', async function() {
        const query = this.value;
        
        if (query.length < 1) {
            resultsDiv.classList.add('hidden');
            return;
        }

        try {
            const response = await fetch(
                `{{ route('relationships.autocomplete', $card) }}?q=${encodeURIComponent(query)}`
            );
            const cards = await response.json();
            
            if (cards.length === 0) {
                resultsDiv.innerHTML = '<div class="p-2 text-gray-500">No results found</div>';
                resultsDiv.classList.remove('hidden');
                return;
            }

            resultsDiv.innerHTML = cards.map(card => `
                <div 
                    class="p-2 cursor-pointer hover:bg-blue-50 border-b last:border-b-0"
                    onclick="selectCard(${card.id}, '${card.unique_name}')"
                >
                    ${card.display}
                </div>
            `).join('');
            resultsDiv.classList.remove('hidden');
        } catch (error) {
            console.error('Error fetching autocomplete:', error);
        }
    });

    window.selectCard = function(id, uniqueName) {
        hiddenInput.value = id;
        searchInput.value = uniqueName;
        resultsDiv.classList.add('hidden');
    };

    document.addEventListener('click', function(e) {
        if (e.target !== searchInput && e.target !== resultsDiv) {
            resultsDiv.classList.add('hidden');
        }
    });
});
</script>
