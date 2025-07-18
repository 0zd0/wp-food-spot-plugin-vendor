<?php

/**
 * @package php-font-lib
 * @link    https://github.com/dompdf/php-font-lib
 * @license http://www.gnu.org/copyleft/lesser.html GNU Lesser General Public License
 */

namespace Onepix\FoodSpotVendor\FontLib\Table\Type;

use Onepix\FoodSpotVendor\FontLib\Table\Table;

/**
 * `prep` font table.
 *
 * @package php-font-lib
 */
class prep extends Table
{
  private $rawData;
  protected function _parse() {
    $font = $this->getFont();
    $font->seek($this->entry->offset);
    $this->rawData = $font->read($this->entry->length);
  }
  function _encode() {
    return $this->getFont()->write($this->rawData, $this->entry->length);
  }
}
