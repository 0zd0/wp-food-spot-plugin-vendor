<?php

/**
 * @package dompdf
 * @link    https://github.com/dompdf/dompdf
 * @license http://www.gnu.org/copyleft/lesser.html GNU Lesser General Public License
 */
namespace Onepix\FoodSpotVendor\Dompdf;

/**
 * Create canvas instances
 *
 * The canvas factory creates canvas instances based on the
 * availability of rendering backends and config options.
 *
 * @package dompdf
 */
class CanvasFactory
{
    /**
     * Constructor is private: this is a static class
     */
    private function __construct()
    {
    }
    /**
     * @param \Dompdf         $dompdf
     * @param string|float[] $paper
     * @param string         $orientation
     * @param string|null    $class
     *
     * @return Canvas
     */
    static function get_instance(Dompdf $dompdf, $paper, string $orientation, ?string $class = null)
    {
        $backend = strtolower($dompdf->getOptions()->getPdfBackend());
        if (isset($class) && class_exists($class, false)) {
            $class .= "_Adapter";
        } else if (($backend === "auto" || $backend === "pdflib") && class_exists("PDFLib", false)) {
            $class = "Onepix\\FoodSpotVendor\\Dompdf\\Adapter\\PDFLib";
        } else if ($backend === "gd" && extension_loaded('gd')) {
            $class = "Onepix\\FoodSpotVendor\\Dompdf\\Adapter\\GD";
        } else {
            $class = "Onepix\\FoodSpotVendor\\Dompdf\\Adapter\\CPDF";
        }
        return new $class($paper, $orientation, $dompdf);
    }
}