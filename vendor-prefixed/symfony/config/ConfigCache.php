<?php

/*
 * This file is part of the Symfony package.
 *
 * (c) Fabien Potencier <fabien@symfony.com>
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

namespace Onepix\FoodSpotVendor\Symfony\Component\Config;

use Onepix\FoodSpotVendor\Symfony\Component\Config\Resource\ResourceInterface;
use Onepix\FoodSpotVendor\Symfony\Component\Config\Resource\SelfCheckingResourceChecker;
use Onepix\FoodSpotVendor\Symfony\Component\Config\Resource\SkippingResourceChecker;

/**
 * ConfigCache caches arbitrary content in files on disk.
 *
 * When in debug mode, those metadata resources that implement
 * \Symfony\Component\Config\Resource\SelfCheckingResourceInterface will
 * be used to check cache freshness.
 *
 * @author Fabien Potencier <fabien@symfony.com>
 * @author Matthias Pigulla <mp@webfactory.de>
 */
class ConfigCache extends ResourceCheckerConfigCache
{
    /**
     * @param string                                 $file                 The absolute cache path
     * @param bool                                   $debug                Whether debugging is enabled or not
     * @param string|null                            $metaFile             The absolute path to the meta file
     * @param class-string<ResourceInterface>[]|null $skippedResourceTypes
     */
    public function __construct(
        string $file,
        private bool $debug,
        ?string $metaFile = null,
        ?array $skippedResourceTypes = null,
    ) {
        $checkers = [];
        if ($this->debug) {
            if (null !== $skippedResourceTypes) {
                $checkers[] = new SkippingResourceChecker($skippedResourceTypes);
            }
            $checkers[] = new SelfCheckingResourceChecker();
        }

        parent::__construct($file, $checkers, $metaFile);
    }

    /**
     * Checks if the cache is still fresh.
     *
     * This implementation always returns true when debug is off and the
     * cache file exists.
     */
    public function isFresh(): bool
    {
        if (!$this->debug && is_file($this->getPath())) {
            return true;
        }

        return parent::isFresh();
    }
}
