<?php

namespace Aerial\Http\Controllers;

use Illuminate\Http\Request;

class CallController
{
    public function __invoke(Request $request)
    {
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
    }
}
