<?php

namespace Onepix\FoodSpotVendor\Illuminate\Contracts\Cache;

interface Factory
{
    /**
     * Get a cache store instance by name.
     *
     * @param  string|null  $name
     * @return \Onepix\FoodSpotVendor\Illuminate\Contracts\Cache\Repository
     */
    public function store($name = null);
}
