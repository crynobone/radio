<?php

declare(strict_types = 1);

namespace Aerial\Contracts;

interface Castable
{
    public static function fromAerial($value);

    public function toAerial();
}
