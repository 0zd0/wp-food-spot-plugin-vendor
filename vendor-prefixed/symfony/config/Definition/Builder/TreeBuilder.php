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

use Onepix\FoodSpotVendor\Symfony\Component\Config\Definition\NodeInterface;

/**
 * This is the entry class for building a config tree.
 *
 * @author Johannes M. Schmitt <schmittjoh@gmail.com>
 */
class TreeBuilder implements NodeParentInterface
{
    protected ?NodeInterface $tree = null;
    protected ?NodeDefinition $root = null;

    public function __construct(string $name, string $type = 'array', ?NodeBuilder $builder = null)
    {
        $builder ??= new NodeBuilder();
        $this->root = $builder->node($name, $type)->setParent($this);
    }

    /**
     * @return NodeDefinition|ArrayNodeDefinition The root node (as an ArrayNodeDefinition when the type is 'array')
     */
    public function getRootNode(): NodeDefinition|ArrayNodeDefinition
    {
        return $this->root;
    }

    /**
     * Builds the tree.
     *
     * @throws \RuntimeException
     */
    public function buildTree(): NodeInterface
    {
        return $this->tree ??= $this->root->getNode(true);
    }

    public function setPathSeparator(string $separator): void
    {
        // unset last built as changing path separator changes all nodes
        $this->tree = null;

        $this->root->setPathSeparator($separator);
    }
}
