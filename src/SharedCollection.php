<?php

namespace LuisaeDev\Spaceship;

use Illuminate\Support\Collection;

class SharedCollection
{
    /** @param string Attribute name that is used for specify the keys at the shared collection */
    protected static $keyName = 'id';

    /** @param Collection Shared collection */
    private static $collection;

    /** @param mixed Pivot key that specified wich model is currently used */
    private mixed $pivotKey;

    /**
     * Add a model and set as used at the shared collection.
     *
     * @param  mixed  $model
     * @return void
     */
    protected function addModel(mixed $model): void
    {
        $this->pivotKey = self::registerModel($model);
    }

    /**
     * Define wich model is used from the shared collection.
     *
     * @param  string|array  $key
     * @return void
     */
    protected function useModel(string|array $key): void
    {
        // If the specified key is an array, search the model for a specific attribute
        if (is_array($key)) {
            $model = self::collection()->firstWhere($key[0], $key[1]);

        // Else, the model is obtained by its collection's key
        } else {
            $model = self::collection()->get($key);
        }

        // Define the pivot key related to the current used model
        if ($model) {
            $this->pivotKey = $model->{self::$keyName};
        } else {
            $this->pivotKey = null;
        }
    }

    /**
     * Return the used model from the shared collection.
     *
     * @return mixed
     */
    protected function getModel(): mixed
    {
        if (! is_null($this->pivotKey)) {
            return self::collection()->get($this->pivotKey);
        } else {
            return null;
        }
    }

    /**
     * Check if there is a used model and if the model exists.
     *
     * @return bool
     */
    protected function hasModel(): bool
    {
        if ($this->getModel()) {
            return true;
        } else {
            return false;
        }
    }

    /**
     * Forget the current used model from the shared collection, and clear the pivot key.
     *
     * @return void
     */
    protected function forgetModel(): void
    {
        if ($this->hasModel()) {
            self::collection()->forget($this->pivotKey);
        }
        $this->pivotKey = null;
    }

    /**
     * Register a model at the shared collection.
     *
     * @param  mixed  $model
     * @return mixed
     */
    public static function registerModel(mixed $model): mixed
    {
        if (self::collection()->doesntContain($model)) {
            self::collection()->put($model->{self::$keyName}, $model);
        }

        return $model->{self::$keyName};
    }

    /**
     * Return the shared collection.
     *
     * @return Collection
     */
    private static function collection(): Collection
    {
        if (is_null(self::$collection)) {
            self::$collection = collect();
        }

        return self::$collection;
    }
}
