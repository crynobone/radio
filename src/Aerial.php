<?php

declare(strict_types = 1);

namespace Aerial;

use Aerial\Contracts\Castable;
use Exception;
use Illuminate\Database\Eloquent\Collection as EloquentCollection;
use Illuminate\Support\Collection;
use Illuminate\Support\Stringable;
use ReflectionClass;
use ReflectionMethod;
use ReflectionProperty;

trait Aerial
{
    public function callAerialMethod(string $method, array $args = [])
    {
        if (! method_exists($this, $method)) {
            throw new Exception(
                sprintf(
                    '[Aerial] Method %s does not exist on component %s.',
                    $method,
                    static::class,
                ),
            );
        }

        return $this->{$method}(
            ...$args
        );
    }

    public function hydrateAerialState(array $state = []): void
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
                    $value = $type::fromAerial($value);
                }
            }

            $this->{$key} = $value;
        }
    }

    public function getAerialMethods(): Collection
    {
        return collect(
            $this->getReflection()->getMethods(ReflectionMethod::IS_PUBLIC)
        )->filter(function (ReflectionMethod $method) {
            return ! str_starts_with($method->getName(), '__');
        })->map(function (ReflectionMethod $method) {
            return $method->getName();
        })->values();
    }

    public function getAerialState(): Collection
    {
        return collect(
            $this->getReflection()->getProperties(ReflectionProperty::IS_PUBLIC)
        )->mapWithKeys(function (ReflectionProperty $property) {
            $value = $property->getValue($this);

            if ($value instanceof Stringable) {
                $value = $value->__toString();
            } elseif ($value instanceof Castable) {
                $value = $value->toAerial();
            }

            return [$property->getName() => $value];
        });
    }

    protected function getReflection(): ReflectionClass
    {
        return new ReflectionClass($this);
    }
}
