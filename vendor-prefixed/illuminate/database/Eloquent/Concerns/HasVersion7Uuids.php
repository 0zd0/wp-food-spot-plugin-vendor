<?php

namespace Onepix\FoodSpotVendor\Illuminate\Database\Eloquent\Concerns;

use Onepix\FoodSpotVendor\Illuminate\Support\Str;

trait HasVersion7Uuids
{
    use HasUuids;

    /**
     * Generate a new UUID (version 7) for the model.
     *
     * @return string
     */
    public function newUniqueId()
    {
        return (string) Str::uuid7();
    }
}
