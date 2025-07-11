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
 * @since         3.0.0
 * @license       https://opensource.org/licenses/mit-license.php MIT License
 */
namespace Onepix\FoodSpotVendor\Cake\Core;

use Onepix\FoodSpotVendor\Cake\Utility\Inflector;

/**
 * Provides methods that allow other classes access to conventions based inflections.
 */
trait ConventionsTrait
{
    /**
     * Creates a fixture name
     *
     * @param string $name Model class name
     * @return string Singular model key
     */
    protected function _fixtureName(string $name): string
    {
        return Inflector::camelize($name);
    }

    /**
     * Creates the proper entity name (singular) for the specified name
     *
     * @param string $name Name
     * @return string Camelized and plural model name
     */
    protected function _entityName(string $name): string
    {
        return Inflector::singularize(Inflector::camelize($name));
    }

    /**
     * Creates the proper underscored model key for associations
     *
     * If the input contains a dot, assume that the right side is the real table name.
     *
     * @param string $name Model class name
     * @return string Singular model key
     */
    protected function _modelKey(string $name): string
    {
        [, $name] = onepix_foodspotvendor_pluginSplit($name);

        return Inflector::underscore(Inflector::singularize($name)) . '_id';
    }

    /**
     * Creates the proper model name from a foreign key
     *
     * @param string $key Foreign key
     * @return string Model name
     */
    protected function _modelNameFromKey(string $key): string
    {
        $key = str_replace('_id', '', $key);

        return Inflector::camelize(Inflector::pluralize($key));
    }

    /**
     * Creates the singular name for use in views.
     *
     * @param string $name Name to use
     * @return string Variable name
     */
    protected function _singularName(string $name): string
    {
        return Inflector::variable(Inflector::singularize($name));
    }

    /**
     * Creates the plural variable name for views
     *
     * @param string $name Name to use
     * @return string Plural name for views
     */
    protected function _variableName(string $name): string
    {
        return Inflector::variable($name);
    }

    /**
     * Creates the singular human name used in views
     *
     * @param string $name Controller name
     * @return string Singular human name
     */
    protected function _singularHumanName(string $name): string
    {
        return Inflector::humanize(Inflector::underscore(Inflector::singularize($name)));
    }

    /**
     * Creates a camelized version of $name
     *
     * @param string $name name
     * @return string Camelized name
     */
    protected function _camelize(string $name): string
    {
        return Inflector::camelize($name);
    }

    /**
     * Creates the plural human name used in views
     *
     * @param string $name Controller name
     * @return string Plural human name
     */
    protected function _pluralHumanName(string $name): string
    {
        return Inflector::humanize(Inflector::underscore($name));
    }

    /**
     * Find the correct path for a plugin. Scans $pluginPaths for the plugin you want.
     *
     * @param string $pluginName Name of the plugin you want ie. DebugKit
     * @return string Path to the correct plugin.
     */
    protected function _pluginPath(string $pluginName): string
    {
        if (Plugin::isLoaded($pluginName)) {
            return Plugin::path($pluginName);
        }

        return current(App::path('plugins')) . $pluginName . DIRECTORY_SEPARATOR;
    }

    /**
     * Return plugin's namespace
     *
     * @param string $pluginName Plugin name
     * @return string Plugin's namespace
     */
    protected function _pluginNamespace(string $pluginName): string
    {
        return str_replace('/', '\\', $pluginName);
    }
}
