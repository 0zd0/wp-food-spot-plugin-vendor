<?php

namespace Onepix\FoodSpotVendor\Illuminate\Support;

use Onepix\FoodSpotVendor\Carbon\Carbon as BaseCarbon;
use Onepix\FoodSpotVendor\Carbon\CarbonImmutable as BaseCarbonImmutable;
use Onepix\FoodSpotVendor\Illuminate\Support\Traits\Conditionable;
use Onepix\FoodSpotVendor\Illuminate\Support\Traits\Dumpable;
use Onepix\FoodSpotVendor\Ramsey\Uuid\Uuid;
use Symfony\Component\Uid\Ulid;

class Carbon extends BaseCarbon
{
    use Conditionable, Dumpable;

    /**
     * {@inheritdoc}
     */
    public static function setTestNow(mixed $testNow = null): void
    {
        BaseCarbon::setTestNow($testNow);
        BaseCarbonImmutable::setTestNow($testNow);
    }

    /**
     * Create a Carbon instance from a given ordered UUID or ULID.
     */
    public static function createFromId(Uuid|Ulid|string $id): static
    {
        if (is_string($id)) {
            $id = Ulid::isValid($id) ? Ulid::fromString($id) : Uuid::fromString($id);
        }

        return static::createFromInterface($id->getDateTime());
    }
}
