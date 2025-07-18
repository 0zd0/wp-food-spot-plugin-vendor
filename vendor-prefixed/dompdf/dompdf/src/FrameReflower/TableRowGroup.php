<?php
/**
 * @package dompdf
 * @link    https://github.com/dompdf/dompdf
 * @license http://www.gnu.org/copyleft/lesser.html GNU Lesser General Public License
 */
namespace Onepix\FoodSpotVendor\Dompdf\FrameReflower;

use Onepix\FoodSpotVendor\Dompdf\Exception;
use Onepix\FoodSpotVendor\Dompdf\FrameDecorator\Block as BlockFrameDecorator;
use Onepix\FoodSpotVendor\Dompdf\FrameDecorator\Table as TableFrameDecorator;
use Onepix\FoodSpotVendor\Dompdf\FrameDecorator\TableRowGroup as TableRowGroupFrameDecorator;

/**
 * Reflows table row groups (e.g. tbody tags)
 *
 * @package dompdf
 */
class TableRowGroup extends AbstractFrameReflower
{

    /**
     * TableRowGroup constructor.
     * @param TableRowGroupFrameDecorator $frame
     */
    function __construct(TableRowGroupFrameDecorator $frame)
    {
        parent::__construct($frame);
    }

    /**
     * @param BlockFrameDecorator|null $block
     */
    function reflow(?BlockFrameDecorator $block = null)
    {
        /** @var TableRowGroupFrameDecorator */
        $frame = $this->_frame;
        $page = $frame->get_root();
        $parent = $frame->get_parent();
        $dompdf_generated = $parent->get_frame()->get_node()->nodeName === "dompdf_generated";

        // Counters and generated content
        $this->_set_content();

        $style = $frame->get_style();
        $cb = $frame->get_containing_block();

        foreach ($frame->get_children() as $child) {
            $child->set_containing_block($cb["x"], $cb["y"], $cb["w"], $cb["h"]);
            $child->reflow();

            // Check if a split has occurred
            $page->check_page_break($child);

            if ($page->is_full()) {
                break;
            }
        }

        if ($page->is_full() && $dompdf_generated && $frame->get_parent() === null) {
            return;
        }

        $table = TableFrameDecorator::find_parent_table($frame);
        if ($table === null) {
            throw new Exception("Parent table not found for table row group");
        }
        $cellmap = $table->get_cellmap();

        // Stop reflow if a page break has occurred before the frame, in which
        // case it is not part of its parent table's cell map yet
        if ($page->is_full() && !$cellmap->frame_exists_in_cellmap($frame)) {
            return;
        }

        $style->set_used("width", $cellmap->get_frame_width($frame));
        $style->set_used("height", $cellmap->get_frame_height($frame));

        $frame->set_position($cellmap->get_frame_position($frame));
    }
}
