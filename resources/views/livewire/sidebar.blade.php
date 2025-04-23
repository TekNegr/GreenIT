<div class="h-screen w-64 bg-gray-800 text-white p-4 overflow-y-auto fixed left-0 top-0">
<<<<<<< HEAD
    <h2 class="text-xl font-bold mb-4">Application Logs</h2>
    
    <div class="space-y-2 text-sm">
        @foreach($logs as $log)
            @if(!empty(trim($log)))
                <div class="p-2 bg-gray-700 rounded break-words">
                    {{ $log }}
                </div>
            @endif
        @endforeach
=======
    <h2 class="text-xl font-bold mb-4">Building Data</h2>
    
    <div class="space-y-2 text-sm" id="buildingList">
        <!-- Buildings will be loaded here -->
>>>>>>> 3e757375a0cecffcd3fd974d0173cfa68ba026da
    </div>

    <script>
        document.addEventListener('DOMContentLoaded', function() {
<<<<<<< HEAD
            setInterval(() => {
                Livewire.dispatch('refreshLogs');
            }, 5000);
=======
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
>>>>>>> 3e757375a0cecffcd3fd974d0173cfa68ba026da
        });
    </script>
</div>
