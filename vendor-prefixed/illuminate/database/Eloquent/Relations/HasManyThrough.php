<?php

namespace Onepix\FoodSpotVendor\Illuminate\Database\Eloquent\Relations;

use Onepix\FoodSpotVendor\Illuminate\Database\Eloquent\Builder;
use Onepix\FoodSpotVendor\Illuminate\Database\Eloquent\Collection as EloquentCollection;
use Onepix\FoodSpotVendor\Illuminate\Database\Eloquent\Relations\Concerns\InteractsWithDictionary;

/**
 * @template TRelatedModel of \Illuminate\Database\Eloquent\Model
 * @template TIntermediateModel of \Illuminate\Database\Eloquent\Model
 * @template TDeclaringModel of \Illuminate\Database\Eloquent\Model
 *
 * @extends \Onepix\FoodSpotVendor\Illuminate\Database\Eloquent\Relations\HasOneOrManyThrough<TRelatedModel, TIntermediateModel, TDeclaringModel, \Onepix\FoodSpotVendor\Illuminate\Database\Eloquent\Collection<int, TRelatedModel>>
 */
class HasManyThrough extends HasOneOrManyThrough
{
    use InteractsWithDictionary;

    /**
     * Convert the relationship to a "has one through" relationship.
     *
     * @return \Onepix\FoodSpotVendor\Illuminate\Database\Eloquent\Relations\HasOneThrough<TRelatedModel, TIntermediateModel, TDeclaringModel>
     */
    public function one()
    {
        return HasOneThrough::noConstraints(fn () => new HasOneThrough(
            onepix_foodspotvendor_tap($this->getQuery(), fn (Builder $query) => $query->getQuery()->joins = []),
            $this->farParent,
            $this->throughParent,
            $this->getFirstKeyName(),
            $this->secondKey,
            $this->getLocalKeyName(),
            $this->getSecondLocalKeyName(),
        ));
    }

    /** @inheritDoc */
    public function initRelation(array $models, $relation)
    {
        foreach ($models as $model) {
            $model->setRelation($relation, $this->related->newCollection());
        }

        return $models;
    }

    /** @inheritDoc */
    public function match(array $models, EloquentCollection $results, $relation)
    {
        $dictionary = $this->buildDictionary($results);

        // Once we have the dictionary we can simply spin through the parent models to
        // link them up with their children using the keyed dictionary to make the
        // matching very convenient and easy work. Then we'll just return them.
        foreach ($models as $model) {
            if (isset($dictionary[$key = $this->getDictionaryKey($model->getAttribute($this->localKey))])) {
                $model->setRelation(
                    $relation, $this->related->newCollection($dictionary[$key])
                );
            }
        }

        return $models;
    }

    /** @inheritDoc */
    public function getResults()
    {
        return ! is_null($this->farParent->{$this->localKey})
                ? $this->get()
                : $this->related->newCollection();
    }
}
