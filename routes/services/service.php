<?php

use App\Http\Controllers\Services\ServiceController;
use Illuminate\Support\Facades\Route;

Route::get('/load', [ServiceController::class, 'load'])->name('service.load');
Route::get('/filter-nopol', [ServiceController::class, 'filter'])->name('service.filter');
Route::post('/update', [ServiceController::class, 'update'])->name('service.update');
Route::post('/pause', [ServiceController::class, 'pause'])->name('service.pause');
Route::put('/cancel', [ServiceController::class, 'cancel'])->name('service.cancel');
Route::delete('/delete', [ServiceController::class, 'delete'])->name('service.delete');
