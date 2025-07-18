<?php

namespace Onepix\FoodSpotVendor\Illuminate\Contracts\Filesystem;

interface Factory
{
    /**
     * Get a filesystem implementation.
     *
     * @param  string|null  $name
     * @return \Onepix\FoodSpotVendor\Illuminate\Contracts\Filesystem\Filesystem
     */
    public function disk($name = null);
}
