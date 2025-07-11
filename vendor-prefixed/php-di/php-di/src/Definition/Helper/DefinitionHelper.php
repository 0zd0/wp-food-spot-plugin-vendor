<?php

declare(strict_types=1);

namespace Onepix\FoodSpotVendor\DI\Definition\Helper;

use Onepix\FoodSpotVendor\DI\Definition\Definition;

/**
 * Helps defining container entries.
 *
 * @author Matthieu Napoli <matthieu@mnapoli.fr>
 */
interface DefinitionHelper
{
    /**
     * @param string $entryName Container entry name
     */
    public function getDefinition(string $entryName) : Definition;
}
