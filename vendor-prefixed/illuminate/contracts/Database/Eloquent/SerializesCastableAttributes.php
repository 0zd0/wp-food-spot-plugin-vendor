<?php

namespace Onepix\FoodSpotVendor\Illuminate\Contracts\Database\Eloquent;

use Onepix\FoodSpotVendor\Illuminate\Database\Eloquent\Model;

interface SerializesCastableAttributes
{
    /**
     * Serialize the attribute when converting the model to an array.
     *
     * @param  \Onepix\FoodSpotVendor\Illuminate\Database\Eloquent\Model  $model
     * @param  string  $key
     * @param  mixed  $value
     * @param  array<string, mixed>  $attributes
     * @return mixed
     */
    public function serialize(Model $model, string $key, mixed $value, array $attributes);
}
