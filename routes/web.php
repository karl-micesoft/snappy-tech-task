<?php

use App\Http\Controllers\StoreController;
use App\Http\Middleware\CheckJwt;
use Illuminate\Support\Facades\Route;

Route::get('/stores', [StoreController::class, 'index']);
Route::middleware(CheckJwt::class)->post('/stores', [StoreController::class, 'store']);
Route::get('/stores/nearby', [StoreController::class, 'nearby']);
Route::get('/stores/deliver', [StoreController::class, 'deliver']);
