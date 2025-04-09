<div class="h-screen w-64 bg-gray-800 text-white p-4 overflow-y-auto fixed left-0 top-0">
    <h2 class="text-xl font-bold mb-4">Application Logs</h2>
    
    <div class="space-y-2 text-sm">
        @foreach($logs as $log)
            @if(!empty(trim($log)))
                <div class="p-2 bg-gray-700 rounded break-words">
                    {{ $log }}
                </div>
            @endif
        @endforeach
    </div>

    <script>
        document.addEventListener('DOMContentLoaded', function() {
            setInterval(() => {
                Livewire.dispatch('refreshLogs');
            }, 5000);
        });
    </script>
</div>
