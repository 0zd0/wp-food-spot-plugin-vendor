<?php

namespace Onepix\FoodSpotVendor\Illuminate\Support;

if (! function_exists('Onepix\FoodSpotVendor\Illuminate\Support\enum_value')) {
    /**
     * Return a scalar value for the given value that might be an enum.
     *
     * @internal
     *
     * @template TValue
     * @template TDefault
     *
     * @param  TValue  $value
     * @param  TDefault|callable(TValue): TDefault  $default
     * @return ($value is empty ? TDefault : mixed)
     */
    function enum_value($value, $default = null)
    {
        return match (true) {
            $value instanceof \BackedEnum => $value->value,
            $value instanceof \UnitEnum => $value->name,

            default => $value ?? onepix_foodspotvendor_value($default),
        };
    }
}
