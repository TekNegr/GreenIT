<?php

use Illuminate\Support\Facades\Route;
use Illuminate\Support\Facades\Log;
use App\Http\Controllers\BatimentController;
use App\Http\Controllers\ApiController;
use App\Http\Controllers\AppartementController;


Route::get('/', function () {
    return view('main');
});

Route::get('/import-dpe', [BatimentController::class, 'importDpe'])->name('dpe.import');
Route::get('/dpe', [BatimentController::class, 'showDpeData'])->name('dpe.index');

Route::post('/log/scene-view', function() {
    Log::channel('sidebar')->info(request()->getContent());
    return response()->json(['status' => 'logged']);
});

// Route::get('/test', function () {
//     return view('dpe.index');
// });

Route::get('/test', [ApiController::class, 'testApiPage']);
Route::post('/test/fetch-data', [ApiController::class, 'fetchData']);

Route::get('/log-test', function() {
    Log::channel('sidebar')->info('SIDEBAR CHANNEL TEST');
    Log::channel('Controller')->info('CONTROLLER CHANNEL TEST');
    return response()->json(['status' => 'logged']);
});

Route::get('/batiments', [BatimentController::class, 'index']);
Route::get('/batiments/filter', [BatimentController::class, 'filter']);
Route::get('/api/buildings/geojson', [BatimentController::class, 'getBuildingsGeoJson']);
Route::post('/api/fetch-appartements', [AppartementController::class, 'fetchAppartements']);
