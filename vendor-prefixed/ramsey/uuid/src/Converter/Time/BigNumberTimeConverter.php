<?php

/**
 * This file is part of the ramsey/uuid library
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 *
 * @copyright Copyright (c) Ben Ramsey <ben@benramsey.com>
 * @license http://opensource.org/licenses/MIT MIT
 */

declare(strict_types=1);

namespace Onepix\FoodSpotVendor\Ramsey\Uuid\Converter\Time;

use Onepix\FoodSpotVendor\Ramsey\Uuid\Converter\TimeConverterInterface;
use Onepix\FoodSpotVendor\Ramsey\Uuid\Math\BrickMathCalculator;
use Onepix\FoodSpotVendor\Ramsey\Uuid\Type\Hexadecimal;
use Onepix\FoodSpotVendor\Ramsey\Uuid\Type\Time;

/**
 * Previously used to integrate moontoast/math as a bignum arithmetic library, BigNumberTimeConverter is deprecated in
 * favor of GenericTimeConverter
 *
 * @deprecated Please transition to {@see GenericTimeConverter}.
 *
 * @immutable
 */
class BigNumberTimeConverter implements TimeConverterInterface
{
    private TimeConverterInterface $converter;

    public function __construct()
    {
        $this->converter = new GenericTimeConverter(new BrickMathCalculator());
    }

    public function calculateTime(string $seconds, string $microseconds): Hexadecimal
    {
        return $this->converter->calculateTime($seconds, $microseconds);
    }

    public function convertTime(Hexadecimal $uuidTimestamp): Time
    {
        return $this->converter->convertTime($uuidTimestamp);
    }
}
