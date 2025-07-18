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
 * @since         4.3.0
 * @license       https://opensource.org/licenses/mit-license.php MIT License
 */
namespace Onepix\FoodSpotVendor\Cake\Database\Expression;

use Onepix\FoodSpotVendor\Cake\Chronos\ChronosDate;
use Onepix\FoodSpotVendor\Cake\Database\ExpressionInterface;
use Onepix\FoodSpotVendor\Cake\Database\Query;
use Onepix\FoodSpotVendor\Cake\Database\TypedResultInterface;
use Onepix\FoodSpotVendor\Cake\Database\ValueBinder;
use DateTimeInterface;
use Stringable;

/**
 * Trait that holds shared functionality for case related expressions.
 *
 * @internal
 */
trait CaseExpressionTrait
{
    /**
     * Infers the abstract type for the given value.
     *
     * @param mixed $value The value for which to infer the type.
     * @return string|null The abstract type, or `null` if it could not be inferred.
     */
    protected function inferType(mixed $value): ?string
    {
        $type = null;

        if (is_string($value)) {
            $type = 'string';
        } elseif (is_int($value)) {
            $type = 'integer';
        } elseif (is_float($value)) {
            $type = 'float';
        } elseif (is_bool($value)) {
            $type = 'boolean';
        } elseif ($value instanceof ChronosDate) {
            $type = 'date';
        } elseif ($value instanceof DateTimeInterface) {
            $type = 'datetime';
        } elseif (
            $value instanceof Stringable
        ) {
            $type = 'string';
        } elseif (
            $this->_typeMap !== null &&
            $value instanceof IdentifierExpression
        ) {
            $type = $this->_typeMap->type($value->getIdentifier());
        } elseif ($value instanceof TypedResultInterface) {
            $type = $value->getReturnType();
        }

        return $type;
    }

    /**
     * Compiles a nullable value to SQL.
     *
     * @param \Onepix\FoodSpotVendor\Cake\Database\ValueBinder $binder The value binder to use.
     * @param \Onepix\FoodSpotVendor\Cake\Database\ExpressionInterface|object|scalar|null $value The value to compile.
     * @param string|null $type The value type.
     * @return string
     */
    protected function compileNullableValue(ValueBinder $binder, mixed $value, ?string $type = null): string
    {
        if (
            $type !== null &&
            !($value instanceof ExpressionInterface)
        ) {
            $value = $this->_castToExpression($value, $type);
        }

        if ($value === null) {
            $value = 'NULL';
        } elseif ($value instanceof Query) {
            $value = sprintf('(%s)', $value->sql($binder));
        } elseif ($value instanceof ExpressionInterface) {
            $value = $value->sql($binder);
        } else {
            $placeholder = $binder->placeholder('c');
            $binder->bind($placeholder, $value, $type);
            $value = $placeholder;
        }

        return $value;
    }
}
