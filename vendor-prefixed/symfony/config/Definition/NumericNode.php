<?php

/*
 * This file is part of the Symfony package.
 *
 * (c) Fabien Potencier <fabien@symfony.com>
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

namespace Onepix\FoodSpotVendor\Symfony\Component\Config\Definition;

use Onepix\FoodSpotVendor\Symfony\Component\Config\Definition\Exception\InvalidConfigurationException;

/**
 * This node represents a numeric value in the config tree.
 *
 * @author David Jeanmonod <david.jeanmonod@gmail.com>
 */
class NumericNode extends ScalarNode
{
    public function __construct(
        ?string $name,
        ?NodeInterface $parent = null,
        protected int|float|null $min = null,
        protected int|float|null $max = null,
        string $pathSeparator = BaseNode::DEFAULT_PATH_SEPARATOR,
    ) {
        parent::__construct($name, $parent, $pathSeparator);
    }

    protected function finalizeValue(mixed $value): mixed
    {
        $value = parent::finalizeValue($value);

        $errorMsg = null;
        if (isset($this->min) && $value < $this->min) {
            $errorMsg = \sprintf('The value %s is too small for path "%s". Should be greater than or equal to %s', $value, $this->getPath(), $this->min);
        }
        if (isset($this->max) && $value > $this->max) {
            $errorMsg = \sprintf('The value %s is too big for path "%s". Should be less than or equal to %s', $value, $this->getPath(), $this->max);
        }
        if (isset($errorMsg)) {
            $ex = new InvalidConfigurationException($errorMsg);
            $ex->setPath($this->getPath());
            throw $ex;
        }

        return $value;
    }

    protected function isValueEmpty(mixed $value): bool
    {
        // a numeric value cannot be empty
        return false;
    }
}
