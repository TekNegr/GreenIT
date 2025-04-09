<?php

use Illuminate\Support\Facades\Route;
use Illuminate\Support\Facades\Log;

Route::post('/log/scene-view', function() {
    Log::channel('sidebar')->info(request()->getContent());
    return response()->json(['status' => 'logged']);
});

Route::get('/', function () {
    return view('main');
});

Route::get('/batiments', [BatimentController::class, 'index']);
Route::get('/batiments/filter', [BatimentController::class, 'filter']);
