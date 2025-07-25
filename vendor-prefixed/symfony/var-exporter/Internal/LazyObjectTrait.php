<?php

/*
 * This file is part of the Symfony package.
 *
 * (c) Fabien Potencier <fabien@symfony.com>
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

namespace Onepix\FoodSpotVendor\Symfony\Component\VarExporter\Internal;

use Symfony\Component\Serializer\Attribute\Ignore;

if (\PHP_VERSION_ID >= 80300) {
    /**
     * @internal
     */
    trait LazyObjectTrait
    {
        #[Ignore]
        private readonly LazyObjectState $lazyObjectState;
    }
} else {
    /**
     * @internal
     */
    trait LazyObjectTrait
    {
        #[Ignore]
        private LazyObjectState $lazyObjectState;
    }
}
