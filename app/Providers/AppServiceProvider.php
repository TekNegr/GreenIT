<?php

namespace App\Providers;

use Illuminate\Support\ServiceProvider;
use App\Http\Controllers\GeneralController;

class AppServiceProvider extends ServiceProvider
{
    /**
     * Register any application services.
     */
    public function register(): void
    {
        //
    }

    /**
     * Bootstrap any application services.
     */
    public function boot(): void
    {
        // Dispatch ImportDpeData job at app launch with default BBox (example coordinates)
        $defaultBBox = [2.2241, 48.8156, 2.4699, 48.9022]; // Paris approx bbox
        $apiUri = 'https://data.ademe.fr/data-fair/api/v1/datasets/dpe-france/lines?bbox=$lonMin,$latMin,$lonMax,$latMax&rows=$MaxRows';
        $generalController = new GeneralController();
        $generalController->handleBBoxUpdate(new \Illuminate\Http\Request([
            'bbox' => implode(',', $defaultBBox),
        ]));
        \App\Jobs\ImportDpeData::dispatch($defaultBBox, $apiUri);
    }
}
