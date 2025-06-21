<?php

declare(strict_types=1);

namespace Onepix\FoodSpotVendor\DI\Definition\Source;

use Onepix\FoodSpotVendor\DI\Definition\Definition;

/**
 * Describes a definition source to which we can add new definitions.
 *
 * @author Matthieu Napoli <matthieu@mnapoli.fr>
 */
interface MutableDefinitionSource extends DefinitionSource
{
    public function addDefinition(Definition $definition) : void;
}
