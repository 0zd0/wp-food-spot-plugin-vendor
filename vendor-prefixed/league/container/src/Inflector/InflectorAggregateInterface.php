<?php

declare(strict_types=1);

namespace Onepix\FoodSpotVendor\League\Container\Inflector;

use IteratorAggregate;
use Onepix\FoodSpotVendor\League\Container\ContainerAwareInterface;

interface InflectorAggregateInterface extends ContainerAwareInterface, IteratorAggregate
{
    public function add(string $type, ?callable $callback = null): Inflector;
    public function inflect(object $object);
}
