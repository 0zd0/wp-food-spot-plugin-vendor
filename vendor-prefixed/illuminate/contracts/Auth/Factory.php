<?php

namespace Onepix\FoodSpotVendor\Illuminate\Contracts\Auth;

interface Factory
{
    /**
     * Get a guard instance by name.
     *
     * @param  string|null  $name
     * @return \Onepix\FoodSpotVendor\Illuminate\Contracts\Auth\Guard|\Onepix\FoodSpotVendor\Illuminate\Contracts\Auth\StatefulGuard
     */
    public function guard($name = null);

    /**
     * Set the default guard the factory should serve.
     *
     * @param  string  $name
     * @return void
     */
    public function shouldUse($name);
}
