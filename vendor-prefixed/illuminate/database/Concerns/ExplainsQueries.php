<?php

namespace Onepix\FoodSpotVendor\Illuminate\Database\Concerns;

use Onepix\FoodSpotVendor\Illuminate\Support\Collection;

trait ExplainsQueries
{
    /**
     * Explains the query.
     *
     * @return \Onepix\FoodSpotVendor\Illuminate\Support\Collection
     */
    public function explain()
    {
        $sql = $this->toSql();

        $bindings = $this->getBindings();

        $explanation = $this->getConnection()->select('EXPLAIN '.$sql, $bindings);

        return new Collection($explanation);
    }
}
