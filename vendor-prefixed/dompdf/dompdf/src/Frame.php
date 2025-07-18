<?php

/**
 * @package dompdf
 * @link    https://github.com/dompdf/dompdf
 * @license http://www.gnu.org/copyleft/lesser.html GNU Lesser General Public License
 */
namespace Onepix\FoodSpotVendor\Dompdf;

use Onepix\FoodSpotVendor\Dompdf\Css\Style;
use Onepix\FoodSpotVendor\Dompdf\Frame\FrameListIterator;
/**
 * The main Frame class
 *
 * This class represents a single HTML element.  This class stores
 * positioning information as well as containing block location and
 * dimensions. Style information for the element is stored in a {@link
 * Style} object. Tree structure is maintained via the parent & children
 * links.
 *
 * @package dompdf
 */
class Frame
{
    const WS_TEXT = 1;
    const WS_SPACE = 2;
    /**
     * The DOMElement or DOMText object this frame represents
     *
     * @var \DOMElement|\DOMText
     */
    protected $_node;
    /**
     * Unique identifier for this frame.  Used to reference this frame
     * via the node.
     *
     * @var int
     */
    protected $_id;
    /**
     * Unique id counter
     *
     * @var int
     */
    public static $ID_COUNTER = 0;
    /*protected*/
    /**
     * This frame's calculated style
     *
     * @var Style|null
     */
    protected $_style;
    /**
     * This frame's parent in the document tree.
     *
     * @var Frame|null
     */
    protected $_parent;
    /**
     * This frame's first child.  All children are handled as a
     * doubly-linked list.
     *
     * @var Frame|null
     */
    protected $_first_child;
    /**
     * This frame's last child.
     *
     * @var Frame|null
     */
    protected $_last_child;
    /**
     * This frame's previous sibling in the document tree.
     *
     * @var Frame|null
     */
    protected $_prev_sibling;
    /**
     * This frame's next sibling in the document tree.
     *
     * @var Frame|null
     */
    protected $_next_sibling;
    /**
     * This frame's containing block (used in layout): array(x, y, w, h)
     *
     * @var float[]
     */
    protected $_containing_block;
    /**
     * Position on the page of the top-left corner of the margin box of
     * this frame: array(x,y)
     *
     * @var float[]
     */
    protected $_position;
    /**
     * Absolute opacity of this frame
     *
     * @var float
     */
    protected $_opacity;
    /**
     * This frame's decorator
     *
     * @var FrameDecorator\AbstractFrameDecorator
     */
    protected $_decorator;
    /**
     * This frame's containing line box
     *
     * @var LineBox|null
     */
    protected $_containing_line;
    /**
     * @var array
     */
    protected $_is_cache = [];
    /**
     * Tells whether the frame was already pushed to the next page
     *
     * @var bool
     */
    public $_already_pushed = false;
    /**
     * @var bool
     */
    public $_float_next_line = false;
    /**
     * @var int
     */
    public static $_ws_state = self::WS_SPACE;
    /**
     * Class constructor
     *
     * @param \DOMNode $node the DOMNode this frame represents
     */
    public function __construct(\DOMNode $node)
    {
        $this->_node = $node;
        $this->_parent = null;
        $this->_first_child = null;
        $this->_last_child = null;
        $this->_prev_sibling = $this->_next_sibling = null;
        $this->_style = null;
        $this->_containing_block = ["x" => null, "y" => null, "w" => null, "h" => null];
        $this->_containing_block[0] =& $this->_containing_block["x"];
        $this->_containing_block[1] =& $this->_containing_block["y"];
        $this->_containing_block[2] =& $this->_containing_block["w"];
        $this->_containing_block[3] =& $this->_containing_block["h"];
        $this->_position = ["x" => null, "y" => null];
        $this->_position[0] =& $this->_position["x"];
        $this->_position[1] =& $this->_position["y"];
        $this->_opacity = 1.0;
        $this->_decorator = null;
        $this->set_id(self::$ID_COUNTER++);
    }
    /**
     * WIP : preprocessing to remove all the unused whitespace
     */
    protected function ws_trim()
    {
        if ($this->ws_keep()) {
            return;
        }
        if (self::$_ws_state === self::WS_SPACE) {
            $node = $this->_node;
            if ($node->nodeName === "#text" && !empty($node->nodeValue)) {
                $node->nodeValue = preg_replace("/[ \t\r\n\f]+/u", " ", trim($node->nodeValue));
                self::$_ws_state = self::WS_TEXT;
            }
        }
    }
    /**
     * @return bool
     */
    protected function ws_keep()
    {
        $whitespace = $this->get_style()->white_space;
        return in_array($whitespace, ["pre", "pre-wrap", "pre-line"]);
    }
    /**
     * @return bool
     */
    protected function ws_is_text()
    {
        $node = $this->get_node();
        if ($node->nodeName === "img") {
            return true;
        }
        if (!$this->is_in_flow()) {
            return false;
        }
        if ($this->is_text_node()) {
            return trim($node->nodeValue) !== "";
        }
        return true;
    }
    /**
     * "Destructor": forcibly free all references held by this frame
     *
     * @param bool $recursive if true, call dispose on all children
     */
    public function dispose($recursive = false)
    {
        if ($recursive) {
            while ($child = $this->_first_child) {
                $child->dispose(true);
            }
        }
        // Remove this frame from the tree
        if ($this->_prev_sibling) {
            $this->_prev_sibling->_next_sibling = $this->_next_sibling;
        }
        if ($this->_next_sibling) {
            $this->_next_sibling->_prev_sibling = $this->_prev_sibling;
        }
        if ($this->_parent && $this->_parent->_first_child === $this) {
            $this->_parent->_first_child = $this->_next_sibling;
        }
        if ($this->_parent && $this->_parent->_last_child === $this) {
            $this->_parent->_last_child = $this->_prev_sibling;
        }
        if ($this->_parent) {
            $this->_parent->get_node()->removeChild($this->_node);
        }
        $this->_style = null;
        unset($this->_style);
    }
    /**
     * Re-initialize the frame
     */
    public function reset()
    {
        $this->_position["x"] = null;
        $this->_position["y"] = null;
        $this->_containing_block["x"] = null;
        $this->_containing_block["y"] = null;
        $this->_containing_block["w"] = null;
        $this->_containing_block["h"] = null;
        $this->_style->reset();
    }
    /**
     * @return \DOMElement|\DOMText
     */
    public function get_node()
    {
        return $this->_node;
    }
    /**
     * @return int
     */
    public function get_id()
    {
        return $this->_id;
    }
    /**
     * @return Style
     */
    public function get_style()
    {
        return $this->_style;
    }
    /**
     * @deprecated
     * @return Style
     */
    public function get_original_style()
    {
        return $this->_style;
    }
    /**
     * @return Frame
     */
    public function get_parent()
    {
        return $this->_parent;
    }
    /**
     * @return FrameDecorator\AbstractFrameDecorator
     */
    public function get_decorator()
    {
        return $this->_decorator;
    }
    /**
     * @return Frame
     */
    public function get_first_child()
    {
        return $this->_first_child;
    }
    /**
     * @return Frame
     */
    public function get_last_child()
    {
        return $this->_last_child;
    }
    /**
     * @return Frame
     */
    public function get_prev_sibling()
    {
        return $this->_prev_sibling;
    }
    /**
     * @return Frame
     */
    public function get_next_sibling()
    {
        return $this->_next_sibling;
    }
    /**
     * @return FrameListIterator
     */
    public function get_children(): FrameListIterator
    {
        return new FrameListIterator($this);
    }
    // Layout property accessors
    /**
     * Containing block dimensions
     *
     * @param string|null $i The key of the wanted containing block's dimension (x, y, w, h)
     *
     * @return float[]|float
     */
    public function get_containing_block($i = null)
    {
        if (isset($i)) {
            return $this->_containing_block[$i];
        }
        return $this->_containing_block;
    }
    /**
     * Block position
     *
     * @param string|null $i The key of the wanted position value (x, y)
     *
     * @return float[]|float
     */
    public function get_position($i = null)
    {
        if (isset($i)) {
            return $this->_position[$i];
        }
        return $this->_position;
    }
    //........................................................................
    /**
     * Return the width of the margin box of the frame, in pt.  Meaningless
     * unless the width has been calculated properly.
     *
     * @return float
     */
    public function get_margin_width(): float
    {
        $style = $this->_style;
        return (float) $style->length_in_pt([$style->width, $style->margin_left, $style->margin_right, $style->border_left_width, $style->border_right_width, $style->padding_left, $style->padding_right], $this->_containing_block["w"]);
    }
    /**
     * Return the height of the margin box of the frame, in pt.  Meaningless
     * unless the height has been calculated properly.
     *
     * @return float
     */
    public function get_margin_height(): float
    {
        $style = $this->_style;
        return (float) $style->length_in_pt([$style->height, (float) $style->length_in_pt([$style->border_top_width, $style->border_bottom_width, $style->margin_top, $style->margin_bottom, $style->padding_top, $style->padding_bottom], $this->_containing_block["w"])], $this->_containing_block["h"]);
    }
    /**
     * Return the content box (x,y,w,h) of the frame.
     *
     * Width and height might be reported as 0 if they have not been resolved
     * yet.
     *
     * @return float[]
     */
    public function get_content_box(): array
    {
        $style = $this->_style;
        $cb = $this->_containing_block;
        $x = $this->_position["x"] + (float) $style->length_in_pt([$style->margin_left, $style->border_left_width, $style->padding_left], $cb["w"]);
        $y = $this->_position["y"] + (float) $style->length_in_pt([$style->margin_top, $style->border_top_width, $style->padding_top], $cb["w"]);
        $w = (float) $style->length_in_pt($style->width, $cb["w"]);
        $h = (float) $style->length_in_pt($style->height, $cb["h"]);
        return [0 => $x, "x" => $x, 1 => $y, "y" => $y, 2 => $w, "w" => $w, 3 => $h, "h" => $h];
    }
    /**
     * Return the padding box (x,y,w,h) of the frame.
     *
     * Width and height might be reported as 0 if they have not been resolved
     * yet.
     *
     * @return float[]
     */
    public function get_padding_box(): array
    {
        $style = $this->_style;
        $cb = $this->_containing_block;
        $x = $this->_position["x"] + (float) $style->length_in_pt([$style->margin_left, $style->border_left_width], $cb["w"]);
        $y = $this->_position["y"] + (float) $style->length_in_pt([$style->margin_top, $style->border_top_width], $cb["h"]);
        $w = (float) $style->length_in_pt([$style->padding_left, $style->width, $style->padding_right], $cb["w"]);
        $h = (float) $style->length_in_pt([$style->padding_top, $style->padding_bottom, $style->length_in_pt($style->height, $cb["h"])], $cb["w"]);
        return [0 => $x, "x" => $x, 1 => $y, "y" => $y, 2 => $w, "w" => $w, 3 => $h, "h" => $h];
    }
    /**
     * Return the border box of the frame.
     *
     * Width and height might be reported as 0 if they have not been resolved
     * yet.
     *
     * @return float[]
     */
    public function get_border_box(): array
    {
        $style = $this->_style;
        $cb = $this->_containing_block;
        $x = $this->_position["x"] + (float) $style->length_in_pt($style->margin_left, $cb["w"]);
        $y = $this->_position["y"] + (float) $style->length_in_pt($style->margin_top, $cb["w"]);
        $w = (float) $style->length_in_pt([$style->border_left_width, $style->padding_left, $style->width, $style->padding_right, $style->border_right_width], $cb["w"]);
        $h = (float) $style->length_in_pt([$style->border_top_width, $style->padding_top, $style->padding_bottom, $style->border_bottom_width, $style->length_in_pt($style->height, $cb["h"])], $cb["w"]);
        return [0 => $x, "x" => $x, 1 => $y, "y" => $y, 2 => $w, "w" => $w, 3 => $h, "h" => $h];
    }
    /**
     * @param float|null $opacity
     *
     * @return float
     */
    public function get_opacity(?float $opacity = null): float
    {
        if ($opacity !== null) {
            $this->set_opacity($opacity);
        }
        return $this->_opacity;
    }
    /**
     * @return LineBox|null
     */
    public function &get_containing_line()
    {
        return $this->_containing_line;
    }
    //........................................................................
    // Set methods
    /**
     * @param int $id
     */
    public function set_id($id)
    {
        $this->_id = $id;
        // We can only set attributes of DOMElement objects (nodeType == 1).
        // Since these are the only objects that we can assign CSS rules to,
        // this shortcoming is okay.
        if ($this->_node->nodeType == XML_ELEMENT_NODE) {
            $this->_node->setAttribute("frame_id", $id);
        }
    }
    /**
     * @param Style $style
     */
    public function set_style(Style $style): void
    {
        // $style->set_frame($this);
        $this->_style = $style;
    }
    /**
     * @param FrameDecorator\AbstractFrameDecorator $decorator
     */
    public function set_decorator(\Onepix\FoodSpotVendor\Dompdf\FrameDecorator\AbstractFrameDecorator $decorator)
    {
        $this->_decorator = $decorator;
    }
    /**
     * @param float|float[]|null $x
     * @param float|null $y
     * @param float|null $w
     * @param float|null $h
     */
    public function set_containing_block($x = null, $y = null, $w = null, $h = null)
    {
        if (is_array($x)) {
            foreach ($x as $key => $val) {
                ${$key} = $val;
            }
        }
        if (is_numeric($x)) {
            $this->_containing_block["x"] = $x;
        }
        if (is_numeric($y)) {
            $this->_containing_block["y"] = $y;
        }
        if (is_numeric($w)) {
            $this->_containing_block["w"] = $w;
        }
        if (is_numeric($h)) {
            $this->_containing_block["h"] = $h;
        }
    }
    /**
     * @param float|float[]|null $x
     * @param float|null $y
     */
    public function set_position($x = null, $y = null)
    {
        if (is_array($x)) {
            list($x, $y) = [$x["x"], $x["y"]];
        }
        if (is_numeric($x)) {
            $this->_position["x"] = $x;
        }
        if (is_numeric($y)) {
            $this->_position["y"] = $y;
        }
    }
    /**
     * @param float $opacity
     */
    public function set_opacity(float $opacity): void
    {
        $parent = $this->get_parent();
        $base_opacity = $parent && $parent->_opacity !== null ? $parent->_opacity : 1.0;
        $this->_opacity = $base_opacity * $opacity;
    }
    /**
     * @param LineBox $line
     */
    public function set_containing_line(LineBox $line)
    {
        $this->_containing_line = $line;
    }
    /**
     * Indicates if the margin height is auto sized
     *
     * @return bool
     */
    public function is_auto_height()
    {
        $style = $this->_style;
        return in_array("auto", [$style->height, $style->margin_top, $style->margin_bottom, $style->border_top_width, $style->border_bottom_width, $style->padding_top, $style->padding_bottom, $this->_containing_block["h"]], true);
    }
    /**
     * Indicates if the margin width is auto sized
     *
     * @return bool
     */
    public function is_auto_width()
    {
        $style = $this->_style;
        return in_array("auto", [$style->width, $style->margin_left, $style->margin_right, $style->border_left_width, $style->border_right_width, $style->padding_left, $style->padding_right, $this->_containing_block["w"]], true);
    }
    /**
     * Tells if the frame is a text node
     *
     * @return bool
     */
    public function is_text_node(): bool
    {
        if (isset($this->_is_cache["text_node"])) {
            return $this->_is_cache["text_node"];
        }
        return $this->_is_cache["text_node"] = $this->get_node()->nodeName === "#text";
    }
    /**
     * @return bool
     */
    public function is_positioned(): bool
    {
        if (isset($this->_is_cache["positioned"])) {
            return $this->_is_cache["positioned"];
        }
        $position = $this->get_style()->position;
        return $this->_is_cache["positioned"] = in_array($position, Style::POSITIONED_TYPES, true);
    }
    /**
     * @return bool
     */
    public function is_absolute(): bool
    {
        if (isset($this->_is_cache["absolute"])) {
            return $this->_is_cache["absolute"];
        }
        return $this->_is_cache["absolute"] = $this->get_style()->is_absolute();
    }
    /**
     * Whether the frame is a block container.
     *
     * @return bool
     */
    public function is_block(): bool
    {
        if (isset($this->_is_cache["block"])) {
            return $this->_is_cache["block"];
        }
        return $this->_is_cache["block"] = in_array($this->get_style()->display, Style::BLOCK_TYPES, true);
    }
    /**
     * Whether the frame has a block-level display type.
     *
     * @return bool
     */
    public function is_block_level(): bool
    {
        if (isset($this->_is_cache["block_level"])) {
            return $this->_is_cache["block_level"];
        }
        $display = $this->get_style()->display;
        return $this->_is_cache["block_level"] = in_array($display, Style::BLOCK_LEVEL_TYPES, true);
    }
    /**
     * Whether the frame has an inline-level display type.
     *
     * @return bool
     */
    public function is_inline_level(): bool
    {
        if (isset($this->_is_cache["inline_level"])) {
            return $this->_is_cache["inline_level"];
        }
        $display = $this->get_style()->display;
        return $this->_is_cache["inline_level"] = in_array($display, Style::INLINE_LEVEL_TYPES, true);
    }
    /**
     * @return bool
     */
    public function is_in_flow(): bool
    {
        if (isset($this->_is_cache["in_flow"])) {
            return $this->_is_cache["in_flow"];
        }
        return $this->_is_cache["in_flow"] = $this->get_style()->is_in_flow();
    }
    /**
     * @return bool
     */
    public function is_pre(): bool
    {
        if (isset($this->_is_cache["pre"])) {
            return $this->_is_cache["pre"];
        }
        $white_space = $this->get_style()->white_space;
        return $this->_is_cache["pre"] = in_array($white_space, ["pre", "pre-wrap"], true);
    }
    /**
     * @return bool
     */
    public function is_table(): bool
    {
        if (isset($this->_is_cache["table"])) {
            return $this->_is_cache["table"];
        }
        $display = $this->get_style()->display;
        return $this->_is_cache["table"] = in_array($display, Style::TABLE_TYPES, true);
    }
    /**
     * Inserts a new child at the beginning of the Frame
     *
     * @param Frame $child       The new Frame to insert
     * @param bool  $update_node Whether or not to update the DOM
     */
    public function prepend_child(Frame $child, $update_node = true)
    {
        if ($update_node) {
            $this->_node->insertBefore($child->_node, $this->_first_child ? $this->_first_child->_node : null);
        }
        // Remove the child from its parent
        if ($child->_parent) {
            $child->_parent->remove_child($child, false);
        }
        $child->_parent = $this;
        $decorator = $child->get_decorator();
        // force an update to the cached parent
        if ($decorator !== null) {
            $decorator->get_parent(false);
        }
        $child->_prev_sibling = null;
        // Handle the first child
        if (!$this->_first_child) {
            $this->_first_child = $child;
            $this->_last_child = $child;
            $child->_next_sibling = null;
        } else {
            $this->_first_child->_prev_sibling = $child;
            $child->_next_sibling = $this->_first_child;
            $this->_first_child = $child;
        }
    }
    /**
     * Inserts a new child at the end of the Frame
     *
     * @param Frame $child       The new Frame to insert
     * @param bool  $update_node Whether or not to update the DOM
     */
    public function append_child(Frame $child, $update_node = true)
    {
        if ($update_node) {
            $this->_node->appendChild($child->_node);
        }
        // Remove the child from its parent
        if ($child->_parent) {
            $child->_parent->remove_child($child, false);
        }
        $child->_parent = $this;
        $decorator = $child->get_decorator();
        // force an update to the cached parent
        if ($decorator !== null) {
            $decorator->get_parent(false);
        }
        $child->_next_sibling = null;
        // Handle the first child
        if (!$this->_last_child) {
            $this->_first_child = $child;
            $this->_last_child = $child;
            $child->_prev_sibling = null;
        } else {
            $this->_last_child->_next_sibling = $child;
            $child->_prev_sibling = $this->_last_child;
            $this->_last_child = $child;
        }
    }
    /**
     * Inserts a new child immediately before the specified frame
     *
     * @param Frame $new_child   The new Frame to insert
     * @param Frame $ref         The Frame after the new Frame
     * @param bool  $update_node Whether or not to update the DOM
     *
     * @throws Exception
     */
    public function insert_child_before(Frame $new_child, Frame $ref, $update_node = true)
    {
        if ($ref === $this->_first_child) {
            $this->prepend_child($new_child, $update_node);
            return;
        }
        if (is_null($ref)) {
            $this->append_child($new_child, $update_node);
            return;
        }
        if ($ref->_parent !== $this) {
            throw new Exception("Reference child is not a child of this node.");
        }
        // Update the node
        if ($update_node) {
            $this->_node->insertBefore($new_child->_node, $ref->_node);
        }
        // Remove the child from its parent
        if ($new_child->_parent) {
            $new_child->_parent->remove_child($new_child, false);
        }
        $new_child->_parent = $this;
        $decorator = $new_child->get_decorator();
        // force an update to the cached parent
        if ($decorator !== null) {
            $decorator->get_parent(false);
        }
        $new_child->_next_sibling = $ref;
        $new_child->_prev_sibling = $ref->_prev_sibling;
        if ($ref->_prev_sibling) {
            $ref->_prev_sibling->_next_sibling = $new_child;
        }
        $ref->_prev_sibling = $new_child;
    }
    /**
     * Inserts a new child immediately after the specified frame
     *
     * @param Frame $new_child   The new Frame to insert
     * @param Frame $ref         The Frame before the new Frame
     * @param bool  $update_node Whether or not to update the DOM
     *
     * @throws Exception
     */
    public function insert_child_after(Frame $new_child, Frame $ref, $update_node = true)
    {
        if ($ref === $this->_last_child) {
            $this->append_child($new_child, $update_node);
            return;
        }
        if (is_null($ref)) {
            $this->prepend_child($new_child, $update_node);
            return;
        }
        if ($ref->_parent !== $this) {
            throw new Exception("Reference child is not a child of this node.");
        }
        // Update the node
        if ($update_node) {
            if ($ref->_next_sibling) {
                $next_node = $ref->_next_sibling->_node;
                $this->_node->insertBefore($new_child->_node, $next_node);
            } else {
                $new_child->_node = $this->_node->appendChild($new_child->_node);
            }
        }
        // Remove the child from its parent
        if ($new_child->_parent) {
            $new_child->_parent->remove_child($new_child, false);
        }
        $new_child->_parent = $this;
        $decorator = $new_child->get_decorator();
        // force an update to the cached parent
        if ($decorator !== null) {
            $decorator->get_parent(false);
        }
        $new_child->_prev_sibling = $ref;
        $new_child->_next_sibling = $ref->_next_sibling;
        if ($ref->_next_sibling) {
            $ref->_next_sibling->_prev_sibling = $new_child;
        }
        $ref->_next_sibling = $new_child;
    }
    /**
     * Remove a child frame
     *
     * @param Frame $child
     * @param bool  $update_node Whether or not to remove the DOM node
     *
     * @throws Exception
     * @return Frame The removed child frame
     */
    public function remove_child(Frame $child, $update_node = true)
    {
        if ($child->_parent !== $this) {
            throw new Exception("Child not found in this frame");
        }
        if ($update_node) {
            $this->_node->removeChild($child->_node);
        }
        if ($child === $this->_first_child) {
            $this->_first_child = $child->_next_sibling;
        }
        if ($child === $this->_last_child) {
            $this->_last_child = $child->_prev_sibling;
        }
        if ($child->_prev_sibling) {
            $child->_prev_sibling->_next_sibling = $child->_next_sibling;
        }
        if ($child->_next_sibling) {
            $child->_next_sibling->_prev_sibling = $child->_prev_sibling;
        }
        $child->_next_sibling = null;
        $child->_prev_sibling = null;
        $child->_parent = null;
        // Force an update to the cached decorator parent
        $decorator = $child->get_decorator();
        if ($decorator !== null) {
            $decorator->get_parent(false);
        }
        return $child;
    }
    //........................................................................
    // Debugging function:
    /**
     * @return string
     */
    public function __toString()
    {
        // Skip empty text frames
        //     if ( $this->is_text_node() &&
        //          preg_replace("/\s/", "", $this->_node->data) === "" )
        //       return "";
        $str = "<b>" . $this->_node->nodeName . ":</b><br/>";
        //$str .= spl_object_hash($this->_node) . "<br/>";
        $str .= "Id: " . $this->get_id() . "<br/>";
        $str .= "Class: " . get_class($this) . "<br/>";
        if ($this->is_text_node()) {
            $tmp = htmlspecialchars($this->_node->nodeValue);
            $str .= "<pre>'" . mb_substr($tmp, 0, 70) . (mb_strlen($tmp) > 70 ? "..." : "") . "'</pre>";
        } elseif ($css_class = $this->_node->getAttribute("class")) {
            $str .= "CSS class: '{$css_class}'<br/>";
        }
        if ($this->_parent) {
            $str .= "\nParent:" . $this->_parent->_node->nodeName . " (" . spl_object_hash($this->_parent->_node) . ") " . "<br/>";
        }
        if ($this->_prev_sibling) {
            $str .= "Prev: " . $this->_prev_sibling->_node->nodeName . " (" . spl_object_hash($this->_prev_sibling->_node) . ") " . "<br/>";
        }
        if ($this->_next_sibling) {
            $str .= "Next: " . $this->_next_sibling->_node->nodeName . " (" . spl_object_hash($this->_next_sibling->_node) . ") " . "<br/>";
        }
        $d = $this->get_decorator();
        while ($d && $d != $d->get_decorator()) {
            $str .= "Decorator: " . get_class($d) . "<br/>";
            $d = $d->get_decorator();
        }
        $str .= "Position: " . Helpers::pre_r($this->_position, true);
        $str .= "\nContaining block: " . Helpers::pre_r($this->_containing_block, true);
        $str .= "\nMargin width: " . Helpers::pre_r($this->get_margin_width(), true);
        $str .= "\nMargin height: " . Helpers::pre_r($this->get_margin_height(), true);
        $str .= "\nStyle: <pre>" . $this->_style->__toString() . "</pre>";
        if ($this->_decorator instanceof \Onepix\FoodSpotVendor\Dompdf\FrameDecorator\Block) {
            $str .= "Lines:<pre>";
            foreach ($this->_decorator->get_line_boxes() as $line) {
                foreach ($line->get_frames() as $frame) {
                    if ($frame instanceof \Onepix\FoodSpotVendor\Dompdf\FrameDecorator\Text) {
                        $str .= "\ntext: ";
                        $str .= "'" . htmlspecialchars($frame->get_text()) . "'";
                    } else {
                        $str .= "\nBlock: " . $frame->get_node()->nodeName . " (" . spl_object_hash($frame->get_node()) . ")";
                    }
                }
                $str .= "\ny => " . $line->y . "\n" . "w => " . $line->w . "\n" . "h => " . $line->h . "\n" . "left => " . $line->left . "\n" . "right => " . $line->right . "\n";
            }
            $str .= "</pre>";
        }
        $str .= "\n";
        if (php_sapi_name() === "cli") {
            $str = strip_tags(str_replace(["<br/>", "<b>", "</b>"], ["\n", "", ""], $str));
        }
        return $str;
    }
}