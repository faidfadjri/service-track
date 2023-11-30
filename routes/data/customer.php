<?php

use App\Http\Controllers\Data\CustomerController;
use Illuminate\Support\Facades\Route;

Route::get('/', [CustomerController::class, 'index']);
Route::get('/load', [CustomerController::class, 'load'])->name('customer.load');
Route::delete('/delete', [CustomerController::class, 'delete'])->name('customer.delete');
