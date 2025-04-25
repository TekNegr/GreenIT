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
        $this->currentBBox = $bbox;
        $this->isCached = $isCached;
        $this->emit('bboxStatusUpdated', ['bbox' => $bbox, 'isCached' => $isCached]);
    }

    public function render()
    {
        return view('livewire.sidebar', [
            'currentBBox' => $this->currentBBox,
            'isCached' => $this->isCached,
            'logs' => $this->logs,
        ]);
    }
}
