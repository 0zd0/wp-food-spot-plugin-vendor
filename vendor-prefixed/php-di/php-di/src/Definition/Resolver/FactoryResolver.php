<?php

declare(strict_types=1);

namespace Onepix\FoodSpotVendor\DI\Definition\Resolver;

use Onepix\FoodSpotVendor\DI\Definition\Definition;
use Onepix\FoodSpotVendor\DI\Definition\Exception\InvalidDefinition;
use Onepix\FoodSpotVendor\DI\Definition\FactoryDefinition;
use Onepix\FoodSpotVendor\DI\Invoker\FactoryParameterResolver;
use Onepix\FoodSpotVendor\Invoker\Exception\NotCallableException;
use Onepix\FoodSpotVendor\Invoker\Exception\NotEnoughParametersException;
use Onepix\FoodSpotVendor\Invoker\Invoker;
use Onepix\FoodSpotVendor\Invoker\ParameterResolver\AssociativeArrayResolver;
use Onepix\FoodSpotVendor\Invoker\ParameterResolver\DefaultValueResolver;
use Onepix\FoodSpotVendor\Invoker\ParameterResolver\NumericArrayResolver;
use Onepix\FoodSpotVendor\Invoker\ParameterResolver\ResolverChain;
use Onepix\FoodSpotVendor\Psr\Container\ContainerInterface;

/**
 * Resolves a factory definition to a value.
 *
 * @template-implements DefinitionResolver<FactoryDefinition>
 *
 * @since 4.0
 * @author Matthieu Napoli <matthieu@mnapoli.fr>
 */
class FactoryResolver implements DefinitionResolver
{
    private ?Invoker $invoker = null;

    /**
     * The resolver needs a container. This container will be passed to the factory as a parameter
     * so that the factory can access other entries of the container.
     */
    public function __construct(
        private ContainerInterface $container,
        private DefinitionResolver $resolver,
    ) {
    }

    /**
     * Resolve a factory definition to a value.
     *
     * This will call the callable of the definition.
     *
     * @param FactoryDefinition $definition
     */
    public function resolve(Definition $definition, array $parameters = []) : mixed
    {
        if (! $this->invoker) {
            $parameterResolver = new ResolverChain([
                new AssociativeArrayResolver,
                new FactoryParameterResolver($this->container),
                new NumericArrayResolver,
                new DefaultValueResolver,
            ]);

            $this->invoker = new Invoker($parameterResolver, $this->container);
        }

        $callable = $definition->getCallable();

        try {
            $providedParams = [$this->container, $definition];
            $extraParams = $this->resolveExtraParams($definition->getParameters());
            $providedParams = array_merge($providedParams, $extraParams, $parameters);

            return $this->invoker->call($callable, $providedParams);
        } catch (NotCallableException $e) {
            // Custom error message to help debugging
            if (is_string($callable) && class_exists($callable) && method_exists($callable, '__invoke')) {
                throw new InvalidDefinition(sprintf(
                    'Entry "%s" cannot be resolved: factory %s. Invokable classes cannot be automatically resolved if autowiring is disabled on the container, you need to enable autowiring or define the entry manually.',
                    $definition->getName(),
                    $e->getMessage()
                ));
            }

            throw new InvalidDefinition(sprintf(
                'Entry "%s" cannot be resolved: factory %s',
                $definition->getName(),
                $e->getMessage()
            ));
        } catch (NotEnoughParametersException $e) {
            throw new InvalidDefinition(sprintf(
                'Entry "%s" cannot be resolved: %s',
                $definition->getName(),
                $e->getMessage()
            ));
        }
    }

    public function isResolvable(Definition $definition, array $parameters = []) : bool
    {
        return true;
    }

    private function resolveExtraParams(array $params) : array
    {
        $resolved = [];
        foreach ($params as $key => $value) {
            // Nested definitions
            if ($value instanceof Definition) {
                $value = $this->resolver->resolve($value);
            }
            $resolved[$key] = $value;
        }

        return $resolved;
    }
}
