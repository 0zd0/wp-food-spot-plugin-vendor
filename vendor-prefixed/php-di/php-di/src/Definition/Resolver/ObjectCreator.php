<?php

declare(strict_types=1);

namespace Onepix\FoodSpotVendor\DI\Definition\Resolver;

use Onepix\FoodSpotVendor\DI\Definition\Definition;
use Onepix\FoodSpotVendor\DI\Definition\Exception\InvalidDefinition;
use Onepix\FoodSpotVendor\DI\Definition\ObjectDefinition;
use Onepix\FoodSpotVendor\DI\Definition\ObjectDefinition\PropertyInjection;
use Onepix\FoodSpotVendor\DI\DependencyException;
use Onepix\FoodSpotVendor\DI\Proxy\ProxyFactory;
use Exception;
use ProxyManager\Proxy\LazyLoadingInterface;
use Onepix\FoodSpotVendor\Psr\Container\NotFoundExceptionInterface;
use ReflectionClass;
use ReflectionProperty;

/**
 * Create objects based on an object definition.
 *
 * @template-implements DefinitionResolver<ObjectDefinition>
 *
 * @since 4.0
 * @author Matthieu Napoli <matthieu@mnapoli.fr>
 */
class ObjectCreator implements DefinitionResolver
{
    private ParameterResolver $parameterResolver;

    /**
     * @param DefinitionResolver $definitionResolver Used to resolve nested definitions.
     * @param ProxyFactory       $proxyFactory       Used to create proxies for lazy injections.
     */
    public function __construct(
        private DefinitionResolver $definitionResolver,
        private ProxyFactory $proxyFactory,
    ) {
        $this->parameterResolver = new ParameterResolver($definitionResolver);
    }

    /**
     * Resolve a class definition to a value.
     *
     * This will create a new instance of the class using the injections points defined.
     *
     * @param ObjectDefinition $definition
     */
    public function resolve(Definition $definition, array $parameters = []) : ?object
    {
        // Lazy?
        if ($definition->isLazy()) {
            return $this->createProxy($definition, $parameters);
        }

        return $this->createInstance($definition, $parameters);
    }

    /**
     * The definition is not resolvable if the class is not instantiable (interface or abstract)
     * or if the class doesn't exist.
     *
     * @param ObjectDefinition $definition
     */
    public function isResolvable(Definition $definition, array $parameters = []) : bool
    {
        return $definition->isInstantiable();
    }

    /**
     * Returns a proxy instance.
     */
    private function createProxy(ObjectDefinition $definition, array $parameters) : LazyLoadingInterface
    {
        /** @var class-string $className */
        $className = $definition->getClassName();

        return $this->proxyFactory->createProxy(
            $className,
            function (& $wrappedObject, $proxy, $method, $params, & $initializer) use ($definition, $parameters) {
                $wrappedObject = $this->createInstance($definition, $parameters);
                $initializer = null; // turning off further lazy initialization

                return true;
            }
        );
    }

    /**
     * Creates an instance of the class and injects dependencies..
     *
     * @param array $parameters Optional parameters to use to create the instance.
     *
     * @throws DependencyException
     * @throws InvalidDefinition
     */
    private function createInstance(ObjectDefinition $definition, array $parameters) : object
    {
        // Check that the class is instantiable
        if (! $definition->isInstantiable()) {
            // Check that the class exists
            if (! $definition->classExists()) {
                throw InvalidDefinition::create($definition, sprintf(
                    'Entry "%s" cannot be resolved: the class doesn\'t exist',
                    $definition->getName()
                ));
            }

            throw InvalidDefinition::create($definition, sprintf(
                'Entry "%s" cannot be resolved: the class is not instantiable',
                $definition->getName()
            ));
        }

        /** @psalm-var class-string $classname */
        $classname = $definition->getClassName();
        $classReflection = new ReflectionClass($classname);

        $constructorInjection = $definition->getConstructorInjection();

        /** @psalm-suppress InvalidCatch */
        try {
            $args = $this->parameterResolver->resolveParameters(
                $constructorInjection,
                $classReflection->getConstructor(),
                $parameters
            );

            $object = new $classname(...$args);

            $this->injectMethodsAndProperties($object, $definition);
        } catch (NotFoundExceptionInterface $e) {
            throw new DependencyException(sprintf(
                'Error while injecting dependencies into %s: %s',
                $classReflection->getName(),
                $e->getMessage()
            ), 0, $e);
        } catch (InvalidDefinition $e) {
            throw InvalidDefinition::create($definition, sprintf(
                'Entry "%s" cannot be resolved: %s',
                $definition->getName(),
                $e->getMessage()
            ));
        }

        return $object;
    }

    protected function injectMethodsAndProperties(object $object, ObjectDefinition $objectDefinition) : void
    {
        // Property injections
        foreach ($objectDefinition->getPropertyInjections() as $propertyInjection) {
            $this->injectProperty($object, $propertyInjection);
        }

        // Method injections
        foreach ($objectDefinition->getMethodInjections() as $methodInjection) {
            $methodReflection = new \ReflectionMethod($object, $methodInjection->getMethodName());
            $args = $this->parameterResolver->resolveParameters($methodInjection, $methodReflection);

            $methodReflection->invokeArgs($object, $args);
        }
    }

    /**
     * Inject dependencies into properties.
     *
     * @param object            $object            Object to inject dependencies into
     * @param PropertyInjection $propertyInjection Property injection definition
     *
     * @throws DependencyException
     */
    private function injectProperty(object $object, PropertyInjection $propertyInjection) : void
    {
        $propertyName = $propertyInjection->getPropertyName();

        $value = $propertyInjection->getValue();

        if ($value instanceof Definition) {
            try {
                $value = $this->definitionResolver->resolve($value);
            } catch (DependencyException $e) {
                throw $e;
            } catch (Exception $e) {
                throw new DependencyException(sprintf(
                    'Error while injecting in %s::%s. %s',
                    $object::class,
                    $propertyName,
                    $e->getMessage()
                ), 0, $e);
            }
        }

        self::setPrivatePropertyValue($propertyInjection->getClassName(), $object, $propertyName, $value);
    }

    public static function setPrivatePropertyValue(?string $className, $object, string $propertyName, mixed $propertyValue) : void
    {
        $className = $className ?: $object::class;

        $property = new ReflectionProperty($className, $propertyName);
        if (! $property->isPublic() && \PHP_VERSION_ID < 80100) {
            $property->setAccessible(true);
        }
        $property->setValue($object, $propertyValue);
    }
}
