<?php

namespace Onepix\FoodSpotVendor\Illuminate\Support\Facades;

/**
 * @method static \Onepix\FoodSpotVendor\Illuminate\Pipeline\Pipeline send(mixed $passable)
 * @method static \Onepix\FoodSpotVendor\Illuminate\Pipeline\Pipeline through(array|mixed $pipes)
 * @method static \Onepix\FoodSpotVendor\Illuminate\Pipeline\Pipeline pipe(array|mixed $pipes)
 * @method static \Onepix\FoodSpotVendor\Illuminate\Pipeline\Pipeline via(string $method)
 * @method static mixed then(\Closure $destination)
 * @method static mixed thenReturn()
 * @method static \Onepix\FoodSpotVendor\Illuminate\Pipeline\Pipeline finally(\Closure $callback)
 * @method static \Onepix\FoodSpotVendor\Illuminate\Pipeline\Pipeline setContainer(\Onepix\FoodSpotVendor\Illuminate\Contracts\Container\Container $container)
 * @method static \Onepix\FoodSpotVendor\Illuminate\Pipeline\Pipeline|mixed when(\Closure|mixed|null $value = null, callable|null $callback = null, callable|null $default = null)
 * @method static \Onepix\FoodSpotVendor\Illuminate\Pipeline\Pipeline|mixed unless(\Closure|mixed|null $value = null, callable|null $callback = null, callable|null $default = null)
 *
 * @see \Onepix\FoodSpotVendor\Illuminate\Pipeline\Pipeline
 */
class Pipeline extends Facade
{
    /**
     * Indicates if the resolved instance should be cached.
     *
     * @var bool
     */
    protected static $cached = false;

    /**
     * Get the registered name of the component.
     *
     * @return string
     */
    protected static function getFacadeAccessor()
    {
        return 'pipeline';
    }
}
