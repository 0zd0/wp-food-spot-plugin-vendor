<?php
namespace Onepix\FoodSpotVendor\Dompdf\Css\Content;

abstract class ContentPart
{
    public function equals(self $other): bool
    {
        return $other instanceof static;
    }
}
