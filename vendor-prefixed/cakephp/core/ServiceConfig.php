<?php
declare(strict_types=1);

/**
 * CakePHP(tm) : Rapid Development Framework (https://cakephp.org)
 * Copyright (c) Cake Software Foundation, Inc. (https://cakefoundation.org)
 *
 * Licensed under The MIT License
 * For full copyright and license information, please see the LICENSE.txt
 * Redistributions of files must retain the above copyright notice.
 *
 * @copyright     Copyright (c) Cake Software Foundation, Inc. (https://cakefoundation.org)
 * @link          https://cakephp.org CakePHP(tm) Project
 * @since         4.2.0
 * @license       https://opensource.org/licenses/mit-license.php MIT License
 */
namespace Onepix\FoodSpotVendor\Cake\Core;

/**
 * Read-only wrapper for configuration data
 *
 * Intended for use with {@link \Cake\Core\Container} as
 * a typehintable way for services to have application
 * configuration injected as arrays cannot be typehinted.
 */
class ServiceConfig
{
    /**
     * Read a configuration key
     *
     * @param string $path The path to read.
     * @param mixed $default The default value to use if $path does not exist.
     * @return mixed The configuration data or $default value.
     */
    public function get(string $path, mixed $default = null): mixed
    {
        return Configure::read($path, $default);
    }

    /**
     * Check if $path exists and has a non-null value.
     *
     * @param string $path The path to check.
     * @return bool True if the configuration data exists.
     */
    public function has(string $path): bool
    {
        return Configure::check($path);
    }
}
