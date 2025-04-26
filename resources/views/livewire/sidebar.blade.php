    @livewireStyles
@livewireScripts

<head>
    <meta name="csrf-token" content="{{ csrf_token() }}">
</head>

<div class="h-screen w-64 bg-gray-800 text-white p-4 overflow-y-auto fixed left-0 top-0">
    <h2 class="text-xl font-bold mb-4">Building Data</h2>
    
    <div class="space-y-2 text-sm" id="buildingList">
        <!-- Buildings will be loaded here -->
    </div>

    <div class="mt-4 p-2 bg-gray-700 rounded text-xs h-48 overflow-y-auto" id="logContainer">
        <h3 class="font-bold mb-2">Live Logs</h3>
        <ul id="logList" class="list-disc list-inside max-h-40 overflow-y-auto"></ul>
    </div>

    <div class="mt-4 p-2 bg-gray-700 rounded text-xs">
        <h3 class="font-bold mb-2">Current BBox Status</h3>
        <p>
            <strong>Coordinates:</strong>
            <span id="bboxCoordinates">
                @if($currentBBox)
                    {{ implode(', ', $currentBBox) }}
                @else
                    N/A
                @endif
            </span>
        </p>
        <p>
            <strong>Cached:</strong>
            <span id="bboxCachedStatus">
                @if($isCached)
                    Yes
                @else
                    No
                @endif
            </span>
        </p>
    </div>

    <script>
        document.addEventListener('DOMContentLoaded', function() {
            // window.livewire.on('bboxStatusUpdated', data => {
            //     document.getElementById('bboxCoordinates').textContent = data.bbox.join(', ');
            //     document.getElementById('bboxCachedStatus').textContent = data.isCached ? 'Yes' : 'No';
            // });
            if (window.CTM) {
            // Listen for bbox changes from CTM.js and emit Livewire event
            window.CTM.onBBoxChange = function(newBBox) {
                window.livewire.emit('updateBBoxStatus', { bbox: newBBox, isCached: false });
            };

            // Initialize with current bbox
            const bbox = window.CTM.getBBox();
            window.livewire.emit('updateBBoxStatus', { bbox: bbox, isCached: false });
        } else {
            document.getElementById('bboxCoordinates').textContent = 'CTM.js not loaded';
        }
});

        
    </script>

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
