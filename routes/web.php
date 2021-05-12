<?php

declare(strict_types = 1);

use Radio\Http\Controllers\CallController;
use Illuminate\Support\Facades\Route;

Route::post('/radio/call', CallController::class)->middleware(config('radio.middleware'))->name('radio.call');
