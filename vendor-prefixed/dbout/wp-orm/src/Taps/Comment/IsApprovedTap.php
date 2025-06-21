<?php
/**
 * Copyright © Dimitri BOUTEILLE (https://github.com/dimitriBouteille)
 * See LICENSE.txt for license details.
 *
 * Author: Dimitri BOUTEILLE <bonjour@dimitri-bouteille.fr>
 */

namespace Onepix\FoodSpotVendor\Dbout\WpOrm\Taps\Comment;

use Onepix\FoodSpotVendor\Dbout\WpOrm\Builders\CommentBuilder;
use Onepix\FoodSpotVendor\Dbout\WpOrm\Models\Comment;

readonly class IsApprovedTap
{
    /**
     * @param bool $isApproved
     */
    public function __construct(
        protected bool $isApproved = true
    ) {
    }

    /**
     * @param CommentBuilder $builder
     * @return void
     */
    public function __invoke(CommentBuilder $builder): void
    {
        $builder->where(Comment::APPROVED, $this->isApproved);
    }
}
