<?php

use App\Http\Controllers\Services\TrackController;
use Illuminate\Support\Facades\Route;


Route::get('/', [TrackController::class, 'index']);
Route::post('/result', [TrackController::class, 'search'])->name('track.search');