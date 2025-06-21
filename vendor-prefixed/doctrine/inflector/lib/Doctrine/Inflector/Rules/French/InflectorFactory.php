<?php

declare(strict_types=1);

namespace Onepix\FoodSpotVendor\Doctrine\Inflector\Rules\French;

use Onepix\FoodSpotVendor\Doctrine\Inflector\GenericLanguageInflectorFactory;
use Onepix\FoodSpotVendor\Doctrine\Inflector\Rules\Ruleset;

final class InflectorFactory extends GenericLanguageInflectorFactory
{
    protected function getSingularRuleset(): Ruleset
    {
        return Rules::getSingularRuleset();
    }

    protected function getPluralRuleset(): Ruleset
    {
        return Rules::getPluralRuleset();
    }
}
