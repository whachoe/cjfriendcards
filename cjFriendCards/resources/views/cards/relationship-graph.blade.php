@extends('layout')

@section('title', 'Relationship Graph')

@section('content')

<div class="mb-6 flex justify-between items-center">
    <h1 class="text-3xl font-bold text-primary-dark">Relationship Graph</h1>
    <div class="flex gap-2">
        <button id="layout-tree" class="bg-primary-accent text-white px-4 py-2 rounded hover:brightness-110 transition text-sm">Tree Layout</button>
        <button id="layout-circle" class="bg-primary-secondary text-primary-dark px-4 py-2 rounded hover:brightness-110 transition text-sm">Circle Layout</button>
        <button id="layout-grid" class="bg-primary-secondary text-primary-dark px-4 py-2 rounded hover:brightness-110 transition text-sm">Grid Layout</button>
        <button id="reset-zoom" class="bg-primary-dark text-white px-4 py-2 rounded hover:brightness-110 transition text-sm">Reset View</button>
    </div>
</div>

@if($cards->isEmpty())
    <div class="bg-primary-light p-8 rounded-lg shadow text-center border border-primary-accent">
        <p class="text-primary-dark mb-4">No cards yet. Create cards and relationships to see the graph!</p>
        <a href="{{ route('cards.create') }}" class="bg-primary-accent text-white px-6 py-2 rounded hover:brightness-110 transition">Create Card</a>
    </div>
@else
    <div class="bg-primary-light rounded-lg shadow p-6 border border-primary-accent mb-6">
        <div id="cy" style="width: 100%; height: 600px; background-color: white; border-radius: 0.5rem;"></div>
    </div>

    <div class="bg-primary-light rounded-lg shadow p-6 border border-primary-accent">
        <h2 class="text-xl font-semibold text-primary-dark mb-4">Legend</h2>
        <div class="grid grid-cols-2 md:grid-cols-3 gap-4">
            <div class="flex items-center gap-2">
                <div class="w-10 h-10 rounded-lg bg-primary-accent"></div>
                <span class="text-sm text-primary-dark">Card (Node)</span>
            </div>
            <div class="flex items-center gap-2">
                <div class="w-10 h-3 bg-primary-dark"></div>
                <span class="text-sm text-primary-dark">Relationship (Edge)</span>
            </div>
            <div class="flex items-center gap-2">
                <svg class="w-10 h-10" viewBox="0 0 40 40">
                    <line x1="5" y1="20" x2="35" y2="20" stroke="var(--color-primary-hover)" stroke-width="3" marker-end="url(#arrowhead)"/>
                    <defs>
                        <marker id="arrowhead" markerWidth="10" markerHeight="10" refX="9" refY="3" orient="auto">
                            <polygon points="0 0, 10 3, 0 6" fill="var(--color-primary-hover)" />
                        </marker>
                    </defs>
                </svg>
                <span class="text-sm text-primary-dark">Parent/Child Direction</span>
            </div>
        </div>
        <p class="text-sm text-primary-dark mt-4 italic">Click on any node to navigate to that card's detail page.</p>
    </div>
@endif

@endsection

@push('scripts')
<script src="https://cdn.jsdelivr.net/npm/cytoscape@3.28.1/dist/cytoscape.min.js"></script>
<script>
    @if(!$cards->isEmpty())
    document.addEventListener('DOMContentLoaded', function() {
        // Build graph data from Laravel
        const nodes = [
            @foreach($cards as $card)
            {
                data: {
                    id: '{{ $card->unique_name }}',
                    label: '{{ $card->full_name }}',
                    url: '{{ route('cards.show', $card) }}'
                }
            },
            @endforeach
        ];

        const edges = [
            @foreach($cards as $card)
                @foreach($card->relationships as $rel)
                {
                    data: {
                        source: '{{ $card->unique_name }}',
                        target: '{{ $rel->relatedCard->unique_name }}',
                        label: '{{ $rel->relationship_type }}',
                        isParentChild: {{ in_array($rel->relationship_type, ['parent', 'child']) ? 'true' : 'false' }}
                    }
                },
                @endforeach
            @endforeach
        ];

        // Initialize Cytoscape
        const cy = cytoscape({
            container: document.getElementById('cy'),
            elements: {
                nodes: nodes,
                edges: edges
            },
            style: [
                {
                    selector: 'node',
                    style: {
                        'background-color': 'data(color)',
                        'label': 'data(label)',
                        'text-valign': 'center',
                        'text-halign': 'center',
                        'color': '#fff',
                        'text-outline-color': '#000',
                        'text-outline-width': 2,
                        'width': 60,
                        'height': 60,
                        'font-size': 12,
                        'background-color': '#ff6b35',
                        'border-width': 2,
                        'border-color': '#d7263d'
                    }
                },
                {
                    selector: 'edge',
                    style: {
                        'width': 3,
                        'line-color': '#8b4513',
                        'target-arrow-color': '#d7263d',
                        'target-arrow-shape': 'triangle',
                        'curve-style': 'bezier',
                        'label': 'data(label)',
                        'font-size': 10,
                        'color': '#8b4513',
                        'text-rotation': 'autorotate',
                        'text-background-color': '#fef3c7',
                        'text-background-opacity': 0.8,
                        'text-background-padding': 3
                    }
                },
                {
                    selector: 'edge[isParentChild = true]',
                    style: {
                        'line-color': '#d7263d',
                        'width': 4
                    }
                },
                {
                    selector: 'node:selected',
                    style: {
                        'background-color': '#f5c518',
                        'border-color': '#ff6b35',
                        'border-width': 4
                    }
                }
            ],
            layout: {
                name: 'breadthfirst',
                directed: true,
                spacingFactor: 1.5,
                animate: true,
                animationDuration: 500
            }
        });

        // Make nodes clickable
        cy.on('tap', 'node', function(evt) {
            const node = evt.target;
            const url = node.data('url');
            if (url) {
                window.location.href = url;
            }
        });

        // Layout buttons
        document.getElementById('layout-tree').addEventListener('click', function() {
            cy.layout({
                name: 'breadthfirst',
                directed: true,
                spacingFactor: 1.5,
                animate: true,
                animationDuration: 500
            }).run();
            
            // Update button states
            document.querySelectorAll('[id^="layout-"]').forEach(btn => {
                btn.classList.remove('bg-primary-accent', 'text-white');
                btn.classList.add('bg-primary-secondary', 'text-primary-dark');
            });
            this.classList.remove('bg-primary-secondary', 'text-primary-dark');
            this.classList.add('bg-primary-accent', 'text-white');
        });

        document.getElementById('layout-circle').addEventListener('click', function() {
            cy.layout({
                name: 'circle',
                animate: true,
                animationDuration: 500
            }).run();
            
            // Update button states
            document.querySelectorAll('[id^="layout-"]').forEach(btn => {
                btn.classList.remove('bg-primary-accent', 'text-white');
                btn.classList.add('bg-primary-secondary', 'text-primary-dark');
            });
            this.classList.remove('bg-primary-secondary', 'text-primary-dark');
            this.classList.add('bg-primary-accent', 'text-white');
        });

        document.getElementById('layout-grid').addEventListener('click', function() {
            cy.layout({
                name: 'grid',
                animate: true,
                animationDuration: 500
            }).run();
            
            // Update button states
            document.querySelectorAll('[id^="layout-"]').forEach(btn => {
                btn.classList.remove('bg-primary-accent', 'text-white');
                btn.classList.add('bg-primary-secondary', 'text-primary-dark');
            });
            this.classList.remove('bg-primary-secondary', 'text-primary-dark');
            this.classList.add('bg-primary-accent', 'text-white');
        });

        document.getElementById('reset-zoom').addEventListener('click', function() {
            cy.fit();
            cy.center();
        });

        // Initial fit
        cy.fit();
        cy.center();
    });
    @endif
</script>
@endpush
