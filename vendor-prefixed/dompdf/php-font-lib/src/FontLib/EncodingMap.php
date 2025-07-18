<?php
/**
 * @package php-font-lib
 * @link    https://github.com/dompdf/php-font-lib
 * @license http://www.gnu.org/copyleft/lesser.html GNU Lesser General Public License
 */

namespace Onepix\FoodSpotVendor\FontLib;

/**
 * Encoding map used to map a code point to a Unicode char.
 *
 * @package php-font-lib
 */
class EncodingMap {
  private $f;

  function __construct($file) {
    $this->f = fopen($file, "r");
  }

  function parse() {
    $map = array();

    while ($line = fgets($this->f)) {
      if (preg_match('/^[\!\=]([0-9A-F]{2,})\s+U\+([0-9A-F]{2})([0-9A-F]{2})\s+([^\s]+)/', $line, $matches)) {
        $unicode = (hexdec($matches[2]) << 8) + hexdec($matches[3]);
        $map[hexdec($matches[1])] = array($unicode, $matches[4]);
      }
    }

    ksort($map);

    return $map;
  }
}
