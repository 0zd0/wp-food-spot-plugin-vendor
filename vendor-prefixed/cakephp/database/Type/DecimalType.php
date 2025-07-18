<?php
declare(strict_types=1);

/**
 * CakePHP(tm) : Rapid Development Framework (https://cakephp.org)
 * Copyright (c) Cake Software Foundation, Inc. (https://cakefoundation.org)
 *
 * Licensed under The MIT License
 * For full copyright and license information, please see the LICENSE.txt
 * Redistributions of files must retain the above copyright notice.
 *
 * @copyright     Copyright (c) Cake Software Foundation, Inc. (https://cakefoundation.org)
 * @link          https://cakephp.org CakePHP(tm) Project
 * @since         3.3.4
 * @license       https://opensource.org/licenses/mit-license.php MIT License
 */
namespace Onepix\FoodSpotVendor\Cake\Database\Type;

use Onepix\FoodSpotVendor\Cake\Database\Driver;
use Onepix\FoodSpotVendor\Cake\Database\Exception\DatabaseException;
use Cake\I18n\Number;
use InvalidArgumentException;
use PDO;
use Stringable;

/**
 * Decimal type converter.
 *
 * Use to convert decimal data between PHP and the database types.
 */
class DecimalType extends BaseType implements BatchCastingInterface
{
    /**
     * The class to use for representing number objects
     *
     * @var class-string<\Cake\I18n\Number>|string
     */
    public static string $numberClass = Number::class;

    /**
     * Whether numbers should be parsed using a locale aware parser
     * when marshaling string inputs.
     *
     * @var bool
     */
    protected bool $_useLocaleParser = false;

    /**
     * Convert decimal strings into the database format.
     *
     * @param mixed $value The value to convert.
     * @param \Onepix\FoodSpotVendor\Cake\Database\Driver $driver The driver instance to convert with.
     * @return string|float|int|null
     * @throws \InvalidArgumentException
     */
    public function toDatabase(mixed $value, Driver $driver): string|float|int|null
    {
        if ($value === null || $value === '') {
            return null;
        }

        if (is_numeric($value)) {
            return $value;
        }

        if ($value instanceof Stringable) {
            $str = (string)$value;

            if (is_numeric($str)) {
                return $str;
            }
        }

        throw new InvalidArgumentException(sprintf(
            'Cannot convert value `%s` of type `%s` to a decimal',
            print_r($value, true),
            get_debug_type($value),
        ));
    }

    /**
     * {@inheritDoc}
     *
     * @param mixed $value The value to convert.
     * @param \Onepix\FoodSpotVendor\Cake\Database\Driver $driver The driver instance to convert with.
     * @return string|null
     */
    public function toPHP(mixed $value, Driver $driver): ?string
    {
        if ($value === null) {
            return null;
        }

        return (string)$value;
    }

    /**
     * @inheritDoc
     */
    public function manyToPHP(array $values, array $fields, Driver $driver): array
    {
        foreach ($fields as $field) {
            if (!isset($values[$field])) {
                continue;
            }

            $values[$field] = (string)$values[$field];
        }

        return $values;
    }

    /**
     * @inheritDoc
     */
    public function toStatement(mixed $value, Driver $driver): int
    {
        return PDO::PARAM_STR;
    }

    /**
     * Marshalls request data into decimal strings.
     *
     * @param mixed $value The value to convert.
     * @return string|null Converted value.
     */
    public function marshal(mixed $value): ?string
    {
        if ($value === null || $value === '') {
            return null;
        }
        if (is_string($value) && $this->_useLocaleParser) {
            return $this->_parseValue($value);
        }
        if (is_numeric($value)) {
            return (string)$value;
        }
        if (is_string($value) && preg_match('/^[0-9,. ]+$/', $value)) {
            return $value;
        }

        return null;
    }

    /**
     * Sets whether to parse numbers passed to the marshal() function
     * by using a locale aware parser.
     *
     * @param bool $enable Whether to enable
     * @return $this
     * @throws \Onepix\FoodSpotVendor\Cake\Database\Exception\DatabaseException
     */
    public function useLocaleParser(bool $enable = true)
    {
        if ($enable === false) {
            $this->_useLocaleParser = $enable;

            return $this;
        }
        if (
            static::$numberClass === Number::class ||
            is_subclass_of(static::$numberClass, Number::class)
        ) {
            $this->_useLocaleParser = $enable;

            return $this;
        }
        throw new DatabaseException(
            sprintf('Cannot use locale parsing with the %s class', static::$numberClass),
        );
    }

    /**
     * Converts localized string into a decimal string after parsing it using
     * the locale aware parser.
     *
     * @param string $value The value to parse and convert to an float.
     * @return string
     */
    protected function _parseValue(string $value): string
    {
        $class = static::$numberClass;

        return (string)$class::parseFloat($value);
    }
}
