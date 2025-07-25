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
namespace Onepix\FoodSpotVendor\Cake\Datasource\Exception;

use Onepix\FoodSpotVendor\Cake\Core\Exception\CakeException;

/**
 * Used when a model cannot be found.
 */
class MissingModelException extends CakeException
{
    /**
     * @var string
     */
    protected string $_messageTemplate = 'Model class `%s` of type `%s` could not be found.';
}
