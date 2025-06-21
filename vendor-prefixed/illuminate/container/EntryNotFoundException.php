<?php

namespace Onepix\FoodSpotVendor\Illuminate\Container;

use Exception;
use Onepix\FoodSpotVendor\Psr\Container\NotFoundExceptionInterface;

class EntryNotFoundException extends Exception implements NotFoundExceptionInterface
{
    //
}
