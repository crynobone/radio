<?php

declare(strict_types = 1);

namespace Radio;

use Radio\Contracts\Castable;
use Exception;
use Illuminate\Database\Eloquent\Collection as EloquentCollection;
use Illuminate\Support\Collection;
use Illuminate\Support\Stringable;
use ReflectionClass;
use ReflectionMethod;
use ReflectionProperty;

trait Radio
{
    public function __invoke()
    {
        //
    }

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

    public function hydrateRadioState(array $state = []): void
    {
        $reflection = $this->getReflection();

        foreach ($state as $key => $value) {
            if (! property_exists($this, $key)) continue;

            $property = $reflection->getProperty($key);

            if ($property->hasType()) {
                $type = $property->getType()->getName();

                if ($type === Collection::class) {
                    $value = Collection::make($value);
                } elseif ($type === EloquentCollection::class) {
                    $value = EloquentCollection::make($value);
                } elseif ($type === Stringable::class) {
                    $value = new Stringable($value);
                } elseif (class_exists($type) && in_array(Castable::class, class_implements($type))) {
                    $value = $type::fromRadio($value);
                }
            }

            $this->{$key} = $value;
        }
    }

    public function dehydrateRadioMethods(): array
    {
        return [
            'methods' => collect(
                    $this->getReflection()->getMethods(ReflectionMethod::IS_PUBLIC)
                )
                    ->filter(fn (ReflectionMethod $method) => $this->isRadioCallableMethodName($method->getName()))
                    ->map(fn (ReflectionMethod $method) => $method->getName())
                    ->values(),
        ];
    }

    public function dehydrateRadioState(): array
    {
        return [
            'state' => collect(
                    $this->getReflection()->getProperties(ReflectionProperty::IS_PUBLIC)
                )->mapWithKeys(function (ReflectionProperty $property) {
                    $value = $property->getValue($this);

                    if ($value instanceof Stringable) {
                        $value = $value->__toString();
                    } elseif ($value instanceof Castable) {
                        $value = $value->toRadio();
                    }

                    return [$property->getName() => $value];
                }),
        ];
    }

    public function dehydrateRadioData(): array
    {
        $data = collect(
            $this->getReflection()->getMethods()
        )
            ->filter(fn (ReflectionMethod $method) => $this->isRadioDehydrationMethodName($method->getName()))
            ->map(fn (ReflectionMethod $method) => $method->invoke($this))
            ->values()
            ->toArray();

        return array_merge(...$data);
    }

    protected function isRadioCallableMethodName(string $name): bool
    {
        return ! str_starts_with($name, '__') && ! method_exists(Radio::class, $name) && ! str_starts_with($name, 'dehydrateRadio');
    }

    protected function isRadioDehydrationMethodName(string $name): bool
    {
        return str_starts_with($name, 'dehydrateRadio') && $name !== 'dehydrateRadioData';
    }

    protected function getReflection(): ReflectionClass
    {
        return new ReflectionClass($this);
    }
}
