<?php
/**
 * Copyright © Dimitri BOUTEILLE (https://github.com/dimitriBouteille)
 * See LICENSE.txt for license details.
 *
 * Author: Dimitri BOUTEILLE <bonjour@dimitri-bouteille.fr>
 */

namespace Onepix\FoodSpotVendor\Dbout\WpOrm\Builders;

use Onepix\FoodSpotVendor\Dbout\WpOrm\Models\TermTaxonomy;
use Onepix\FoodSpotVendor\Illuminate\Database\Eloquent\Collection;

class TermBuilder extends AbstractBuilder
{
    /**
     * @param string $taxonomy
     * @return Collection
     */
    public function findAllByTaxonomy(string $taxonomy): Collection
    {
        return $this->whereHas('termTaxonomy', function ($query) use ($taxonomy) {
            return $query->where(TermTaxonomy::TAXONOMY, $taxonomy);
        })->get();
    }
}
