<?php

namespace Onepix\FoodSpotVendor\Illuminate\Events;

use Closure;

if (! function_exists('Onepix\FoodSpotVendor\Illuminate\Events\queueable')) {
    /**
     * Create a new queued Closure event listener.
     *
     * @param  \Closure  $closure
     * @return \Onepix\FoodSpotVendor\Illuminate\Events\QueuedClosure
     */
    function queueable(Closure $closure)
    {
        return new QueuedClosure($closure);
    }
}
