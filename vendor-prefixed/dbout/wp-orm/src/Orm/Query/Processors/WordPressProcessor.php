<?php
/**
 * Copyright © Dimitri BOUTEILLE (https://github.com/dimitriBouteille)
 * See LICENSE.txt for license details.
 *
 * Author: Dimitri BOUTEILLE <bonjour@dimitri-bouteille.fr>
 */

namespace Onepix\FoodSpotVendor\Dbout\WpOrm\Orm\Query\Processors;

use Onepix\FoodSpotVendor\Dbout\WpOrm\Orm\Database;
use Onepix\FoodSpotVendor\Illuminate\Database\Query\Builder;
use Onepix\FoodSpotVendor\Illuminate\Database\Query\Processors\Processor;

class WordPressProcessor extends Processor
{
    /**
     * @inheritDoc
     */
    public function processInsertGetId(Builder $query, $sql, $values, $sequence = null): ?int
    {
        /** @var Database $co */
        $co = $query->getConnection();
        $co->insert($sql, $values);

        $id = $co->lastInsertId();
        return is_numeric($id) ? (int) $id : $id;
    }
}
