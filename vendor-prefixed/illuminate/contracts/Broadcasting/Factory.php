<?php

namespace Onepix\FoodSpotVendor\Illuminate\Contracts\Broadcasting;

interface Factory
{
    /**
     * Get a broadcaster implementation by name.
     *
     * @param  string|null  $name
     * @return \Onepix\FoodSpotVendor\Illuminate\Contracts\Broadcasting\Broadcaster
     */
    public function connection($name = null);
}
