<?php

declare (strict_types=1);
namespace Onepix\FoodSpotVendor\Carbon\Doctrine;

use Onepix\FoodSpotVendor\Carbon\Carbon;
use DateTime;
use Doctrine\DBAL\Platforms\AbstractPlatform;
use Doctrine\DBAL\Types\VarDateTimeType;
class DateTimeType extends VarDateTimeType implements CarbonDoctrineType
{
    /** @use \CarbonTypeConverter<Onepix\FoodSpotVendor\Carbon> */
    use CarbonTypeConverter;
    /**
     * @SuppressWarnings(PHPMD.UnusedFormalParameter)
     */
    public function convertToPHPValue(mixed $value, AbstractPlatform $platform): ?Onepix\FoodSpotVendor\Carbon
    {
        return $this->doConvertToPHPValue($value);
    }
}