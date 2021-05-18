<?php

declare(strict_types = 1);

namespace Radio;

use Radio\Concerns;
use ReflectionMethod;

trait Radio
{
    use Concerns\CanBeReflected;
    use Concerns\WithMethods;
    use Concerns\WithState;

    public function __invoke()
    {
        //
    }

    public function dehydrateRadioData(): array
    {
        $this->callRadioHook('dehydrating');

        $data = collect(
            $this->getReflection()->getMethods(),
        )
            ->filter(fn (ReflectionMethod $method) => $this->isRadioDehydrationMethodName($method->getName()))
            ->map(fn (ReflectionMethod $method) => $method->invoke($this))
            ->values()
            ->toArray();

        try {
            return array_merge(...$data);
        } finally {
            $this->callRadioHook('dehydrated');
        }
    }

    protected function isRadioDehydrationMethodName(string $name): bool
    {
        return str_starts_with($name, 'dehydrateRadio') && $name !== 'dehydrateRadioData';
    }

    protected function callRadioHook(string $name, ...$args): void
    {
        if (! method_exists(static::class, $name)) return;

        $this->{$name}(...$args);
    }
}
