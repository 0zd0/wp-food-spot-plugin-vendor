<?php

declare(strict_types=1);

namespace Onepix\FoodSpotVendor\DI\Definition\Helper;

use Onepix\FoodSpotVendor\DI\Definition\AutowireDefinition;

/**
 * Helps defining how to create an instance of a class using autowiring.
 *
 * @author Matthieu Napoli <matthieu@mnapoli.fr>
 */
class AutowireDefinitionHelper extends CreateDefinitionHelper
{
    public const DEFINITION_CLASS = AutowireDefinition::class;

    /**
     * Defines a value for a specific argument of the constructor.
     *
     * This method is usually used together with attributes or autowiring, when a parameter
     * is not (or cannot be) type-hinted. Using this method instead of constructor() allows to
     * avoid defining all the parameters (letting them being resolved using attributes or autowiring)
     * and only define one.
     *
     * @param string|int $parameter Parameter name of position for which the value will be given.
     * @param mixed $value Value to give to this parameter.
     *
     * @return $this
     */
    public function constructorParameter(string|int $parameter, mixed $value) : self
    {
        $this->constructor[$parameter] = $value;

        return $this;
    }

    /**
     * Defines a method to call and a value for a specific argument.
     *
     * This method is usually used together with attributes or autowiring, when a parameter
     * is not (or cannot be) type-hinted. Using this method instead of method() allows to
     * avoid defining all the parameters (letting them being resolved using attributes or
     * autowiring) and only define one.
     *
     * If multiple calls to the method have been configured already (e.g. in a previous definition)
     * then this method only overrides the parameter for the *first* call.
     *
     * @param string $method Name of the method to call.
     * @param string|int $parameter Parameter name of position for which the value will be given.
     * @param mixed $value Value to give to this parameter.
     *
     * @return $this
     */
    public function methodParameter(string $method, string|int $parameter, mixed $value) : self
    {
        // Special case for the constructor
        if ($method === '__construct') {
            $this->constructor[$parameter] = $value;

            return $this;
        }

        if (! isset($this->methods[$method])) {
            $this->methods[$method] = [0 => []];
        }

        $this->methods[$method][0][$parameter] = $value;

        return $this;
    }
}
