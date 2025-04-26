@livewireStyles
@livewireScripts

<head>
    <meta name="csrf-token" content="{{ csrf_token() }}">
</head>

<div class="h-screen w-64 bg-gray-800 text-white p-4 overflow-y-auto fixed left-0 top-0">
    <h2 class="text-xl font-bold mb-4">Building Data</h2>
    
    <!-- Mode Toggle -->
    <div class="flex items-center mb-4 space-x-2">
        <label for="toggleBuildings" class="text-sm font-medium">Show Buildings</label>
        <input type="checkbox" id="toggleBuildings" wire:model="showBuildings" wire:change="toggleMode" class="cursor-pointer">
    </div>

    <!-- Data List -->
    <div class="space-y-2 text-sm" id="dataList" wire:key="data-list-{{ $showBuildings ? 'building' : 'apartment' }}">
        @if ($showBuildings)
            <!-- Display buildings -->
            <div id="buildingList">
                @forelse ($buildings as $building)
                    <div class="p-2 bg-gray-700 rounded mb-2">
                        <p class="font-bold">{{ $building['avg_dpe_grade'] ?? 'N/A' }}</p>
                        <p class="text-xs">{{ $building['address_text'] ?? 'Unknown address' }}</p>
                    </div>
                @empty
                    <div>No building data available.</div>
                @endforelse
            </div>
        @else
            <!-- Display apartments -->
            <div id="apartmentList">
                @forelse ($apartments as $apartment)
                    <div class="p-2 bg-gray-700 rounded mb-2">
                        <p class="font-bold">{{ $apartment['dpe_grade'] ?? 'N/A' }}</p>
                        <p class="text-xs">{{ $apartment['address'] ?? 'Unknown address' }}</p>
                    </div>
                @empty
                    <div>No apartment data available.</div>
                @endforelse
            </div>
        @endif
    </div>

    <script>
        document.addEventListener('DOMContentLoaded', function() {
            // Connect to building data updates
            window.addEventListener('buildingDataUpdate', function(e) {
                const container = document.getElementById('buildingList');
                container.innerHTML = '';
                
                e.detail.features.forEach(building => {
                    const div = document.createElement('div');
                    div.className = 'p-2 bg-gray-700 rounded mb-2';
                    div.innerHTML = `
                        <p class="font-bold">${building.properties.dpe_class || 'N/A'}</p>
                        <p class="text-xs">${building.properties.address || 'Unknown address'}</p>
                    `;
                    container.appendChild(div);
                });
            });

            // Listen for Livewire log messages
            window.livewire.on('logMessage', message => {
                const logList = document.getElementById('logList');
                const li = document.createElement('li');
                li.textContent = message;
                logList.appendChild(li);
                // Keep scroll at bottom
                logList.scrollTop = logList.scrollHeight;
            });

            // Make pagination functions globally available
            window.pagination = {
                currentPage: 1,
                pageSize: 20,
                
                loadBuildings: function(page = 1) {
                    this.currentPage = page;
                    fetch(`/api/buildings/geojson?page=${page}&limit=${this.pageSize}`)
                        .then(response => response.json())
                        .then(data => {
                            window.dispatchEvent(new CustomEvent('buildingDataUpdate', {
                                detail: data
                            }));
                            
                            // Update pagination controls
                            document.getElementById('currentPage').textContent = `Page ${this.currentPage}`;
                            document.getElementById('prevBtn').disabled = this.currentPage === 1;
                        });
                }
            };

            // Load first page
            window.pagination.loadBuildings(1);

            // Add pagination controls
            document.getElementById('buildingList').insertAdjacentHTML('afterend', `
                <div class="pagination-controls mt-4 flex justify-between items-center">
                    <button id="prevBtn" 
                            onclick="window.pagination.loadBuildings(window.pagination.currentPage - 1)" 
                            class="px-3 py-1 bg-gray-700 rounded disabled:opacity-50" 
                            disabled>
                        Previous
                    </button>
                    <span id="currentPage">Page 1</span>
                    <button onclick="window.pagination.loadBuildings(window.pagination.currentPage + 1)" 
                            class="px-3 py-1 bg-gray-700 rounded">
                        Next
                    </button>
                </div>
            `);
        });
    </script>
</div>
