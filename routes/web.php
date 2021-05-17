<?php

declare(strict_types = 1);

use Radio\Http\Controllers\CallController;
use Illuminate\Support\Facades\Route;
use Radio\Http\Controllers\ScriptsController;

Route::name('radio.')->prefix('/radio')->group(function () {
    Route::post('/call', CallController::class)
        ->middleware(config('radio.middleware'))
        ->name('call');

    Route::get('/scripts/{path}', ScriptsController::class)
        ->name('script');
});