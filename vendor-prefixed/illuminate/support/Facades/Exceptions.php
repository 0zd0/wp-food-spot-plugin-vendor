<?php

namespace Onepix\FoodSpotVendor\Illuminate\Support\Facades;

use Onepix\FoodSpotVendor\Illuminate\Contracts\Debug\ExceptionHandler;
use Onepix\FoodSpotVendor\Illuminate\Support\Arr;
use Onepix\FoodSpotVendor\Illuminate\Support\Testing\Fakes\ExceptionHandlerFake;

/**
 * @method static void register()
 * @method static \Illuminate\Foundation\Exceptions\ReportableHandler reportable(callable $reportUsing)
 * @method static \Illuminate\Foundation\Exceptions\Handler renderable(callable $renderUsing)
 * @method static \Illuminate\Foundation\Exceptions\Handler map(\Closure|string $from, \Closure|string|null $to = null)
 * @method static \Illuminate\Foundation\Exceptions\Handler dontReport(array|string $exceptions)
 * @method static \Illuminate\Foundation\Exceptions\Handler ignore(array|string $exceptions)
 * @method static \Illuminate\Foundation\Exceptions\Handler dontFlash(array|string $attributes)
 * @method static \Illuminate\Foundation\Exceptions\Handler level(string $type, string $level)
 * @method static void report(\Throwable $e)
 * @method static bool shouldReport(\Throwable $e)
 * @method static \Illuminate\Foundation\Exceptions\Handler throttleUsing(callable $throttleUsing)
 * @method static \Illuminate\Foundation\Exceptions\Handler stopIgnoring(array|string $exceptions)
 * @method static \Illuminate\Foundation\Exceptions\Handler buildContextUsing(\Closure $contextCallback)
 * @method static \Symfony\Component\HttpFoundation\Response render(\Illuminate\Http\Request $request, \Throwable $e)
 * @method static \Illuminate\Foundation\Exceptions\Handler respondUsing(callable $callback)
 * @method static \Illuminate\Foundation\Exceptions\Handler shouldRenderJsonWhen(callable $callback)
 * @method static \Illuminate\Foundation\Exceptions\Handler dontReportDuplicates()
 * @method static \Onepix\FoodSpotVendor\Illuminate\Contracts\Debug\ExceptionHandler handler()
 * @method static void assertReported(\Closure|string $exception)
 * @method static void assertReportedCount(int $count)
 * @method static void assertNotReported(\Closure|string $exception)
 * @method static void assertNothingReported()
 * @method static void renderForConsole(\Onepix\FoodSpotVendor\Symfony\Component\Console\Output\OutputInterface $output, \Throwable $e)
 * @method static \Onepix\FoodSpotVendor\Illuminate\Support\Testing\Fakes\ExceptionHandlerFake throwOnReport()
 * @method static \Onepix\FoodSpotVendor\Illuminate\Support\Testing\Fakes\ExceptionHandlerFake throwFirstReported()
 * @method static \Onepix\FoodSpotVendor\Illuminate\Support\Testing\Fakes\ExceptionHandlerFake setHandler(\Onepix\FoodSpotVendor\Illuminate\Contracts\Debug\ExceptionHandler $handler)
 *
 * @see \Illuminate\Foundation\Exceptions\Handler
 * @see \Onepix\FoodSpotVendor\Illuminate\Support\Testing\Fakes\ExceptionHandlerFake
 */
class Exceptions extends Facade
{
    /**
     * Replace the bound instance with a fake.
     *
     * @param  array<int, class-string<\Throwable>>|class-string<\Throwable>  $exceptions
     * @return \Onepix\FoodSpotVendor\Illuminate\Support\Testing\Fakes\ExceptionHandlerFake
     */
    public static function fake(array|string $exceptions = [])
    {
        $exceptionHandler = static::isFake()
            ? static::getFacadeRoot()->handler()
            : static::getFacadeRoot();

        return onepix_foodspotvendor_tap(new ExceptionHandlerFake($exceptionHandler, Arr::wrap($exceptions)), function ($fake) {
            static::swap($fake);
        });
    }

    /**
     * Get the registered name of the component.
     *
     * @return string
     */
    protected static function getFacadeAccessor()
    {
        return ExceptionHandler::class;
    }
}
