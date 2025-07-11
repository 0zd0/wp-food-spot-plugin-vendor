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

/**
 * This class builds validation conditions.
 *
 * @author Christophe Coevoet <stof@notk.org>
 */
class ValidationBuilder
{
    public array $rules = [];

    public function __construct(
        protected NodeDefinition $node,
    ) {
    }

    /**
     * Registers a closure to run as normalization or an expression builder to build it if null is provided.
     *
     * @return ExprBuilder|$this
     */
    public function rule(?\Closure $closure = null): ExprBuilder|static
    {
        if (null !== $closure) {
            $this->rules[] = $closure;

            return $this;
        }

        return $this->rules[] = new ExprBuilder($this->node);
    }
}
