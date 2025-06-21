<?php

declare(strict_types=1);

namespace Onepix\FoodSpotVendor\League\Container\Exception;

use Onepix\FoodSpotVendor\Psr\Container\NotFoundExceptionInterface;
use InvalidArgumentException;

class NotFoundException extends InvalidArgumentException implements NotFoundExceptionInterface
{
}
