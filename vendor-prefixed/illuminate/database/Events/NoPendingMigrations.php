<?php

namespace Onepix\FoodSpotVendor\Illuminate\Database\Events;

use Onepix\FoodSpotVendor\Illuminate\Contracts\Database\Events\MigrationEvent;

class NoPendingMigrations implements MigrationEvent
{
    /**
     * The migration method that was called.
     *
     * @var string
     */
    public $method;

    /**
     * Create a new event instance.
     *
     * @param  string  $method
     * @return void
     */
    public function __construct($method)
    {
        $this->method = $method;
    }
}
