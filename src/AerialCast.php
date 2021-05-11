<?php

declare(strict_types = 1);

namespace Aerial;

interface AerialCast
{
    public function toAerial();

    public static function fromAerial($value);
}
