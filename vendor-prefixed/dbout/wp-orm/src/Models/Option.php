<?php
/**
 * Copyright © Dimitri BOUTEILLE (https://github.com/dimitriBouteille)
 * See LICENSE.txt for license details.
 *
 * Author: Dimitri BOUTEILLE <bonjour@dimitri-bouteille.fr>
 */

namespace Onepix\FoodSpotVendor\Dbout\WpOrm\Models;

use Onepix\FoodSpotVendor\Dbout\WpOrm\Builders\OptionBuilder;
use Onepix\FoodSpotVendor\Dbout\WpOrm\Enums\YesNo;
use Onepix\FoodSpotVendor\Dbout\WpOrm\Orm\AbstractModel;

/**
 * @method Option setOptionName(string $name)
 * @method string getOptionName()
 * @method Option setOptionValue($value)
 * @method mixed getOptionValue()
 * @method Option setAutoload(string|YesNo $autoload)
 * @method string getAutoload()
 * @method static OptionBuilder query()
 */
class Option extends AbstractModel
{
    final public const OPTION_ID = 'option_id';
    final public const NAME = 'option_name';
    final public const VALUE = 'option_value';
    final public const AUTOLOAD = 'autoload';

    /**
     * @inheritDoc
     */
    protected $primaryKey = self::OPTION_ID;

    /**
     * @inheritDoc
     */
    protected $table = 'options';

    /**
     * @inheritDoc
     */
    public $timestamps = false;

    /**
     * @inheritDoc
     */
    public function newEloquentBuilder($query): OptionBuilder
    {
        return new OptionBuilder($query);
    }

    /**
     * @param string $optionName
     * @return self|null
     */
    public static function findOneByName(string $optionName): ?self
    {
        /** @var self|null $result */
        $result = self::query()->firstWhere(self::NAME, $optionName);
        return $result;
    }
}
