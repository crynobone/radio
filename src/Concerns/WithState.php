<?php

declare(strict_types = 1);

namespace Radio\Concerns;

use Illuminate\Database\Eloquent\Collection as EloquentCollection;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Collection;
use Illuminate\Support\Stringable;
use Radio\Attributes\Computed;
use Radio\Contracts\Castable;
use ReflectionProperty;

trait WithState
{
    public function hydrateRadioState(array $state = [], array $meta = []): void
    {
        $this->callRadioHook('hydrating');

        $reflection = $this->getReflection();

        foreach ($state as $key => $value) {
            if (! property_exists($this, $key)) continue;

            /** @var \ReflectionProperty $property */
            $property = $reflection->getProperty($key);

            $this->{$key} = $this->transformRadioPropertyValueForHydration(
                $key,
                $value,
                $property->hasType() ? $property->getType()->getName() : null,
                $meta
            );
        }

        $this->callRadioHook('hydrated');
    }

    protected function transformRadioPropertyValueForHydration(string $key, $value, ?string $type = null, array $meta = [])
    {
        if ($type) {
            if (is_subclass_of($type, Model::class) && $key = data_get($meta, "models.{$key}.key") && $columns = data_get($meta, "models.{$key}.columns")) {
                $model = $type::findOrFail($key);
                
                foreach ($value as $column => $data) {
                    if (! array_key_exists($column, $columns)) continue;
                    if ($column === $model->getKeyName()) continue;

                    $model->{$column} = $data;
                }

                $value = $model;
            } elseif ($type === Collection::class) {
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
        $models = collect();

        $state = collect(
            $this->getReflection()->getProperties(ReflectionProperty::IS_PUBLIC),
        )
            ->mapWithKeys(function (ReflectionProperty $property) {
                return [$property->getName() => $this->transformRadioPropertyValueForDehydration(
                    $property->getValue($this),
                    $property->getAttributes()
                )];
            })
            ->each(function ($value, string $key) use ($models) {
                if ($value instanceof Model) {
                    $models[$key] = [
                        'key' => $value->getKey(),
                        'columns' => $value->attributesToArray()
                    ];
                }
            });

        return [
            'state' => $state,
            'models' => $models,
        ];
    }

    protected function transformRadioPropertyValueForDehydration($value, array $attributes = [])
    {
        if ($value instanceof Stringable) {
            $value = $value->__toString();
        } elseif ($value instanceof Castable) {
            $value = $value->toRadio();
        }

        /** @var \ReflectionAttribute[] $attributes */
        foreach ($attributes as $attribute) {
            $attribute = $attribute->newInstance();

            if ($attribute instanceof Computed) {
                $value = $this->{$attribute->method}();
            }
        }

        return $value;
    }
}