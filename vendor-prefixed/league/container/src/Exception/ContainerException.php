<?php

declare(strict_types=1);

namespace Onepix\FoodSpotVendor\League\Container\Exception;

use Onepix\FoodSpotVendor\Psr\Container\ContainerExceptionInterface;
use RuntimeException;

class ContainerException extends RuntimeException implements ContainerExceptionInterface
{
}
