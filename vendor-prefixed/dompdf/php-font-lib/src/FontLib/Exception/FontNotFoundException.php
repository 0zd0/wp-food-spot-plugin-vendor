<?php

namespace Onepix\FoodSpotVendor\FontLib\Exception;

class FontNotFoundException extends \Exception
{
    public function __construct($fontPath)
    {
        $this->message = 'Font not found in: ' . $fontPath;
    }
}