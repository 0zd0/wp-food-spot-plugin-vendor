<?php
/**
 * Copyright © Dimitri BOUTEILLE (https://github.com/dimitriBouteille)
 * See LICENSE.txt for license details.
 *
 * Author: Dimitri BOUTEILLE <bonjour@dimitri-bouteille.fr>
 */

namespace Onepix\FoodSpotVendor\Dbout\WpOrm\Api;

use Onepix\FoodSpotVendor\Dbout\WpOrm\MetaMappingConfig;

interface WithMetaModelInterface
{
    /**
     * @return MetaMappingConfig
     */
    public function getMetaConfigMapping(): MetaMappingConfig;
}
