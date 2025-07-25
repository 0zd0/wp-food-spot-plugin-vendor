<?php

namespace Onepix\FoodSpotVendor\Illuminate\Database\Eloquent\Attributes;

use Attribute;

#[Attribute(Attribute::TARGET_CLASS)]
class CollectedBy
{
    /**
     * Create a new attribute instance.
     *
     * @param  class-string<\Onepix\FoodSpotVendor\Illuminate\Database\Eloquent\Collection<*, *>>  $collectionClass
     * @return void
     */
    public function __construct(public string $collectionClass)
    {
    }
}
