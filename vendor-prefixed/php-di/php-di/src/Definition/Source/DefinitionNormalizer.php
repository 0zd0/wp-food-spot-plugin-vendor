<?php

declare(strict_types=1);

namespace Onepix\FoodSpotVendor\DI\Definition\Source;

use Onepix\FoodSpotVendor\DI\Definition\ArrayDefinition;
use Onepix\FoodSpotVendor\DI\Definition\AutowireDefinition;
use Onepix\FoodSpotVendor\DI\Definition\DecoratorDefinition;
use Onepix\FoodSpotVendor\DI\Definition\Definition;
use Onepix\FoodSpotVendor\DI\Definition\Exception\InvalidDefinition;
use Onepix\FoodSpotVendor\DI\Definition\FactoryDefinition;
use Onepix\FoodSpotVendor\DI\Definition\Helper\DefinitionHelper;
use Onepix\FoodSpotVendor\DI\Definition\ObjectDefinition;
use Onepix\FoodSpotVendor\DI\Definition\ValueDefinition;

/**
 * Turns raw definitions/definition helpers into definitions ready
 * to be resolved or compiled.
 *
 * @author Matthieu Napoli <matthieu@mnapoli.fr>
 */
class DefinitionNormalizer
{
    public function __construct(
        private Autowiring $autowiring,
    ) {
    }

    /**
     * Normalize a definition that is *not* nested in another one.
     *
     * This is usually a definition declared at the root of a definition array.
     *
     * @param string $name The definition name.
     * @param string[] $wildcardsReplacements Replacements for wildcard definitions.
     *
     * @throws InvalidDefinition
     */
    public function normalizeRootDefinition(mixed $definition, string $name, ?array $wildcardsReplacements = null) : Definition
    {
        if ($definition instanceof DefinitionHelper) {
            $definition = $definition->getDefinition($name);
        } elseif (is_array($definition)) {
            $definition = new ArrayDefinition($definition);
        } elseif ($definition instanceof \Closure) {
            $definition = new FactoryDefinition($name, $definition);
        } elseif (! $definition instanceof Definition) {
            $definition = new ValueDefinition($definition);
        }

        // For a class definition, we replace * in the class name with the matches
        // *Interface -> *Impl => FooInterface -> FooImpl
        if ($wildcardsReplacements && $definition instanceof ObjectDefinition) {
            $definition->replaceWildcards($wildcardsReplacements);
        }

        if ($definition instanceof AutowireDefinition) {
            /** @var AutowireDefinition $definition */
            $definition = $this->autowiring->autowire($name, $definition);
        }

        $definition->setName($name);

        try {
            $definition->replaceNestedDefinitions([$this, 'normalizeNestedDefinition']);
        } catch (InvalidDefinition $e) {
            throw InvalidDefinition::create($definition, sprintf(
                'Definition "%s" contains an error: %s',
                $definition->getName(),
                $e->getMessage()
            ), $e);
        }

        return $definition;
    }

    /**
     * Normalize a definition that is nested in another one.
     *
     * @throws InvalidDefinition
     */
    public function normalizeNestedDefinition(mixed $definition) : mixed
    {
        $name = '<nested definition>';

        if ($definition instanceof DefinitionHelper) {
            $definition = $definition->getDefinition($name);
        } elseif (is_array($definition)) {
            $definition = new ArrayDefinition($definition);
        } elseif ($definition instanceof \Closure) {
            $definition = new FactoryDefinition($name, $definition);
        }

        if ($definition instanceof DecoratorDefinition) {
            throw new InvalidDefinition('Decorators cannot be nested in another definition');
        }

        if ($definition instanceof AutowireDefinition) {
            $definition = $this->autowiring->autowire($name, $definition);
        }

        if ($definition instanceof Definition) {
            $definition->setName($name);

            // Recursively traverse nested definitions
            $definition->replaceNestedDefinitions([$this, 'normalizeNestedDefinition']);
        }

        return $definition;
    }
}
