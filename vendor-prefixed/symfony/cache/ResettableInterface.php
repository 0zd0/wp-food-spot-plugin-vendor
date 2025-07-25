<?php

/*
 * This file is part of the Symfony package.
 *
 * (c) Fabien Potencier <fabien@symfony.com>
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

namespace Onepix\FoodSpotVendor\Symfony\Component\Cache;

use Onepix\FoodSpotVendor\Symfony\Contracts\Service\ResetInterface;

/**
 * Resets a pool's local state.
 */
interface ResettableInterface extends ResetInterface
{
}
