<?php

/**
 * MIT License
 * For full license information, please view the LICENSE file that was distributed with this source code.
 */

namespace Onepix\FoodSpotVendor\Phinx\Config;

/**
 * Class to hold features flags to toggle breaking changes in Phinx.
 *
 * New flags should be added very sparingly.
 */
class FeatureFlags
{
    /**
     * @var bool Should Phinx create unsigned primary keys by default?
     */
    public static bool $unsignedPrimaryKeys = true;
    /**
     * @var bool Should Phinx create columns NULL by default?
     */
    public static bool $columnNullDefault = true;
    /**
     * @var bool Should Phinx create datetime columns for addTimestamps instead of timestamp?
     */
    public static bool $addTimestampsUseDateTime = false;

    /**
     * Set the feature flags from the `feature_flags` section of the overall
     * config.
     *
     * @param array $config The `feature_flags` section of the config
     */
    public static function setFlagsFromConfig(array $config): void
    {
        if (isset($config['unsigned_primary_keys'])) {
            self::$unsignedPrimaryKeys = (bool)$config['unsigned_primary_keys'];
        }
        if (isset($config['column_null_default'])) {
            self::$columnNullDefault = (bool)$config['column_null_default'];
        }
        if (isset($config['add_timestamps_use_datetime'])) {
            self::$addTimestampsUseDateTime = (bool)$config['add_timestamps_use_datetime'];
        }
    }
}
