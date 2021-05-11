<?php

declare(strict_types = 1);

namespace Aerial\Contracts;

interface Castable
{
    public function toAerial();

    public static function fromAerial($value);
}
