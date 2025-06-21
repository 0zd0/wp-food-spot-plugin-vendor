<?php

namespace Onepix\FoodSpotVendor\Illuminate\Database\Eloquent\Casts;

use Onepix\FoodSpotVendor\Illuminate\Contracts\Database\Eloquent\Castable;
use Onepix\FoodSpotVendor\Illuminate\Contracts\Database\Eloquent\CastsAttributes;
use Onepix\FoodSpotVendor\Illuminate\Support\Facades\Crypt;

class AsEncryptedArrayObject implements Castable
{
    /**
     * Get the caster class to use when casting from / to this cast target.
     *
     * @param  array  $arguments
     * @return \Onepix\FoodSpotVendor\Illuminate\Contracts\Database\Eloquent\CastsAttributes<\Onepix\FoodSpotVendor\Illuminate\Database\Eloquent\Casts\ArrayObject<array-key, mixed>, iterable>
     */
    public static function castUsing(array $arguments)
    {
        return new class implements CastsAttributes
        {
            public function get($model, $key, $value, $attributes)
            {
                if (isset($attributes[$key])) {
                    return new ArrayObject(Json::decode(Crypt::decryptString($attributes[$key])));
                }

                return null;
            }

            public function set($model, $key, $value, $attributes)
            {
                if (! is_null($value)) {
                    return [$key => Crypt::encryptString(Json::encode($value))];
                }

                return null;
            }

            public function serialize($model, string $key, $value, array $attributes)
            {
                return ! is_null($value) ? $value->getArrayCopy() : null;
            }
        };
    }
}
