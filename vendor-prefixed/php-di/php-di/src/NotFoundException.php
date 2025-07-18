<?php

declare(strict_types=1);

namespace Onepix\FoodSpotVendor\DI;

use Onepix\FoodSpotVendor\Psr\Container\NotFoundExceptionInterface;

/**
 * Exception thrown when a class or a value is not found in the container.
 */
class NotFoundException extends \Exception implements NotFoundExceptionInterface
{
}
