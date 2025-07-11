<?php

declare(strict_types=1);

namespace Onepix\FoodSpotVendor\DI\Definition;

use Onepix\FoodSpotVendor\DI\DependencyException;
use Onepix\FoodSpotVendor\Psr\Container\ContainerInterface;
use Onepix\FoodSpotVendor\Psr\Container\NotFoundExceptionInterface;

/**
 * Definition of a string composed of other strings.
 *
 * @since 5.0
 * @author Matthieu Napoli <matthieu@mnapoli.fr>
 */
class StringDefinition implements Definition, SelfResolvingDefinition
{
    /** Entry name. */
    private string $name = '';

    public function __construct(
        private string $expression,
    ) {
    }

    public function getName() : string
    {
        return $this->name;
    }

    public function setName(string $name) : void
    {
        $this->name = $name;
    }

    public function getExpression() : string
    {
        return $this->expression;
    }

    public function resolve(ContainerInterface $container) : string
    {
        return self::resolveExpression($this->name, $this->expression, $container);
    }

    public function isResolvable(ContainerInterface $container) : bool
    {
        return true;
    }

    public function replaceNestedDefinitions(callable $replacer) : void
    {
        // no nested definitions
    }

    public function __toString() : string
    {
        return $this->expression;
    }

    /**
     * Resolve a string expression.
     */
    public static function resolveExpression(
        string $entryName,
        string $expression,
        ContainerInterface $container,
    ) : string {
        $callback = function (array $matches) use ($entryName, $container) {
            /** @psalm-suppress InvalidCatch */
            try {
                return $container->get($matches[1]);
            } catch (NotFoundExceptionInterface $e) {
                throw new DependencyException(sprintf(
                    "Error while parsing string expression for entry '%s': %s",
                    $entryName,
                    $e->getMessage()
                ), 0, $e);
            }
        };

        $result = preg_replace_callback('#\{([^{}]+)}#', $callback, $expression);
        if ($result === null) {
            throw new \RuntimeException(sprintf('An unknown error occurred while parsing the string definition: \'%s\'', $expression));
        }

        return $result;
    }
}
