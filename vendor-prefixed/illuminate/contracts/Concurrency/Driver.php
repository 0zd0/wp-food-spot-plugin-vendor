<?php

namespace Onepix\FoodSpotVendor\Illuminate\Contracts\Concurrency;

use Closure;
use Onepix\FoodSpotVendor\Illuminate\Support\Defer\DeferredCallback;

interface Driver
{
    /**
     * Run the given tasks concurrently and return an array containing the results.
     */
    public function run(Closure|array $tasks): array;

    /**
     * Defer the execution of the given tasks.
     */
    public function defer(Closure|array $tasks): DeferredCallback;
}
