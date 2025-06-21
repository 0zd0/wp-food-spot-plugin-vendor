<?php
/**
 * Copyright © Dimitri BOUTEILLE (https://github.com/dimitriBouteille)
 * See LICENSE.txt for license details.
 *
 * Author: Dimitri BOUTEILLE <bonjour@dimitri-bouteille.fr>
 */

namespace Onepix\FoodSpotVendor\Dbout\WpOrm\Scopes;

use Onepix\FoodSpotVendor\Dbout\WpOrm\Api\CustomModelTypeInterface;
use Onepix\FoodSpotVendor\Dbout\WpOrm\Exceptions\WpOrmException;
use Onepix\FoodSpotVendor\Illuminate\Database\Eloquent\Builder;
use Onepix\FoodSpotVendor\Illuminate\Database\Eloquent\Model;
use Onepix\FoodSpotVendor\Illuminate\Database\Eloquent\Scope;

class CustomModelTypeScope implements Scope
{
    /**
     * @inheritDoc
     * @throws WpOrmException
     */
    public function apply(Builder $builder, Model $model): void
    {
        if (!$model instanceof CustomModelTypeInterface) {
            throw new WpOrmException(sprintf(
                'The object %s must be implement %s.',
                get_class($model),
                CustomModelTypeInterface::class
            ));
        }

        $builder->where($model->getCustomTypeColumn(), $model->getCustomTypeCode());
    }
}
