<?php

use Illuminate\Support\Facades\Route;

Route::get('/browser/init', function () {
    return view('browser::Init.view');
});
