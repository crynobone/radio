<?php

declare(strict_types = 1);

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Route;

Route::post('/aerial/call', function (Request $request) {
    $component = app($request->input('component'));

    $component->hydrateAerialState(
        $request->input('state'),
    );

    $result = $component->callAerialMethod(
        $request->input('method'),
        array_values($request->input('args')),
    );

    return response()->json([
        'result' => $result,
        'state' => $component->getAerialState(),
    ]);
})->middleware('signed')->name('aerial.call');