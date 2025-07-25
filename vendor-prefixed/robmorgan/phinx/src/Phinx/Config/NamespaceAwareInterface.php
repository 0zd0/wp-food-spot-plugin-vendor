<?php

/**
 * MIT License
 * For full license information, please view the LICENSE file that was distributed with this source code.
 */

namespace Onepix\FoodSpotVendor\Phinx\Config;

/**
 * Config aware getNamespaceByPath method.
 */
interface NamespaceAwareInterface
{
    /**
     * Get Migration Namespace associated with path.
     *
     * @param string $path Path
     * @return string|null
     */
    public function getMigrationNamespaceByPath(string $path): ?string;

    /**
     * Get Seed Namespace associated with path.
     *
     * @param string $path Path
     * @return string|null
     */
    public function getSeedNamespaceByPath(string $path): ?string;
}
