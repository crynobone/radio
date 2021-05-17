<?php

declare(strict_types = 1);

namespace Radio;

use Exception;
use Illuminate\Support\Facades\URL;

if (! function_exists('Radio\radio')) {
    function radio(string $component, array $data = []): void {
        if (! class_exists($component)) {
            throw new Exception("[Radio] Class `{$component}` does not exist.");
        }

        $component = app($component);

        if (method_exists($component, '__invoke')) {
            app()->call($component, $data);
        }

        $args = json_encode(array_merge([
            'component' => $component::class,
            'url' => URL::signedRoute('radio.call'),
        ], $component->dehydrateRadioData()));

        $constructor = htmlspecialchars("Radio.mount({$args})");

        echo $constructor;
    }
}
