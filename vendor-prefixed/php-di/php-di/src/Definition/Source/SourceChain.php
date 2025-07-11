<?php

declare(strict_types=1);

namespace Onepix\FoodSpotVendor\DI\Definition\Source;

use Onepix\FoodSpotVendor\DI\Definition\Definition;
use Onepix\FoodSpotVendor\DI\Definition\ExtendsPreviousDefinition;

/**
 * Manages a chain of other definition sources.
 *
 * @author Matthieu Napoli <matthieu@mnapoli.fr>
 */
class SourceChain implements DefinitionSource, MutableDefinitionSource
{
    private ?MutableDefinitionSource $mutableSource;

    /**
     * @param list<DefinitionSource> $sources
     */
    public function __construct(
        private array $sources,
    ) {
    }

    /**
     * @param int $startIndex Use this parameter to start looking from a specific
     *                        point in the source chain.
     */
    public function getDefinition(string $name, int $startIndex = 0) : ?Definition
    {
        $count = count($this->sources);
        for ($i = $startIndex; $i < $count; ++$i) {
            $source = $this->sources[$i];

            $definition = $source->getDefinition($name);

            if ($definition) {
                if ($definition instanceof ExtendsPreviousDefinition) {
                    $this->resolveExtendedDefinition($definition, $i);
                }

                return $definition;
            }
        }

        return null;
    }

    public function getDefinitions() : array
    {
        $allDefinitions = array_merge(...array_map(fn ($source) => $source->getDefinitions(), $this->sources));

        /** @var string[] $allNames */
        $allNames = array_keys($allDefinitions);

        $allValues = array_filter(array_map(fn ($name) => $this->getDefinition($name), $allNames));

        return array_combine($allNames, $allValues);
    }

    public function addDefinition(Definition $definition) : void
    {
        if (! $this->mutableSource) {
            throw new \LogicException("The container's definition source has not been initialized correctly");
        }

        $this->mutableSource->addDefinition($definition);
    }

    private function resolveExtendedDefinition(ExtendsPreviousDefinition $definition, int $currentIndex)
    {
        // Look in the next sources only (else infinite recursion, and we can only extend
        // entries defined in the previous definition files - a previous == next here because
        // the array was reversed ;) )
        $subDefinition = $this->getDefinition($definition->getName(), $currentIndex + 1);

        if ($subDefinition) {
            $definition->setExtendedDefinition($subDefinition);
        }
    }

    public function setMutableDefinitionSource(MutableDefinitionSource $mutableSource) : void
    {
        $this->mutableSource = $mutableSource;

        array_unshift($this->sources, $mutableSource);
    }
}
