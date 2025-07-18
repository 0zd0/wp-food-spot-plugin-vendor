<?php

/*
 * This file is part of the Symfony package.
 *
 * (c) Fabien Potencier <fabien@symfony.com>
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

namespace Onepix\FoodSpotVendor\Symfony\Component\Config\Definition\Builder;

use Onepix\FoodSpotVendor\Symfony\Component\Config\Definition\Exception\InvalidDefinitionException;

/**
 * Abstract class that contains common code of integer and float node definitions.
 *
 * @author David Jeanmonod <david.jeanmonod@gmail.com>
 */
abstract class NumericNodeDefinition extends ScalarNodeDefinition
{
    protected int|float|null $min = null;
    protected int|float|null $max = null;

    /**
     * Ensures that the value is smaller than the given reference.
     *
     * @return $this
     *
     * @throws \InvalidArgumentException when the constraint is inconsistent
     */
    public function max(int|float $max): static
    {
        if (isset($this->min) && $this->min > $max) {
            throw new \InvalidArgumentException(\sprintf('You cannot define a max(%s) as you already have a min(%s).', $max, $this->min));
        }
        $this->max = $max;

        return $this;
    }

    /**
     * Ensures that the value is bigger than the given reference.
     *
     * @return $this
     *
     * @throws \InvalidArgumentException when the constraint is inconsistent
     */
    public function min(int|float $min): static
    {
        if (isset($this->max) && $this->max < $min) {
            throw new \InvalidArgumentException(\sprintf('You cannot define a min(%s) as you already have a max(%s).', $min, $this->max));
        }
        $this->min = $min;

        return $this;
    }

    /**
     * @throws InvalidDefinitionException
     */
    public function cannotBeEmpty(): static
    {
        throw new InvalidDefinitionException('->cannotBeEmpty() is not applicable to NumericNodeDefinition.');
    }
}
