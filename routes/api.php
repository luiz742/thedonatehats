<?php

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Route;
use App\Http\Controllers\DonationController;

Route::get('/donations/{id}', [DonationController::class, 'show']);


Route::get('/user', function (Request $request) {
    return $request->user();
})->middleware('auth:sanctum');
