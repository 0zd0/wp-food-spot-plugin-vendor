<?php

declare(strict_types=1);

namespace Onepix\FoodSpotVendor\DI\Definition\ObjectDefinition;

/**
 * Describe an injection in a class property.
 *
 * @author Matthieu Napoli <matthieu@mnapoli.fr>
 */
class PropertyInjection
{
    private string $propertyName;

    /**
     * Value that should be injected in the property.
     */
    private mixed $value;

    /**
     * Use for injecting in properties of parent classes: the class name
     * must be the name of the parent class because private properties
     * can be attached to the parent classes, not the one we are resolving.
     */
    private ?string $className;

    /**
     * @param string $propertyName Property name
     * @param mixed $value Value that should be injected in the property
     */
    public function __construct(string $propertyName, mixed $value, ?string $className = null)
    {
        $this->propertyName = $propertyName;
        $this->value = $value;
        $this->className = $className;
    }

    public function getPropertyName() : string
    {
        return $this->propertyName;
    }

    /**
     * @return mixed Value that should be injected in the property
     */
    public function getValue() : mixed
    {
        return $this->value;
    }

    public function getClassName() : ?string
    {
        return $this->className;
    }

    public function replaceNestedDefinition(callable $replacer) : void
    {
        $this->value = $replacer($this->value);
    }
}
