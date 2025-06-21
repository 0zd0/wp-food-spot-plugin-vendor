<?php

namespace Onepix\FoodSpotVendor\Illuminate\Container\Attributes;

use Attribute;
use Onepix\FoodSpotVendor\Illuminate\Contracts\Container\Container;
use Onepix\FoodSpotVendor\Illuminate\Contracts\Container\ContextualAttribute;

#[Attribute(Attribute::TARGET_PARAMETER)]
class Storage implements ContextualAttribute
{
    /**
     * Create a new class instance.
     */
    public function __construct(public ?string $disk = null)
    {
    }

    /**
     * Resolve the storage disk.
     *
     * @param  self  $attribute
     * @param  \Onepix\FoodSpotVendor\Illuminate\Contracts\Container\Container  $container
     * @return \Onepix\FoodSpotVendor\Illuminate\Contracts\Filesystem\Filesystem
     */
    public static function resolve(self $attribute, Container $container)
    {
        return $container->make('filesystem')->disk($attribute->disk);
    }
}
