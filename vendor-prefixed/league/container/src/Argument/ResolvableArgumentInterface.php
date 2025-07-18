<?php

declare(strict_types=1);

namespace Onepix\FoodSpotVendor\League\Container\Argument;

interface ResolvableArgumentInterface extends ArgumentInterface
{
    public function getValue(): string;
}
