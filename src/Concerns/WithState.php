<?php

declare(strict_types = 1);

namespace Radio\Concerns;

use Illuminate\Database\Eloquent\Collection as EloquentCollection;
use Illuminate\Support\Collection;
use Illuminate\Support\Stringable;
use Radio\Contracts\Castable;
use ReflectionProperty;

trait WithState
{
    public function hydrateRadioState(array $state = []): void
    {
        $this->callRadioHook('hydrating');

        $reflection = $this->getReflection();

        foreach ($state as $key => $value) {
            if (! property_exists($this, $key)) continue;

            $property = $reflection->getProperty($key);

            $this->{$key} = $this->transformRadioPropertyValueForHydration(
                $value,
                $property->hasType() ? $property->getType()->getName() : null,
            );
        }

        $this->callRadioHook('hydrated');
    }

    protected function transformRadioPropertyValueForHydration($value, ?string $type = null)
    {
        if ($type) {
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

        return $value;
    }

    public function dehydrateRadioState(): array
    {
        $state = collect(
            $this->getReflection()->getProperties(ReflectionProperty::IS_PUBLIC),
        )
            ->mapWithKeys(function (ReflectionProperty $property) {
                return [$property->getName() => $this->transformRadioPropertyValueForDehydration(
                    $property->getValue($this),
                )];
            });

        return ['state' => $state];
    }

    protected function transformRadioPropertyValueForDehydration($value)
    {
        if ($value instanceof Stringable) {
            $value = $value->__toString();
        } elseif ($value instanceof Castable) {
            $value = $value->toRadio();
        }

        return $value;
    }
}