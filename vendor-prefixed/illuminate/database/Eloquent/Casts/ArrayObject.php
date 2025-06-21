<?php

namespace Onepix\FoodSpotVendor\Illuminate\Database\Eloquent\Casts;

use ArrayObject as BaseArrayObject;
use Onepix\FoodSpotVendor\Illuminate\Contracts\Support\Arrayable;
use Onepix\FoodSpotVendor\Illuminate\Support\Collection;
use JsonSerializable;

/**
 * @template TKey of array-key
 * @template TItem
 *
 * @extends  \ArrayObject<TKey, TItem>
 */
class ArrayObject extends BaseArrayObject implements Arrayable, JsonSerializable
{
    /**
     * Get a collection containing the underlying array.
     *
     * @return \Onepix\FoodSpotVendor\Illuminate\Support\Collection
     */
    public function onepix_foodspotvendor_collect()
    {
        return new Collection($this->getArrayCopy());
    }

    /**
     * Get the instance as an array.
     *
     * @return array
     */
    public function toArray()
    {
        return $this->getArrayCopy();
    }

    /**
     * Get the array that should be JSON serialized.
     *
     * @return array
     */
    public function jsonSerialize(): array
    {
        return $this->getArrayCopy();
    }
}
