<?php

namespace Onepix\FoodSpotVendor\Illuminate\Contracts\Database\Query;

use Onepix\FoodSpotVendor\Illuminate\Database\Grammar;

interface Expression
{
    /**
     * Get the value of the expression.
     *
     * @param  \Onepix\FoodSpotVendor\Illuminate\Database\Grammar  $grammar
     * @return string|int|float
     */
    public function getValue(Grammar $grammar);
}
