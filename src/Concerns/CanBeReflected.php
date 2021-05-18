<?php

declare(strict_types = 1);

namespace Radio\Concerns;

use ReflectionClass;

trait CanBeReflected
{
    protected function getReflection(): ReflectionClass
    {
        return new ReflectionClass($this);
    }
}