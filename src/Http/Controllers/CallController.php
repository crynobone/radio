<?php

declare(strict_types = 1);

namespace Radio\Http\Controllers;

use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;

class CallController
{
    public function __invoke(Request $request): JsonResponse
    {
        $component = app($request->input('component'));

        $component->hydrateRadioState(
            $request->input('state'),
        );

        $result = $component->callRadioMethod(
            $request->input('method'),
            array_values($request->input('args')),
        );

        return response()->json([
            'result' => $result,
            'state' => $component->getRadioState(),
        ]);
    }
}
