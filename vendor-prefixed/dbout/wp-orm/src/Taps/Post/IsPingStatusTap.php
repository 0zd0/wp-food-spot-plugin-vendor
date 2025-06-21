<?php
/**
 * Copyright © Dimitri BOUTEILLE (https://github.com/dimitriBouteille)
 * See LICENSE.txt for license details.
 *
 * Author: Dimitri BOUTEILLE <bonjour@dimitri-bouteille.fr>
 */

namespace Onepix\FoodSpotVendor\Dbout\WpOrm\Taps\Post;

use Onepix\FoodSpotVendor\Dbout\WpOrm\Builders\PostBuilder;
use Onepix\FoodSpotVendor\Dbout\WpOrm\Enums\PingStatus;
use Onepix\FoodSpotVendor\Dbout\WpOrm\Models\Post;

readonly class IsPingStatusTap
{
    /**
     * @param string|PingStatus $status
     */
    public function __construct(
        protected string|PingStatus $status
    ) {
    }

    /**
     * @param PostBuilder $builder
     * @return void
     */
    public function __invoke(PostBuilder $builder): void
    {
        $status = $this->status;
        if ($status instanceof PingStatus) {
            $status = $status->value;
        }

        $builder->where(Post::PING_STATUS, $status);
    }
}
