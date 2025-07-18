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
 * @since         3.0.0
 * @license       https://opensource.org/licenses/mit-license.php MIT License
 */
namespace Onepix\FoodSpotVendor\Cake\Database\Expression;

use Onepix\FoodSpotVendor\Cake\Database\ExpressionInterface;
use Onepix\FoodSpotVendor\Cake\Database\Query;
use Onepix\FoodSpotVendor\Cake\Database\Type\ExpressionTypeCasterTrait;
use Onepix\FoodSpotVendor\Cake\Database\TypedResultInterface;
use Onepix\FoodSpotVendor\Cake\Database\TypedResultTrait;
use Onepix\FoodSpotVendor\Cake\Database\ValueBinder;

/**
 * This class represents a function call string in a SQL statement. Calls can be
 * constructed by passing the name of the function and a list of params.
 * For security reasons, all params passed are quoted by default unless
 * explicitly told otherwise.
 */
class FunctionExpression extends QueryExpression implements TypedResultInterface
{
    use ExpressionTypeCasterTrait;
    use TypedResultTrait;

    /**
     * The name of the function to be constructed when generating the SQL string
     *
     * @var string
     */
    protected string $_name;

    /**
     * Constructor. Takes a name for the function to be invoked and a list of params
     * to be passed into the function. Optionally you can pass a list of types to
     * be used for each bound param.
     *
     * By default, all params that are passed will be quoted. If you wish to use
     * literal arguments, you need to explicitly hint this function.
     *
     * ### Examples:
     *
     * `$f = new FunctionExpression('CONCAT', ['CakePHP', ' rules']);`
     *
     * Previous line will generate `CONCAT('CakePHP', ' rules')`
     *
     * `$f = new FunctionExpression('CONCAT', ['name' => 'literal', ' rules']);`
     *
     * Will produce `CONCAT(name, ' rules')`
     *
     * @param string $name the name of the function to be constructed
     * @param array $params list of arguments to be passed to the function
     * If associative the key would be used as argument when value is 'literal'
     * @param array<string, string>|array<string|null> $types Associative array of types to be associated with the
     * passed arguments
     * @param string $returnType The return type of this expression
     */
    public function __construct(string $name, array $params = [], array $types = [], string $returnType = 'string')
    {
        $this->_name = $name;
        $this->_returnType = $returnType;
        parent::__construct($params, $types, ',');
    }

    /**
     * Sets the name of the SQL function to be invoke in this expression.
     *
     * @param string $name The name of the function
     * @return $this
     */
    public function setName(string $name)
    {
        $this->_name = $name;

        return $this;
    }

    /**
     * Gets the name of the SQL function to be invoke in this expression.
     *
     * @return string
     */
    public function getName(): string
    {
        return $this->_name;
    }

    /**
     * Adds one or more arguments for the function call.
     *
     * @param \Onepix\FoodSpotVendor\Cake\Database\ExpressionInterface|array|string $conditions list of arguments to be passed to the function
     * If associative the key would be used as argument when value is 'literal'
     * @param array<string, string> $types Associative array of types to be associated with the
     * passed arguments
     * @param bool $prepend Whether to prepend or append to the list of arguments
     * @see \Onepix\FoodSpotVendor\Cake\Database\Expression\FunctionExpression::__construct() for more details.
     * @return $this
     */
    public function add(ExpressionInterface|array|string $conditions, array $types = [], bool $prepend = false)
    {
        $put = $prepend ? 'array_unshift' : 'array_push';
        $typeMap = $this->getTypeMap()->setTypes($types);
        /** @var array $conditions */
        foreach ($conditions as $k => $p) {
            if ($p === 'literal') {
                $put($this->_conditions, $k);
                continue;
            }

            if ($p === 'identifier') {
                $put($this->_conditions, new IdentifierExpression($k));
                continue;
            }

            $type = $typeMap->type($k);

            if ($type !== null && !$p instanceof ExpressionInterface) {
                $p = $this->_castToExpression($p, $type);
            }

            if ($p instanceof ExpressionInterface) {
                $put($this->_conditions, $p);
                continue;
            }

            $put($this->_conditions, ['value' => $p, 'type' => $type]);
        }

        return $this;
    }

    /**
     * @inheritDoc
     */
    public function sql(ValueBinder $binder): string
    {
        $parts = [];
        foreach ($this->_conditions as $condition) {
            if ($condition instanceof Query) {
                $condition = sprintf('(%s)', $condition->sql($binder));
            } elseif ($condition instanceof ExpressionInterface) {
                $condition = $condition->sql($binder);
            } elseif (is_array($condition)) {
                $p = $binder->placeholder('param');
                $binder->bind($p, $condition['value'], $condition['type']);
                $condition = $p;
            }
            $parts[] = $condition;
        }

        return $this->_name . sprintf('(%s)', implode(
            $this->_conjunction . ' ',
            $parts,
        ));
    }

    /**
     * The name of the function is in itself an expression to generate, thus
     * always adding 1 to the amount of expressions stored in this object.
     *
     * @return int
     */
    public function count(): int
    {
        return 1 + count($this->_conditions);
    }
}
