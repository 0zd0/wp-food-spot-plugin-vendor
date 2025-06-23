<?php

namespace Onepix\FoodSpotVendor\Illuminate\Support\Traits;

trait Tappable
{
    /**
     * Call the given Closure with this instance then return the instance.
     *
     * @param  (callable($this): mixed)|null  $callback
     * @return ($callback is null ? \Onepix\FoodSpotVendor\Illuminate\Support\HigherOrderTapProxy : $this)
     */
    public function tap($callback = null)
    {
        return onepix_foodspotvendor_tap($this, $callback);
    }
}
