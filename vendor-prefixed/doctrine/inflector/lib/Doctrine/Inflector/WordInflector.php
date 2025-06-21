<?php

declare(strict_types=1);

namespace Onepix\FoodSpotVendor\Doctrine\Inflector;

interface WordInflector
{
    public function inflect(string $word): string;
}
