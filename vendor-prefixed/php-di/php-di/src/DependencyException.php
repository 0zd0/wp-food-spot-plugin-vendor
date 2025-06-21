<?php

declare(strict_types=1);

namespace Onepix\FoodSpotVendor\DI;

use Onepix\FoodSpotVendor\Psr\Container\ContainerExceptionInterface;

/**
 * Exception for the Container.
 */
class DependencyException extends \Exception implements ContainerExceptionInterface
{
}
