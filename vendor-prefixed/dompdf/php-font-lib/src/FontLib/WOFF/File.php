<?php
/**
 * @package php-font-lib
 * @link    https://github.com/dompdf/php-font-lib
 * @license http://www.gnu.org/copyleft/lesser.html GNU Lesser General Public License
 */

namespace Onepix\FoodSpotVendor\FontLib\WOFF;

use Onepix\FoodSpotVendor\FontLib\Table\DirectoryEntry;

/**
 * WOFF font file.
 *
 * @package php-font-lib
 *
 * @property TableDirectoryEntry[] $directory
 */
class File extends \Onepix\FoodSpotVendor\FontLib\TrueType\File {
  function parseHeader() {
    if (!empty($this->header)) {
      return;
    }

    $this->header = new Header($this);
    $this->header->parse();
  }

  public function load($file) {
    parent::load($file);

    $this->parseTableEntries();
    $dataOffset = $this->pos() + count($this->directory) * 20;

    $fw = $this->getTempFile(false);
    $fr = $this->f;

    $this->f = $fw;
    $offset  = $this->header->encode();

    foreach ($this->directory as $entry) {
      // Read ...
      $this->f = $fr;
      $this->seek($entry->offset);
      $data = $this->read($entry->length);

      if ($entry->length < $entry->origLength) {
        $data = (string) gzuncompress($data);
      }

      // Prepare data ...
      $length        = mb_strlen($data, '8bit');
      $entry->length = $entry->origLength = $length;
      $entry->offset = $dataOffset;

      // Write ...
      $this->f = $fw;

      // Woff Entry
      $this->seek($offset);
      $offset += $this->write($entry->tag, 4); // tag
      $offset += $this->writeUInt32($dataOffset); // offset
      $offset += $this->writeUInt32($length); // length
      $offset += $this->writeUInt32($length); // origLength
      $offset += $this->writeUInt32(DirectoryEntry::computeChecksum($data)); // checksum

      // Data
      $this->seek($dataOffset);
      $dataOffset += $this->write($data, $length);
    }

    $this->f = $fw;
    $this->seek(0);

    // Need to re-parse this, don't know why
    $this->header    = null;
    $this->directory = array();
    $this->parseTableEntries();
  }
}
