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
use Onepix\FoodSpotVendor\Dbout\WpOrm\Models\User;

readonly class IsUserTap
{
    /**
     * @param int|User $user
     */
    public function __construct(
        protected int|User $user
    ) {
    }

    /**
     * @param CommentBuilder $builder
     * @return void
     */
    public function __invoke(CommentBuilder $builder): void
    {
        $user = $this->user;
        if ($user instanceof User) {
            $user = $user->getId();
        }

        $builder->where(Comment::USER_ID, $user);
    }
}
