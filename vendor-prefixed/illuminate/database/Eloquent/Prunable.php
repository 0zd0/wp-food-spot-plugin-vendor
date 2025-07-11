<?php

namespace Onepix\FoodSpotVendor\Illuminate\Database\Eloquent;

use Onepix\FoodSpotVendor\Illuminate\Database\Events\ModelsPruned;
use LogicException;

trait Prunable
{
    /**
     * Prune all prunable models in the database.
     *
     * @param  int  $chunkSize
     * @return int
     */
    public function pruneAll(int $chunkSize = 1000)
    {
        $total = 0;

        $this->prunable()
            ->when(in_array(SoftDeletes::class, onepix_foodspotvendor_class_uses_recursive(static::class)), function ($query) {
                $query->withTrashed();
            })->chunkById($chunkSize, function ($models) use (&$total) {
                $models->each->prune();

                $total += $models->count();

                onepix_foodspotvendor_event(new ModelsPruned(static::class, $total));
            });

        return $total;
    }

    /**
     * Get the prunable model query.
     *
     * @return \Onepix\FoodSpotVendor\Illuminate\Database\Eloquent\Builder<static>
     */
    public function prunable()
    {
        throw new LogicException('Please implement the prunable method on your model.');
    }

    /**
     * Prune the model in the database.
     *
     * @return bool|null
     */
    public function prune()
    {
        $this->pruning();

        return in_array(SoftDeletes::class, onepix_foodspotvendor_class_uses_recursive(static::class))
                ? $this->forceDelete()
                : $this->delete();
    }

    /**
     * Prepare the model for pruning.
     *
     * @return void
     */
    protected function pruning()
    {
        //
    }
}
