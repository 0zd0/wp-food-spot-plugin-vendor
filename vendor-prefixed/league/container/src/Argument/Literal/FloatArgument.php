<?php

declare(strict_types=1);

namespace Onepix\FoodSpotVendor\League\Container\Argument\Literal;

use Onepix\FoodSpotVendor\League\Container\Argument\LiteralArgument;

class FloatArgument extends LiteralArgument
{
    public function __construct(float $value)
    {
        parent::__construct($value, LiteralArgument::TYPE_FLOAT);
    }
}
