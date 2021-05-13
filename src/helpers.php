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

        $constructor = htmlspecialchars(sprintf(
            'Radio.mount("%s", %s, %s, "%s")',
            addslashes($component::class),
            $component->getRadioState()->toJson(),
            $component->getRadioMethods()->toJson(),
            URL::signedRoute('radio.call'),
        ), ENT_QUOTES);

        echo $constructor;
    }
}
