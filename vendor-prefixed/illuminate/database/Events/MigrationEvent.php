<?php

namespace Onepix\FoodSpotVendor\Illuminate\Database\Events;

use Onepix\FoodSpotVendor\Illuminate\Contracts\Database\Events\MigrationEvent as MigrationEventContract;
use Onepix\FoodSpotVendor\Illuminate\Database\Migrations\Migration;

abstract class MigrationEvent implements MigrationEventContract
{
    /**
     * A migration instance.
     *
     * @var \Onepix\FoodSpotVendor\Illuminate\Database\Migrations\Migration
     */
    public $migration;

    /**
     * The migration method that was called.
     *
     * @var string
     */
    public $method;

    /**
     * Create a new event instance.
     *
     * @param  \Onepix\FoodSpotVendor\Illuminate\Database\Migrations\Migration  $migration
     * @param  string  $method
     * @return void
     */
    public function __construct(Migration $migration, $method)
    {
        $this->method = $method;
        $this->migration = $migration;
    }
}
