<?php

/*
 * This file is part of the Symfony package.
 *
 * (c) Fabien Potencier <fabien@symfony.com>
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

namespace Onepix\FoodSpotVendor\Symfony\Contracts\Cache;

use Onepix\FoodSpotVendor\Psr\Cache\CacheItemPoolInterface;
use Onepix\FoodSpotVendor\Psr\Cache\InvalidArgumentException;
use Onepix\FoodSpotVendor\Psr\Log\LoggerInterface;

// Help opcache.preload discover always-needed symbols
class_exists(InvalidArgumentException::class);

/**
 * An implementation of CacheInterface for PSR-6 CacheItemPoolInterface classes.
 *
 * @author Nicolas Grekas <p@tchwork.com>
 */
trait CacheTrait
{
    public function get(string $key, callable $callback, ?float $beta = null, ?array &$metadata = null): mixed
    {
        return $this->doGet($this, $key, $callback, $beta, $metadata);
    }

    public function delete(string $key): bool
    {
        return $this->deleteItem($key);
    }

    private function doGet(CacheItemPoolInterface $pool, string $key, callable $callback, ?float $beta, ?array &$metadata = null, ?LoggerInterface $logger = null): mixed
    {
        if (0 > $beta ??= 1.0) {
            throw new class(\sprintf('Argument "$beta" provided to "%s::get()" must be a positive number, %f given.', static::class, $beta)) extends \InvalidArgumentException implements InvalidArgumentException {};
        }

        $item = $pool->getItem($key);
        $recompute = !$item->isHit() || \INF === $beta;
        $metadata = $item instanceof ItemInterface ? $item->getMetadata() : [];

        if (!$recompute && $metadata) {
            $expiry = $metadata[ItemInterface::METADATA_EXPIRY] ?? false;
            $ctime = $metadata[ItemInterface::METADATA_CTIME] ?? false;

            if ($recompute = $ctime && $expiry && $expiry <= ($now = microtime(true)) - $ctime / 1000 * $beta * log(random_int(1, \PHP_INT_MAX) / \PHP_INT_MAX)) {
                // force applying defaultLifetime to expiry
                $item->expiresAt(null);
                $logger?->info('Item "{key}" elected for early recomputation {delta}s before its expiration', [
                    'key' => $key,
                    'delta' => \sprintf('%.1f', $expiry - $now),
                ]);
            }
        }

        if ($recompute) {
            $save = true;
            $item->set($callback($item, $save));
            if ($save) {
                $pool->save($item);
            }
        }

        return $item->get();
    }
}
