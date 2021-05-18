<?php

use Illuminate\Support\Facades\Route;

Route::get('/browser/init', function () {
    return view('browser::Init.view');
})->name('browser.init');

Route::get('/browser/computed', function () {
    return view('browser::Computed.view');
})->name('browser.computed');
