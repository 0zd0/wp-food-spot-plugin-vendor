<?php

/*
 * This file is part of the Symfony package.
 *
 * (c) Fabien Potencier <fabien@symfony.com>
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

namespace Onepix\FoodSpotVendor\Symfony\Component\Cache\Traits\Relay;

if (version_compare(phpversion('relay'), '0.9.0', '>=')) {
    /**
     * @internal
     */
    trait GeosearchTrait
    {
        public function geosearch($key, $position, $shape, $unit, $options = []): \Relay\Relay|array|false
        {
            return $this->initializeLazyObject()->geosearch(...\func_get_args());
        }
    }
} else {
    /**
     * @internal
     */
    trait GeosearchTrait
    {
        public function geosearch($key, $position, $shape, $unit, $options = []): \Relay\Relay|array
        {
            return $this->initializeLazyObject()->geosearch(...\func_get_args());
        }
    }
}
