<?php
/**
 * Copyright © Dimitri BOUTEILLE (https://github.com/dimitriBouteille)
 * See LICENSE.txt for license details.
 *
 * Author: Dimitri BOUTEILLE <bonjour@dimitri-bouteille.fr>
 */

namespace Onepix\FoodSpotVendor\Dbout\WpOrm\Builders;

use Onepix\FoodSpotVendor\Dbout\WpOrm\Models\Comment;
use Onepix\FoodSpotVendor\Illuminate\Database\Eloquent\Collection;

class CommentBuilder extends AbstractBuilder
{
    /**
     * @param string $type
     * @return Collection
     */
    public function findAllByType(string $type): Collection
    {
        return $this
            ->whereTypes([$type])
            ->get();
    }

    /**
     * @param mixed ...$types
     * @return $this
     */
    public function whereTypes(...$types): self
    {
        return $this->_whereOrIn(Comment::TYPE, $types);
    }
}
