<?php
/**
 * @package dompdf
 * @link    https://github.com/dompdf/dompdf
 * @license http://www.gnu.org/copyleft/lesser.html GNU Lesser General Public License
 */
namespace Onepix\FoodSpotVendor\Dompdf\Frame;

use Iterator;
use Onepix\FoodSpotVendor\Dompdf\Frame;

/**
 * Pre-order Iterator
 *
 * Returns frames in preorder traversal order (parent then children)
 *
 * @package dompdf
 */
class FrameTreeIterator implements Iterator
{
    /**
     * @var Frame
     */
    protected $_root;

    /**
     * @var Frame[]
     */
    protected $_stack = [];

    /**
     * @var int
     */
    protected $_num;

    /**
     * @param Frame $root
     */
    public function __construct(Frame $root)
    {
        $this->_stack[] = $this->_root = $root;
        $this->_num = 0;
    }

    public function rewind(): void
    {
        $this->_stack = [$this->_root];
        $this->_num = 0;
    }

    /**
     * @return bool
     */
    public function valid(): bool
    {
        return count($this->_stack) > 0;
    }

    /**
     * @return int
     */
    public function key(): int
    {
        return $this->_num;
    }

    /**
     * @return Frame
     */
    public function current(): Frame
    {
        return end($this->_stack);
    }

    public function next(): void
    {
        $b = array_pop($this->_stack);
        $this->_num++;

        // Push all children onto the stack in reverse order
        if ($c = $b->get_last_child()) {
            $this->_stack[] = $c;
            while ($c = $c->get_prev_sibling()) {
                $this->_stack[] = $c;
            }
        }
    }
}
