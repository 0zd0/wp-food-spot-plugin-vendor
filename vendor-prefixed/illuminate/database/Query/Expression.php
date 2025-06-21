<?php

namespace Onepix\FoodSpotVendor\Illuminate\Database\Query;

use Onepix\FoodSpotVendor\Illuminate\Contracts\Database\Query\Expression as ExpressionContract;
use Onepix\FoodSpotVendor\Illuminate\Database\Grammar;

/**
 * @template TValue of string|int|float
 */
class Expression implements ExpressionContract
{
    /**
     * Create a new raw query expression.
     *
     * @param  TValue  $value
     * @return void
     */
    public function __construct(
        protected $value
    ) {
    }

    /**
     * Get the value of the expression.
     *
     * @param  \Onepix\FoodSpotVendor\Illuminate\Database\Grammar  $grammar
     * @return TValue
     */
    public function getValue(Grammar $grammar)
    {
        return $this->value;
    }
}
