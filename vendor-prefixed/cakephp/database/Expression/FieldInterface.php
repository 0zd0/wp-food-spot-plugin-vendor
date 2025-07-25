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

/**
 * Describes a getter and a setter for the a field property. Useful for expressions
 * that contain an identifier to compare against.
 */
interface FieldInterface
{
    /**
     * Sets the field name
     *
     * @param \Onepix\FoodSpotVendor\Cake\Database\ExpressionInterface|array|string $field The field to compare with.
     * @return void
     */
    public function setField(ExpressionInterface|array|string $field): void;

    /**
     * Returns the field name
     *
     * @return \Onepix\FoodSpotVendor\Cake\Database\ExpressionInterface|array|string
     */
    public function getField(): ExpressionInterface|array|string;
}
