<?php

namespace Onepix\FoodSpotVendor\Illuminate\Contracts\Database\Eloquent;

use Onepix\FoodSpotVendor\Illuminate\Database\Eloquent\Model;

interface CastsInboundAttributes
{
    /**
     * Transform the attribute to its underlying model values.
     *
     * @param  \Onepix\FoodSpotVendor\Illuminate\Database\Eloquent\Model  $model
     * @param  string  $key
     * @param  mixed  $value
     * @param  array<string, mixed>  $attributes
     * @return mixed
     */
    public function set(Model $model, string $key, mixed $value, array $attributes);
}
