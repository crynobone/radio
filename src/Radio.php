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
        $data = collect(
            $this->getReflection()->getMethods(),
        )
            ->filter(fn (ReflectionMethod $method) => $this->isRadioDehydrationMethodName($method->getName()))
            ->map(fn (ReflectionMethod $method) => $method->invoke($this))
            ->values()
            ->toArray();

        return array_merge(...$data);
    }

    protected function isRadioDehydrationMethodName(string $name): bool
    {
        return str_starts_with($name, 'dehydrateRadio') && $name !== 'dehydrateRadioData';
    }
}
