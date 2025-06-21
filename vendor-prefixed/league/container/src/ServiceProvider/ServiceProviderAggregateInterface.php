<?php

declare(strict_types=1);

namespace Onepix\FoodSpotVendor\League\Container\ServiceProvider;

use IteratorAggregate;
use Onepix\FoodSpotVendor\League\Container\ContainerAwareInterface;

interface ServiceProviderAggregateInterface extends ContainerAwareInterface, IteratorAggregate
{
    public function add(ServiceProviderInterface $provider): ServiceProviderAggregateInterface;
    public function provides(string $id): bool;
    public function register(string $service): void;
}
