<?php

declare(strict_types=1);

namespace Onepix\FoodSpotVendor\League\Container\Definition;

use Onepix\FoodSpotVendor\League\Container\ContainerAwareInterface;

interface DefinitionInterface extends ContainerAwareInterface
{
    public function addArgument($arg): DefinitionInterface;
    public function addArguments(array $args): DefinitionInterface;
    public function addMethodCall(string $method, array $args = []): DefinitionInterface;
    public function addMethodCalls(array $methods = []): DefinitionInterface;
    public function addTag(string $tag): DefinitionInterface;
    public function getAlias(): string;
    public function getConcrete();
    public function hasTag(string $tag): bool;
    public function isShared(): bool;
    public function resolve();
    public function resolveNew();
    public function setAlias(string $id): DefinitionInterface;
    public function setConcrete($concrete): DefinitionInterface;
    public function setShared(bool $shared): DefinitionInterface;
}
