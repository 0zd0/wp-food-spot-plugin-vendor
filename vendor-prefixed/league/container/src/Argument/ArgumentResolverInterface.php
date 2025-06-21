<?php

declare(strict_types=1);

namespace Onepix\FoodSpotVendor\League\Container\Argument;

use Onepix\FoodSpotVendor\League\Container\ContainerAwareInterface;
use ReflectionFunctionAbstract;

interface ArgumentResolverInterface extends ContainerAwareInterface
{
    public function resolveArguments(array $arguments): array;
    public function reflectArguments(ReflectionFunctionAbstract $method, array $args = []): array;
}
