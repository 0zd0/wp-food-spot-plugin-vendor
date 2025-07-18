<?php
/**
 * Copyright © Dimitri BOUTEILLE (https://github.com/dimitriBouteille)
 * See LICENSE.txt for license details.
 *
 * Author: Dimitri BOUTEILLE <bonjour@dimitri-bouteille.fr>
 */

namespace Onepix\FoodSpotVendor\Dbout\WpOrm\Taps\Attachment;

use Onepix\FoodSpotVendor\Dbout\WpOrm\Builders\PostBuilder;
use Onepix\FoodSpotVendor\Dbout\WpOrm\Models\Post;

readonly class IsMimeTypeTap
{
    /**
     * @param string $mimeType
     */
    public function __construct(
        protected string $mimeType
    ) {
    }

    /**
     * @param PostBuilder $builder
     * @return void
     */
    public function __invoke(PostBuilder $builder): void
    {
        $builder->where(Post::MIME_TYPE, $this->mimeType);
    }
}
