<?php
/**
 * @package php-font-lib
 * @link    https://github.com/dompdf/php-font-lib
 * @license http://www.gnu.org/copyleft/lesser.html GNU Lesser General Public License
 * @version $Id: Font_Table_glyf.php 46 2012-04-02 20:22:38Z fabien.menager $
 */
namespace Onepix\FoodSpotVendor\FontLib\Glyph;

use Onepix\FoodSpotVendor\FontLib\Table\Type\glyf;
use Onepix\FoodSpotVendor\FontLib\TrueType\File;
use Onepix\FoodSpotVendor\FontLib\BinaryStream;

/**
 * `glyf` font table.
 *
 * @package php-font-lib
 */
class Outline extends BinaryStream {
  /**
   * @var \Onepix\FoodSpotVendor\FontLib\Table\Type\glyf
   */
  protected $table;

  protected $offset;
  protected $size;

  // Data
  public $numberOfContours;
  public $xMin;
  public $yMin;
  public $xMax;
  public $yMax;

  /**
   * @var string|null
   */
  public $raw;

  /**
   * @param glyf $table
   * @param                 $offset
   * @param                 $size
   *
   * @return Outline
   */
  static function init(glyf $table, $offset, $size, BinaryStream $font) {
    $font->seek($offset);

    if ($size === 0 || $font->readInt16() > -1) {
      /** @var OutlineSimple $glyph */
      $glyph = new OutlineSimple($table, $offset, $size);
    }
    else {
      /** @var OutlineComposite $glyph */
      $glyph = new OutlineComposite($table, $offset, $size);
    }

    $glyph->parse($font);

    return $glyph;
  }

  /**
   * @return File
   */
  function getFont() {
    return $this->table->getFont();
  }

  function __construct(glyf $table, $offset = null, $size = null) {
    $this->table  = $table;
    $this->offset = $offset;
    $this->size   = $size;
  }

  function parse(BinaryStream $font) {
    $font->seek($this->offset);

      $this->raw = $font->read($this->size);
  }

  function parseData() {
    $font = $this->getFont();
    $font->seek($this->offset);

    $this->numberOfContours = $font->readInt16();
    $this->xMin             = $font->readFWord();
    $this->yMin             = $font->readFWord();
    $this->xMax             = $font->readFWord();
    $this->yMax             = $font->readFWord();
  }

  function encode() {
    $font = $this->getFont();

    return $font->write($this->raw, mb_strlen((string) $this->raw, '8bit'));
  }

  function getSVGContours() {
    // Inherit
  }

  function getGlyphIDs() {
    return array();
  }
}

