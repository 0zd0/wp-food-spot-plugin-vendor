<?php

namespace Onepix\FoodSpotVendor\Illuminate\Bus\Events;

use Onepix\FoodSpotVendor\Illuminate\Bus\Batch;

class BatchDispatched
{
    /**
     * The batch instance.
     *
     * @var \Onepix\FoodSpotVendor\Illuminate\Bus\Batch
     */
    public $batch;

    /**
     * Create a new event instance.
     *
     * @param  \Onepix\FoodSpotVendor\Illuminate\Bus\Batch  $batch
     * @return void
     */
    public function __construct(Batch $batch)
    {
        $this->batch = $batch;
    }
}
