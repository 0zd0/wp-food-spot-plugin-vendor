<?php

declare(strict_types=1);

namespace Onepix\FoodSpotVendor\Doctrine\Inflector;

use Onepix\FoodSpotVendor\Doctrine\Inflector\Rules\English;
use Onepix\FoodSpotVendor\Doctrine\Inflector\Rules\French;
use Onepix\FoodSpotVendor\Doctrine\Inflector\Rules\NorwegianBokmal;
use Onepix\FoodSpotVendor\Doctrine\Inflector\Rules\Portuguese;
use Onepix\FoodSpotVendor\Doctrine\Inflector\Rules\Spanish;
use Onepix\FoodSpotVendor\Doctrine\Inflector\Rules\Turkish;
use InvalidArgumentException;

use function sprintf;

final class InflectorFactory
{
    public static function create(): LanguageInflectorFactory
    {
        return self::createForLanguage(Language::ENGLISH);
    }

    public static function createForLanguage(string $language): LanguageInflectorFactory
    {
        switch ($language) {
            case Language::ENGLISH:
                return new English\InflectorFactory();

            case Language::FRENCH:
                return new French\InflectorFactory();

            case Language::NORWEGIAN_BOKMAL:
                return new NorwegianBokmal\InflectorFactory();

            case Language::PORTUGUESE:
                return new Portuguese\InflectorFactory();

            case Language::SPANISH:
                return new Spanish\InflectorFactory();

            case Language::TURKISH:
                return new Turkish\InflectorFactory();

            default:
                throw new InvalidArgumentException(sprintf(
                    'Language "%s" is not supported.',
                    $language
                ));
        }
    }
}
