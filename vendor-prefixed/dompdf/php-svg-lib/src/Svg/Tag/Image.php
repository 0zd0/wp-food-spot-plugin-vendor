<?php
/**
 * @package php-svg-lib
 * @link    http://github.com/dompdf/php-svg-lib
 * @license GNU LGPLv3+ http://www.gnu.org/copyleft/lesser.html
 */

namespace Onepix\FoodSpotVendor\Svg\Tag;

use Onepix\FoodSpotVendor\Svg\Style;

class Image extends AbstractTag
{
    protected $x = 0;
    protected $y = 0;
    protected $width = 0;
    protected $height = 0;
    protected $href = null;

    protected function before($attributes)
    {
        parent::before($attributes);

        $surface = $this->document->getSurface();
        $surface->save();

        $this->applyTransform($attributes);
    }

    public function start($attributes)
    {
        $height = $this->document->getHeight();
        $width = $this->document->getWidth();
        $this->y = $height;

        if (isset($attributes['x'])) {
            $this->x = $this->convertSize($attributes['x'], $width);
        }
        if (isset($attributes['y'])) {
            $this->y = $height - $this->convertSize($attributes['y'], $height);
        }

        if (isset($attributes['width'])) {
            $this->width = $this->convertSize($attributes['width'], $width);
        }
        if (isset($attributes['height'])) {
            $this->height = $this->convertSize($attributes['height'], $height);
        }

        if (isset($attributes['xlink:href'])) {
            $this->href = $attributes['xlink:href'];
        }

        if (isset($attributes['href'])) {
            $this->href = $attributes['href'];
        }

        $this->document->getSurface()->transform(1, 0, 0, -1, 0, $height);

        $scheme = \strtolower(parse_url($this->href, PHP_URL_SCHEME) ?: "");
        if (
            $scheme === "phar" || \strtolower(\substr($this->href, 0, 7)) === "phar://"
            || ($this->document->allowExternalReferences === false && $scheme !== "data")
        ) {
            return;
        }

        $this->document->getSurface()->drawImage($this->href, $this->x, $this->y, $this->width, $this->height);
    }

    protected function after()
    {
        $this->document->getSurface()->restore();
    }
} 
