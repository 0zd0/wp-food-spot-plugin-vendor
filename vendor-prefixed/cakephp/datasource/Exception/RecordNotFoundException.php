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
use Onepix\FoodSpotVendor\Cake\Core\Exception\HttpErrorCodeInterface;

/**
 * Exception raised when a particular record was not found
 */
class RecordNotFoundException extends CakeException implements HttpErrorCodeInterface
{
    /**
     * @inheritDoc
     */
    protected int $_defaultCode = 404;
}
