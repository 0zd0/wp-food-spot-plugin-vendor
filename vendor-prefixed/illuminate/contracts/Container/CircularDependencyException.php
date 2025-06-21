<?php

namespace Onepix\FoodSpotVendor\Illuminate\Contracts\Container;

use Exception;
use Onepix\FoodSpotVendor\Psr\Container\ContainerExceptionInterface;

class CircularDependencyException extends Exception implements ContainerExceptionInterface
{
    //
}
