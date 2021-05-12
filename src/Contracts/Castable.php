<?php

declare(strict_types = 1);

namespace Radio\Contracts;

interface Castable
{
    public static function fromRadio($value);

    public function toRadio();
}
