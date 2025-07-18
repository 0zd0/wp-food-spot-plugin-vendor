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

use Onepix\FoodSpotVendor\Symfony\Component\Config\Definition\BooleanNode;
use Onepix\FoodSpotVendor\Symfony\Component\Config\Definition\Exception\InvalidDefinitionException;

/**
 * This class provides a fluent interface for defining a node.
 *
 * @author Johannes M. Schmitt <schmittjoh@gmail.com>
 */
class BooleanNodeDefinition extends ScalarNodeDefinition
{
    public function __construct(?string $name, ?NodeParentInterface $parent = null)
    {
        parent::__construct($name, $parent);

        $this->nullEquivalent = true;
    }

    /**
     * Instantiate a Node.
     */
    protected function instantiateNode(): BooleanNode
    {
        return new BooleanNode($this->name, $this->parent, $this->pathSeparator, null === $this->nullEquivalent);
    }

    /**
     * @throws InvalidDefinitionException
     */
    public function cannotBeEmpty(): static
    {
        throw new InvalidDefinitionException('->cannotBeEmpty() is not applicable to BooleanNodeDefinition.');
    }

    public function defaultNull(): static
    {
        $this->nullEquivalent = null;

        return parent::defaultNull();
    }

    public function defaultValue(mixed $value): static
    {
        if (null === $value) {
            $this->nullEquivalent = null;
        }

        return parent::defaultValue($value);
    }
}
