<?php

/*
 * This file is part of the Symfony package.
 *
 * (c) Fabien Potencier <fabien@symfony.com>
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

namespace Onepix\FoodSpotVendor\Symfony\Component\Cache\Messenger;

use Onepix\FoodSpotVendor\Symfony\Component\Cache\Adapter\AdapterInterface;
use Onepix\FoodSpotVendor\Symfony\Component\Cache\CacheItem;
use Symfony\Component\DependencyInjection\ReverseContainer;

/**
 * Conveys a cached value that needs to be computed.
 */
final class EarlyExpirationMessage
{
    public static function create(ReverseContainer $reverseContainer, callable $callback, CacheItem $item, AdapterInterface $pool): ?self
    {
        try {
            $item = clone $item;
            $item->set(null);
        } catch (\Exception) {
            return null;
        }

        $pool = $reverseContainer->getId($pool);

        if (\is_object($callback)) {
            if (null === $id = $reverseContainer->getId($callback)) {
                return null;
            }

            $callback = '@'.$id;
        } elseif (!\is_array($callback)) {
            $callback = (string) $callback;
        } elseif (!\is_object($callback[0])) {
            $callback = [(string) $callback[0], (string) $callback[1]];
        } else {
            if (null === $id = $reverseContainer->getId($callback[0])) {
                return null;
            }

            $callback = ['@'.$id, (string) $callback[1]];
        }

        return new self($item, $pool, $callback);
    }

    public function getItem(): CacheItem
    {
        return $this->item;
    }

    public function getPool(): string
    {
        return $this->pool;
    }

    /**
     * @return string|string[]
     */
    public function getCallback(): string|array
    {
        return $this->callback;
    }

    public function findPool(ReverseContainer $reverseContainer): AdapterInterface
    {
        return $reverseContainer->getService($this->pool);
    }

    public function findCallback(ReverseContainer $reverseContainer): callable
    {
        if (\is_string($callback = $this->callback)) {
            return '@' === $callback[0] ? $reverseContainer->getService(substr($callback, 1)) : $callback;
        }
        if ('@' === $callback[0][0]) {
            $callback[0] = $reverseContainer->getService(substr($callback[0], 1));
        }

        return $callback;
    }

    private function __construct(
        private CacheItem $item,
        private string $pool,
        private string|array $callback,
    ) {
    }
}
