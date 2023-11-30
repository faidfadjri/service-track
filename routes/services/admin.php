<?php

use App\Http\Controllers\Services\AdminController;
use Illuminate\Support\Facades\Route;


Route::get('/', [AdminController::class, 'index']);
Route::get('/excel', [AdminController::class, 'excel'])->name('admin.excel');
Route::get('/search-nopol', [AdminController::class, 'search'])->name('admin.search');
Route::post('/add-job', [AdminController::class, 'addJob'])->name('admin.addjob');
Route::post('/store', [AdminController::class, 'store'])->name('admin.store');
Route::put('/update', [AdminController::class, 'update'])->name('admin.update');