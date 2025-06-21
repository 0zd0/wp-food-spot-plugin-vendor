<?php

namespace Onepix\FoodSpotVendor\Illuminate\Container\Attributes;

use Attribute;
use Onepix\FoodSpotVendor\Illuminate\Contracts\Container\Container;
use Onepix\FoodSpotVendor\Illuminate\Contracts\Container\ContextualAttribute;

#[Attribute(Attribute::TARGET_PARAMETER)]
class Cache implements ContextualAttribute
{
    /**
     * Create a new class instance.
     */
    public function __construct(public ?string $store = null)
    {
    }

    /**
     * Resolve the cache store.
     *
     * @param  self  $attribute
     * @param  \Onepix\FoodSpotVendor\Illuminate\Contracts\Container\Container  $container
     * @return \Onepix\FoodSpotVendor\Illuminate\Contracts\Cache\Repository
     */
    public static function resolve(self $attribute, Container $container)
    {
        return $container->make('cache')->store($attribute->store);
    }
}
