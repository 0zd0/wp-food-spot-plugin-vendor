<?php

declare(strict_types=1);

namespace Onepix\FoodSpotVendor\DI;

use Onepix\FoodSpotVendor\DI\Definition\ArrayDefinitionExtension;
use Onepix\FoodSpotVendor\DI\Definition\EnvironmentVariableDefinition;
use Onepix\FoodSpotVendor\DI\Definition\Helper\AutowireDefinitionHelper;
use Onepix\FoodSpotVendor\DI\Definition\Helper\CreateDefinitionHelper;
use Onepix\FoodSpotVendor\DI\Definition\Helper\FactoryDefinitionHelper;
use Onepix\FoodSpotVendor\DI\Definition\Reference;
use Onepix\FoodSpotVendor\DI\Definition\StringDefinition;
use Onepix\FoodSpotVendor\DI\Definition\ValueDefinition;

if (! function_exists('Onepix\FoodSpotVendor\DI\value')) {
    /**
     * Helper for defining a value.
     */
    function onepix_foodspotvendor_value(mixed $value) : ValueDefinition
    {
        return new ValueDefinition($value);
    }
}

if (! function_exists('Onepix\FoodSpotVendor\DI\create')) {
    /**
     * Helper for defining an object.
     *
     * @param string|null $className Class name of the object.
     *                               If null, the name of the entry (in the container) will be used as class name.
     */
    function create(?string $className = null) : CreateDefinitionHelper
    {
        return new CreateDefinitionHelper($className);
    }
}

if (! function_exists('Onepix\FoodSpotVendor\DI\autowire')) {
    /**
     * Helper for autowiring an object.
     *
     * @param string|null $className Class name of the object.
     *                               If null, the name of the entry (in the container) will be used as class name.
     */
    function autowire(?string $className = null) : AutowireDefinitionHelper
    {
        return new AutowireDefinitionHelper($className);
    }
}

if (! function_exists('Onepix\FoodSpotVendor\DI\factory')) {
    /**
     * Helper for defining a container entry using a factory function/callable.
     *
     * @param callable|array|string $factory The factory is a callable that takes the container as parameter
     *        and returns the value to register in the container.
     */
    function factory(callable|array|string $factory) : FactoryDefinitionHelper
    {
        return new FactoryDefinitionHelper($factory);
    }
}

if (! function_exists('Onepix\FoodSpotVendor\DI\decorate')) {
    /**
     * Decorate the previous definition using a callable.
     *
     * Example:
     *
     *     'foo' => decorate(function ($foo, $container) {
     *         return new CachedFoo($foo, $container->get('cache'));
     *     })
     *
     * @param callable $callable The callable takes the decorated object as first parameter and
     *                           the container as second.
     */
    function decorate(callable|array|string $callable) : FactoryDefinitionHelper
    {
        return new FactoryDefinitionHelper($callable, true);
    }
}

if (! function_exists('Onepix\FoodSpotVendor\DI\get')) {
    /**
     * Helper for referencing another container entry in an object definition.
     */
    function get(string $entryName) : Reference
    {
        return new Reference($entryName);
    }
}

if (! function_exists('Onepix\FoodSpotVendor\DI\env')) {
    /**
     * Helper for referencing environment variables.
     *
     * @param string $variableName The name of the environment variable.
     * @param mixed $defaultValue The default value to be used if the environment variable is not defined.
     */
    function onepix_foodspotvendor_env(string $variableName, mixed $defaultValue = null) : EnvironmentVariableDefinition
    {
        // Only mark as optional if the default value was *explicitly* provided.
        $isOptional = 2 === func_num_args();

        return new EnvironmentVariableDefinition($variableName, $isOptional, $defaultValue);
    }
}

if (! function_exists('Onepix\FoodSpotVendor\DI\add')) {
    /**
     * Helper for extending another definition.
     *
     * Example:
     *
     *     'log.backends' => \Onepix\FoodSpotVendor\DI\add(\Onepix\FoodSpotVendor\DI\get('My\Custom\LogBackend'))
     *
     * or:
     *
     *     'log.backends' => \Onepix\FoodSpotVendor\DI\add([
     *         DI\get('My\Custom\LogBackend')
     *     ])
     *
     * @param mixed|array $values A value or an array of values to add to the array.
     *
     * @since 5.0
     */
    function add($values) : ArrayDefinitionExtension
    {
        if (! is_array($values)) {
            $values = [$values];
        }

        return new ArrayDefinitionExtension($values);
    }
}

if (! function_exists('Onepix\FoodSpotVendor\DI\string')) {
    /**
     * Helper for concatenating strings.
     *
     * Example:
     *
     *     'log.filename' => \Onepix\FoodSpotVendor\DI\string('{app.path}/app.log')
     *
     * @param string $expression A string expression. Use the `{}` placeholders to reference other container entries.
     *
     * @since 5.0
     */
    function string(string $expression) : StringDefinition
    {
        return new StringDefinition($expression);
    }
}
