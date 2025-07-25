<?php

namespace Onepix\FoodSpotVendor\Illuminate\Support\Facades;

/**
 * @method static \Onepix\FoodSpotVendor\Psr\Log\LoggerInterface build(array $config)
 * @method static \Onepix\FoodSpotVendor\Psr\Log\LoggerInterface stack(array $channels, string|null $channel = null)
 * @method static \Onepix\FoodSpotVendor\Psr\Log\LoggerInterface channel(string|null $channel = null)
 * @method static \Onepix\FoodSpotVendor\Psr\Log\LoggerInterface driver(string|null $driver = null)
 * @method static \Illuminate\Log\LogManager shareContext(array $context)
 * @method static array sharedContext()
 * @method static \Illuminate\Log\LogManager withoutContext()
 * @method static \Illuminate\Log\LogManager flushSharedContext()
 * @method static string|null getDefaultDriver()
 * @method static void setDefaultDriver(string $name)
 * @method static \Illuminate\Log\LogManager extend(string $driver, \Closure $callback)
 * @method static void forgetChannel(string|null $driver = null)
 * @method static array getChannels()
 * @method static void emergency(string|\Stringable $message, array $context = [])
 * @method static void alert(string|\Stringable $message, array $context = [])
 * @method static void critical(string|\Stringable $message, array $context = [])
 * @method static void error(string|\Stringable $message, array $context = [])
 * @method static void warning(string|\Stringable $message, array $context = [])
 * @method static void notice(string|\Stringable $message, array $context = [])
 * @method static void info(string|\Stringable $message, array $context = [])
 * @method static void debug(string|\Stringable $message, array $context = [])
 * @method static void log(mixed $level, string|\Stringable $message, array $context = [])
 * @method static \Illuminate\Log\LogManager setApplication(\Onepix\FoodSpotVendor\Illuminate\Contracts\Foundation\Application $app)
 * @method static void write(string $level, \Onepix\FoodSpotVendor\Illuminate\Contracts\Support\Arrayable|\Onepix\FoodSpotVendor\Illuminate\Contracts\Support\Jsonable|\Onepix\FoodSpotVendor\Illuminate\Support\Stringable|array|string $message, array $context = [])
 * @method static \Illuminate\Log\Logger withContext(array $context = [])
 * @method static void listen(\Closure $callback)
 * @method static \Onepix\FoodSpotVendor\Psr\Log\LoggerInterface getLogger()
 * @method static \Onepix\FoodSpotVendor\Illuminate\Contracts\Events\Dispatcher getEventDispatcher()
 * @method static void setEventDispatcher(\Onepix\FoodSpotVendor\Illuminate\Contracts\Events\Dispatcher $dispatcher)
 * @method static \Illuminate\Log\Logger|mixed when(\Closure|mixed|null $value = null, callable|null $callback = null, callable|null $default = null)
 * @method static \Illuminate\Log\Logger|mixed unless(\Closure|mixed|null $value = null, callable|null $callback = null, callable|null $default = null)
 *
 * @see \Illuminate\Log\LogManager
 */
class Log extends Facade
{
    /**
     * Get the registered name of the component.
     *
     * @return string
     */
    protected static function getFacadeAccessor()
    {
        return 'log';
    }
}
