<?php

declare(strict_types=1);

namespace Onepix\FoodSpotVendor\DI\Definition\Helper;

use Onepix\FoodSpotVendor\DI\Definition\Exception\InvalidDefinition;
use Onepix\FoodSpotVendor\DI\Definition\ObjectDefinition;
use Onepix\FoodSpotVendor\DI\Definition\ObjectDefinition\MethodInjection;
use Onepix\FoodSpotVendor\DI\Definition\ObjectDefinition\PropertyInjection;

/**
 * Helps defining how to create an instance of a class.
 *
 * @author Matthieu Napoli <matthieu@mnapoli.fr>
 */
class CreateDefinitionHelper implements DefinitionHelper
{
    private const DEFINITION_CLASS = ObjectDefinition::class;

    private ?string $className;

    private ?bool $lazy = null;

    /**
     * Array of constructor parameters.
     */
    protected array $constructor = [];

    /**
     * Array of properties and their value.
     */
    private array $properties = [];

    /**
     * Array of methods and their parameters.
     */
    protected array $methods = [];

    /**
     * Helper for defining an object.
     *
     * @param string|null $className Class name of the object.
     *                               If null, the name of the entry (in the container) will be used as class name.
     */
    public function __construct(?string $className = null)
    {
        $this->className = $className;
    }

    /**
     * Define the entry as lazy.
     *
     * A lazy entry is created only when it is used, a proxy is injected instead.
     *
     * @return $this
     */
    public function lazy() : self
    {
        $this->lazy = true;

        return $this;
    }

    /**
     * Defines the arguments to use to call the constructor.
     *
     * This method takes a variable number of arguments, example:
     *     ->constructor($param1, $param2, $param3)
     *
     * @param mixed ...$parameters Parameters to use for calling the constructor of the class.
     *
     * @return $this
     */
    public function constructor(mixed ...$parameters) : self
    {
        $this->constructor = $parameters;

        return $this;
    }

    /**
     * Defines a value to inject in a property of the object.
     *
     * @param string $property Entry in which to inject the value.
     * @param mixed  $value    Value to inject in the property.
     *
     * @return $this
     */
    public function property(string $property, mixed $value) : self
    {
        $this->properties[$property] = $value;

        return $this;
    }

    /**
     * Defines a method to call and the arguments to use.
     *
     * This method takes a variable number of arguments after the method name, example:
     *
     *     ->method('myMethod', $param1, $param2)
     *
     * Can be used multiple times to declare multiple calls.
     *
     * @param string $method       Name of the method to call.
     * @param mixed ...$parameters Parameters to use for calling the method.
     *
     * @return $this
     */
    public function method(string $method, mixed ...$parameters) : self
    {
        if (! isset($this->methods[$method])) {
            $this->methods[$method] = [];
        }

        $this->methods[$method][] = $parameters;

        return $this;
    }

    public function getDefinition(string $entryName) : ObjectDefinition
    {
        $class = $this::DEFINITION_CLASS;
        /** @var ObjectDefinition $definition */
        $definition = new $class($entryName, $this->className);

        if ($this->lazy !== null) {
            $definition->setLazy($this->lazy);
        }

        if (! empty($this->constructor)) {
            $parameters = $this->fixParameters($definition, '__construct', $this->constructor);
            $constructorInjection = MethodInjection::constructor($parameters);
            $definition->setConstructorInjection($constructorInjection);
        }

        if (! empty($this->properties)) {
            foreach ($this->properties as $property => $value) {
                $definition->addPropertyInjection(
                    new PropertyInjection($property, $value)
                );
            }
        }

        if (! empty($this->methods)) {
            foreach ($this->methods as $method => $calls) {
                foreach ($calls as $parameters) {
                    $parameters = $this->fixParameters($definition, $method, $parameters);
                    $methodInjection = new MethodInjection($method, $parameters);
                    $definition->addMethodInjection($methodInjection);
                }
            }
        }

        return $definition;
    }

    /**
     * Fixes parameters indexed by the parameter name -> reindex by position.
     *
     * This is necessary so that merging definitions between sources is possible.
     *
     * @throws InvalidDefinition
     */
    private function fixParameters(ObjectDefinition $definition, string $method, array $parameters) : array
    {
        $fixedParameters = [];

        foreach ($parameters as $index => $parameter) {
            // Parameter indexed by the parameter name, we reindex it with its position
            if (is_string($index)) {
                $callable = [$definition->getClassName(), $method];

                try {
                    $reflectionParameter = new \ReflectionParameter($callable, $index);
                } catch (\ReflectionException $e) {
                    throw InvalidDefinition::create($definition, sprintf("Parameter with name '%s' could not be found. %s.", $index, $e->getMessage()));
                }

                $index = $reflectionParameter->getPosition();
            }

            $fixedParameters[$index] = $parameter;
        }

        return $fixedParameters;
    }
}
