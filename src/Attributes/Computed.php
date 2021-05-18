<?php

declare(strict_types = 1);

namespace Radio\Attributes;

use Attribute;

#[Attribute(Attribute::TARGET_PROPERTY)]
class Computed
{
    public function __construct(
        public string $method,
    ) {}
}