<?php
/**
 * Copyright © Dimitri BOUTEILLE (https://github.com/dimitriBouteille)
 * See LICENSE.txt for license details.
 *
 * Author: Dimitri BOUTEILLE <bonjour@dimitri-bouteille.fr>
 */

namespace Onepix\FoodSpotVendor\Dbout\WpOrm\Taps\Option;

use Onepix\FoodSpotVendor\Dbout\WpOrm\Builders\OptionBuilder;
use Onepix\FoodSpotVendor\Dbout\WpOrm\Enums\YesNo;
use Onepix\FoodSpotVendor\Dbout\WpOrm\Models\Option;

readonly class IsAutoloadTap
{
    public function __construct(
        protected bool|YesNo $autoload = YesNo::Yes
    ) {
    }

    /**
     * @param OptionBuilder $builder
     * @return void
     */
    public function __invoke(OptionBuilder $builder): void
    {
        $autoload = $this->autoload;
        if (is_bool($autoload)) {
            $autoload = $autoload ? YesNo::Yes : YesNo::No;
        }

        $builder->where(Option::AUTOLOAD, $autoload->value);
    }
}
