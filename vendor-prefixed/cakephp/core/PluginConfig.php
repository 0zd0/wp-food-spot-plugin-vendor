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
 * @since         5.1.0
 * @license       https://opensource.org/licenses/mit-license.php MIT License
 */
namespace Onepix\FoodSpotVendor\Cake\Core;

use Onepix\FoodSpotVendor\Cake\Core\Exception\CakeException;
use Onepix\FoodSpotVendor\Cake\Utility\Hash;

/**
 * PluginConfig contains all available plugins and their config if/how they should be loaded
 *
 * @internal
 */
class PluginConfig
{
    /**
     * Load the path information stored in vendor/cakephp-plugins.php
     *
     * This file is generated by the cakephp/plugin-installer package and used
     * to locate plugins on the filesystem as applications can use `extra.plugin-paths`
     * in their composer.json file to move plugin outside of vendor/
     *
     * @internal
     * @return void
     */
    public static function loadInstallerConfig(): void
    {
        if (Configure::check('plugins')) {
            return;
        }
        $vendorFile = dirname(__DIR__, 2) . DIRECTORY_SEPARATOR . 'cakephp-plugins.php';
        if (!is_file($vendorFile)) {
            $vendorFile = dirname(__DIR__, 4) . DIRECTORY_SEPARATOR . 'cakephp-plugins.php';
            if (!is_file($vendorFile)) {
                Configure::write(['plugins' => []]);

                return;
            }
        }

        $config = require $vendorFile;
        Configure::write($config);
    }

    /**
     * Get the config how plugins should be loaded
     *
     * @param string|null $path The absolute path to the composer.lock file to retrieve the versions from
     * @return array
     */
    public static function getAppConfig(?string $path = null): array
    {
        self::loadInstallerConfig();

        // phpcs:ignore
        $pluginLoadConfig = @include CONFIG . 'plugins.php';
        if (is_array($pluginLoadConfig)) {
            $pluginLoadConfig = Hash::normalize($pluginLoadConfig);
        } else {
            $pluginLoadConfig = [];
        }

        try {
            $composerVersions = self::getVersions($path);
        } catch (CakeException) {
            $composerVersions = [];
        }

        $result = [];
        $availablePlugins = Configure::read('plugins', []);
        if ($availablePlugins && is_array($availablePlugins)) {
            foreach ($availablePlugins as $pluginName => $pluginPath) {
                if ($pluginLoadConfig && array_key_exists($pluginName, $pluginLoadConfig)) {
                    $options = $pluginLoadConfig[$pluginName];
                    $hooks = PluginInterface::VALID_HOOKS;
                    $mainConfig = [
                        'isLoaded' => true,
                        'onlyDebug' => $options['onlyDebug'] ?? false,
                        'onlyCli' => $options['onlyCli'] ?? false,
                        'optional' => $options['optional'] ?? false,
                    ];
                    foreach ($hooks as $hook) {
                        $mainConfig[$hook] = $options[$hook] ?? true;
                    }
                    $result[$pluginName] = $mainConfig;
                } else {
                    $result[$pluginName]['isLoaded'] = false;
                }

                try {
                    $packageName = self::getPackageNameFromPath($pluginPath);
                    $result[$pluginName]['packagePath'] = $pluginPath;
                    $result[$pluginName]['package'] = $packageName;
                } catch (CakeException) {
                    $packageName = null;
                }
                if ($composerVersions && $packageName) {
                    if (array_key_exists($packageName, $composerVersions['packages'])) {
                        $result[$pluginName]['version'] = $composerVersions['packages'][$packageName];
                        $result[$pluginName]['isDevPackage'] = false;
                    } elseif (array_key_exists($packageName, $composerVersions['devPackages'])) {
                        $result[$pluginName]['version'] = $composerVersions['devPackages'][$packageName];
                        $result[$pluginName]['isDevPackage'] = true;
                    }
                }
            }
        }

        $diff = array_diff(array_keys($pluginLoadConfig), array_keys($availablePlugins));
        foreach ($diff as $unknownPlugin) {
            $result[$unknownPlugin]['isLoaded'] = false;
            $result[$unknownPlugin]['isUnknown'] = true;
        }

        return $result;
    }

    /**
     * @param string|null $path The absolute path to the composer.lock file to retrieve the versions from
     * @return array
     */
    public static function getVersions(?string $path = null): array
    {
        $lockFilePath = $path ?? ROOT . DIRECTORY_SEPARATOR . 'composer.lock';
        if (!file_exists($lockFilePath)) {
            throw new CakeException(sprintf('composer.lock does not exist in %s', $lockFilePath));
        }
        $lockFile = file_get_contents($lockFilePath);
        if ($lockFile === false) {
            throw new CakeException(sprintf('Could not read composer.lock: %s', $lockFilePath));
        }
        $lockFileJson = json_decode($lockFile, true);
        if (json_last_error() !== JSON_ERROR_NONE) {
            throw new CakeException(sprintf(
                'Error parsing composer.lock: %s',
                json_last_error_msg(),
            ));
        }

        $packages = Hash::combine($lockFileJson['packages'], '{n}.name', '{n}.version');
        $devPackages = Hash::combine($lockFileJson['packages-dev'], '{n}.name', '{n}.version');

        return [
            'packages' => $packages,
            'devPackages' => $devPackages,
        ];
    }

    /**
     * @param string $path
     * @return string
     */
    protected static function getPackageNameFromPath(string $path): string
    {
        $jsonPath = $path . DS . 'composer.json';
        if (!file_exists($jsonPath)) {
            throw new CakeException(sprintf('composer.json does not exist in %s', $jsonPath));
        }
        $jsonString = file_get_contents($jsonPath);
        if ($jsonString === false) {
            throw new CakeException(sprintf('Could not read composer.json: %s', $jsonPath));
        }
        $json = json_decode($jsonString, true);
        if (json_last_error() !== JSON_ERROR_NONE) {
            throw new CakeException(sprintf(
                'Error parsing %ss: %s',
                $jsonPath,
                json_last_error_msg(),
            ));
        }

        return $json['name'];
    }
}
