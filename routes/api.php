<?php

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Route;
use App\Http\Controllers\AppartementController;
use App\Http\Controllers\BatimentController;
use App\Http\Controllers\ApiController;

Route::post('/fetch-appartements', [AppartementController::class, 'fetchAppartements']);
