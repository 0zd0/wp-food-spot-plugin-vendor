<?php

/*
 * This file is part of Pimple.
 *
 * Copyright (c) 2009 Fabien Potencier
 *
 * Permission is hereby granted, free of charge, to any person obtaining a copy
 * of this software and associated documentation files (the "Software"), to deal
 * in the Software without restriction, including without limitation the rights
 * to use, copy, modify, merge, publish, distribute, sublicense, and/or sell
 * copies of the Software, and to permit persons to whom the Software is furnished
 * to do so, subject to the following conditions:
 *
 * The above copyright notice and this permission notice shall be included in all
 * copies or substantial portions of the Software.
 *
 * THE SOFTWARE IS PROVIDED "AS IS", WITHOUT WARRANTY OF ANY KIND, EXPRESS OR
 * IMPLIED, INCLUDING BUT NOT LIMITED TO THE WARRANTIES OF MERCHANTABILITY,
 * FITNESS FOR A PARTICULAR PURPOSE AND NONINFRINGEMENT. IN NO EVENT SHALL THE
 * AUTHORS OR COPYRIGHT HOLDERS BE LIABLE FOR ANY CLAIM, DAMAGES OR OTHER
 * LIABILITY, WHETHER IN AN ACTION OF CONTRACT, TORT OR OTHERWISE, ARISING FROM,
 * OUT OF OR IN CONNECTION WITH THE SOFTWARE OR THE USE OR OTHER DEALINGS IN
 * THE SOFTWARE.
 */

namespace Onepix\FoodSpotVendor\Carbon_Fields\Pimple;

/**
 * Lazy service iterator.
 *
 * @author Pascal Luna <skalpa@zetareticuli.org>
 */
final class ServiceIterator implements \Iterator
{
    private $container;
    private $ids;

    public function __construct(Container $container, array $ids)
    {
        $this->container = $container;
        $this->ids = $ids;
    }

    /**
     * @return void
     */
    #[\ReturnTypeWillChange]
    public function rewind()
    {
        \reset($this->ids);
    }

    /**
     * @return mixed
     */
    #[\ReturnTypeWillChange]
    public function current()
    {
        return $this->container[\current($this->ids)];
    }

    /**
     * @return mixed
     */
    #[\ReturnTypeWillChange]
    public function key()
    {
        return \current($this->ids);
    }

    /**
     * @return void
     */
    #[\ReturnTypeWillChange]
    public function next()
    {
        \next($this->ids);
    }

    /**
     * @return bool
     */
    #[\ReturnTypeWillChange]
    public function valid()
    {
        return null !== \key($this->ids);
    }
}
