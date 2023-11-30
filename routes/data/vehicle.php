<?php

use App\Http\Controllers\Data\VehicleController;
use Illuminate\Support\Facades\Route;

Route::get('/', [VehicleController::class, 'index']);
Route::get('/load', [VehicleController::class, 'load'])->name('vehicle.load');
Route::delete('/delete', [VehicleController::class, 'delete'])->name('vehicle.delete');
