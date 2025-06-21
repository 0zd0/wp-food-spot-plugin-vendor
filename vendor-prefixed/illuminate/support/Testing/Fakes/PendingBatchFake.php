<?php

namespace Onepix\FoodSpotVendor\Illuminate\Support\Testing\Fakes;

use Onepix\FoodSpotVendor\Illuminate\Bus\PendingBatch;
use Onepix\FoodSpotVendor\Illuminate\Support\Collection;

class PendingBatchFake extends PendingBatch
{
    /**
     * The fake bus instance.
     *
     * @var \Onepix\FoodSpotVendor\Illuminate\Support\Testing\Fakes\BusFake
     */
    protected $bus;

    /**
     * Create a new pending batch instance.
     *
     * @param  \Onepix\FoodSpotVendor\Illuminate\Support\Testing\Fakes\BusFake  $bus
     * @param  \Onepix\FoodSpotVendor\Illuminate\Support\Collection  $jobs
     * @return void
     */
    public function __construct(BusFake $bus, Collection $jobs)
    {
        $this->bus = $bus;
        $this->jobs = $jobs;
    }

    /**
     * Dispatch the batch.
     *
     * @return \Onepix\FoodSpotVendor\Illuminate\Bus\Batch
     */
    public function dispatch()
    {
        return $this->bus->recordPendingBatch($this);
    }

    /**
     * Dispatch the batch after the response is sent to the browser.
     *
     * @return \Onepix\FoodSpotVendor\Illuminate\Bus\Batch
     */
    public function dispatchAfterResponse()
    {
        return $this->bus->recordPendingBatch($this);
    }
}
