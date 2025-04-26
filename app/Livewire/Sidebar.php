<?php

namespace App\Livewire;

use Livewire\Component;
use Illuminate\Support\Facades\File;
use Illuminate\Support\Facades\Log;

class Sidebar extends Component
{
    public $logs = [];
    public $currentBBox = null;
    public $isCached = false;

    public function mount()
    {
        $this->getLogs();
    }

    public function getLogs()
    {
        $logFiles = File::glob(storage_path('logs/laravel-*.log'));
        $this->logs = [];

        foreach ($logFiles as $file) {
            $this->logs = array_merge(
                $this->logs,
                array_reverse(explode("\n", File::get($file)))
            );
        }

        $this->logs = array_slice($this->logs, 0, 30); // Show last 30 lines
        
        // Log the operation
        $logMessage = sprintf(
            'Sidebar logs refreshed at %s - %d entries loaded',
            now()->toDateTimeString(),
            count($this->logs)
        );
        Log::channel('sidebar')->info($logMessage);
    }

    protected $listeners = [
        'refreshLogs' => 'getLogs',
        'updateBBoxStatus' => 'updateBBoxStatus',
    ];

    public function updateBBoxStatus($bbox, $isCached)
    {
        // Convert bbox coordinates from projected system to lat/lon if needed
        // Assuming bbox is [minX, minY, maxX, maxY] in projected coordinates (e.g., EPSG:2154)
        // We will convert to [minLon, minLat, maxLon, maxLat] in EPSG:4326

        if ($bbox && count($bbox) === 4) {
            // Use proj4php or similar library for conversion if available
            // For now, let's assume bbox is already in lat/lon; if not, conversion logic should be added here

            // Example placeholder for conversion:
            // $convertedBBox = $this->convertProjectedBBoxToLatLon($bbox);
            // $this->currentBBox = $convertedBBox;

            $this->currentBBox = $bbox; // Replace with conversion if needed

            // Dispatch the ImportDpeData job asynchronously with the updated bbox
            \App\Jobs\ImportDpeData::dispatch($this->currentBBox);
        } else {
            $this->currentBBox = null;
        }

        $this->isCached = $isCached;
        $this->emit('bboxStatusUpdated', ['bbox' => $this->currentBBox, 'isCached' => $isCached]);
    }

    // Example conversion function placeholder
    /*
    protected function convertProjectedBBoxToLatLon(array $bbox)
    {
        // Implement coordinate conversion here using a PHP library or service
        // Return converted bbox as [minLon, minLat, maxLon, maxLat]
    }
    */

    public function render()
    {
        return view('livewire.sidebar', [
            'currentBBox' => $this->currentBBox,
            'isCached' => $this->isCached,
            'logs' => $this->logs,
        ]);
    }
}
