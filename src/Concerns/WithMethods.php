<?php

declare(strict_types = 1);

namespace Radio\Concerns;

use Exception;
use Radio\Radio;
use ReflectionMethod;

trait WithMethods
{
    public function callRadioMethod(string $method, array $args = [])
    {
        if (! method_exists($this, $method)) {
            throw new Exception(
                sprintf(
                    '[Radio] Method `%s` does not exist on component `%s`.',
                    $method,
                    static::class,
                ),
            );
        }

        return $this->{$method}(
            ...$args
        );
    }

    public function dehydrateRadioMethods(): array
    {
        $methods = collect(
            $this->getReflection()->getMethods(ReflectionMethod::IS_PUBLIC),
        )
            ->filter(fn (ReflectionMethod $method) => $this->isRadioCallableMethodName($method->getName()))
            ->map(fn (ReflectionMethod $method) => $method->getName())
            ->values();

        return ['methods' => $methods];
    }

    protected function isRadioCallableMethodName(string $name): bool
    {
        return ! str_starts_with($name, '__') && ! method_exists(Radio::class, $name) && ! str_starts_with($name, 'dehydrateRadio');
    }
}