<?php

namespace Onepix\FoodSpotVendor\Illuminate\Contracts\Support;

interface Renderable
{
    /**
     * Get the evaluated contents of the object.
     *
     * @return string
     */
    public function render();
}
