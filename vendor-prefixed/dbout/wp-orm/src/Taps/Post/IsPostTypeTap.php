<?php
/**
 * Copyright © Dimitri BOUTEILLE (https://github.com/dimitriBouteille)
 * See LICENSE.txt for license details.
 *
 * Author: Dimitri BOUTEILLE <bonjour@dimitri-bouteille.fr>
 */

namespace Onepix\FoodSpotVendor\Dbout\WpOrm\Taps\Post;

use Onepix\FoodSpotVendor\Dbout\WpOrm\Builders\PostBuilder;
use Onepix\FoodSpotVendor\Dbout\WpOrm\Models\Post;

readonly class IsPostTypeTap
{
    /**
     * @param string $postType
     */
    public function __construct(
        protected string $postType
    ) {
    }

    /**
     * @param PostBuilder $builder
     * @return void
     */
    public function __invoke(PostBuilder $builder): void
    {
        $builder->where(Post::TYPE, $this->postType);
    }
}
