<?php

namespace Onepix\FoodSpotVendor\Illuminate\Contracts\Queue;

interface Factory
{
    /**
     * Resolve a queue connection instance.
     *
     * @param  string|null  $name
     * @return \Onepix\FoodSpotVendor\Illuminate\Contracts\Queue\Queue
     */
    public function connection($name = null);
}
