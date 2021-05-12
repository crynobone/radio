<?php

declare(strict_types = 1);

namespace Aerial;

use Exception;
use Illuminate\Support\Facades\URL;

if (! function_exists('Aerial\aerial')) {
    function aerial(string $component, array $data = []): void {
        if (! class_exists($component)) {
            throw new Exception("[Aerial] Class `{$component}` does not exist.");
        }

        $component = app($component);

        app()->call($component, $data);

        $constructor = htmlspecialchars(sprintf(
            'Aerial.mount("%s", %s, %s, "%s")',
            addslashes($component::class),
            $component->getAerialState()->toJson(),
            $component->getAerialMethods()->toJson(),
            URL::signedRoute('aerial.call'),
        ), ENT_QUOTES);

        echo $constructor;
    }
}
