<?php

declare(strict_types = 1);

use Radio\Http\Controllers\CallController;
use Illuminate\Support\Facades\Route;
use Radio\Http\Controllers\ScriptsController;

Route::post('/radio/call', CallController::class)->middleware(config('radio.middleware'))->name('radio.call');
Route::get('/radio/scripts/{path}', ScriptsController::class)->name('radio.scripts');
