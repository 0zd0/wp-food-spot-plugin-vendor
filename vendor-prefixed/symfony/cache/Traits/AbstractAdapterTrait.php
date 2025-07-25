<?php

/*
 * This file is part of the Symfony package.
 *
 * (c) Fabien Potencier <fabien@symfony.com>
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

namespace Onepix\FoodSpotVendor\Symfony\Component\Cache\Traits;

use Onepix\FoodSpotVendor\Psr\Cache\CacheItemInterface;
use Onepix\FoodSpotVendor\Psr\Log\LoggerAwareTrait;
use Onepix\FoodSpotVendor\Symfony\Component\Cache\CacheItem;
use Onepix\FoodSpotVendor\Symfony\Component\Cache\Exception\InvalidArgumentException;

/**
 * @author Nicolas Grekas <p@tchwork.com>
 *
 * @internal
 */
trait AbstractAdapterTrait
{
    use LoggerAwareTrait;

    /**
     * needs to be set by class, signature is function(string <key>, mixed <value>, bool <isHit>).
     */
    private static \Closure $createCacheItem;

    /**
     * needs to be set by class, signature is function(array <deferred>, string <namespace>, array <&expiredIds>).
     */
    private static \Closure $mergeByLifetime;

    private readonly string $rootNamespace;
    private string $namespace = '';
    private int $defaultLifetime;
    private string $namespaceVersion = '';
    private bool $versioningIsEnabled = false;
    private array $deferred = [];
    private array $ids = [];

    /**
     * The maximum length to enforce for identifiers or null when no limit applies.
     */
    protected ?int $maxIdLength = null;

    /**
     * Fetches several cache items.
     *
     * @param array $ids The cache identifiers to fetch
     */
    abstract protected function doFetch(array $ids): iterable;

    /**
     * Confirms if the cache contains specified cache item.
     *
     * @param string $id The identifier for which to check existence
     */
    abstract protected function doHave(string $id): bool;

    /**
     * Deletes all items in the pool.
     *
     * @param string $namespace The prefix used for all identifiers managed by this pool
     */
    abstract protected function doClear(string $namespace): bool;

    /**
     * Removes multiple items from the pool.
     *
     * @param array $ids An array of identifiers that should be removed from the pool
     */
    abstract protected function doDelete(array $ids): bool;

    /**
     * Persists several cache items immediately.
     *
     * @param array $values   The values to cache, indexed by their cache identifier
     * @param int   $lifetime The lifetime of the cached values, 0 for persisting until manual cleaning
     *
     * @return array|bool The identifiers that failed to be cached or a boolean stating if caching succeeded or not
     */
    abstract protected function doSave(array $values, int $lifetime): array|bool;

    public function hasItem(mixed $key): bool
    {
        $id = $this->getId($key);

        if (isset($this->deferred[$key])) {
            $this->commit();
        }

        try {
            return $this->doHave($id);
        } catch (\Exception $e) {
            CacheItem::log($this->logger, 'Failed to check if key "{key}" is cached: '.$e->getMessage(), ['key' => $key, 'exception' => $e, 'cache-adapter' => get_debug_type($this)]);

            return false;
        }
    }

    public function clear(string $prefix = ''): bool
    {
        $this->deferred = [];
        if ($cleared = $this->versioningIsEnabled) {
            $rootNamespace = $this->rootNamespace ??= $this->namespace;
            if ('' === $namespaceVersionToClear = $this->namespaceVersion) {
                foreach ($this->doFetch([static::NS_SEPARATOR.$rootNamespace]) as $v) {
                    $namespaceVersionToClear = $v;
                }
            }
            $namespaceToClear = $rootNamespace.$namespaceVersionToClear;
            $namespaceVersion = self::formatNamespaceVersion(mt_rand());
            try {
                $e = $this->doSave([static::NS_SEPARATOR.$rootNamespace => $namespaceVersion], 0);
            } catch (\Exception $e) {
            }
            if (true !== $e && [] !== $e) {
                $cleared = false;
                $message = 'Failed to save the new namespace'.($e instanceof \Exception ? ': '.$e->getMessage() : '.');
                CacheItem::log($this->logger, $message, ['exception' => $e instanceof \Exception ? $e : null, 'cache-adapter' => get_debug_type($this)]);
            } else {
                $this->namespaceVersion = $namespaceVersion;
                $this->ids = [];
            }
        } else {
            $namespaceToClear = $this->namespace.$prefix;
        }

        try {
            return $this->doClear($namespaceToClear) || $cleared;
        } catch (\Exception $e) {
            CacheItem::log($this->logger, 'Failed to clear the cache: '.$e->getMessage(), ['exception' => $e, 'cache-adapter' => get_debug_type($this)]);

            return false;
        }
    }

    public function deleteItem(mixed $key): bool
    {
        return $this->deleteItems([$key]);
    }

    public function deleteItems(array $keys): bool
    {
        $ids = [];

        foreach ($keys as $key) {
            $ids[$key] = $this->getId($key);
            unset($this->deferred[$key]);
        }

        try {
            if ($this->doDelete($ids)) {
                return true;
            }
        } catch (\Exception) {
        }

        $ok = true;

        // When bulk-delete failed, retry each item individually
        foreach ($ids as $key => $id) {
            try {
                $e = null;
                if ($this->doDelete([$id])) {
                    continue;
                }
            } catch (\Exception $e) {
            }
            $message = 'Failed to delete key "{key}"'.($e instanceof \Exception ? ': '.$e->getMessage() : '.');
            CacheItem::log($this->logger, $message, ['key' => $key, 'exception' => $e, 'cache-adapter' => get_debug_type($this)]);
            $ok = false;
        }

        return $ok;
    }

    public function getItem(mixed $key): CacheItem
    {
        $id = $this->getId($key);

        if (isset($this->deferred[$key])) {
            $this->commit();
        }

        $isHit = false;
        $value = null;

        try {
            foreach ($this->doFetch([$id]) as $value) {
                $isHit = true;
            }

            return (self::$createCacheItem)($key, $value, $isHit);
        } catch (\Exception $e) {
            CacheItem::log($this->logger, 'Failed to fetch key "{key}": '.$e->getMessage(), ['key' => $key, 'exception' => $e, 'cache-adapter' => get_debug_type($this)]);
        }

        return (self::$createCacheItem)($key, null, false);
    }

    public function getItems(array $keys = []): iterable
    {
        $ids = [];
        $commit = false;

        foreach ($keys as $key) {
            $ids[] = $this->getId($key);
            $commit = $commit || isset($this->deferred[$key]);
        }

        if ($commit) {
            $this->commit();
        }

        try {
            $items = $this->doFetch($ids);
        } catch (\Exception $e) {
            CacheItem::log($this->logger, 'Failed to fetch items: '.$e->getMessage(), ['keys' => $keys, 'exception' => $e, 'cache-adapter' => get_debug_type($this)]);
            $items = [];
        }
        $ids = array_combine($ids, $keys);

        return $this->generateItems($items, $ids);
    }

    public function save(CacheItemInterface $item): bool
    {
        if (!$item instanceof CacheItem) {
            return false;
        }
        $this->deferred[$item->getKey()] = $item;

        return $this->commit();
    }

    public function saveDeferred(CacheItemInterface $item): bool
    {
        if (!$item instanceof CacheItem) {
            return false;
        }
        $this->deferred[$item->getKey()] = $item;

        return true;
    }

    public function withSubNamespace(string $namespace): static
    {
        $this->rootNamespace ??= $this->namespace;

        $clone = clone $this;
        $clone->namespace .= CacheItem::validateKey($namespace).static::NS_SEPARATOR;

        return $clone;
    }

    /**
     * Enables/disables versioning of items.
     *
     * When versioning is enabled, clearing the cache is atomic and doesn't require listing existing keys to proceed,
     * but old keys may need garbage collection and extra round-trips to the back-end are required.
     *
     * Calling this method also clears the memoized namespace version and thus forces a resynchronization of it.
     *
     * @return bool the previous state of versioning
     */
    public function enableVersioning(bool $enable = true): bool
    {
        $wasEnabled = $this->versioningIsEnabled;
        $this->versioningIsEnabled = $enable;
        $this->namespaceVersion = '';
        $this->ids = [];

        return $wasEnabled;
    }

    public function reset(): void
    {
        if ($this->deferred) {
            $this->commit();
        }
        $this->namespaceVersion = '';
        $this->ids = [];
    }

    public function __sleep(): array
    {
        throw new \BadMethodCallException('Cannot serialize '.__CLASS__);
    }

    public function __wakeup(): void
    {
        throw new \BadMethodCallException('Cannot unserialize '.__CLASS__);
    }

    public function __destruct()
    {
        if ($this->deferred) {
            $this->commit();
        }
    }

    private function generateItems(iterable $items, array &$keys): \Generator
    {
        $f = self::$createCacheItem;

        try {
            foreach ($items as $id => $value) {
                if (!isset($keys[$id])) {
                    throw new InvalidArgumentException(\sprintf('Could not match value id "%s" to keys "%s".', $id, implode('", "', $keys)));
                }
                $key = $keys[$id];
                unset($keys[$id]);
                yield $key => $f($key, $value, true);
            }
        } catch (\Exception $e) {
            CacheItem::log($this->logger, 'Failed to fetch items: '.$e->getMessage(), ['keys' => array_values($keys), 'exception' => $e, 'cache-adapter' => get_debug_type($this)]);
        }

        foreach ($keys as $key) {
            yield $key => $f($key, null, false);
        }
    }

    /**
     * @internal
     */
    protected function getId(mixed $key, ?string $namespace = null): string
    {
        $namespace ??= $this->namespace;

        if ('' !== $this->namespaceVersion) {
            $namespace .= $this->namespaceVersion;
        } elseif ($this->versioningIsEnabled) {
            $rootNamespace = $this->rootNamespace ??= $this->namespace;
            $this->ids = [];
            $this->namespaceVersion = '1'.static::NS_SEPARATOR;
            try {
                foreach ($this->doFetch([static::NS_SEPARATOR.$rootNamespace]) as $v) {
                    $this->namespaceVersion = $v;
                }
                $e = true;
                if ('1'.static::NS_SEPARATOR === $this->namespaceVersion) {
                    $this->namespaceVersion = self::formatNamespaceVersion(time());
                    $e = $this->doSave([static::NS_SEPARATOR.$rootNamespace => $this->namespaceVersion], 0);
                }
            } catch (\Exception $e) {
            }
            if (true !== $e && [] !== $e) {
                $message = 'Failed to save the new namespace'.($e instanceof \Exception ? ': '.$e->getMessage() : '.');
                CacheItem::log($this->logger, $message, ['exception' => $e instanceof \Exception ? $e : null, 'cache-adapter' => get_debug_type($this)]);
            }

            $namespace .= $this->namespaceVersion;
        }

        if (\is_string($key) && isset($this->ids[$key])) {
            $id = $this->ids[$key];
        } else {
            \assert('' !== CacheItem::validateKey($key));
            $this->ids[$key] = $key;

            if (\count($this->ids) > 1000) {
                $this->ids = \array_slice($this->ids, 500, null, true); // stop memory leak if there are many keys
            }

            if (null === $this->maxIdLength) {
                return $namespace.$key;
            }
            if (\strlen($id = $namespace.$key) <= $this->maxIdLength) {
                return $id;
            }

            // Use xxh128 to favor speed over security, which is not an issue here
            $this->ids[$key] = $id = substr_replace(base64_encode(hash('xxh128', $key, true)), static::NS_SEPARATOR, -(\strlen($this->namespaceVersion) + 2));
        }
        $id = $namespace.$id;

        if (null !== $this->maxIdLength && \strlen($id) > $this->maxIdLength) {
            return base64_encode(hash('xxh128', $id, true));
        }

        return $id;
    }

    /**
     * @internal
     */
    public static function handleUnserializeCallback(string $class): never
    {
        throw new \DomainException('Class not found: '.$class);
    }

    private static function formatNamespaceVersion(int $value): string
    {
        return strtr(substr_replace(base64_encode(pack('V', $value)), static::NS_SEPARATOR, 5), '/', '_');
    }
}
