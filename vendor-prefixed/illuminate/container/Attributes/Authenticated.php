<?php

namespace Onepix\FoodSpotVendor\Illuminate\Container\Attributes;

use Attribute;
use Onepix\FoodSpotVendor\Illuminate\Contracts\Container\Container;
use Onepix\FoodSpotVendor\Illuminate\Contracts\Container\ContextualAttribute;

#[Attribute(Attribute::TARGET_PARAMETER)]
class Authenticated implements ContextualAttribute
{
    /**
     * Create a new class instance.
     */
    public function __construct(public ?string $guard = null)
    {
    }

    /**
     * Resolve the currently authenticated user.
     *
     * @param  self  $attribute
     * @param  \Onepix\FoodSpotVendor\Illuminate\Contracts\Container\Container  $container
     * @return \Onepix\FoodSpotVendor\Illuminate\Contracts\Auth\Authenticatable|null
     */
    public static function resolve(self $attribute, Container $container)
    {
        return call_user_func($container->make('auth')->userResolver(), $attribute->guard);
    }
}
