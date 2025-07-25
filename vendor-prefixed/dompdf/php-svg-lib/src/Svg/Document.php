<?php
/**
 * @package php-svg-lib
 * @link    http://github.com/dompdf/php-svg-lib
 * @license GNU LGPLv3+ http://www.gnu.org/copyleft/lesser.html
 */

namespace Onepix\FoodSpotVendor\Svg;

use Onepix\FoodSpotVendor\Svg\Surface\SurfaceInterface;
use Onepix\FoodSpotVendor\Svg\Tag\AbstractTag;
use Onepix\FoodSpotVendor\Svg\Tag\Anchor;
use Onepix\FoodSpotVendor\Svg\Tag\Circle;
use Onepix\FoodSpotVendor\Svg\Tag\Ellipse;
use Onepix\FoodSpotVendor\Svg\Tag\Group;
use Onepix\FoodSpotVendor\Svg\Tag\ClipPath;
use Onepix\FoodSpotVendor\Svg\Tag\Image;
use Onepix\FoodSpotVendor\Svg\Tag\Line;
use Onepix\FoodSpotVendor\Svg\Tag\LinearGradient;
use Onepix\FoodSpotVendor\Svg\Tag\Path;
use Onepix\FoodSpotVendor\Svg\Tag\Polygon;
use Onepix\FoodSpotVendor\Svg\Tag\Polyline;
use Onepix\FoodSpotVendor\Svg\Tag\Rect;
use Onepix\FoodSpotVendor\Svg\Tag\Stop;
use Onepix\FoodSpotVendor\Svg\Tag\Symbol;
use Onepix\FoodSpotVendor\Svg\Tag\Text;
use Onepix\FoodSpotVendor\Svg\Tag\StyleTag;
use Onepix\FoodSpotVendor\Svg\Tag\UseTag;

class Document extends AbstractTag
{
    protected $filename;
    protected $_defs_depth = 0;
    public $inDefs = false;

    protected $x;
    protected $y;
    protected $width;
    protected $height;

    protected $subPathInit;
    protected $pathBBox;
    protected $viewBox;

    /** @var SurfaceInterface */
    protected $surface;

    /** @var AbstractTag[] */
    protected $stack = array();

    /** @var AbstractTag[] */
    protected $defs = array();

    /** @var \Onepix\FoodSpotVendor\Sabberworm\CSS\CSSList\Document[] */
    protected $styleSheets = array();

    public $allowExternalReferences = true;

    public function loadFile($filename)
    {
        $this->filename = $filename;
    }

    protected function initParser() {
        $parser = xml_parser_create("utf-8");
        xml_parser_set_option($parser, XML_OPTION_CASE_FOLDING, false);
        xml_set_element_handler(
            $parser,
            array($this, "_tagStart"),
            array($this, "_tagEnd")
        );
        xml_set_character_data_handler(
            $parser,
            array($this, "_charData")
        );

        return $parser;
    }

    public function __construct() {

    }

    /**
     * Increase the nesting level for defs-like elements
     *
     * @return int
     */
    public function enterDefs () {
        $this->_defs_depth++;
        $this->inDefs = true;
        return $this->_defs_depth;
    }

    /**
     * Decrease the nesting level for defs-like elements
     *
     * @return int
     */
    public function exitDefs () {
        $this->_defs_depth--;
        if ($this->_defs_depth < 0) {
            $this->_defs_depth = 0;
        }
        $this->inDefs = ($this->_defs_depth > 0 ? true : false);
        return $this->_defs_depth;
    }

    /**
     * @return SurfaceInterface
     */
    public function getSurface()
    {
        return $this->surface;
    }

    public function getStack()
    {
        return $this->stack;
    }

    public function getWidth()
    {
        return $this->width;
    }

    public function getHeight()
    {
        return $this->height;
    }

    public function getDiagonal()
    {
        return sqrt(($this->width)**2 + ($this->height)**2) / sqrt(2);
    }

    public function getDimensions() {
        $rootAttributes = null;

        $parser = xml_parser_create("utf-8");
        xml_parser_set_option($parser, XML_OPTION_CASE_FOLDING, false);
        xml_set_element_handler(
            $parser,
            function ($parser, $name, $attributes) use (&$rootAttributes) {
                if ($name === "svg" && $rootAttributes === null) {
                    $attributes = array_change_key_case($attributes, CASE_LOWER);

                    $rootAttributes = $attributes;
                }
            },
            function ($parser, $name) {}
        );

        $fp = fopen($this->filename, "r");
        while ($line = fread($fp, 8192)) {
            xml_parse($parser, $line, false);

            if ($rootAttributes !== null) {
                break;
            }
        }
        xml_parse($parser, "", true);

        xml_parser_free($parser);

        return $this->handleSizeAttributes($rootAttributes);
    }

    public function handleSizeAttributes($attributes){
        if ($this->width === null) {
            if (isset($attributes["width"])) {
                $width = $this->convertSize($attributes["width"], 400);
                $this->width  = $width;
            }

            if (isset($attributes["height"])) {
                $height = $this->convertSize($attributes["height"], 300);
                $this->height = $height;
            }

            if (isset($attributes['viewbox'])) {
                $viewBox = preg_split('/[\s,]+/is', trim($attributes['viewbox']));
                if (count($viewBox) == 4) {
                    $this->x = $viewBox[0];
                    $this->y = $viewBox[1];

                    if (!$this->width) {
                        $this->width = $viewBox[2];
                    }
                    if (!$this->height) {
                        $this->height = $viewBox[3];
                    }
                }
            }
        }

        return array(
            0        => $this->width,
            1        => $this->height,

            "width"  => $this->width,
            "height" => $this->height,
        );
    }

    public function getDocument(){
        return $this;
    }

    /**
     * Append a style sheet
     *
     * @param \Onepix\FoodSpotVendor\Sabberworm\CSS\CSSList\Document $stylesheet
     */
    public function appendStyleSheet($stylesheet) {
        $this->styleSheets[] = $stylesheet;
    }

    /**
     * Get the document style sheets
     *
     * @return \Onepix\FoodSpotVendor\Sabberworm\CSS\CSSList\Document[]
     */
    public function getStyleSheets() {
        return $this->styleSheets;
    }

    protected function before($attributes)
    {
        $surface = $this->getSurface();

        $style = new DefaultStyle($this);
        $style->inherit($this);
        $style->fromAttributes($attributes);

        $this->setStyle($style);

        $surface->setStyle($style);
    }

    public function render(SurfaceInterface $surface)
    {
        $this->_defs_depth = 0;
        $this->inDefs = false;
        $this->surface = $surface;

        $parser = $this->initParser();

        if ($this->x || $this->y) {
            $surface->translate(-$this->x, -$this->y);
        }

        $fp = fopen($this->filename, "r");
        while ($line = fread($fp, 8192)) {
            xml_parse($parser, $line, false);
        }

        xml_parse($parser, "", true);

        xml_parser_free($parser);
    }

    protected function svgOffset($attributes)
    {
        $this->attributes = $attributes;

        $this->handleSizeAttributes($attributes);
    }

    public function getDef($id) {
        $id = ltrim($id, "#");

        return isset($this->defs[$id]) ? $this->defs[$id] : null;
    }

    private function _tagStart($parser, $name, $attributes)
    {
        $this->x = 0;
        $this->y = 0;

        $tag = null;

        $attributes = array_change_key_case($attributes, CASE_LOWER);

        switch (strtolower($name)) {
            case 'defs':
                $this->enterDefs();
                return;

            case 'svg':
                if (count($this->attributes)) {
                    $tag = new Group($this, $name);
                }
                else {
                    $tag = $this;
                    $this->svgOffset($attributes);
                }
                break;

            case 'path':
                $tag = new Path($this, $name);
                break;

            case 'rect':
                $tag = new Rect($this, $name);
                break;

            case 'circle':
                $tag = new Circle($this, $name);
                break;

            case 'ellipse':
                $tag = new Ellipse($this, $name);
                break;

            case 'image':
                $tag = new Image($this, $name);
                break;

            case 'line':
                $tag = new Line($this, $name);
                break;

            case 'polyline':
                $tag = new Polyline($this, $name);
                break;

            case 'polygon':
                $tag = new Polygon($this, $name);
                break;

            case 'lineargradient':
                $tag = new LinearGradient($this, $name);
                break;

            case 'radialgradient':
                $tag = new LinearGradient($this, $name);
                break;

            case 'stop':
                $tag = new Stop($this, $name);
                break;

            case 'style':
                $tag = new StyleTag($this, $name);
                break;

            case 'a':
                $tag = new Anchor($this, $name);
                break;

            case 'g':
                $tag = new Group($this, $name);
                break;

            case 'symbol':
                $this->enterDefs();
                $tag = new Symbol($this, $name);
                break;
    
            case 'clippath':
                $tag = new ClipPath($this, $name);
                break;

            case 'use':
                $tag = new UseTag($this, $name);
                break;

            case 'text':
                $tag = new Text($this, $name);
                break;

            case 'desc':
                return;
        }

        if ($tag) {
            if (isset($attributes["id"])) {
                $this->defs[$attributes["id"]] = $tag;
            }
            else {
                /** @var AbstractTag $top */
                $top = end($this->stack);
                if ($top && $top != $tag) {
                    $top->children[] = $tag;
                }
            }

            $this->stack[] = $tag;

            $tag->handle($attributes);
        }
    }

    function _charData($parser, $data)
    {
        $stack_top = end($this->stack);

        if ($stack_top instanceof Text || $stack_top instanceof StyleTag) {
            $stack_top->appendText($data);
        }
    }

    function _tagEnd($parser, $name)
    {
        /** @var AbstractTag $tag */
        $tag = null;
        switch (strtolower($name)) {
            case 'defs':
                $this->exitDefs();
                return;

            case 'symbol':
                $this->exitDefs();
                $tag = array_pop($this->stack);
                break;
    
            case 'svg':
            case 'path':
            case 'rect':
            case 'circle':
            case 'ellipse':
            case 'image':
            case 'line':
            case 'polyline':
            case 'polygon':
            case 'radialgradient':
            case 'lineargradient':
            case 'stop':
            case 'style':
            case 'text':
            case 'g':
            case 'clippath':
            case 'use':
            case 'a':
                $tag = array_pop($this->stack);
                break;
        }

        if ((!$this->inDefs && $tag) || $tag instanceof StyleTag) {
            $tag->handleEnd();
        }
    }
} 
