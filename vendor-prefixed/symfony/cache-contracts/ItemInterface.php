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

use Onepix\FoodSpotVendor\Psr\Cache\CacheException;
use Onepix\FoodSpotVendor\Psr\Cache\CacheItemInterface;
use Onepix\FoodSpotVendor\Psr\Cache\InvalidArgumentException;

/**
 * Augments PSR-6's CacheItemInterface with support for tags and metadata.
 *
 * @author Nicolas Grekas <p@tchwork.com>
 */
interface ItemInterface extends CacheItemInterface
{
    /**
     * References the Unix timestamp stating when the item will expire.
     */
    public const METADATA_EXPIRY = 'expiry';

    /**
     * References the time the item took to be created, in milliseconds.
     */
    public const METADATA_CTIME = 'ctime';

    /**
     * References the list of tags that were assigned to the item, as string[].
     */
    public const METADATA_TAGS = 'tags';

    /**
     * Reserved characters that cannot be used in a key or tag.
     */
    public const RESERVED_CHARACTERS = '{}()/\@:';

    /**
     * Adds a tag to a cache item.
     *
     * Tags are strings that follow the same validation rules as keys.
     *
     * @param string|string[] $tags A tag or array of tags
     *
     * @return $this
     *
     * @throws InvalidArgumentException When $tag is not valid
     * @throws CacheException           When the item comes from a pool that is not tag-aware
     */
    public function tag(string|iterable $tags): static;

    /**
     * Returns a list of metadata info that were saved alongside with the cached value.
     *
     * See ItemInterface::METADATA_* consts for keys potentially found in the returned array.
     */
    public function getMetadata(): array;
}
