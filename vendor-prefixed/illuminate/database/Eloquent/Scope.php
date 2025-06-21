<?php

namespace Onepix\FoodSpotVendor\Illuminate\Database\Eloquent;

interface Scope
{
    /**
     * Apply the scope to a given Eloquent query builder.
     *
     * @param  \Onepix\FoodSpotVendor\Illuminate\Database\Eloquent\Builder  $builder
     * @param  \Onepix\FoodSpotVendor\Illuminate\Database\Eloquent\Model  $model
     * @return void
     */
    public function apply(Builder $builder, Model $model);
}
