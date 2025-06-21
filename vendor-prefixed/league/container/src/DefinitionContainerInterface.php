<?php

declare(strict_types=1);

namespace Onepix\FoodSpotVendor\League\Container;

use Onepix\FoodSpotVendor\League\Container\Definition\DefinitionInterface;
use Onepix\FoodSpotVendor\League\Container\Inflector\InflectorInterface;
use Onepix\FoodSpotVendor\League\Container\ServiceProvider\ServiceProviderInterface;
use Onepix\FoodSpotVendor\Psr\Container\ContainerInterface;

interface DefinitionContainerInterface extends ContainerInterface
{
    public function add(string $id, $concrete = null): DefinitionInterface;
    public function addServiceProvider(ServiceProviderInterface $provider): self;
    public function addShared(string $id, $concrete = null): DefinitionInterface;
    public function extend(string $id): DefinitionInterface;
    public function getNew($id);
    public function inflector(string $type, ?callable $callback = null): InflectorInterface;
}
