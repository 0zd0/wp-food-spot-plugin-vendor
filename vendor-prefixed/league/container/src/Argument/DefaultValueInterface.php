<?php

declare(strict_types=1);

namespace Onepix\FoodSpotVendor\League\Container\Argument;

interface DefaultValueInterface extends ArgumentInterface
{
    /**
     * @return mixed
     */
    public function getDefaultValue();
}
